<?php
require_once '../includes/db_connect.php';

$username = 'admin';
$newPassword = 'admin123';

// Hash the password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

echo "<h2>Admin Password Reset</h2>";

// Check if admin table exists
try {
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'admins'");
    if ($tableCheck->rowCount() == 0) {
        // Create the admins table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admins (
                admin_id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL
            )
        ");
        echo "✅ Admins table created successfully.<br>";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

try {
    // Check if admin exists
    $check = $pdo->prepare("SELECT admin_id FROM admins WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hashedPassword, $username]);
        echo "✅ Admin password updated successfully!<br>";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);
        echo "✅ Admin account created successfully!<br>";
    }
    
    // Verify the password
    $verify = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $verify->execute([$username]);
    $admin = $verify->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($newPassword, $admin['password'])) {
        echo "✅ Password verification successful!<br>";
    } else {
        echo "⚠️ Password verification failed. Please check the database.<br>";
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Show current admin data
echo "<h3>Current Admin Data:</h3>";
$show = $pdo->query("SELECT admin_id, username, password FROM admins");
while ($row = $show->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['admin_id'] . "<br>";
    echo "Username: " . $row['username'] . "<br>";
    echo "Password Hash: " . substr($row['password'], 0, 30) . "...<br><br>";
}

echo "<strong>Login Credentials:</strong><br>";
echo "🔑 Username: <b>admin</b><br>";
echo "🔑 Password: <b>admin123</b><br><br>";

echo "<a href='login.php' style='display:inline-block; padding:10px 20px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:6px;'>Go to Admin Login →</a>";
?>