<?php
$hamsterResult = $conn->query("SELECT color1, color2, shadow1, shadow2 FROM hamsters WHERE user_id = '$user_ID' ORDER BY user_id DESC LIMIT 1");
$hamsterData = $hamsterResult->fetch_assoc();
$color1 = $hamsterData['color1'] ?? "#ffcc00";
$color2 = $hamsterData['color2'] ?? "#d4a500";
$shadow1 = $hamsterData['shadow1'] ?? "#ffcc00";
$shadow2 = $hamsterData['shadow2'] ?? "#d4a500";