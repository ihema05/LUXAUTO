<?php
require_once 'db_config.php';

// Admin credentials
$email = 'Blur243569@gmail.com';
$password = '1234566';
$is_admin = 1;

// Generate password hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing admin
    $stmt = $conn->prepare("UPDATE users SET password = ?, is_admin = ? WHERE email = ?");
    $stmt->bind_param("sis", $hashed_password, $is_admin, $email);
} else {
    // Create new admin
    $stmt = $conn->prepare("INSERT INTO users (email, password, is_admin) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $email, $hashed_password, $is_admin);
}

if ($stmt->execute()) {
    echo "Admin account created/updated successfully!<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
} else {
    echo "Error creating/updating admin account.";
}

$stmt->close();
$conn->close();
?> 