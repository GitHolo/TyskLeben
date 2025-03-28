<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" type="image/x-icon" href="./images/papas-pizzeria.jpg">
</head>

<body class="flex flex-col items-center justify-center h-screen bg-gray-100 p-4">
    <h1 class="text-2xl font-bold mb-6">Welcome to Tysk Leben!</h1>
    <div class="w-80 p-6 bg-white border border-gray-300 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Login</h2>
        <form method="post" action="" class="space-y-4">
            <input type="text" name="login" placeholder="Email or Login" required
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div id="email-error" class="text-red-500 text-sm"></div>
            <input type="password" name="password" placeholder="Password" required
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="submit" name="submit" value="Login"
                class="w-full p-2 bg-blue-500 text-white rounded-md cursor-pointer hover:bg-blue-600">
        </form>
        <br>
        <form action="register.php" method="post" class="flex justify-center">
            <input type="submit" value="Go to Register"
                class="w-3/5 p-2 bg-gray-500 text-white rounded-md cursor-pointer hover:bg-gray-600">
        </form>
        <br>
        <form action="index.php" method="post" class="flex justify-center">
            <input type="submit" value="Home"
                class="w-2/5 p-2 bg-gray-700 text-white rounded-md cursor-pointer hover:bg-gray-800">
        </form>
    </div>
</body>

</html>

<?php
require "./config.php";

// If the user is already logged in, redirect to their profile page


function set_user_cookie($user_ID)
{
    setcookie("user_ID", $user_ID, time() + (86400 * 30), "/");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure both email and password are provided
    if (isset($_POST['login']) && isset($_POST['password'])) {
        $email = $_POST['login'];
        $password = $_POST['password'];
        $sql = "SELECT user_ID, email, password FROM login WHERE email = '$email' OR login = '$email'";
        $result = $conn->query($sql);

        // If a matching user is found, verify the password
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_ID'] = $row['user_ID'];
                $_SESSION['login'] = $row['login'];
                set_user_cookie($row['user_ID']);
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Incorrect password');</script>";
            }
        } else {
            echo "<script>alert('No user found with this email');</script>";
        }

    }
} else if (isset($_COOKIE['user_ID'])) {
    $_SESSION['user_ID'] = $_COOKIE['user_ID'];
    header("Location: index.php");
    exit();
}

// Close the database connection
$conn->close();
?>