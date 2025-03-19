<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben - Shop Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="styles/index.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex flex-col items-center min-h-screen">
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

    // Get user's hamster
    require "./api/get_hamster.php";
    include "./assets/site/header.php";
    ?>

    <main class="relative w-full max-w-2xl bg-white p-6 rounded-lg shadow-md mt-20 text-center">
        <h1 class="text-2xl font-bold mb-4">Cashier</h1>
        <?php include './assets/site/game.php'; ?>

        <button id="openBook" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Open Product Book</button>

        <div id="productBook" class="hidden p-4 bg-gray-200 rounded shadow-md">
            <h2 class="text-xl font-bold mb-2">Select Products</h2>
            <div class="grid grid-cols-3 gap-2">
                <button class="product bg-yellow-300 px-3 py-2 rounded" data-product="BROT" data-price="2">Brot -
                    2€</button>
                <button class="product bg-blue-300 px-3 py-2 rounded" data-product="MILCH" data-price="1.5">Milch -
                    1.5€</button>
                <button class="product bg-red-300 px-3 py-2 rounded" data-product="EI" data-price="0.5">Ei -
                    0.5€</button>
            </div>
            <button id="closeBook" class="hidden mt-2 bg-red-500 text-white px-4 py-2 rounded">Close</button>
        </div>

        <div id="checkout" class="mt-4 p-4 bg-gray-200 rounded shadow-md">
            <h2 class="text-xl font-bold mb-2">Checkout</h2>
            <ul id="cart" class="text-left mb-2"></ul>
            <input id="moneyInput" type="number" step="0.01" placeholder="Enter money received" class="border p-2 mt-2">
            <button id="checkoutButton" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Checkout</button>
        </div>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const customerArea = document.getElementById("customerArea");
            const colorSchemes = [
                { color1: "#D4A373", color2: "#FAE1DD" },
                { color1: "#A3C4F3", color2: "#FFD6A5" },
                { color1: "#FFAFCC", color2: "#CDB4DB" },
                { color1: "#90DBF4", color2: "#FFC8DD" }
            ];

            function getRandomColorScheme() {
                return colorSchemes[Math.floor(Math.random() * colorSchemes.length)];
            }

            function spawnCustomer() {
                const foods = ["BROT", "MILCH", "EI", "ZWEI MILCH"];
                const foodRequest = foods[Math.floor(Math.random() * foods.length)];
                const { color1, color2 } = getRandomColorScheme();

                const customer = document.createElement("div");
                customer.classList.add("absolute", "bottom-0", "transition-all", "z-40");
                customer.style.left = "-100px";
                customer.innerHTML = `
                    <div class='text-center'>
                        <p class='bg-white p-2 rounded shadow-md'>gib mir ${foodRequest}!</p>
                        <object class="customer-hamster" type="image/svg+xml" data="./assets/svg/hamster.svg" width="80" height="80"></object>
                    </div>
                `;
                customerArea.appendChild(customer);

                setTimeout(() => customer.style.left = "50%", 500);

                customer.querySelector(".customer-hamster").addEventListener("load", function () {
                    const svgDoc = this.contentDocument;
                    if (svgDoc) {
                        svgDoc.getElementById("buttColor").setAttribute("fill", color1);
                        svgDoc.getElementById("buttS").setAttribute("fill", darkenColor(color1));

                        svgDoc.getElementById("earColor").setAttribute("fill", color1);
                        svgDoc.getElementById("earS").setAttribute("fill", darkenColor(color1));
                        svgDoc.getElementById("faceColor").setAttribute("fill", color1);
                        svgDoc.getElementById("faceS").setAttribute("fill", darkenColor(color1));
                        svgDoc.getElementById("chestColor").setAttribute("fill", color2);
                        svgDoc.getElementById("chestS").setAttribute("fill", darkenColor(color2));
                        svgDoc.getElementById("earFluff").setAttribute("fill", color2)
                    }
                });
            }

            spawnCustomer();
        });
        document.addEventListener("DOMContentLoaded", function () {
            const openBook = document.getElementById("openBook");
            const closeBook = document.getElementById("closeBook");
            const productBook = document.getElementById("productBook");
            const checkoutList = document.getElementById("cart");
            const checkoutButton = document.getElementById("checkoutButton");
            const moneyInput = document.getElementById("moneyInput");
            let total = 0;

            openBook.addEventListener("click", () => {
                productBook.classList.remove("hidden");
                openBook.classList.add("hidden");
                closeBook.classList.remove("hidden");
            });

            closeBook.addEventListener("click", () => {
                productBook.classList.add("hidden");
                closeBook.classList.add("hidden");
                openBook.classList.remove("hidden");
            });

            document.querySelectorAll(".product").forEach(item => {
                item.addEventListener("click", function () {
                    const product = this.dataset.product;
                    const price = parseFloat(this.dataset.price);
                    total += price;

                    const li = document.createElement("li");
                    li.textContent = `${product} - ${price}€`;
                    checkoutList.appendChild(li);
                });
            });

            checkoutButton.addEventListener("click", function () {
                const enteredAmount = parseFloat(moneyInput.value);
                if (enteredAmount === total) {
                    alert("Transaction Successful! Correct amount given.");
                } else {
                    alert("Incorrect amount! Try again.");
                }
                total = 0;
                checkoutList.innerHTML = "";
                moneyInput.value = "";
                spawnCustomer();
            });
        });
    </script>
</body>

</html>