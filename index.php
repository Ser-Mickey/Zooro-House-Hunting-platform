<?php
session_start();
require_once 'db_connect.php';

// Fetch a few available property listings for display
$query = "SELECT * FROM property_listings WHERE status = 'available' ORDER BY created_at DESC LIMIT 6";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zooro Kenya | House Hunting Made Easy</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f8fafc; color: #1e293b; }
        
        /* Navigation Bar */
        .navbar { background: #0f172a; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; }
        .navbar .logo { font-size: 24px; font-weight: bold; color: #38bdf8; text-decoration: none; }
        .nav-links { display: flex; gap: 15px; align-items: center; }
        .nav-links a { color: white; text-decoration: none; font-weight: 500; font-size: 15px; }
        .btn-post { background: #2563eb; color: white; padding: 10px 18px; border-radius: 6px; text-decoration: none; font-weight: bold; transition: background 0.2s; }
        .btn-post:hover { background: #1d4ed8; }

        /* Hero Banner */
        .hero { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; text-align: center; padding: 80px 20px; }
        .hero h1 { font-size: 38px; margin-bottom: 10px; }
        .hero p { font-size: 18px; color: #94a3b8; max-width: 600px; margin: 0 auto 30px auto; }

        /* Main Container */
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .section-title { font-size: 22px; margin-bottom: 20px; font-weight: bold; }

        /* Property Cards Grid */
        .property-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .card-body { padding: 20px; }
        .card-title { font-size: 18px; font-weight: bold; margin: 0 0 8px 0; }
        .card-location { color: #64748b; font-size: 14px; margin-bottom: 12px; }
        .card-price { font-size: 20px; font-weight: bold; color: #16a34a; margin-bottom: 15px; }
        .btn-view { display: block; text-align: center; background: #f1f5f9; color: #0f172a; padding: 10px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .btn-view:hover { background: #e2e8f0; }
    </style>
</head>
<body>

    <!-- Header / Navigation -->
    <header class="navbar">
        <a href="index.php" class="logo">🏡 Zooro Kenya</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['landlord_id'])): ?>
                <a href="landlord_dashboard.php">Dashboard</a>
                <a href="post_house.php" class="btn-post">+ Post a House</a>
                <a href="landlord_logout.php" style="color: #ef4444;">Log Out</a>
            <?php else: ?>
                <a href="landlord_login.php">Landlord Login</a>
                <a href="landlord_register.php">Register</a>
                <a href="post_house.php" class="btn-post">🏘️ Post a House</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Find Your Next Home in Nairobi</h1>
        <p>Connecting verified landlords with active house seekers instantly across all Nairobi jurisdictions.</p>
    </section>

    <!-- Main Content -->
    <main class="container">
        <div class="section-title">✨ Featured Available Listings</div>

        <div class="property-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="card-location">📍 <?php echo htmlspecialchars($row['location']); ?> • <?php echo htmlspecialchars($row['house_type']); ?></div>
                            <div class="card-price">KSh <?php echo number_format($row['rent'], 2); ?> / mo</div>
                            <a href="#" class="btn-view">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #64748b;">No available properties listed at the moment.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>