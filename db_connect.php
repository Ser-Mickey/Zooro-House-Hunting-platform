<?php
// db_connect.php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP MySQL password is empty
$db   = 'zooro';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>