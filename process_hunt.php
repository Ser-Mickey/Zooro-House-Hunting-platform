<?php
// process_hunt.php
require_once 'db.php'; // Includes database connection[cite: 2]

if ($_SERVER["REQUEST_METHOD"] == "POST") {[cite: 2]
    // Retrieve values using the input names from post.html[cite: 2]
    $hunt_name       = $_POST['hunt_name'];[cite: 2]
    $hunt_phone      = $_POST['hunt_phone'];[cite: 2]
    $hunt_location   = $_POST['hunt_location'];[cite: 2]
    $hunt_house_type = $_POST['hunt_house_type'];[cite: 2]
    $hunt_budget     = $_POST['hunt_budget'];[cite: 2]
    $hunt_timeframe  = $_POST['hunt_timeframe'];[cite: 2]
    $hunt_notes      = $_POST['hunt_notes'];[cite: 2]

    if (!empty($hunt_name) && !empty($hunt_phone) && !empty($hunt_location)) {
        // Execute SQL INSERT query[cite: 1]
        $sql = "INSERT INTO house_hunt_requests (hunt_name, hunt_phone, hunt_location, hunt_house_type, hunt_budget, hunt_timeframe, hunt_notes) 
                VALUES ('$hunt_name', '$hunt_phone', '$hunt_location', '$hunt_house_type', '$hunt_budget', '$hunt_timeframe', '$hunt_notes')";[cite: 1]

        if (mysqli_query($conn, $sql)) {[cite: 1]
            echo "<h2>House hunt request submitted successfully!</h2><p><a href='post.html'>Return to Post Page</a></p>";
        } else {
            echo "Database Error: " . mysqli_error($conn);[cite: 1]
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

mysqli_close($conn);[cite: 1]
?>