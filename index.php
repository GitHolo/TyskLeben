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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center bg-[url(./assets/svg/bg.svg)] bg-cover">
    <!-- Header -->
    <?php include './assets/site/header.php'; ?>

    <!-- Main Content -->
    <main class="w-full max-w-xl bg-white p-6 rounded-lg shadow-md mt-20 text-center">
        <h1 class="text-2xl font-bold mb-4">Home</h1>
        <p class="text-lg">Money: 💰 <span class="font-bold"><?php echo $money; ?></span></p>
        <p class="text-lg">Food: 🍎 <span class="font-bold"><?php echo $food; ?></span></p>

        <!-- Hamster and Hat Container -->
        <div class="mt-6 flex justify-center relative">
            <!-- Hamster SVG -->
            <object id="hamsterPreview" type="image/svg+xml" data="./assets/svg/hamster.svg" width="480"
                height="480"></object>

            <!-- Hat (positioned above the hamster) -->
            <?php if ($hat_image): ?>
                <img id="hatPreview" src="<?php echo htmlspecialchars($hat_image) . '?t=' . time(); ?>" alt="Hat"
                    class="absolute justify-self-center self-center h-[420px] w-[460px]">
            <?php endif; ?>
        </div>

        <a href="./game.php" class="bg-blue-500 text-white px-4 py-2 rounded">Go to work</a>

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