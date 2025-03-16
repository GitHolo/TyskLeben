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
$result = $conn->query("SELECT money, food FROM game WHERE user_id = '$user_ID'");
$userData = $result->fetch_assoc();
$money = $userData['money'] ?? 0;
$food = $userData['food'] ?? 0;

// Get user's hamster
$hamsterResult = $conn->query("SELECT color1, color2, shadow1, shadow2 FROM hamsters WHERE user_id = '$user_ID' ORDER BY user_id DESC LIMIT 1");
$hamsterData = $hamsterResult->fetch_assoc();
$color1 = $hamsterData['color1'] ?? "#ffcc00";
$color2 = $hamsterData['color2'] ?? "#d4a500";
$shadow1 = $hamsterData['shadow1'] ?? "#ffcc00";
$shadow2 = $hamsterData['shadow2'] ?? "#d4a500";
?>

<body class="bg-gray-100 min-h-screen flex flex-col items-center">
    <!-- Header -->
    <header class="w-full flex items-center justify-between p-4 bg-white shadow-md fixed">
        <a href="#" class="text-lg font-semibold hover:text-blue-500">Home</a>
        <div class="flex items-center space-x-4 ml-10">
            <span class="font-bold">💰 <?php echo $money; ?></span>
            <span class="font-bold">🍎 <?php echo $food; ?></span>
        </div>
        <div id="hContainer" class="flex space-x-4 items-center">
            <a href="#" class="hover:text-blue-500">Shop</a>
            <a href="#" class="hover:text-blue-500">Edit</a>
            <a href="logout.php">
                <img src="./assets/svg/logout.svg" alt="Logout" class="w-6 h-6 hover:opacity-75">
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-md bg-white p-6 rounded-lg shadow-md mt-20 text-center">
        <h1 class="text-2xl font-bold mb-4">Home</h1>
        <p class="text-lg">Money: 💰 <span class="font-bold"><?php echo $money; ?></span></p>
        <p class="text-lg">Food: 🍎 <span class="font-bold"><?php echo $food; ?></span></p>

        <!-- Hamster SVG -->
        <div class="mt-6 flex justify-center">
            <object id="hamsterPreview" type="image/svg+xml" data="./assets/svg/hamster.svg" width="480"
                height="480"></object>
        </div>

        <script>
            function darkenColor(hex, factor = 0.8) {
                let r = parseInt(hex.substring(1, 3), 16) * factor;
                let g = parseInt(hex.substring(3, 5), 16) * factor;
                let b = parseInt(hex.substring(5, 7), 16) * factor;
                return `rgb(${Math.floor(r)}, ${Math.floor(g)}, ${Math.floor(b)})`;
            }
            document.getElementById("hamsterPreview").addEventListener("load", function () {
                const svgHamster = this.contentDocument;
                if (svgHamster) {
                    let color1 = "<?php echo $color1; ?>";
                    let color2 = "<?php echo $color2; ?>";
                    let shadow1 = darkenColor(color1);
                    let shadow2 = darkenColor(color2);

                    ["buttColor", "faceColor", "earColor"].forEach(id => {
                        let el = svgHamster.getElementById(id);
                        if (el) el.setAttribute("fill", color1);
                    });
                    ["buttS", "faceS", "earS"].forEach(id => {
                        let el = svgHamster.getElementById(id);
                        if (el) el.setAttribute("fill", shadow1);
                    });
                    ["chestS"].forEach(id => {
                        let el = svgHamster.getElementById(id);
                        if (el) el.setAttribute("fill", shadow2);
                    });
                    ["chestColor", "earFluff"].forEach(id => {
                        let el = svgHamster.getElementById(id);
                        if (el) el.setAttribute("fill", color2);
                    });


                }
            });
        </script>

    </main>
</body>

</html>