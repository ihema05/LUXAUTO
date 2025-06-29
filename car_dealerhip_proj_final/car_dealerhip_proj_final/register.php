<?php
require_once 'db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = 0;

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already exists. Please use a different email.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (email, password, is_admin) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $email, $password, $is_admin);

            if ($stmt->execute()) {
                $user_id = $conn->insert_id;
                
                // Insert user credentials
                $stmt = $conn->prepare("INSERT INTO user_credentials (user_id, first_name, last_name) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $first_name, $last_name);
                
                if ($stmt->execute()) {
                    $conn->commit();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['email'] = $email;
                    $_SESSION['is_admin'] = $is_admin;
                    
                    // Set a message to inform user about completing their profile
                    $_SESSION['success'] = "Registration successful! Please complete your profile information to continue.";
                    
                    // Redirect to user-credentials page
                    header("Location: user-credentials.php");
                    exit();
                } else {
                    throw new Exception("Error creating user credentials");
                }
            } else {
                throw new Exception("Error creating user account");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error creating account. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Luxury Auto Gallery</title>
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
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn active">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="auth-hero">
        <div class="auth-content">
            <h1>Join Our Community</h1>
            <p>Create your account to start your luxury journey</p>
        </div>
    </section>

    <section class="auth-form-section">
        <div class="auth-form-container">
            <h2>Create Your Account</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            
            <form method="POST" action="" class="auth-form" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name">
                </div>
                
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
                        <input type="password" id="password" name="password" required placeholder="Create a password">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                    </div>
                </div>
                
                <button type="submit" class="auth-submit-btn">
                    <span>Create Account</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>

    <script>
    function validateForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return false;
        }
        
        if (password.length < 6) {
            alert("Password must be at least 6 characters long!");
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html> 