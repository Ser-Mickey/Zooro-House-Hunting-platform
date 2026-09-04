<?php
// process_booking.php
require_once 'db_connect.php'; // was require_once 'db.php' — nonexistent file, fatal error on every request

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_title = trim($_POST['property_title'] ?? '');
    $property_price = $_POST['property_price'] ?? 0;
    $client_name    = trim($_POST['client_name'] ?? '');
    $client_phone   = trim($_POST['client_phone'] ?? '');
    $client_email   = trim($_POST['client_email'] ?? '');
    $preferred_date = $_POST['preferred_date'] ?? '';
    $notes          = trim($_POST['notes'] ?? '');

    if (!empty($client_name) && !empty($client_phone) && !empty($preferred_date)) {
        $stmt = $conn->prepare(
            "INSERT INTO property_bookings (property_title, property_price, client_name, client_phone, client_email, preferred_date, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sdsssss", $property_title, $property_price, $client_name, $client_phone, $client_email, $preferred_date, $notes);
        // 7 params -> type string needs 7 chars: s d s s s s s (title, price, name, phone, email, date, notes)

        if ($stmt->execute()) {
            echo "<h2>Application submitted successfully!</h2><p>The landlord or agent will contact you shortly.</p><p><a href='listings.html'>Back to Listings</a></p>";
        } else {
            error_log("property_bookings insert failed: " . $stmt->error);
            echo "<h2>Something went wrong submitting your application.</h2><p><a href='listings.html'>Back to Listings</a></p>";
        }
        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
}

$conn->close();
