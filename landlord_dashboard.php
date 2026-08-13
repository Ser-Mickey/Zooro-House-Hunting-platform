<?php
require_once 'db_connect.php';
require_once 'landlord_auth.php'; // Ensures landlord is logged in and verified

$landlordId   = $_SESSION['landlord_id'];
$landlordName = $_SESSION['landlord_name'];
$jurisdiction = $_SESSION['jurisdiction_location'];

$message = '';

// Handle Property Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $propertyId = intval($_POST['property_id']);
    $newStatus  = $_POST['status'];

    if (in_array($newStatus, ['available', 'booked', 'occupied'])) {
        $stmt = $conn->prepare("UPDATE property_listings SET status = ? WHERE property_id = ? AND landlord_id = ?");
        $stmt->bind_param("sii", $newStatus, $propertyId, $landlordId);
        if ($stmt->execute()) {
            $message = "Property status updated to '" . ucfirst($newStatus) . "'.";
        }
        $stmt->close();
    }
}

// 1. Fetch House Hunt Requests matching Jurisdiction
$huntStmt = $conn->prepare("SELECT * FROM house_hunt_requests WHERE hunt_location LIKE ? ORDER BY created_at DESC");
$searchJurisdiction = "%{$jurisdiction}%";
$huntStmt->bind_param("s", $searchJurisdiction);
$huntStmt->execute();
$huntsResult = $huntStmt->get_result();

// 2. Fetch Landlord's Property Listings
$propStmt = $conn->prepare("SELECT * FROM property_listings WHERE landlord_id = ? ORDER BY created_at DESC");
$propStmt->bind_param("i", $landlordId);
$propStmt->execute();
$propsResult = $propStmt->get_result();

// Count property state metrics
$availableCount = 0;
$bookedCount    = 0;
$occupiedCount  = 0;

$properties = [];
while ($row = $propsResult->fetch_assoc()) {
    $properties[] = $row;
    if ($row['status'] === 'available') $availableCount++;
    if ($row['status'] === 'booked')    $bookedCount++;
    if ($row['status'] === 'occupied')  $occupiedCount++;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landlord Portal | Zooro Kenya</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .navbar { background: #1e293b; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { margin: 0; font-size: 20px; }
        .navbar a { color: #f8fafc; text-decoration: none; background: #ef4444; padding: 8px 14px; border-radius: 4px; font-weight: bold; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); border-left: 5px solid #cbd5e1; }
        .stat-card.available { border-left-color: #22c55e; }
        .stat-card.booked { border-left-color: #f59e0b; }
        .stat-card.occupied { border-left-color: #ef4444; }
        .stat-card h3 { margin: 0 0 5px 0; color: #64748b; font-size: 14px; }
        .stat-card .number { font-size: 28px; font-weight: bold; color: #0f172a; }

        .section-title { font-size: 18px; color: #0f172a; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 40px; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: #f1f5f9; color: #475569; font-size: 13px; text-transform: uppercase; }
        
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; display: inline-block; text-transform: capitalize; }
        .badge-available { background: #dcfce7; color: #15803d; }
        .badge-booked { background: #fef3c7; color: #b45309; }
        .badge-occupied { background: #fee2e2; color: #b91c1c; }

        .btn-status { padding: 4px 8px; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; color: white; margin-right: 4px; }
        .btn-available { background: #22c55e; }
        .btn-booked { background: #f59e0b; }
        .btn-occupied { background: #ef4444; }
        
        .alert-success { background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>🏡 Zooro Landlord Portal | <?php echo htmlspecialchars($jurisdiction); ?> Jurisdiction</h1>
        <div>
            <span>Welcome, <strong><?php echo htmlspecialchars($landlordName); ?></strong></span>
            <a href="landlord_logout.php" style="margin-left: 15px;">Log Out</a>
        </div>
    </div>

    <div class="container">
        
        <?php if ($message): ?>
            <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Inventory Stats -->
        <div class="stats-grid">
            <div class="stat-card available">
                <h3>Available Houses</h3>
                <div class="number"><?php echo $availableCount; ?></div>
            </div>
            <div class="stat-card booked">
                <h3>Booked Houses</h3>
                <div class="number"><?php echo $bookedCount; ?></div>
            </div>
            <div class="stat-card occupied">
                <h3>Occupied Houses</h3>
                <div class="number"><?php echo $occupiedCount; ?></div>
            </div>
        </div>

        <!-- 1. Property Management Section -->
        <div class="section-title">
            <span>🏘️ Managed Property Inventory</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Property Title</th>
                    <th>Type</th>
                    <th>Rent (KSh)</th>
                    <th>Status</th>
                    <th>Action (Change Status)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($properties) > 0): ?>
                    <?php foreach ($properties as $prop): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($prop['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($prop['house_type']); ?></td>
                            <td>KSh <?php echo number_format($prop['rent'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $prop['status']; ?>">
                                    <?php echo $prop['status']; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="property_id" value="<?php echo $prop['property_id']; ?>">
                                    
                                    <?php if ($prop['status'] !== 'available'): ?>
                                        <button type="submit" name="status" value="available" class="btn-status btn-available">Set Available</button>
                                    <?php endif; ?>
                                    
                                    <?php if ($prop['status'] !== 'booked'): ?>
                                        <button type="submit" name="status" value="booked" class="btn-status btn-booked">Set Booked</button>
                                    <?php endif; ?>
                                    
                                    <?php if ($prop['status'] !== 'occupied'): ?>
                                        <button type="submit" name="status" value="occupied" class="btn-status btn-occupied">Set Occupied</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #64748b;">No properties listed under this account yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 2. House Hunt Leads in Jurisdiction -->
        <div class="section-title">
            <span>🔍 Client House-Hunt Leads in "<?php echo htmlspecialchars($jurisdiction); ?>"</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Phone</th>
                    <th>House Needed</th>
                    <th>Max Budget</th>
                    <th>Timeframe</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($huntsResult && $huntsResult->num_rows > 0): ?>
                    <?php while ($hunt = $huntsResult->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($hunt['client_name']); ?></strong></td>
                            <td><a href="tel:<?php echo htmlspecialchars($hunt['client_phone']); ?>"><?php echo htmlspecialchars($hunt['client_phone']); ?></a></td>
                            <td><?php echo htmlspecialchars($hunt['house_type']); ?></td>
                            <td>KSh <?php echo number_format($hunt['max_budget'], 2); ?></td>
                            <td><?php echo htmlspecialchars($hunt['timeframe']); ?></td>
                            <td><?php echo htmlspecialchars($hunt['notes'] ?? 'None'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center; color: #64748b;">No active client hunt requests in your jurisdiction right now.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</body>
</html>