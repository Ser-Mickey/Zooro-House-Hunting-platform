<?php
// db.php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "myDB";

// Open connection to MySQL server
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verify connection status
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());[cite: 1]
}
?>