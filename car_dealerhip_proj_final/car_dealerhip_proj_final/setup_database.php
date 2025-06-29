<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create connection without database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Drop existing tables if they exist
    $conn->query("DROP TABLE IF EXISTS car_dealership.users");
    $conn->query("DROP TABLE IF EXISTS car_dealership.cars");

    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS car_dealership";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully or already exists<br>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }

    // Select the database
    $conn->select_db("car_dealership");

    // Create users table with admin column
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully or already exists<br>";
        
        // Create admin user if not exists
        $admin_email = "admin@luxauto.com";
        $admin_password = password_hash("123456789", PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT IGNORE INTO users (email, password, is_admin) VALUES (?, ?, TRUE)");
        $stmt->bind_param("ss", $admin_email, $admin_password);
        $stmt->execute();
        $stmt->close();
    } else {
        throw new Exception("Error creating users table: " . $conn->error);
    }

    // Create cars table
    $sql = "CREATE TABLE IF NOT EXISTS cars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        make VARCHAR(100) NOT NULL,
        model VARCHAR(100) NOT NULL,
        year INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Cars table created successfully or already exists<br>";
    } else {
        throw new Exception("Error creating cars table: " . $conn->error);
    }

    echo "Database setup completed successfully!";
    
} catch (Exception $e) {
    die("Setup Error: " . $e->getMessage());
}

$conn->close();
?> 