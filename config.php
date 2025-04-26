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
