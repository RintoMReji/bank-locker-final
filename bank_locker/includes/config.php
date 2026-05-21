<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_NAME', 'bank_locker_db');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    return $conn;
}

// Site Configuration
define('SITE_NAME', 'Bank Locker Management System');
define('BANK_NAME', 'SecureBank Ltd.');

// Base URL - determines the subfolder path for the application
// Change this if you move the app to a different folder or to root
define('BASE_URL', '/bank_locker');
