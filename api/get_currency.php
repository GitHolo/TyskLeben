<?php
$result = $conn->query("SELECT money, food FROM game WHERE user_id = '$user_ID'");
$userData = $result->fetch_assoc();
$money = $userData['money'] ?? 0;
$food = $userData['food'] ?? 0;