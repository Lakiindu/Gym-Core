<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership_id'])) {
    $membershipId = intval($_POST['membership_id']);

    $host = "localhost";
    $dbname = "gym_db";
    $user = "postgres";
    $password = "lakindu";

    try {
        $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $userId = $_SESSION['user_id'];

        // Fetch selected membership plan
        $stmt = $conn->prepare("SELECT plan_name, duration_days FROM buy_membership WHERE id = :id AND is_active = TRUE");
        $stmt->execute(['id' => $membershipId]);
        $selectedPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$selectedPlan) {
            $_SESSION['error_message'] = "Invalid membership selected.";
            header("Location: buy_membership.php");
            exit;
        }

        // Plan hierarchy (you can adjust this order)
        $planLevels = ['silver' => 1, 'gold' => 2, 'platinum' => 3, 'premium' => 4];
        $newPlan = strtolower($selectedPlan['plan_name']);
        $newPlanLevel = $planLevels[$newPlan];

        // Check existing active membership
        $checkStmt = $conn->prepare("SELECT * FROM memberships WHERE user_id = :user_id AND status = 'active' ORDER BY end_date DESC LIMIT 1");
        $checkStmt->execute(['user_id' => $userId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $startDate = new DateTime();
        $endDate = (clone $startDate)->modify('+' . $selectedPlan['duration_days'] . ' days');

        if ($existing) {
            $existingPlan = strtolower($existing['plan']);
            $existingPlanLevel = $planLevels[$existingPlan] ?? 0;
            $existingEndDate = new DateTime($existing['end_date']);
            $now = new DateTime();

            if ($existingEndDate >= $now) {
                // Active membership found
                if ($newPlanLevel <= $existingPlanLevel) {
                    $_SESSION['error_message'] = "You already have an active {$existing['plan']} membership. Upgrade to a higher plan to proceed.";
                    header("Location: buy_membership.php");
                    exit;
                }

                // Upgrade logic — UPDATE instead of INSERT
                $updateStmt = $conn->prepare("UPDATE memberships 
                                              SET plan = :plan, start_date = :start_date, end_date = :end_date, status = 'active' 
                                              WHERE id = :membership_id");
                $updateStmt->execute([
                    'plan' => $selectedPlan['plan_name'],
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'membership_id' => $existing['id']
                ]);

                $_SESSION['success_message'] = "Membership upgraded to '{$selectedPlan['plan_name']}' successfully!";
                header("Location: buy_membership.php");
                exit;
            }
        }

        // No active membership — Insert new one
        $insertStmt = $conn->prepare("INSERT INTO memberships (user_id, plan, start_date, end_date, status) 
                                      VALUES (:user_id, :plan, :start_date, :end_date, 'active')");
        $insertStmt->execute([
            'user_id' => $userId,
            'plan' => $selectedPlan['plan_name'],
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ]);

        $_SESSION['success_message'] = "Membership '{$selectedPlan['plan_name']}' bought successfully!";
        header("Location: buy_membership.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: buy_membership.php");
        exit;
    }
} else {
    header("Location: buy_membership.php");
    exit;
}
?>
