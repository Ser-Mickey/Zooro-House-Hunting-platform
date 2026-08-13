<?php
require_once 'db_connect.php';
require_once 'landlord_auth.php'; // Gatekeeper: Enforces login & verification

$landlordId   = $_SESSION['landlord_id'];
$landlordName = $_SESSION['landlord_name'];
$jurisdiction = $_SESSION['jurisdiction_location'];

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = trim($_POST['title'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $houseType     = trim($_POST['house_type'] ?? '');
    $rent          = floatval($_POST['rent'] ?? 0);
    $availableFrom = $_POST['available_from'] ?? '';
    $description   = trim($_POST['description'] ?? '');

    // Validation
    if (empty($title) || empty($location) || empty($houseType) || $rent <= 0 || empty($availableFrom)) {
        $error = "Please fill in all required fields accurately.";
    } else {
        $stmt = $conn->prepare("INSERT INTO property_listings (landlord_id, title, location, house_type, rent, available_from, status, description) VALUES (?, ?, ?, ?, ?, ?, 'available', ?)");
        $stmt->bind_param("isssdss", $landlordId, $title, $location, $houseType, $rent, $availableFrom, $description);

        if ($stmt->execute()) {
            $message = "Property successfully posted and marked as 'Available'!";
        } else {
            $error = "Failed to post property: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post New House | Zooro Kenya</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .navbar { background: #1e293b; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { margin: 0; font-size: 20px; }
        .navbar a { color: #f8fafc; text-decoration: none; font-weight: bold; }
        .form-container { max-width: 600px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #334155; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; box-sizing: border-box; }
        .btn-submit { background: #2563eb; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn-submit:hover { background: #1d4ed8; }
        .alert-success { background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>🏡 Zooro Landlord Portal</h1>
        <a href="landlord_dashboard.php">← Back to Dashboard</a>
    </div>

    <div class="form-container">
        <h2>➕ Post New House Listing</h2>
        <p style="color: #64748b; margin-bottom: 20px;">Posting as <strong><?php echo htmlspecialchars($landlordName); ?></strong> (Jurisdiction: <?php echo htmlspecialchars($jurisdiction); ?>)</p>

        <?php if ($message): ?>
            <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="post_house.php">
            <div class="form-group">
                <label>Property Title *</label>
                <input type="text" name="title" required placeholder="e.g. Modern 2 Bedroom Master Ensuite">
            </div>

            <div class="form-group">
                <label>Location / Area *</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($jurisdiction); ?>" required>
            </div>

            <div class="form-group">
                <label>House Type *</label>
                <select name="house_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="Bedsitter">Bedsitter</option>
                    <option value="Studio">Studio</option>
                    <option value="1 Bedroom">1 Bedroom</option>
                    <option value="2 Bedroom">2 Bedroom</option>
                    <option value="3 Bedroom">3 Bedroom</option>
                </select>
            </div>

            <div class="form-group">
                <label>Monthly Rent (KSh) *</label>
                <input type="number" step="0.01" name="rent" required placeholder="e.g. 45000">
            </div>

            <div class="form-group">
                <label>Available From Date *</label>
                <input type="date" name="available_from" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label>Description & Amenities</label>
                <textarea name="description" rows="4" placeholder="Describe natural lighting, parking, borehole, security, etc."></textarea>
            </div>

            <button type="submit" class="btn-submit">Publish House Listing</button>
        </form>
    </div>

</body>
</html>