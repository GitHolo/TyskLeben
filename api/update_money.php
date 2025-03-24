<?php
require "../config.php"; // Include your database connection

// Retrieve user ID from session or cookie
$userId = isset($_COOKIE['user_ID']) ? $_COOKIE['user_ID'] : null;  // Check if user_ID is set in the cookie

// Ensure the user ID exists
if ($userId === null) {
    echo "Error: User not authenticated.";
    exit;
}

// Check if money is set in the POST request
if (isset($_POST['money'])) {
    // Ensure the 'money' value is valid and convert it to a double
    $earnedMoney = doubleval($_POST['money']);



    // Prepare the SQL statement to update the money in the database
    $stmt = $conn->prepare("UPDATE game SET money = money + ? WHERE user_ID = ?");
    if ($stmt) {
        $stmt->bind_param("di", $earnedMoney, $userId); // Bind the parameters (double for money, int for user_ID)
        $stmt->execute(); // Execute the query

        if ($stmt->affected_rows > 0) {
            echo "Success: Money updated.";
        } else {
            echo "Error: User not found or no update made.";
        }
        $stmt->close(); // Close the prepared statement
    } else {
        echo "Error: Failed to prepare the SQL statement.";
    }
} else {
    echo "Error: Missing 'money' parameter.";
}
?>