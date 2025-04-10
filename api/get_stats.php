<?php
require "../config.php"; // DB connection

session_start();
$userId = $_SESSION['user_id']; // Assuming login system

$query = $pdo->prepare("SELECT money, food FROM hamsters WHERE id = ?");
$query->execute([$userId]);
$data = $query->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);
