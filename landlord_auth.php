<?php
// landlord_auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if logged in
if (!isset($_SESSION['landlord_id'])) {
    header("Location: landlord_login.php?error=please_login");
    exit();
}

// 2. Check if verified
if (empty($_SESSION['is_verified']) || $_SESSION['is_verified'] != 1) {
    echo "
    <div style='max-width: 600px; margin: 50px auto; padding: 20px; font-family: sans-serif; border: 2px solid #eab308; background: #fefce8; border-radius: 8px;'>
        <h2 style='color: #854d0e; margin-top:0;'>⚠️ Verification Pending</h2>
        <p>Hello <strong>" . htmlspecialchars($_SESSION['landlord_name']) . "</strong>,</p>
        <p>Your Landlord National ID (<strong>" . htmlspecialchars($_SESSION['national_id']) . "</strong>) is currently undergoing verification by the Zooro admin team.</p>
        <p>Once approved, you will be able to post houses and view client hunt requests for <strong>" . htmlspecialchars($_SESSION['jurisdiction_location']) . "</strong>.</p>
        <a href='landlord_logout.php' style='display:inline-block; padding: 10px 15px; background: #dc2626; color: #fff; text-decoration: none; border-radius: 5px;'>Log Out</a>
    </div>";
    exit();
}
?>