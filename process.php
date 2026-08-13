<?php
// process.php
require_once 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capturing values from HTML form inputs
    $user_name  = $_POST['username'];
    $user_email = $_POST['email'];

    if (!empty($user_name) && !empty($user_email)) {
        
        // Column names match your phpMyAdmin table: user_name and user_email
        $sql = "INSERT INTO users (user_name, user_email) VALUES ('$user_name', '$user_email')";

        if (mysqli_query($conn, $sql)) {
            echo "Success! Input saved to database.";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Please complete all required fields.";
    }
}

mysqli_close($conn);
?>