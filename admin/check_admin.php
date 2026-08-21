<?php
require_once '../includes/db_connect.php';

echo "<h2>Admin Database Check</h2>";

// Check if table exists
try {
    $tables = $pdo->query("SHOW TABLES LIKE 'admins'");
    if ($tables->rowCount() > 0) {
        echo "✅ Admins table exists<br>";
    } else {
        echo "❌ Admins table does NOT exist. Please run database.sql<br>";
        exit;
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    exit;
}

// Check admin records
try {
    $admins = $pdo->query("SELECT admin_id, username, password FROM admins");
    $count = $admins->rowCount();
    echo "📊 Found $count admin record(s)<br>";
    
    while ($row = $admins->fetch(PDO::FETCH_ASSOC)) {
        echo "<br>Admin ID: " . $row['admin_id'] . "<br>";
        echo "Username: " . $row['username'] . "<br>";
        echo "Password hash: " . substr($row['password'], 0, 30) . "...<br>";
        
        // Test the password
        if (password_verify('admin123', $row['password'])) {
            echo "✅ Password 'admin123' works!<br>";
        } else {
            echo "⚠️ Password 'admin123' does NOT match the stored hash.<br>";
            echo "You may need to run setup_admin.php again.<br>";
        }
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='login.php'>Go to Login →</a>";
?>