<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get existing user credentials
$stmt = $conn->prepare("SELECT * FROM user_credentials WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$credentials = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $zip_code = $_POST['zip_code'];

    if ($credentials) {
        // Update existing credentials
        $stmt = $conn->prepare("UPDATE user_credentials SET first_name = ?, last_name = ?, phone = ?, address = ?, city = ?, zip_code = ? WHERE user_id = ?");
        $stmt->bind_param("ssssssi", $first_name, $last_name, $phone, $address, $city, $zip_code, $user_id);
    } else {
        // Insert new credentials
        $stmt = $conn->prepare("INSERT INTO user_credentials (user_id, first_name, last_name, phone, address, city, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $first_name, $last_name, $phone, $address, $city, $zip_code);
    }

    if ($stmt->execute()) {
        $success_message = "Profile information updated successfully!";
        // Refresh credentials
        $stmt = $conn->prepare("SELECT * FROM user_credentials WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $credentials = $result->fetch_assoc();
    } else {
        $error_message = "Error updating profile information. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarCss.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 40px;
            background: #fff;
            border: 2px solid #c4a484;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #c4a484;
        }
        .profile-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .submit-button {
            grid-column: 1 / -1;
            padding: 12px 24px;
            background-color: #c4a484;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #b39476;
        }
        .success-message {
            color: #155724;
            background-color: #d4edda;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .page-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 600;
        }
        .page-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }
        @media (max-width: 768px) {
            .profile-container {
                margin: 60px 20px;
                padding: 30px;
            }
            .profile-form {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: auto;
            }
        }
    </style>
</head>
<body>
<nav>
        <div class="nav-content">
            <a href="luxury-dealership.html" class="logo">LUX<span style="color: var(--accent-color)">AUTO</span></a>
            <div class="nav-links">
                <a href="luxury-dealership.html">Home</a>
                <a href="CarDearlship.php" class="active">Car </a>
                <a href="CarDearlship.html">Featured</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="user_carparts.php"> Parts</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="warranty.php">Warranty</a>
                    <a href="user-credentials.php" class="active">Profile</a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="admin_dashboard.php">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="login-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="credentials-hero">
        <div class="hero-content">
            <h1>Your Information</h1>
            <p>Please provide your details for warranty and service records</p>
        </div>
    </section>

    <div class="profile-container">
        <div class="profile-header">
            <h1>My Profile</h1>
            <p>Update your personal information</p>
        </div>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="profile-form">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($credentials['first_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($credentials['last_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($credentials['phone'] ?? ''); ?>" required>
            </div>

            <div class="form-group full-width">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($credentials['address'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($credentials['city'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="zip_code">ZIP Code</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($credentials['zip_code'] ?? ''); ?>" required>
            </div>

            <button type="submit" class="submit-button">Update Profile</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>
</body>
</html> 