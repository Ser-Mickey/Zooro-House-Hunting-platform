<?php
session_start();
require_once 'db_connect.php';

$error = '';

if (isset($_GET['error']) && $_GET['error'] === 'please_login') {
    $error = "Please log in to access the Landlord Portal.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please provide both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT landlord_id, full_name, password_hash, national_id, jurisdiction_location, is_verified FROM landlords WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password_hash'])) {
                // Store session info
                $_SESSION['landlord_id']            = $row['landlord_id'];
                $_SESSION['landlord_name']          = $row['full_name'];
                $_SESSION['national_id']            = $row['national_id'];
                $_SESSION['jurisdiction_location'] = $row['jurisdiction_location'];
                $_SESSION['is_verified']           = $row['is_verified'];

                header("Location: landlord_dashboard.php");
                exit();
            } else {
                $error = "Invalid password entered.";
            }
        } else {
            $error = "No landlord account found with that email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landlord Login | Zooro Kenya</title>
    <style>
        body { font-family: sans-serif; background: #f1f5f9; margin: 0; padding: 0; }
        .auth-container { max-width: 420px; margin: 60px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .auth-container h2 { margin-top: 0; color: #1e293b; }
        .alert-error { padding: 10px 15px; background: #fee2e2; color: #991b1b; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #475569; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 4px; }
        .btn-primary { background: #2563eb; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; width: 100%; }
        .btn-primary:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>🔑 Landlord Login</h2>
        <?php if ($error): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="landlord_login.php">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="maina@zooro.co.ke">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
        <p style="margin-top: 15px; text-align: center; color: #64748b;">Need an account? <a href="landlord_register.php">Register Here</a></p>
    </div>
</body>
</html>