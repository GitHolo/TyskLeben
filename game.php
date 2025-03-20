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
            <input id="moneyInput" type="number" step="0.5" placeholder="Enter money received" class="border p-2 mt-2">
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
                const foods = ["BROT", "MILCH", "EI"];
                const foodRequest = foods[Math.floor(Math.random() * foods.length)];

                // Weighted random quantity selection (lower amounts more common)
                const weights = [0.4, 0.25, 0.15, 0.08, 0.05, 0.03, 0.02, 0.01, 0.005, 0.005];
                const quantities = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                const quantityWords = ["EINS", "ZWEI", "DREI", "VIER", "FÜNF", "SECHS", "SIEBEN", "ACHT", "NEUN", "ZEHN"];

                let randomNum = Math.random();
                let cumulative = 0;
                let quantity = 1; // Default fallback
                for (let i = 0; i < weights.length; i++) {
                    cumulative += weights[i];
                    if (randomNum < cumulative) {
                        quantity = quantities[i];
                        break;
                    }
                }

                const { color1, color2 } = getRandomColorScheme();

                const customer = document.createElement("div");
                customer.classList.add("absolute", "bottom-0", "transition-all", "z-40");
                customer.style.left = "-100px";
                customer.innerHTML = `
        <div class='text-center'>
            <p class='bg-white p-2 rounded shadow-md'>gib mir <span class="text-sky-500">${quantityWords[quantity - 1]}</span> <span class="text-red-600">${foodRequest}</span>!</p>
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
                        svgDoc.getElementById("earFluff").setAttribute("fill", color2);
                    }
                });
            }


            spawnCustomer();
        });
        document.addEventListener("DOMContentLoaded", function () {
            const openBook = document.getElementById("openBook");
            const closeBook = document.getElementById("closeBook");
            const productBook = document.getElementById("productBook");
            const cart = document.getElementById("cart");
            const checkoutButton = document.getElementById("checkoutButton");
            const moneyInput = document.getElementById("moneyInput");
            let total = 0;
            let productCount = 0;

            function getRandomPosition(minX, maxX, minY, maxY) {
                return [
                    Math.floor(Math.random() * (maxX - minX + 1)) + minX,
                    Math.floor(Math.random() * (maxY - minY + 1)) + minY
                ];
            }
            function getProductPosition() {
                // Adjust these values to change the area where products appear
                const minX = 0;
                const maxX = 100;
                const minY = 0;
                const maxY = 100;
                return getRandomPosition(minX, maxX, minY, maxY);
            }

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

                    // Create product div
                    const productDiv = document.createElement("div");
                    productDiv.classList.add("absolute", "transition-transform", "duration-300", "ease-out", "opacity-0", "scale-50");
                    const [left, top] = getProductPosition();
                    productDiv.style.left = `${left}px`;
                    productDiv.style.top = `${top}px`;
                    productDiv.innerHTML = `<img src="./assets/svg/${product.toLowerCase()}.svg" alt="${product}" class="w-12 h-12">`;

                    const cartArea = document.getElementById("cartArea");
                    cartArea.appendChild(productDiv);

                    // Animate appearance (fade in & scale up)
                    setTimeout(() => {
                        productDiv.classList.remove("opacity-0", "scale-50");
                        productDiv.classList.add("scale-100");
                    }, 50);

                    // Make the product bounce around when idle
                    bounceAround(productDiv);

                    productCount++;
                });
            });

            // Function to make products bounce randomly
            function bounceAround(element) {
                function move() {
                    const randomX = (Math.random() - 0.5) * 20; // Random small movement
                    const randomY = (Math.random() - 0.5) * 20;

                    element.style.transform = `translate(${randomX}px, ${randomY}px)`;
                    element.style.transition = "transform 0.5s ease-in-out";

                    setTimeout(move, 800 + Math.random() * 500); // Varying interval for a natural effect
                }
                move();
            }

            checkoutButton.addEventListener("click", function () {
                const enteredAmount = parseFloat(moneyInput.value);
                if (enteredAmount === total) {
                    alert("Transaction Successful! Customer is happy.");
                } else {
                    alert("Incorrect order or payment! Try again.");
                }
                total = 0;
                cart.innerHTML = "";
                moneyInput.value = "";
                productCount = 0;
            });
        });
    </script>
</body>

</html>