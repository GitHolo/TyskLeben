<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="shortcut icon" type="image/x-icon" href="./images/papas-pizzeria.jpg">

    <link href="styles/login.css" rel="stylesheet" />
    <script>
        // Function to validate the email format using a regular expression
        function validateEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }

        // Function to check the email input and display an error message if invalid
        function checkEmail() {
            const emailInput = document.querySelector('input[name="email"]');
            const errorDiv = document.getElementById('email-error');
            if (!validateEmail(emailInput.value)) {
                errorDiv.textContent = 'Please enter a valid email address.';
                emailInput.focus();
                return false;
            } else {
                errorDiv.textContent = '';
                return true;
            }
        }

        // Function to validate the entire form, currently only checks the email
        function validateForm() {
            return checkEmail();
        }

        // Event listener to validate the form before submission
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            form.addEventListener('submit', function (event) {
                if (!validateForm()) {
                    event.preventDefault(); // Prevent form submission if validation fails
                }
            });
        });
    </script>
</head>

<body>
    <div class="login-box">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <!-- Email input with oninput event to check email format -->
            <input type="text" name="login" placeholder="Login" required><br>
            <input type="text" name="email" placeholder="Email" required spellcheck="true" oninput="checkEmail()"><br>
            <div id="email-error" style="color: red;"></div> <!-- Div to display email error message -->
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" name="submit" value="Register">
        </form>
        <br>
        <!-- Link to login page -->
        <form style="justify-content: center; display: flex;" action="login.php" method="post">
            <input style="width: 60%;" type="submit" value="Go to Login">
        </form>
        <br>
        <!-- Link to home page -->
        <form style="justify-content: center; display: flex;" action="index.php" method="post">
            <input style="width: 40%;" type="submit" value="Home">
        </form>
    </div>
</body>

</html>

<?php
session_start(); // Start the session to use session variables

// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "htc";

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection to the database failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
            } else { // If there is an error inserting into login table
                echo "<script>alert('Error creating account');</script>";
            }
        }
    }
}

// Close the database connection
$conn->close();
?>