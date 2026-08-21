<?php
session_start();
require_once '../includes/db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Debug output (remove after fixing)
// echo "Username: " . $username . "<br>";
// echo "Password entered: " . $password . "<br>";

try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug: Check if admin found
    if (!$admin) {
        header('Location: login.php?error=' . urlencode('Admin user not found. Please run setup_admin.php first.'));
        exit;
    }
    
    // Debug: Show hash (remove after fixing)
    // echo "Stored hash: " . $admin['password'] . "<br>";
    
    // Verify password
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        // Check if using plain text password (legacy)
        if ($password === $admin['password']) {
            // Re-hash the password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
            $update->execute([$hashed, $admin['admin_id']]);
            
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        }
        header('Location: login.php?error=' . urlencode('Invalid password.'));
        exit;
    }
    
} catch (PDOException $e) {
    header('Location: login.php?error=' . urlencode('Database error: ' . $e->getMessage()));
    exit;
}
?>