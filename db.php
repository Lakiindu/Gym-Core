<?php
$host = 'localhost';
$db   = 'gym_db';
$user = 'postgres';
$pass = 'lakindu';
$charset = 'utf8mb4';

$dsn = "pgsql:host=$host;dbname=$db";  // or for MySQL: "mysql:host=$host;dbname=$db;charset=$charset"
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
