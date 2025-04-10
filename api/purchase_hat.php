<?php
require "../config.php"; // Include your database connection

// Turn on error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the raw POST data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Check if user is logged in
if (!isset($_COOKIE['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_ID = $_COOKIE['user_ID']; // Get the user ID from cookies

// Ensure user ID is an integer to prevent SQL injection
if (!is_numeric($user_ID)) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit();
}

// Get the item and price from the request
$item = $data['item'];
$price = (int) $data['price']; // Ensure the price is an integer

if (empty($item) || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item or price.']);
    exit();
}

// Prepare SQL query to get the hat ID and price based on item name
$query = "SELECT hat_id, price FROM hats WHERE hat_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $item);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $hat = $result->fetch_assoc();
    $hat_id = $hat['hat_id'];

    // Prepare query to get user money
    $query = "SELECT money FROM game WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userMoney = $row['money'];

        if ($userMoney >= $price) {
            // Deduct the money from the user's account
            $newMoney = $userMoney - $price;
            $updateQuery = "UPDATE game SET money = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('di', $newMoney, $user_ID);
            if ($updateStmt->execute()) {
                // Add the hat to the user's collection in the user_hats table
                $insertHatQuery = "INSERT INTO user_hats (user_id, hat_id) VALUES (?, ?)";
                $insertStmt = $conn->prepare($insertHatQuery);
                $insertStmt->bind_param('ii', $user_ID, $hat_id);
                if ($insertStmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Hat purchased successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error adding hat to account.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating user money.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Not enough money to buy this hat.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Hat not found.']);
}

// Close database connections
$stmt->close();
$conn->close();
?>