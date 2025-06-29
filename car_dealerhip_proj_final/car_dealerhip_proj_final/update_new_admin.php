<?php
require_once 'db_config.php';

// Admin credentials
$email = 'Blur243569@gmail.com';
$password = '1234566';

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Update admin password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "Admin password updated successfully!<br>";
    echo "Email: " . $email . "<br>";
    echo "New Password: " . $password . "<br>";
} else {
    echo "Error updating admin password.";
}

$stmt->close();
$conn->close();
?> 