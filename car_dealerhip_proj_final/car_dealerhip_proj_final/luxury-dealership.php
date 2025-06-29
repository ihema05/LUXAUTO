<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarCss.css">
</head>
<body>
    <nav>
        <div class="nav-content">
            <a href="luxury-dealership.php" class="logo">LUX<span style="color: var(--accent-color)">AUTO</span></a>
            <div class="nav-links">
                <a href="luxury-dealership.php" class="active"><span>Home</span></a>
                <a href="CarDearlship.php"><span>Sales</span></a>
                <a href="CarDearlship.html"><span>Featured</span></a>
                <a href="about.html"><span>About</span></a>
                <a href="contact.html"><span>Contact</span></a>
                <a href="user_carparts.php"><span>Parts</span></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="warranty.php"><span>Warranty</span></a>
                    <a href="user-credentials.php"><span>Profile</span></a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="admin_dashboard.php"><span>Admin</span></a>
                    <?php endif; ?>
                    <a href="logout.php" class="login-btn"><span>Logout</span></a>
                <?php else: ?>
                    <a href="login.php" class="login-btn"><span>Login</span></a>
                    <a href="register.php" class="register-btn"><span>Register</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Rest of your home page content -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Luxury Auto Gallery</h1>
            <p>Discover the finest collection of luxury vehicles</p>
            <a href="CarDearlship.php" class="cta-button">View Collection</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>
</body>
</html> 