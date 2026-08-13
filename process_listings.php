<?php
// process_listing.php
require_once 'db.php'; // Includes database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {[cite: 2]
    // Retrieve values using the input names from post.html[cite: 2]
    $landlord_name  = $_POST['landlord_name'];[cite: 2]
    $phone          = $_POST['phone'];[cite: 2]
    $location       = $_POST['location'];[cite: 2]
    $house_type     = $_POST['house_type'];[cite: 2]
    $rent           = $_POST['rent'];[cite: 2]
    $available_from = $_POST['available_from'];[cite: 2]
    $description    = $_POST['description'];[cite: 2]

    // File Upload Handling
    $image_name = "";
    if (isset($_FILES['photos']) && $_FILES['photos']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($_FILES["photos"]["name"]);
        move_uploaded_file($_FILES["photos"]["tmp_name"], $target_dir . $image_name);
    }

    if (!empty($landlord_name) && !empty($phone) && !empty($location) && !empty($rent)) {
        // Execute SQL INSERT query[cite: 1]
        $sql = "INSERT INTO property_listings (landlord_name, phone, location, house_type, rent, available_from, description, photo) 
                VALUES ('$landlord_name', '$phone', '$location', '$house_type', '$rent', '$available_from', '$description', '$image_name')";[cite: 1]

        if (mysqli_query($conn, $sql)) {[cite: 1]
            echo "<h2>Property listing submitted successfully!</h2><p><a href='post.html'>Return to Post Page</a></p>";
        } else {
            echo "Database Error: " . mysqli_error($conn);[cite: 1]
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

mysqli_close($conn);[cite: 1]
?>