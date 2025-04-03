<header class="z-[999] w-full flex items-center justify-between p-4 bg-white shadow-md fixed">
    <a href="./index.php" class="text-lg font-semibold hover:text-blue-500">Home</a>
    <div class="flex items-center space-x-4 ml-10">
        <span class="font-bold">💰 <?php echo $money; ?></span>
        <span class="font-bold">🍎 <?php echo $food; ?></span>
    </div>
    <div id="hContainer" class="flex space-x-4 items-center">
        <a href="./shop.php" class="hover:text-blue-500">Shop</a>
        <a href="./edit.php" class="hover:text-blue-500">Edit</a>
        <a href="logout.php">
            <img src="./assets/svg/logout.svg" alt="Logout" class="w-6 h-6 hover:opacity-75">
        </a>
    </div>
</header>