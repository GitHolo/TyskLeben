<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./assets/js/bounceAround.js"></script>
</head>

<body class="bg-gray-100 flex flex-col items-center min-h-screen bg-[url(./assets/svg/bg.svg)] bg-cover">
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
    // Fetch the user's equipped hat from the user_hats table and join with the hats table to get the image URL
    $query = "
SELECT h.image_url 
FROM user_hats uh 
JOIN hats h ON uh.hat_id = h.hat_id 
WHERE uh.user_id = '$user_ID' AND uh.equipped = '1'
LIMIT 1
";
    $result = $conn->query($query);
    $hat_image = null;

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hat_image = str_replace("-cropped", "", $row['image_url']); // Remove "-cropped" from the image URL
    
    }
    include "./assets/site/header.php";
    ?>

    <main class="relative w-full max-w-2xl bg-white p-6 rounded-lg shadow-md mt-20 text-center h-[1050px]">
        <h1 class="text-2xl font-bold mb-4">Cashier</h1>
        <?php include './assets/site/game.php'; ?>
        <section class="flex justify-around">
            <button id="openBook" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Open Product Book</button>
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
                    data-product="BIER" data-price="1.25"><img src="./assets/svg/bier.svg" class="h-12 w-12" /> -
                    1.25€</button>
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
    </main>
    <audio id="buttonClickSound" src="./assets/sounds/beep.mp3"></audio>
    <audio id="checkoutSuccessSound" src="./assets/sounds/checkout_success.mp3"></audio>
    <audio id="checkoutFailSound" src="./assets/sounds/checkout_fail.wav"></audio>
    <audio id="registerOpenSound" src="./assets/sounds/open_cash.mp3"></audio>
    <audio id="registerCloseSound" src="./assets/sounds/close_cash.mp3"></audio>
    <audio id="bookOpenSound" src="./assets/sounds/book_open.mp3"></audio>
    <audio id="bookCloseSound" src="./assets/sounds/book_close.mp3"></audio>
    <audio id="productClick" src="./assets/sounds/select_product.mp3"></audio>

    <script>
        // Load audio elements
        const buttonClickSound = document.getElementById("buttonClickSound");
        const checkoutSuccessSound = document.getElementById("checkoutSuccessSound");
        const checkoutFailSound = document.getElementById("checkoutFailSound");
        const registerOpenSound = document.getElementById("registerOpenSound");
        const registerCloseSound = document.getElementById("registerCloseSound");
        const bookOpenSound = document.getElementById("bookOpenSound");
        const bookCloseSound = document.getElementById("bookCloseSound");
        const productClick = document.getElementById("productClick");


        // Play sound function
        function playSound(sound) {
            sound.currentTime = 0;  // Restart sound if it's already playing
            sound.play();
        }
        // Loop through products and add event listener
        let cartContents = {};
        document.addEventListener("DOMContentLoaded", function () {

            const moneyInput = document.getElementById("moneyInput");
            const calculatorButtons = document.querySelectorAll(".calculator-button");
            const checkoutButton = document.getElementById("checkoutButton");
            const closeRegisterButton = document.getElementById("closeRegister");
            const openBookButton = document.getElementById("openBook");
            const closeBookButton = document.getElementById("closeBook");
            const productButtons = document.querySelectorAll(".product");



            // Add sound to calculator buttons
            calculatorButtons.forEach(button => {
                button.addEventListener("click", function () {
                    playSound(buttonClickSound);

                    const buttonValue = this.dataset.value;

                    if (buttonValue === "C") {
                        moneyInput.textContent = "0";
                    } else if (buttonValue === "←") {
                        moneyInput.textContent = moneyInput.textContent.slice(0, -1) || "0";
                    } else {
                        if (moneyInput.textContent.length < 10) {
                            if (moneyInput.textContent === "0") moneyInput.textContent = "";
                            moneyInput.textContent += buttonValue;
                        }
                    }
                });
            });
            productButtons.forEach(button => {
                button.addEventListener("click", function () {
                    playSound(productClick);
                });
            });

            openBookButton.addEventListener("click", function () {
                playSound(bookOpenSound);
            });
            closeBookButton.addEventListener("click", function () {
                playSound(bookCloseSound);
            });

            // Add sound to checkout button
            checkoutButton.addEventListener("click", function () {
                const enteredAmount = parseFloat(moneyInput.innerHTML);
            });

            // Add sound to open/close register
            document.getElementById("openRegister").addEventListener("click", function () {
                playSound(registerOpenSound);
            });

            closeRegisterButton.addEventListener("click", function () {
                playSound(registerCloseSound);
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            let lastTransactionAmount = 0;
            const openRegister = document.getElementById("openRegister");
            const closeRegister = document.getElementById("closeRegister");
            const checkoutSection = document.getElementById("checkout");
            const openBook = document.querySelector("button[id='openBook']");
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
                const foodGenders = {
                    "BROT": "n",      // das Brot
                    "MILCH": "f",     // die Milch
                    "EI": "n",        // das Ei
                    "BANANEN": "f",   // die Banane (plural, but treating singular here)
                    "BIER": "n",      // das Bier
                    "TOMATEN": "f"    // die Tomate (plural form shown, but treating as singular item here)
                };
                const weights = [0.4, 0.25, 0.15, 0.08, 0.05, 0.03, 0.02, 0.01, 0.005, 0.005];
                const quantities = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                const quantityWords = ["EINS", "ZWEI", "DREI", "VIER", "FÜNF", "SECHS", "SIEBEN", "ACHT", "NEUN", "ZEHN"];

                // Randomly determine how many different products the customer will order (1-3)
                const numProducts = Math.random() < 0.6 ? 1 : Math.random() < 0.8 ? 2 : 3;

                let orderedProducts = []; // Stores { product, count }
                let expectedTotal = 0;

                for (let i = 0; i < numProducts; i++) {
                    let randomProduct;
                    do {
                        randomProduct = foods[Math.floor(Math.random() * foods.length)];
                    } while (orderedProducts.some(item => item.product === randomProduct)); // Avoid duplicate product requests

                    let randomNum = Math.random();
                    let cumulative = 0;
                    let quantity = 1;
                    for (let j = 0; j < weights.length; j++) {
                        cumulative += weights[j];
                        if (randomNum < cumulative) {
                            quantity = quantities[j];
                            break;
                        }
                    }

                    orderedProducts.push({ product: randomProduct, count: quantity });
                    expectedTotal += quantity * getProductPrice(randomProduct);
                }

                expectedProduct = orderedProducts.map(item => item.product); // Array of expected products
                expectedCount = orderedProducts.map(item => item.count); // Array of expected quantities
                this.expectedTotal = expectedTotal;

                // Generate request text
                let requestText = orderedProducts.map(item => {
                    let quantityText = quantityWords[item.count - 1];

                    // If quantity is 1, apply grammatical gender rules
                    if (item.count === 1) {
                        const gender = foodGenders[item.product];
                        quantityText = gender === "f" ? "EINE" : "EIN";
                    }

                    return `<span class="text-sky-500">${quantityText}</span> 
            <span class="text-red-600">${item.product}</span>`;
                }).join(" und ");

                const { color1, color2 } = getRandomColorScheme();

                const customer = document.createElement("div");
                customer.classList.add("absolute", "bottom-0", "transition-all", "z-40", "duration-[1000ms]");
                customer.style.left = "-200px";
                customer.innerHTML = `
        <div class='text-center'>
            <p class='max-w-[200px] bg-white p-2 rounded shadow-md ml-[50px] opacity-0 transition-opacity duration-300' id='customerRequest'>
                gib mir ${requestText}!
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


            document.querySelectorAll(".product").forEach((item, index) => {
                cartArea = document.getElementById('cartArea');
                item.addEventListener("click", function () {
                    const product = this.dataset.product.trim().toUpperCase(); // Ensure consistency
                    const price = getProductPrice(product); // Get the correct price

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

                // Calculate actual cart total and quantities
                let calculatedTotal = 0;
                let cartProductMap = {};

                for (let product in cartContents) {
                    cartProductMap[product] = cartContents[product].count;
                    calculatedTotal += cartContents[product].price * cartContents[product].count;
                }

                // Check if all expected products are in the cart with correct quantities
                let orderCorrect = expectedProduct.every((product, index) =>
                    cartProductMap[product] === expectedCount[index]
                );

                // Ensure the money paid matches and all products match
                if (enteredAmount === calculatedTotal && orderCorrect) {
                    alert("Transaction Successful! Customer is happy.");
                    happy = true;

                    lastTransactionAmount = (enteredAmount * 0.13 + 1).toFixed(2);
                    let cash = parseFloat(lastTransactionAmount);
                    updateMoneyInDB(cash);
                    addMoneyAnimation(cash);
                    updateHeaderMoney(cash);
                    playSound(checkoutSuccessSound);
                } else {
                    alert("Incorrect order or payment! Try again.");
                    updateMoneyInDB(-0.25);
                    addMoneyAnimation(-0.25);
                    updateHeaderMoney(-0.25);
                    happy = false;
                    console.log("Entered amount:", enteredAmount);
                    console.log("Calculated total:", calculatedTotal);
                    console.log("Cart contents:", cartContents);
                    console.log("Expected products:", expectedProduct);
                    console.log("Expected counts:", expectedCount);
                    playSound(checkoutFailSound);
                }

                // Clear cart
                cartArea.innerHTML = "";
                moneyInput.innerHTML = "0";
                cartContents = {};

                // Update customer reaction & make them leave
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