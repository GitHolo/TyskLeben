<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben - Shop Game</title>
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
            </div>
            <button id="closeBook" class="hidden mt-2 bg-red-500 text-white px-4 py-2 rounded">Close</button>
        </div>

        <div id="checkout" class="hidden mt-4 p-6 bg-gray-800 text-white rounded-lg shadow-lg w-72 justify-self-center">
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


            document.querySelectorAll(".product").forEach(item => {
                item.addEventListener("click", function () {
                    const product = this.dataset.product;
                    const price = parseFloat(this.dataset.price);
                    if (product === expectedProduct) {
                        total += price;
                        productCount++;
                    }

                    const productDiv = document.createElement("div");
                    productDiv.classList.add("absolute", "transition-transform", "duration-300", "ease-out", "opacity-0", "scale-50");
                    const [left, top] = getProductPosition();
                    productDiv.style.left = `${left}px`;
                    productDiv.style.top = `${top}px`;
                    productDiv.innerHTML = `<img src="./assets/svg/${product.toLowerCase()}.svg" alt="${product}" class="w-12 h-12">`;

                    cartArea.appendChild(productDiv);

                    setTimeout(() => {
                        productDiv.classList.remove("opacity-0", "scale-50");
                        productDiv.classList.add("scale-100");
                    }, 50);

                    bounceAround(productDiv);
                });
            });

            // Function to make products bounce randomly


            checkoutButton.addEventListener("click", function () {
                const moneyInput = document.getElementById("moneyInput");
                const enteredAmount = parseFloat(moneyInput.innerHTML);
                let happy = false;

                if (enteredAmount === expectedTotal && productCount === expectedCount) {
                    alert("Transaction Successful! Customer is happy.");
                    happy = true;
                } else {
                    alert("Incorrect order or payment! Try again.");
                    happy = false;
                }

                total = 0;
                while (cartArea.firstChild) {
                    cartArea.removeChild(cartArea.firstChild);
                }

                moneyInput.innerHTML = "0";
                productCount = 0;

                document.querySelectorAll(".customer-hamster").forEach(customer => {
                    const customerContainer = customer.closest("div.absolute.bottom-0");

                    // Get the text bubble and change its content
                    const textBubble = customerContainer.querySelector("#customerRequest");
                    if (happy) {
                        textBubble.innerHTML = '<span class="text-green-600">' + (Math.random() > 0.5 ? "Danke!" : "Auf Wiedersehen!") + '</span>';
                    } else {
                        textBubble.innerHTML = '<span class="text-red-600">' + (Math.random() > 0.5 ? "Schade!" : "NEINN!!") + '</span>';
                    }

                    // Move the customer slowly to the right
                    customerContainer.style.transition = "left 1.5s ease-out";
                    customerContainer.style.left = "100%"; // Moves them out of the screen

                    // Remove customer after animation
                    setTimeout(() => customerContainer.remove(), 1500);
                });

                setTimeout(spawnCustomer, 2000);
            });
            function getProductPrice(product) {
                const prices = { "BROT": 2, "MILCH": 1.5, "EI": 0.5 };
                return prices[product] || 0;
            }
            setTimeout(spawnCustomer(), 1000);
        });

    </script>
</body>

</html>