<?php
require "../config.php"; // Include database connection

// Turn off error reporting to avoid HTML being included in the response
error_reporting(0);
ini_set('display_errors', 0);

// Get the raw POST data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Get user ID from session or cookies
if (!isset($_COOKIE['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}
$user_ID = $conn->real_escape_string($_COOKIE['user_ID']);

// Define the food refill amount
$refillAmount = 50;
$maxFood = 100;

// Get the current food amount for the user
$query = "SELECT food FROM game WHERE user_ID = '$user_ID'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentFood = $row['food'];

    // Calculate new food amount, ensuring it doesn't exceed the max limit
    $newFood = min($currentFood + $refillAmount, $maxFood);

    // Update the user's food in the database
    $updateQuery = "UPDATE game SET food = '$newFood' WHERE user_ID = '$user_ID'";
    if ($conn->query($updateQuery)) {
        echo json_encode(['success' => true, 'message' => "Food refilled to $newFood."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating food.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}
?>