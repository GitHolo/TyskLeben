<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <!-- Link to external CSS for styling the login page -->
    <link href="styles/login.css" rel="stylesheet" />

    <link rel="shortcut icon" type="image/x-icon" href="./images/papas-pizzeria.jpg">
</head>

<body style="flex-direction: column; display: flex;">
    <h1>Welcome to Tysk Leben!</h1>
    <div class="login-box">
        <h2>Login</h2>
        <!-- Login form to submit email and password -->
        <form method="post" action="">
            <input type="text" name="login" placeholder="Email or Login" required><br>
            <div id="email-error" style="color: red;"></div>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" name="submit" value="Login">
        </form>
        <br>
        <!-- Button to navigate to the registration page -->
        <form style="justify-content: center; display: flex;" action="register.php" method="post">
            <input style="width: 60%;" type="submit" value="Go to Register">
        </form>
        <br>
        <!-- Button to navigate to the home page -->
-        <form style="justify-content: center; display: flex;" action="index.php" method="post">
            <input style="width: 40%;" type="submit" value="Home">
        </form>
    </div>
</body>

</html>

<?php
// Start the session to track user information
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "htc";

// Create a new connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the user is already logged in, redirect to their profile page
if (isset($_SESSION['user_ID'])) {
    header("Location: index.php?user_ID=" . $_COOKIE['user_ID']);
    exit();
}

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
                header("Location: index.php?user_ID=" . $_COOKIE['user_ID']);
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
    header("Location: index.php?user_ID=" . $_COOKIE['user_ID']);
    exit();
}

// Close the database connection
$conn->close();
?>