<?php
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userdata";           

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password']; // Encrypt password

// Insert user data into the database
$sql = "INSERT INTO data (username, email, password) VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    header("Location: regdone.html"); // Redirect on successful registration
    exit();
} else {
    header("Location: regerror.html"); // Redirect on error during insertion
    exit();
}

$conn->close();
?>
