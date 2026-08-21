<?php
require_once '../includes/db_connect.php';

echo "<h2>🔐 Complete Admin Setup</h2>";

// Step 1: Check if database exists
try {
    $pdo->query("SELECT 1");
    echo "✅ Database connection successful.<br>";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Step 2: Create admins table if not exists
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            admin_id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL
        )
    ");
    echo "✅ Admins table ready.<br>";
} catch (PDOException $e) {
    die("❌ Failed to create admins table: " . $e->getMessage());
}

// Step 3: Insert/Update admin
$username = 'admin';
$password = 'admin123';
$hashed = password_hash($password, PASSWORD_DEFAULT);

try {
    $check = $pdo->prepare("SELECT admin_id FROM admins WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hashed, $username]);
        echo "✅ Admin password updated.<br>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed]);
        echo "✅ Admin account created.<br>";
    }
} catch (PDOException $e) {
    die("❌ Failed to create admin: " . $e->getMessage());
}

// Step 4: Verify
try {
    $verify = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $verify->execute([$username]);
    $admin = $verify->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        echo "✅ Admin verification successful!<br><br>";
    } else {
        echo "⚠️ Admin verification failed.<br><br>";
    }
} catch (PDOException $e) {
    echo "⚠️ Verification error: " . $e->getMessage() . "<br>";
}

echo "<strong>Login Credentials:</strong><br>";
echo "👤 Username: <b>admin</b><br>";
echo "🔑 Password: <b>admin123</b><br><br>";

echo "<a href='login.php' style='display:inline-block; padding:12px 24px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px; margin-right:10px;'>Go to Login</a>";
echo "<a href='../index.php' style='display:inline-block; padding:12px 24px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;'>Back to Home</a>";
?>