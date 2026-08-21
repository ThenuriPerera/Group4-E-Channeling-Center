<?php
// ================================================================
// DATABASE CONNECTION (PDO)
// Update these 4 values to match your local MySQL / XAMPP setup
// ================================================================
$DB_HOST = "localhost";
$DB_NAME = "echanneling_db";
$DB_USER = "root";
$DB_PASS = "2003.05.01.Sene"; 

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS
    );
    // Throw real exceptions instead of silent failures
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
