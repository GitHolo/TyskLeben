<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="bg-gray-100 min-h-screen flex flex-col items-center bg-[url(./assets/svg/bg.svg)] bg-cover">
    <!-- Header -->
    <?php include './assets/site/header.php'; ?>

    <!-- Main Content -->
    <main class="w-full max-w-md bg-white p-6 rounded-lg shadow-md mt-20 text-center">
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold text-center mb-6">Hamster Shop</h1>

            <div class="grid grid-cols-1 gap-6">
                <!-- Food Shop -->
                <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                    <h2 class="text-2xl font-bold mb-4">Food</h2>
                    <img src="./assets/svg/food.svg" class="w-32 h-32 object-contain" alt="Food">
                    <button class="food-item bg-yellow-400 p-3 mt-4 rounded" data-item="seeds" data-price="5">Buy Seeds
                        - 5€</button>
                </div>

                <!-- Hat Shop -->
                <div class="bg-gray-800 text-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-4">Hats</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <button class="hat-item p-3 rounded" data-item="bowler" data-price="15">
                            <img src="./assets/svg/bowler.svg" class="w-24 h-24 object-cover mx-auto" alt="Bowler Hat">
                        </button>
                        <button class="hat-item p-3 rounded" data-item="cowboy" data-price="12">
                            <img src="./assets/svg/cowboy.svg" class="w-24 h-24 object-contain mx-auto"
                                alt="Cowboy Hat">
                        </button>
                        <button class="hat-item p-3 rounded" data-item="fedora" data-price="20">
                            <img src="./assets/svg/fedora.svg" class="w-24 h-24 object-contain mx-auto"
                                alt="Fedora Hat">
                        </button>
                        <button class="hat-item p-3 rounded" data-item="fez" data-price="10">
                            <img src="./assets/svg/fez.svg" class="w-24 h-24 object-contain mx-auto" alt="Fez Hat">
                        </button>
                        <button class="hat-item p-3 rounded" data-item="newsboy" data-price="15">
                            <img src="./assets/svg/newsboy.svg" class="w-24 h-24 object-contain mx-auto"
                                alt="Newsboy Hat">
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.querySelector('.food-item').addEventListener('click', function () {
                const item = this.dataset.item;
                const price = this.dataset.price;
                alert(`You bought ${item} for ${price}€!`);
                // Implement database update logic
            });

            document.querySelectorAll('.hat-item').forEach(button => {
                button.addEventListener('click', function () {
                    const item = this.dataset.item;
                    const price = this.dataset.price;
                    alert(`You bought a ${item} for ${price}€!`);
                    // Implement database update logic to store owned hats
                });
            });
        </script>
    </main>
</body>

</html>