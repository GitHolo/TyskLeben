<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" type="image/x-icon" href="./images/papas-pizzeria.jpg">
</head>
<body class="flex flex-col items-center justify-center h-screen bg-gray-100 p-4">
    <div class="w-80 p-6 bg-white border border-gray-300 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Register</h2>
        <form method="post" action="register.php" class="space-y-4">
            <input type="text" name="login" placeholder="Login" required
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" name="email" placeholder="Email" required spellcheck="true" oninput="checkEmail()"
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div id="email-error" class="text-red-500 text-sm"></div>
            <input type="password" name="password" placeholder="Password" required
                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="submit" name="submit" value="Register"
                class="w-full p-2 bg-blue-500 text-white rounded-md cursor-pointer hover:bg-blue-600">
        </form>
        <br>
        <form action="login.php" method="post" class="flex justify-center">
            <input type="submit" value="Go to Login"
                class="w-3/5 p-2 bg-gray-500 text-white rounded-md cursor-pointer hover:bg-gray-600">
        </form>
        <br>
        <form action="index.php" method="post" class="flex justify-center">
            <input type="submit" value="Home"
                class="w-2/5 p-2 bg-gray-700 text-white rounded-md cursor-pointer hover:bg-gray-800">
        </form>
    </div>
    <script>
        function validateEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }
        function checkEmail() {
            const emailInput = document.querySelector('input[name="email"]');
            const errorDiv = document.getElementById('email-error');
            if (!validateEmail(emailInput.value)) {
                errorDiv.textContent = 'Please enter a valid email address.';
                return false;
            } else {
                errorDiv.textContent = '';
                return true;
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            form.addEventListener('submit', function (event) {
                if (!checkEmail()) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>


<?php
require "config.php";

function set_user_cookie($user_ID)
{
    setcookie("user_ID", $user_ID, time() + (86400 * 30), "/");
}
// Check if the request method is POST (form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required POST variables are set
    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $login = $_POST['login'];


        // Check if the email already exists in the login table
        $check_sql = "SELECT user_ID FROM login WHERE email='$email' OR login='$login'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) { // If email already exists
            echo "<script>alert('Email or Login already exists');</script>";
        } else {
            // Insert the new user into the login table
            $insert_login_sql = "INSERT INTO login (login, email, password) VALUES ('$login', '$email', '$password')";
            if ($conn->query($insert_login_sql) === TRUE) { // If insertion is successful
                echo "<script>alert('Account created successfully');</script>";
                // Redirect to login page with the user's ID as a query parameter
                $check_sql = "SELECT user_ID FROM login WHERE email='$email' OR login='$login'";
                $check_result = $conn->query($check_sql);
                $row = $check_result->fetch_assoc(); // Fetch the result row
                $_SESSION['user_ID'] = $row['user_ID'];

                $_SESSION['login'] = $login;
                set_user_cookie($row['user_ID']);
                header("Location: create.php");
                exit();
            } else { // If there is an error inserting into login table
                echo "<script>alert('Error creating account');</script>";
            }
        }
    }
} else if (isset($_COOKIE['user_ID'])) {
    $_SESSION['user_ID'] = $_COOKIE['user_ID'];
    header("Location: index.php?user_ID=" . $_COOKIE['user_ID']);
    exit();
}

// Close the database connection
$conn->close();
?>