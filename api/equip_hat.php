<?php
require "../config.php"; // Include database connection

// Check if the user is logged in
if (!isset($_COOKIE['user_ID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_ID = $_COOKIE['user_ID'];
$data = json_decode(file_get_contents('php://input'), true);

$hat_id = $data['hat_id'];
$action = $data['action']; // 'equip' or 'unequip'

if ($action === 'equip') {
    // First, unequip the currently equipped hat
    $conn->query("UPDATE user_hats SET equipped = 0 WHERE user_id = '$user_ID'");

    // Equip the selected hat
    $conn->query("UPDATE user_hats SET equipped = 1 WHERE user_id = '$user_ID' AND hat_id = '$hat_id'");
} elseif ($action === 'unequip') {
    // Set the hat to unequipped
    $conn->query("UPDATE user_hats SET equipped = 0 WHERE user_id = '$user_ID' AND hat_id = '$hat_id'");
}

echo json_encode(['success' => true]);
?>