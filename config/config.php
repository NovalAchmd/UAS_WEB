<?php
// Database configuration constants
define('DB_HOST', 'localhost');     // Database host (usually localhost)
define('DB_USERNAME', 'root');      // Database username 
define('DB_PASSWORD', '');          // Database password
define('DB_NAME', 'blog');       // Database name

// Create database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper handling of special characters
$conn->set_charset("utf8mb4");

// Optional: Set timezone if needed
date_default_timezone_set('Asia/Jakarta');