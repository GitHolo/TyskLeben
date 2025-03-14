<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tysk Leben</title>
    <link href="styles/index.css" rel="stylesheet" />
</head>

<?php
sleep(2);
if (!isset($_COOKIE['user_ID'])) {
    header("Location: login.php");
    exit();
} ?>

<body>
    <main>
        <h1>Welcome</h1>
        
    </main>
</body>

</html>