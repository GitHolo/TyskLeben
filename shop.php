<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="styles/index.css" rel="stylesheet">
</head>

<?php
require "config.php"; // Include database connection

// Check if user is logged in
if (!isset($_COOKIE['user_ID'])) {
    header("Location: login.php");
    exit();
}

$user_ID = $conn->real_escape_string($_COOKIE['user_ID']);

// Get user's money and food
require "./api/get_currency.php";

?>

<body class="bg-gray-100 min-h-screen flex flex-col items-center">
    <!-- Header -->
    <? 

    <!-- Main Content -->
    <main class="w-full max-w-md bg-white p-6 rounded-lg shadow-md mt-20 text-center">
        <h1 class="text-2xl font-bold mb-4">Shop</h1>
        <p class="text-lg">Money: 💰 <span class="font-bold"><?php echo $money; ?></span></p>
        <p class="text-lg">Food: 🍎 <span class="font-bold"><?php echo $food; ?></span></p>


    </main>
</body>

</html>