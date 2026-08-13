<?php
// process_booking.php
require_once 'db.php'; // Includes database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values sent from the application / booking form
    $property_title = $_POST['property_title'];
    $property_price = $_POST['property_price'];
    $client_name    = $_POST['client_name'];
    $client_phone   = $_POST['client_phone'];
    $client_email   = $_POST['client_email'];
    $preferred_date = $_POST['preferred_date'];
    $notes          = $_POST['notes'];

    if (!empty($client_name) && !empty($client_phone) && !empty($preferred_date)) {
        // Execute SQL INSERT query
        $sql = "INSERT INTO property_bookings (property_title, property_price, client_name, client_phone, client_email, preferred_date, notes) 
                VALUES ('$property_title', '$property_price', '$client_name', '$client_phone', '$client_email', '$preferred_date', '$notes')";

        if (mysqli_query($conn, $sql)) {
            echo "<h2>Application submitted successfully!</h2><p>The landlord or agent will contact you shortly.</p><p><a href='listings.html'>Back to Listings</a></p>";
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

mysqli_close($conn);
?>