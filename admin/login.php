<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = "Admin Login";
$basePath = "../";
$error = isset($_GET['error']) ? $_GET['error'] : '';

include '../includes/header.php';
?>

<div class="form-wrapper">
    <h2>🔐 Admin Login</h2>
    <p class="subtitle">Clinic manager / receptionist access only.</p>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required placeholder="Enter username">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter password">
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>

    <p style="margin-top:14px; text-align:center;">
        <a href="../index.php">&larr; Back to home</a>
    </p>
    <p style="margin-top:8px; text-align:center; font-size:12px; color:#999;">
        Default: admin / admin123
    </p>
</div>

<?php include '../includes/footer.php'; ?>