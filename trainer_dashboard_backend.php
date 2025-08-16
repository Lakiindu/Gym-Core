<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'trainer') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$host = "localhost";
$dbname = "gym_db";
$user = "postgres";
$password = "lakindu";

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$trainer_id = (int)$_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$payload = json_decode(file_get_contents('php://input') ?: '{}', true) ?? [];

// Helpers
function ok($data) { echo json_encode($data); exit; }
function bad($msg='Bad request', $code=400){ http_response_code($code); echo json_encode(['error'=>$msg]); exit; }

switch ($action) {

    // KPIs
    case 'kpis': {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainer_clients WHERE trainer_id = :tid");
        $stmt->execute([':tid'=>$trainer_id]);
        $clients = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM workout_plans WHERE trainer_id = :tid");
        $stmt->execute([':tid'=>$trainer_id]);
        $plans = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM sessions 
            WHERE trainer_id = :tid 
              AND session_date BETWEEN CURRENT_DATE AND CURRENT_DATE + INTERVAL '7 days'
        ");
        $stmt->execute([':tid'=>$trainer_id]);
        $sessions7d = (int)$stmt->fetchColumn();

        $q = $pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name='messages' AND column_name IN ('read_at','is_read')");
        $q->execute();
        $cols = $q->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('read_at', $cols, true)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = :tid AND read_at IS NULL");
            $stmt->execute([':tid'=>$trainer_id]);
            $unread = (int)$stmt->fetchColumn();
        } elseif (in_array('is_read', $cols, true)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = :tid AND is_read = FALSE");
            $stmt->execute([':tid'=>$trainer_id]);
            $unread = (int)$stmt->fetchColumn();
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = :tid");
            $stmt->execute([':tid'=>$trainer_id]);
            $unread = (int)$stmt->fetchColumn();
        }

        ok(compact('clients','plans','sessions7d','unread'));
    }

    // Clients list
    case 'clients': {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email,
                   m.status AS membership_status,
                   m.end_date AS membership_end
            FROM trainer_clients tc
            JOIN users u ON u.id = tc.client_id
            LEFT JOIN memberships m ON m.user_id = u.id
            WHERE tc.trainer_id = :tid
            ORDER BY u.username ASC
        ");
        $stmt->execute([':tid' => $trainer_id]);
        ok($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Bookings list (NEW)
    case 'bookings_list': {
        $stmt = $pdo->prepare("
            SELECT tb.id,
                   tb.user_id AS client_id,
                   u.username AS client_name,
                   tb.booking_date,
                   tb.booking_time,
                   tb.status,
                   tb.created_at
            FROM trainer_bookings tb
            JOIN users u ON u.id = tb.user_id
            WHERE tb.trainer_id = :tid
            ORDER BY tb.booking_date DESC, tb.booking_time DESC
        ");
        $stmt->execute([':tid' => $trainer_id]);
        ok($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Workout Plans list
    case 'workout_plans_list': {
        $stmt = $pdo->prepare("
            SELECT wp.id, wp.client_id, wp.title, wp.description, wp.exercises, wp.created_at, wp.updated_at
            FROM workout_plans wp
            WHERE wp.trainer_id = :tid
            ORDER BY wp.created_at DESC
        ");
        $stmt->execute([':tid'=>$trainer_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as &$r){
            if (!empty($r['exercises'])) {
                if (is_string($r['exercises'])) {
                    $decoded = json_decode($r['exercises'], true);
                    $r['exercises'] = $decoded === null ? $r['exercises'] : $decoded;
                }
            } else {
                $r['exercises'] = null;
            }
        }
        ok($rows);
    }

    case 'workout_plan_create': {
        if ($method !== 'POST') bad();
        $client_id = (int)($payload['client_id'] ?? 0);
        $title = trim($payload['title'] ?? '');
        $description = trim($payload['description'] ?? '');
        $exercises = $payload['exercises'] ?? '';

        if (!$client_id || !$title) bad('Missing client_id or title');
        $chk = $pdo->prepare("SELECT 1 FROM trainer_clients WHERE trainer_id=:tid AND client_id=:cid");
        $chk->execute([':tid'=>$trainer_id, ':cid'=>$client_id]);
        if (!$chk->fetch()) bad('Client not assigned to you', 403);

        $stmt = $pdo->prepare("
            INSERT INTO workout_plans (trainer_id, client_id, title, description, exercises, created_at, updated_at)
            VALUES (:tid, :cid, :title, :desc, CAST(:ex AS JSONB), NOW(), NOW())
            RETURNING id
        ");
        $stmt->execute([
            ':tid'=>$trainer_id,
            ':cid'=>$client_id,
            ':title'=>$title,
            ':desc'=>$description,
            ':ex'=>($exercises !== '' ? $exercises : 'null')
        ]);
        ok(['id'=>$stmt->fetchColumn()]);
    }

    case 'workout_plan_update': {
        if ($method !== 'POST') bad();
        $id = (int)($payload['id'] ?? 0);
        $client_id = (int)($payload['client_id'] ?? 0);
        $title = trim($payload['title'] ?? '');
        $description = trim($payload['description'] ?? '');
        $exercises = $payload['exercises'] ?? '';

        if (!$id || !$client_id || !$title) bad('Missing fields');
        $own = $pdo->prepare("SELECT 1 FROM workout_plans WHERE id=:id AND trainer_id=:tid");
        $own->execute([':id'=>$id, ':tid'=>$trainer_id]);
        if (!$own->fetch()) bad('Not found or not yours', 404);

        $chk = $pdo->prepare("SELECT 1 FROM trainer_clients WHERE trainer_id=:tid AND client_id=:cid");
        $chk->execute([':tid'=>$trainer_id, ':cid'=>$client_id]);
        if (!$chk->fetch()) bad('Client not assigned to you', 403);

        $stmt = $pdo->prepare("
            UPDATE workout_plans
               SET client_id=:cid, title=:title, description=:desc, exercises=CAST(:ex AS JSONB), updated_at=NOW()
             WHERE id=:id
        ");
        $stmt->execute([
            ':cid'=>$client_id,
            ':title'=>$title,
            ':desc'=>$description,
            ':ex'=>($exercises !== '' ? $exercises : 'null'),
            ':id'=>$id
        ]);
        ok(['ok'=>true]);
    }

    case 'workout_plan_delete': {
        if ($method !== 'POST') bad();
        $id = (int)($payload['id'] ?? 0);
        if (!$id) bad('Missing id');

        $own = $pdo->prepare("SELECT 1 FROM workout_plans WHERE id=:id AND trainer_id=:tid");
        $own->execute([':id'=>$id, ':tid'=>$trainer_id]);
        if (!$own->fetch()) bad('Not found or not yours', 404);

        $del = $pdo->prepare("DELETE FROM workout_plans WHERE id=:id");
        $del->execute([':id'=>$id]);
        ok(['ok'=>true]);
    }

    // Progress
    case 'progress_series': {
        if ($method !== 'POST') bad();
        $client_id = (int)($payload['client_id'] ?? 0);
        $metric = $payload['metric'] ?? 'weight';
        if (!in_array($metric, ['weight','bmi','strength'], true)) bad('Invalid metric');
        if (!$client_id) bad('Missing client_id');

        $chk = $pdo->prepare("SELECT 1 FROM trainer_clients WHERE trainer_id=:tid AND client_id=:cid");
        $chk->execute([':tid'=>$trainer_id, ':cid'=>$client_id]);
        if (!$chk->fetch()) bad('Client not assigned to you', 403);

        $sql = "SELECT log_date, $metric AS value
                  FROM progress_logs
                 WHERE trainer_id = :tid AND user_id = :cid
                   AND log_date >= CURRENT_DATE - INTERVAL '90 days'
                 ORDER BY log_date ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':tid'=>$trainer_id, ':cid'=>$client_id]);
        ok($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Sessions
    case 'sessions_list': {
        $stmt = $pdo->prepare("
            SELECT id, client_id, session_date, start_time, end_time, status, notes
              FROM sessions
             WHERE trainer_id=:tid
             ORDER BY session_date DESC, start_time DESC
        ");
        $stmt->execute([':tid'=>$trainer_id]);
        ok($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    case 'session_create': {
        if ($method !== 'POST') bad();
        $client_id = (int)($payload['client_id'] ?? 0);
        $session_date = $payload['session_date'] ?? '';
        $start_time = $payload['start_time'] ?? '';
        $end_time   = $payload['end_time'] ?? '';
        $notes      = trim($payload['notes'] ?? '');

        if (!$client_id || !$session_date || !$start_time || !$end_time) bad('Missing fields');
        $chk = $pdo->prepare("SELECT 1 FROM trainer_clients WHERE trainer_id=:tid AND client_id=:cid");
        $chk->execute([':tid'=>$trainer_id, ':cid'=>$client_id]);
        if (!$chk->fetch()) bad('Client not assigned to you', 403);

        $ins = $pdo->prepare("
            INSERT INTO sessions (trainer_id, client_id, session_date, start_time, end_time, status, notes, created_at, updated_at)
            VALUES (:tid, :cid, :sdate, :start, :end, 'scheduled', :notes, NOW(), NOW())
            RETURNING id
        ");
        $ins->execute([
            ':tid'=>$trainer_id,
            ':cid'=>$client_id,
            ':sdate'=>$session_date,
            ':start'=>$start_time,
            ':end'=>$end_time,
            ':notes'=>$notes
        ]);
        ok(['id'=>$ins->fetchColumn()]);
    }

    case 'session_delete': {
        if ($method !== 'POST') bad();
        $id = (int)($payload['id'] ?? 0);
        if (!$id) bad('Missing id');

        $own = $pdo->prepare("SELECT 1 FROM sessions WHERE id=:id AND trainer_id=:tid");
        $own->execute([':id'=>$id, ':tid'=>$trainer_id]);
        if (!$own->fetch()) bad('Not found or not yours', 404);

        $del = $pdo->prepare("DELETE FROM sessions WHERE id=:id");
        $del->execute([':id'=>$id]);
        ok(['ok'=>true]);
    }

    // Messages
    case 'messages_inbox': {
        $stmt = $pdo->prepare("
            SELECT m.id,
                   m.body,
                   COALESCE(m.created_at, m.sent_at) AS created_at,
                   su.username AS sender_name,
                   ru.username AS receiver_name
              FROM messages m
              JOIN users su ON su.id = m.sender_id
              JOIN users ru ON ru.id = m.receiver_id
             WHERE m.sender_id = :tid OR m.receiver_id = :tid
             ORDER BY COALESCE(m.created_at, m.sent_at) DESC
             LIMIT 100
        ");
        $stmt->execute([':tid'=>$trainer_id]);
        ok($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    case 'message_send': {
        if ($method !== 'POST') bad();
        $receiver_id = (int)($payload['receiver_id'] ?? 0);
        $body = trim($payload['body'] ?? '');
        if (!$receiver_id || $body === '') bad('Missing receiver_id or body');

        $chk = $pdo->prepare("SELECT 1 FROM trainer_clients WHERE trainer_id=:tid AND client_id=:cid");
        $chk->execute([':tid'=>$trainer_id, ':cid'=>$receiver_id]);
        if (!$chk->fetch()) bad('Receiver not your client', 403);

        $ins = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, body, created_at)
            VALUES (:sid, :rid, :body, NOW())
            RETURNING id
        ");
        $ins->execute([
            ':sid'=>$trainer_id,
            ':rid'=>$receiver_id,
            ':body'=>$body
        ]);
        ok(['id'=>$ins->fetchColumn()]);
    }

    default:
        bad('Unknown action');
}
