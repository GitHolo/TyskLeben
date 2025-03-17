<?php
header("Content-Type: application/json");
require "../config.php"; // DB connection

// Check if the user is logged in
if (!isset($_COOKIE['user_ID'])) {
    echo json_encode(["success" => false, "error" => "User not authenticated"]);
    exit();
}

$user_ID = $conn->real_escape_string($_COOKIE['user_ID']);

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$color1 = $conn->real_escape_string($data["color1"]);
$color2 = $conn->real_escape_string($data["color2"]);
$shadow1 = $conn->real_escape_string($data["shadow1"]);
$shadow2 = $conn->real_escape_string($data["shadow2"]);

// Save colors to database for the specific user
$sql = "INSERT INTO hamsters (user_id, color1, color2, shadow1, shadow2) VALUES ('$user_ID', '$color1', '$color2', '$shadow1', '$shadow2')";
$sql2 = "INSERT INTO game (user_id, money, food) VALUES ('$user_ID', '100', '100')";
if ($conn->query($sql) === TRUE) {
    $conn->query($sql2);
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
