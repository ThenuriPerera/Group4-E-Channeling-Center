<?php
require_once 'includes/db_connect.php';
$pageTitle = "Login";
$basePath = "";

session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_full_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];

        // Redirect to the page they were trying to access, or home
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

include 'includes/header.php';
?>

<div class="form-wrapper">
    <h2>Login</h2>
    <p class="subtitle">Log in to book and manage your appointments.</p>

    <?php if ($error): ?>
        <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['registered'])): ?>
        <p class="success-msg">Registration successful! Please login.</p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" required placeholder="Enter your username or email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>

    <p style="margin-top:14px; text-align:center;">
        Don't have an account? <a href="register.php" style="color:#0d6efd;">Register here</a>
    </p>
    <p style="text-align:center;"><a href="index.php">&larr; Back to home</a></p>
</div>

<?php include 'includes/footer.php'; ?>