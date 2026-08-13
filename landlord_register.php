<?php
session_start();
require_once 'db_connect.php';

$message = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName     = trim($_POST['full_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $nationalId   = trim($_POST['national_id'] ?? '');
    $jurisdiction = trim($_POST['jurisdiction_location'] ?? '');
    $password     = $_POST['password'] ?? '';

    if (empty($fullName) || empty($email) || empty($phone) || empty($nationalId) || empty($jurisdiction) || empty($password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        // Check for existing Email or National ID
        $checkStmt = $conn->prepare("SELECT landlord_id FROM landlords WHERE email = ? OR national_id = ?");
        $checkStmt->bind_param("ss", $email, $nationalId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $message = "A landlord account with that Email or National ID already exists.";
        } else {
            // Hash password and store record (is_verified defaults to 0)
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO landlords (full_name, email, phone, password_hash, national_id, jurisdiction_location, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
            $stmt->bind_param("ssssss", $fullName, $email, $phone, $passwordHash, $nationalId, $jurisdiction);

            if ($stmt->execute()) {
                $isSuccess = true;
                $message = "Account created successfully! Please log in to view verification status.";
            } else {
                $message = "Error creating account: " . $conn->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landlord Registration | Zooro Kenya</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container { max-width: 480px; margin: 40px auto; padding: 25px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .auth-container h2 { margin-top: 0; color: #1e293b; }
        .alert { padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .alert.success { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>🏡 Register Landlord Account</h2>
        <?php if ($message): ?>
            <div class="alert <?php echo $isSuccess ? 'success' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="landlord_register.php">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" required placeholder="e.g. Maina Kamau">
            </div>
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required placeholder="maina@example.com">
            </div>
            <div class="form-group">
                <label>Phone Number *</label>
                <input type="tel" name="phone" required placeholder="0712345678">
            </div>
            <div class="form-group">
                <label>National ID / Passport No. *</label>
                <input type="text" name="national_id" required placeholder="12345678">
            </div>
            <div class="form-group">
                <label>Primary Jurisdiction Location *</label>
                <input type="text" name="jurisdiction_location" required placeholder="e.g. Kilimani, Westlands, Karen">
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Create Account</button>
        </form>
        <p style="margin-top: 15px; text-align: center;">Already registered? <a href="landlord_login.php">Log In here</a></p>
    </div>
</body>
</html>