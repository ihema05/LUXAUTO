<?php
require_once 'db_config.php';

// Admin credentials
$email = 'Blur243569@gmail.com';
$password = '1234566';
$is_admin = 1;

// Start transaction
$conn->begin_transaction();

try {
    // First, delete any existing admin with this email
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Generate fresh password hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Debug information
    echo "Original password: " . $password . "<br>";
    echo "Generated hash: " . $hashed_password . "<br>";
    
    // Verify the hash works
    if (password_verify($password, $hashed_password)) {
        echo "Hash verification successful!<br>";
    } else {
        echo "Hash verification failed!<br>";
    }
    
    // Create new admin user
    $stmt = $conn->prepare("INSERT INTO users (email, password, is_admin) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $email, $hashed_password, $is_admin);
    
    if ($stmt->execute()) {
        // Verify the stored password
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        echo "Stored hash: " . $user['password'] . "<br>";
        
        if (password_verify($password, $user['password'])) {
            echo "Stored password verification successful!<br>";
        } else {
            echo "Stored password verification failed!<br>";
        }
        
        $conn->commit();
        echo "<br>Admin account reset successfully!<br>";
        echo "Email: " . $email . "<br>";
        echo "Password: " . $password . "<br>";
        echo "Please try logging in with these credentials.";
    } else {
        throw new Exception("Error creating admin account");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$stmt->close();
$conn->close();
?> 