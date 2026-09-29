<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'cinema_db';

// Connect using mysqli
$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die('Database Connection Error: ' . mysqli_connect_error());
}
