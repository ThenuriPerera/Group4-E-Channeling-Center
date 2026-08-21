<?php
require_once 'includes/db_connect.php';
$pageTitle = "Register";
$basePath = "";

session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $contact = trim($_POST['contact'] ?? '');

    // Validate inputs
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($fullName) < 2) {
        $errors[] = "Please enter your full name.";
    }
    if ($age <= 0 || $age > 120) {
        $errors[] = "Please enter a valid age.";
    }
    if (!preg_match('/^[0-9]{10}$/', $contact)) {
        $errors[] = "Please enter a valid 10-digit contact number.";
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->rowCount() > 0) {
            $errors[] = "Username or email already exists. Please choose another.";
        }
    }

    // If no errors, create the user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, full_name, age, contact)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $email, $hashedPassword, $fullName, $age, $contact]);

        // Log the user in automatically
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_username'] = $username;
        $_SESSION['user_full_name'] = $fullName;

        header('Location: index.php?registered=1');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="form-wrapper">
    <h2>Create Account</h2>
    <p class="subtitle">Register to book appointments online.</p>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $err): ?>
            <p class="error-msg"><?php echo htmlspecialchars($err); ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required minlength="3" placeholder="Choose a username">
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="your@email.com">
        </div>

        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required placeholder="e.g. Kasun Perera">
        </div>

        <div class="card-row">
            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" required min="0" max="120" placeholder="e.g. 30">
            </div>
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="tel" id="contact" name="contact" required pattern="[0-9]{10}" placeholder="07XXXXXXXX">
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="Min 6 characters">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm password">
        </div>

        <button type="submit" class="btn-primary">Create Account</button>
    </form>

    <p style="margin-top:14px; text-align:center;">
        Already have an account? <a href="login.php" style="color:#0d6efd;">Login here</a>
    </p>
    <p style="text-align:center;"><a href="index.php">&larr; Back to home</a></p>
</div>

<script>
// Client-side validation for registration
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const contact = document.getElementById('contact').value.trim();
    const age = parseInt(document.getElementById('age').value);

    if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters.');
        return;
    }
    if (password !== confirm) {
        e.preventDefault();
        alert('Passwords do not match.');
        return;
    }
    if (!/^[0-9]{10}$/.test(contact)) {
        e.preventDefault();
        alert('Please enter a valid 10-digit contact number.');
        return;
    }
    if (isNaN(age) || age <= 0 || age > 120) {
        e.preventDefault();
        alert('Please enter a valid age (1-120).');
        return;
    }
});
</script>

<?php include 'includes/footer.php'; ?>