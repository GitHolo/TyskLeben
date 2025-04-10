<?php
require "../config.php"; // Include database connection

// Decrease food by 2 for every user
$query = "UPDATE game SET food = GREATEST(food - 2, 0)";
if ($conn->query($query)) {
    echo "Food has been decreased by 2 for all users.";
} else {
    echo "Error updating food: " . $conn->error;
}
?>