<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben</title>
    <link href="styles/index.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<?php
sleep(2);
if (!isset($_COOKIE['user_ID'])) {
    header("Location: login.php");
    exit();
} ?>

<body>
    <header><a>Home</a>
        <div id="hContainer">
            <a>Shop</a>
            <a>Edit</a>
            <a href="logout.php"><img src="assets/images/svg/logout.svg"></a>
        </div>
    </header>
    <main>
        <h1>Home</h1>

    </main>
</body>

</html>