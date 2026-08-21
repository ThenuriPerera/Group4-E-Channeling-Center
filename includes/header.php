<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle . ' - E-Channeling Center' : 'E-Channeling Center'; ?></title>
<link rel="stylesheet" href="<?php echo isset($basePath) ? $basePath : ''; ?>css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php" class="logo">
            E-Channeling<span>Center</span>
        </a>
        <nav class="main-nav">
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="color:#fff; opacity:0.85;">
                    Welcome, <?php echo htmlspecialchars($_SESSION['user_full_name']); ?>
                </span>
                <a href="<?php echo isset($basePath) ? $basePath : ''; ?>logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo isset($basePath) ? $basePath : ''; ?>login.php">Login</a>
                <a href="<?php echo isset($basePath) ? $basePath : ''; ?>register.php">Register</a>
            <?php endif; ?>
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>admin/login.php">Admin</a>
        </nav>
    </div>
</header>