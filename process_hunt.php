<?php
// process_hunt.php
require_once 'db_connect.php'; // was require_once 'db.php' — that file doesn't exist, causing a fatal error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hunt_name       = trim($_POST['hunt_name'] ?? '');
    $hunt_phone      = trim($_POST['hunt_phone'] ?? '');
    $hunt_location   = trim($_POST['hunt_location'] ?? '');
    $hunt_house_type = trim($_POST['hunt_house_type'] ?? '');
    $hunt_budget     = trim($_POST['hunt_budget'] ?? '');
    $hunt_timeframe  = trim($_POST['hunt_timeframe'] ?? '');
    $hunt_notes      = trim($_POST['hunt_notes'] ?? '');

    if (!empty($hunt_name) && !empty($hunt_phone) && !empty($hunt_location)) {

        // zooro.sql's house_hunt_requests table uses client_name / client_phone / house_type / max_budget /
        // timeframe — NOT the hunt_* names this form was previously inserting under, which is why
        // landlord_dashboard.php's SELECT * FROM house_hunt_requests never matched anything real.
        // hunt_budget arrives as a range string ("KSh 10,000 – 20,000"); max_budget is DECIMAL, so
        // we store the upper bound (or 0 for "Under KSh 10,000" style ranges we can't parse).
        $maxBudget = 0;
        if (preg_match('/([\d,]+)\s*$/', str_replace(['KSh', ' '], '', $hunt_budget), $m)) {
            $maxBudget = (float) str_replace(',', '', $m[1]);
        }

        $stmt = $conn->prepare(
            "INSERT INTO house_hunt_requests (client_name, client_phone, hunt_location, house_type, max_budget, timeframe, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssdss", $hunt_name, $hunt_phone, $hunt_location, $hunt_house_type, $maxBudget, $hunt_timeframe, $hunt_notes);

        if ($stmt->execute()) {
            echo "<h2>House hunt request submitted successfully!</h2><p><a href='post.html'>Return to Post Page</a></p>";
        } else {
            error_log("house_hunt_requests insert failed: " . $stmt->error);
            echo "<h2>Something went wrong submitting your request.</h2><p><a href='post.html'>Return to Post Page</a></p>";
        }
        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
}

$conn->close();
