<?php
require_once 'db_config.php';

// Read and execute the SQL file
$sql = file_get_contents('sqlparts/update_user_credentials.sql');

if ($conn->multi_query($sql)) {
    echo "Database updated successfully!";
} else {
    echo "Error updating database: " . $conn->error;
}

$conn->close();
?> 