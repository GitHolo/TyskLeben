<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="styles/index.css" rel="stylesheet">
    <script src="./assets/js/bounceAround.js"></script>
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
        <section class="flex justify-around">
            <button id=" openBook" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Open Product Book</button>
            <button id="openRegister" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Open Cash Register</button>



        </section>
        <div id="productBook" class="hidden p-4 bg-gray-200 rounded shadow-md">
            <h2 class="text-xl font-bold mb-2">Select Products</h2>
            <div class="grid grid-cols-3 gap-2">
                <button class="product bg-yellow-300 px-3 py-2 rounded flex items-center justify-center"
                    data-product="BROT" data-price="2"><img src="./assets/svg/brot.svg" class="h-12 w-12" /> -
                    2€</button>
                <button class="product bg-blue-300 px-3 py-2 rounded flex items-center justify-center"
                    data-product="MILCH" data-price="1.5"><img src="./assets/svg/milch.svg" class="h-12 w-12" /> -
                    1.5€</button>
                <button class="product bg-red-300 px-3 py-2 rounded flex items-center justify-center" data-product="EI"
                    data-price="0.5"><img src="./assets/svg/ei.svg" class="h-12 w-12" /> -
                    0.5€</button>
                <button class="product bg-green-300 px-3 py-2 rounded flex items-center justify-center"
                    data-product="BANANEN" data-price="1"><img src="./assets/svg/bananen.svg" class="h-12 w-12" /> -
                    1€</button>
                <button class="product bg-purple-300 px-3 py-2 rounded flex items-center justify-center"
                    data-product="BIER" data-price="1"><img src="./assets/svg/bier.svg" class="h-12 w-12" /> -
                    1€</button>
                <button class="product bg-orange-300 px-3 py-2 rounded flex items-center justify-center"
                    data-product="TOMATEN" data-price="0.25"><img src="./assets/svg/tomaten.svg" class="h-12 w-12" /> -
                    0.25€</button>
            </div>
            <button id="closeBook" class="hidden mt-2 bg-red-500 text-white px-4 py-2 rounded">Close</button>
        </div>

        <div id="checkout"
            class="hidden inline-grid mt-4 p-6 bg-gray-800 text-white rounded-lg shadow-lg w-72 justify-self-center">
            <h2 class="text-2xl font-bold mb-3 text-center">Cash Register</h2>

            <!-- Register Screen -->
            <div id="registerScreen" class="bg-gray-900 text-right text-2xl p-3 rounded mb-3 font-mono">
                <span id="moneyInput">0</span> €
            </div>

            <!-- Number Pad -->
            <div class="grid grid-cols-4 gap-2">
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="7">7</button>
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="8">8</button>
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="9">9</button>
                <button class="calculator-button bg-gray-600 p-3 rounded" data-value="C">C</button>

                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="4">4</button>
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="5">5</button>
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="6">6</button>
                <button class="calculator-button bg-gray-600 p-3 rounded" data-value="←">←</button>

                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="1">1</button>
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="2">2</button>
                <button class="calculator-button bg-gray-700 p-3 rounded" data-value="3">3</button>
                <button class="calculator-button bg-gray-600 p-3 rounded" data-value=".">.</button>

                <button class="calculator-button bg-gray-700 p-3 rounded col-span-2" data-value="0">0</button>
                <button id="checkoutButton" class="bg-green-500 p-3 rounded col-span-2">Checkout</button>
            </div>

            <!-- Close Register -->
            <button id="closeRegister" class="w-full mt-3 bg-red-600 p-3 rounded">Close Register</button>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const moneyInput = document.getElementById("moneyInput");
                const calculatorButtons = document.querySelectorAll(".calculator-button");

                calculatorButtons.forEach(button => {
                    button.addEventListener("click", function () {
                        const buttonValue = this.dataset.value;

                        if (buttonValue === "C") {
                            moneyInput.textContent = "0";
                        } else if (buttonValue === "←") {
                            moneyInput.textContent = moneyInput.textContent.slice(0, -1) || "0";
                        } else {
                            if (moneyInput.textContent === "0") moneyInput.textContent = "";
                            moneyInput.textContent += buttonValue;
                        }
                    });
                });
            });
        </script>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let lastTransactionAmount = 0;
            const openRegister = document.getElementById("openRegister");
            const closeRegister = document.getElementById("closeRegister");
            const checkoutSection = document.getElementById("checkout");
            const openBook = document.querySelector("button[id=' openBook']");
            const closeBook = document.getElementById("closeBook");
            const productBook = document.getElementById("productBook");
            const cart = document.getElementById("cart");
            const checkoutButton = document.getElementById("checkoutButton");
            const moneyInput = document.getElementById("moneyInput");
            let total = 0;
            let productCount = 0;
            const customerArea = document.getElementById("customerArea");
            const colorSchemes = [
                // Soft pastel tones with a warm touch
                { color1: "#F3C9C3", color2: "#F7D2D2" },

                // Oceanic blues and coral pinks
                { color1: "#0A74DA", color2: "#FF6F61" },

                // Earthy tones with a splash of nature
                { color1: "#A7B9A7", color2: "#F4E1D2" },

                // Vibrant sunset colors
                { color1: "#FF8C42", color2: "#FDCB82" },

                // Mystical vibes with a pop of lavender
                { color1: "#C4A0C8", color2: "#F5B7B1" },

                // Fresh citrus hues for a zesty look
                { color1: "#FFB84D", color2: "#F6A6D4" },

                // Retro 80s pastel vibes
                { color1: "#F8D5D1", color2: "#56CCF2" },

                // Candy-coated fun with neon touches
                { color1: "#F45D01", color2: "#F1D0A5" },

                // Cool, serene tones with hints of peach
                { color1: "#A0D8EF", color2: "#F3A4B5" },

                // Soft neutrals with a hint of warmth
                { color1: "#D6D4D1", color2: "#E8D7B9" },

                // Whimsical blend of teal and pink
                { color1: "#6B8D8C", color2: "#F4A9C1" }
            ];


            function getRandomColorScheme() {
                return colorSchemes[Math.floor(Math.random() * colorSchemes.length)];
            }

            function spawnCustomer() {
                const foods = ["BROT", "MILCH", "EI", "BANANEN", "BIER", "TOMATEN"];
                expectedProduct = foods[Math.floor(Math.random() * foods.length)];

                const weights = [0.4, 0.25, 0.15, 0.08, 0.05, 0.03, 0.02, 0.01, 0.005, 0.005];
                const quantities = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                const quantityWords = ["EINS", "ZWEI", "DREI", "VIER", "FÜNF", "SECHS", "SIEBEN", "ACHT", "NEUN", "ZEHN"];

                let randomNum = Math.random();
                let cumulative = 0;
                expectedCount = 1;
                for (let i = 0; i < weights.length; i++) {
                    cumulative += weights[i];
                    if (randomNum < cumulative) {
                        expectedCount = quantities[i];
                        break;
                    }
                }

                expectedTotal = expectedCount * getProductPrice(expectedProduct);

                const { color1, color2 } = getRandomColorScheme();

                const customer = document.createElement("div");
                customer.classList.add("absolute", "bottom-0", "transition-all", "z-40", "duration-[1000ms]");
                customer.style.left = "-200px";
                customer.innerHTML = `
            <div class='text-center'>
                <p class='bg-white p-2 rounded shadow-md ml-[50px] opacity-0 transition-opacity duration-300' id='customerRequest'>
                    gib mir <span class="text-sky-500">${quantityWords[expectedCount - 1]}</span> <span class="text-red-600">${expectedProduct}</span>!
                </p>
                <object class="customer-hamster" type="image/svg+xml" data="./assets/svg/hamster.svg" width="140" height="140"></object>
            </div>
        `;
                customerArea.appendChild(customer);

                setTimeout(() => customer.style.left = "50%", 500);

                setTimeout(() => {
                    const customerRequest = customer.querySelector('#customerRequest');
                    if (customerRequest) {
                        customerRequest.style.opacity = '1';
                    }
                }, 2000);

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
            openRegister.addEventListener("click", () => {
                checkoutSection.classList.remove("hidden");
                openBook.closest('section').classList.add("hidden");
            });
            openBook.addEventListener("click", () => {
                productBook.classList.remove("hidden");
                openBook.closest('section').classList.add("hidden");
                closeBook.classList.remove("hidden");
            });

            closeBook.addEventListener("click", () => {
                productBook.classList.add("hidden");
                closeBook.classList.add("hidden");
                openBook.closest('section').classList.remove("hidden");
            });
            closeRegister.addEventListener("click", () => {
                checkoutSection.classList.add("hidden");
                openBook.closest('section').classList.remove("hidden");
            });




            // Add event listener **once** to cartArea (event delegation)
            cartArea.addEventListener("click", function (event) {
                const clickedProduct = event.target.closest(".cart-product"); // Check if clicked item is a cart product
                if (clickedProduct) {
                    const price = parseFloat(clickedProduct.dataset.price); // Retrieve price from dataset
                    cartArea.removeChild(clickedProduct);
                    total -= price;
                    productCount--;
                } else {
                    console.log("oop");
                }
            });

            // Loop through products and add event listener
            let cartContents = {};

            document.querySelectorAll(".product").forEach((item, index) => {
                cartArea = document.getElementById('cartArea');
                item.addEventListener("click", function () {
                    const product = this.dataset.product;
                    const price = parseFloat(this.dataset.price);

                    // Add to cart only if it's the expected product
                    if (cartContents[product]) {
                        cartContents[product].count++;
                    } else {
                        cartContents[product] = { count: 1, price: price };
                    }

                    const productDiv = document.createElement("div");
                    productDiv.classList.add("cart-product", "absolute", "transition-transform", "duration-300", "ease-out", "scale-100");
                    productDiv.dataset.product = product;
                    productDiv.dataset.price = price;
                    productDiv.id = `cart-product-${index}`;

                    const [left, top] = getProductPosition();
                    productDiv.style.left = `${left}px`;
                    productDiv.style.top = `${top}px`;
                    productDiv.innerHTML = `<img src="./assets/svg/${product.toLowerCase()}.svg" alt="${product}" class="w-12 h-12 cursor-pointer">`;

                    cartArea.appendChild(productDiv);
                    bounceAround(productDiv);

                    // Remove product when clicked
                    productDiv.addEventListener("click", function () {
                        cartArea.removeChild(productDiv);

                        // Update cart contents
                        if (cartContents[product]) {
                            cartContents[product].count--;
                            if (cartContents[product].count <= 0) {
                                delete cartContents[product];
                            }
                        }
                    });
                });
            });





            // Checkout
            checkoutButton.addEventListener("click", function () {
                const moneyInput = document.getElementById("moneyInput");
                const enteredAmount = parseFloat(moneyInput.innerHTML);
                let happy = false;

                // Calculate total dynamically
                let calculatedTotal = 0;
                let cartProductCount = 0;
                for (let product in cartContents) {
                    calculatedTotal += cartContents[product].price * cartContents[product].count;
                    cartProductCount += cartContents[product].count;
                }

                // Ensure correct products & payment
                if (enteredAmount === calculatedTotal && cartContents[expectedProduct]?.count === expectedCount) {
                    alert("Transaction Successful! Customer is happy.");
                    happy = true;

                    lastTransactionAmount = (enteredAmount * 0.13).toFixed(2);
                    let cash = parseFloat(lastTransactionAmount);
                    updateMoneyInDB(cash);
                    addMoneyAnimation(cash);
                    updateHeaderMoney(cash);
                } else {
                    alert("Incorrect order or payment! Try again.");
                    updateMoneyInDB(-0.25);
                    addMoneyAnimation(-0.25);
                    updateHeaderMoney(-0.25);
                    happy = false;
                    console.log(enteredAmount);
                    console.log(calculatedTotal);
                    console.log(cartContents);
                    console.log(cartContents[expectedProduct]?.count);
                    console.log(expectedProduct);
                    console.log(expectedCount);

                }

                // Clear cart
                cartArea.innerHTML = "";
                moneyInput.innerHTML = "0";
                cartContents = {};

                // Update customer
                document.querySelectorAll(".customer-hamster").forEach(customer => {
                    const customerContainer = customer.closest("div.absolute.bottom-0");
                    const textBubble = customerContainer.querySelector("#customerRequest");
                    textBubble.innerHTML = happy
                        ? '<span class="text-green-600">' + (Math.random() > 0.5 ? "Danke!" : "Auf Wiedersehen!") + '</span>'
                        : '<span class="text-red-600">' + (Math.random() > 0.5 ? "Schade!" : "NEINN!!") + '</span>';

                    customerContainer.style.transition = "left 1.5s ease-out";
                    customerContainer.style.left = "100%";
                    setTimeout(() => customerContainer.remove(), 1500);
                });

                setTimeout(spawnCustomer, 2000);
            });

            // Ensure correct prices are used
            function getProductPrice(product) {
                const prices = { "BROT": 2, "MILCH": 1.5, "EI": 0.5, "BANANEN": 1, "BIER": 1.25, "TOMATEN": 0.25 };
                return prices[product.trim().toUpperCase()] || 0;
            }
            // Correct spawn timing
            setTimeout(spawnCustomer, 1000);


            function addMoneyAnimation(amount) {
                const headerMoney = document.querySelector("header div span.font-bold");

                // Create floating money text
                const moneyText = document.createElement("div");

                // Check if amount is positive or negative
                if (amount >= 0) {
                    moneyText.innerHTML = `+${amount.toFixed(2)} 💰`;  // Add '+' for positive amount
                    moneyText.classList.add("text-green-600");  // Green for positive
                } else {
                    moneyText.innerHTML = `${amount.toFixed(2)} 💰`;  // No '+' for negative amount, just a minus
                    moneyText.classList.add("text-red-600");  // Red for negative
                }

                // Add other necessary classes separately
                moneyText.classList.add("z-[9999]", "absolute", "font-bold", "text-lg", "transition-all");

                // Append to header
                const headerContainer = document.querySelector("header");
                headerContainer.appendChild(moneyText);

                // Get header money position
                const rect = headerMoney.getBoundingClientRect();
                moneyText.style.position = "absolute";
                moneyText.style.left = `${rect.left + rect.width / 2}px`;
                moneyText.style.top = `${rect.top - 5}px`;

                // Animate upwards and fade out
                setTimeout(() => {
                    moneyText.style.transform = "translateY(-20px)";
                    moneyText.style.opacity = "0";
                }, 500);

                // Remove after animation
                setTimeout(() => moneyText.remove(), 1000);
            }



            // Function to send update request to the server
            function updateMoneyInDB(amount) {
                console.log("Calling updateMoneyInDB with amount:", amount);
                fetch("./api/update_money.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `money=${amount}`
                })
                    .then(response => response.text())
                    .then(data => console.log("Money updated:", data))
                    .catch(error => console.error("Error updating money:", error));
            }



            // Updates the money amount in the header
            function updateHeaderMoney(amount) {
                const headerMoney = document.querySelector("header div span.font-bold");
                let currentMoney = parseFloat(headerMoney.textContent.replace("💰", "").trim());
                headerMoney.textContent = `💰 ${(currentMoney + amount).toFixed(2)}`;
            }




        });


    </script>
</body>

</html>