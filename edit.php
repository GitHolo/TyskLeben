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
// Fetch hats the user already owns
$query = "
    SELECT h.hat_id, h.hat_name, h.price, h.image_url, uh.equipped
    FROM hats h
    JOIN user_hats uh ON h.hat_id = uh.hat_id AND uh.user_id = '$user_ID'
";
$result = $conn->query($query);

// Fetch all available hats to buy (for future purchases)
$availableHatsQuery = "
    SELECT h.hat_id, h.hat_name, h.price, h.image_url 
    FROM hats h
    LEFT JOIN user_hats uh ON h.hat_id = uh.hat_id AND uh.user_id = '$user_ID'
    WHERE uh.user_id IS NULL
";
$availableHatsResult = $conn->query($availableHatsQuery);

// Get the user's current money
$moneyQuery = "SELECT money FROM game WHERE user_ID = '$user_ID'";
$moneyResult = $conn->query($moneyQuery);
$userMoney = $moneyResult->fetch_assoc()['money'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hats</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center bg-[url(./assets/svg/bg.svg)] bg-cover">

    <!-- Header -->
    <?php include './assets/site/header.php'; ?>

    <main class="w-full max-w-4xl bg-white p-8 rounded-lg shadow-lg mt-20 mb-12">
        <div class="container mx-auto text-center">

            <h1 class="text-4xl font-extrabold text-gray-800 mb-6">Choose Your Hat</h1>
            <p class="text-xl text-gray-600 mb-6">Current Balance: <span
                    class="font-semibold"><?php echo $userMoney; ?>€</span></p>

            <!-- Equipped Hats Section -->
            <section class="mb-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Equipped Hats</h2>
                <?php if ($availableHatsResult->num_rows > 0): ?>

                    <div class="flex flex-wrap justify-center gap-6">
                        <?php while ($hat = $result->fetch_assoc()): ?>
                            <div
                                class="hat-item p-6 bg-gray-800 shadow-xl rounded-xl w-48 flex flex-col items-center transition-all duration-200 hover:shadow-2xl">
                                <img class="w-32 h-32 object-fit rounded-full border-4 border-gray-200 mb-4"
                                    src="<?php echo $hat['image_url']; ?>" alt="<?php echo $hat['hat_name']; ?>">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo $hat['hat_name']; ?></h3>
                                <button
                                    class="w-full py-2 px-4 rounded-lg text-white transition-all duration-300 <?php echo $hat['equipped'] ? 'bg-green-500 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'; ?>"
                                    <?php echo $hat['equipped'] ? 'disabled' : ''; ?>
                                    data-hat-id="<?php echo $hat['hat_id']; ?>" data-hat-name="<?php echo $hat['hat_name']; ?>"
                                    data-equip-status="<?php echo $hat['equipped']; ?>">
                                    <?php echo $hat['equipped'] ? 'Equipped' : 'Equip'; ?>
                                </button>

                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-lg text-gray-600 text-center">No available hats.</p>
                <?php endif; ?>
            </section>

            <!-- Available Hats to Buy -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Available Hats</h2>
                <?php if ($availableHatsResult->num_rows > 0): ?>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 bg-gray-800 p-8 rounded-3xl shadow-2xl">
                        <?php while ($hat = $availableHatsResult->fetch_assoc()): ?>
                            <div
                                class="bg-white shadow-xl rounded-xl overflow-hidden transition-all duration-200 hover:shadow-2xl">
                                <img class="w-full h-56 object-fit" src="<?php echo $hat['image_url']; ?>"
                                    alt="<?php echo $hat['hat_name']; ?>">
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo $hat['hat_name']; ?></h3>
                                    <p class="text-lg text-gray-600">Price: <span
                                            class="font-semibold"><?php echo $hat['price']; ?>€</span></p>
                                    <button
                                        class="mt-4 w-full py-2 px-4 rounded-lg text-white transition-all duration-300 <?php echo $hat['price'] > $userMoney ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'; ?>"
                                        <?php echo $hat['price'] > $userMoney ? 'disabled' : ''; ?>
                                        data-hat-id="<?php echo $hat['hat_id']; ?>"
                                        data-hat-name="<?php echo $hat['hat_name']; ?>"
                                        data-price="<?php echo $hat['price']; ?>">
                                        <?php echo $hat['price'] > $userMoney ? 'Insufficient Funds' : 'Buy'; ?>
                                    </button>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-lg text-gray-600 text-center">No available hats.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

</body>


<script>
    // Handle "Equip" button click for already owned hats
    document.querySelectorAll('.hat-item button[data-equip-status]').forEach(button => {
        button.addEventListener('click', function () {
            const hatId = this.dataset.hatId;
            const equipStatus = this.dataset.equipStatus;
            const action = equipStatus == '1' ? 'unequip' : 'equip';

            // Send the equip/unequip request to the server
            fetch('./api/equip_hat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    hat_id: hatId,
                    action: action
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Response Data:', data);
                    if (data.success) {
                        alert(`You ${action}ed the hat successfully!`);
                        location.reload(); // Reload the page to update equipped status
                    } else {
                        alert(data.message);
                    }
                })
        });
    });

    // Handle "Buy" button click for available hats
    document.querySelectorAll('.bg-blue-500[data-price]').forEach(button => {
        button.addEventListener('click', function () {
            const hatName = this.dataset.hatName;
            const price = parseInt(this.dataset.price);

            // Send the purchase request to the server
            fetch('./api/purchase_hat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item: hatName,
                    price: price
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Response Data:', data);
                    if (data.success) {
                        alert(`You successfully bought the ${hatName}!`);
                        location.reload(); // Reload the page to update the hat list and user's money
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });

</script>
</main>
</body>

</html>