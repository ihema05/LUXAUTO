<?php
require_once 'db_config.php';

// Generate new password hash
$new_password = "123456789";
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update admin password
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = 'admin@luxuryautogallery.com'");
$stmt->bind_param("s", $hashed_password);

if ($stmt->execute()) {
    echo "Admin password updated successfully!";
} else {
    echo "Error updating admin password.";
}

$stmt->close();
$conn->close();
?> 