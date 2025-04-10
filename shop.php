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
                <script>
                    document.querySelector('.food-item').addEventListener('click', function () {
                        const item = this.dataset.item;
                        const price = this.dataset.price;


                        // Send the purchase request to the server
                        fetch('./api/purchase_food.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                item: item,  // This could be something like 'Food'
                                price: price
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Response Data:', data); // Log the response to verify it
                                if (data.success) {
                                    alert(`You successfully bought ${item}. Your food is refilled to ${data.message}`);
                                    location.reload();
                                } else {
                                    alert(data.message); // Show error message (e.g., not enough money, etc.)
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    });

                </script>
                <!-- Hat Shop -->
                <div class="bg-gray-800 text-white p-8 rounded-3xl shadow-2xl">
                    <h2 class="text-3xl font-extrabold mb-6 text-center">Hats</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <?php

                        // Fetch all hats from the database
                        $query = "SELECT hat_id, hat_name, price, image_url 
          FROM hats 
          WHERE hat_id NOT IN (SELECT hat_id FROM user_hats WHERE user_id = '$user_ID')";
                        $result = $conn->query($query);


                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Output each hat as a button (you can customize the HTML here)
                                echo '<button class="hat-item p-4 rounded-lg transform transition-all duration-300 hover:scale-105 hover:bg-gray-700 hover:shadow-xl" data-item="' . $row['hat_name'] . '" data-price="' . $row['price'] . '">
                <img src="' . $row['image_url'] . '" class="w-24 h-24 object-contain mx-auto mb-3" alt="' . $row['hat_name'] . '">
                <p class="text-center text-lg">' . $row['hat_name'] . ' - $' . $row['price'] . '</p>
              </button>';
                            }
                        } else {
                            echo "No hats available.";
                        }
                        ?>

                    </div>
                </div>

            </div>
        </div>

        <script>
            document.querySelector('.food-item').addEventListener('click', function () {
                const item = this.dataset.item;
                const price = this.dataset.price;

                // Simple validation to ensure price and item are valid
                if (!item || isNaN(price) || price <= 0) {
                    alert('Invalid item or price.');
                    return;
                }

                alert(`You bought ${item} for ${price}€!`);
                // Implement database update logic here if needed
            });

            document.querySelectorAll('.hat-item').forEach(button => {
                button.addEventListener('click', function () {
                    const item = this.dataset.item;
                    const price = parseInt(this.dataset.price);

                    // Validate item and price before sending the request
                    if (!item || isNaN(price) || price <= 0) {
                        alert('Invalid item or price.');
                        return;
                    }

                    // Send the purchase request to the server
                    fetch('./api/purchase_hat.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            item: item,
                            price: price
                        })
                    })
                        .then(response => {
                            // Check if response is OK before processing
                            if (!response.ok) {
                                throw new Error('Network response was not ok.');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response Data:', data); // Log the response to verify it
                            if (data.success) {
                                alert(`You bought a ${item} for ${price}€!`);
                                location.reload();
                            } else {
                                alert(data.message || 'An error occurred.'); // Show error message (e.g., insufficient funds)
                            }
                        })

                });
            });
        </script>

    </main>
</body>

</html>