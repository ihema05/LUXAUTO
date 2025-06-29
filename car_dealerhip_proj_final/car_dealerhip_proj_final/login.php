<?php
require_once 'db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, email, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Debug information
        error_log("Attempting login for email: " . $email);
        error_log("Stored hash: " . $user['password']);
        error_log("Attempting to verify password");
        
        if (password_verify($password, $user['password'])) {
            error_log("Password verification successful");
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirect based on user type
            if ($user['is_admin']) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: CarDearlship.php");
            }
            exit();
        } else {
            error_log("Password verification failed");
            $error = "Invalid password";
        }
    } else {
        error_log("No user found with email: " . $email);
        $error = "Invalid email or password";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Luxury Auto Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="CarCss.css">
</head>
<body>
    <nav>
        <div class="nav-content">
            <a href="luxury-dealership.html" class="logo">LUX<span style="color: var(--accent-color)">AUTO</span></a>
            <div class="nav-links">
                <a href="luxury-dealership.html">Home</a>
                <a href="CarDearlship.php">Car Sales</a>
                <a href="CarDearlship.html">Featured</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="user_carparts.php">Car Parts</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="warranty.php">Warranty</a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="admin_dashboard.php">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="login-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn active">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="auth-hero">
        <div class="auth-content">
            <h1>Welcome Back</h1>
            <p>Sign in to access your luxury experience</p>
        </div>
    </section>

    <section class="auth-form-section">
        <div class="auth-form-container">
            <h2>Login to Your Account</h2>
            <?php 
            if (isset($_SESSION['message'])) {
                echo "<p class='success'>" . $_SESSION['message'] . "</p>";
                unset($_SESSION['message']);
            }
            if (isset($error)) echo "<p class='error'>$error</p>"; 
            ?>
            
            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required placeholder="Enter your email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>
                
                <button type="submit" class="auth-submit-btn">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>
</body>
</html> 