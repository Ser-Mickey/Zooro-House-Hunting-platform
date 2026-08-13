<?php
require_once 'db_connect.php';

$newPassword = 'Password123!';
$newHash     = password_hash($newPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE landlords SET password_hash = ? WHERE email = 'maina@zooro.co.ke'");
$stmt->bind_param("s", $newHash);

if ($stmt->execute()) {
    echo "<h2>✅ Password updated successfully!</h2>";
    echo "<p>You can now log in with:</p>";
    echo "<ul><li><strong>Email:</strong> maina@zooro.co.ke</li><li><strong>Password:</strong> Password123!</li></ul>";
    echo "<p><a href='landlord_login.php'>👉 Click here to Go to Login Page</a></p>";
} else {
    echo "Error updating password: " . $conn->error;
}
?>