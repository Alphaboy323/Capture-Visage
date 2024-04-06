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
$phone = $_POST['phone'];

// Check if the username already exists
$checkUsernameQuery = "SELECT * FROM data WHERE username = '$username'";
$checkUsernameResult = $conn->query($checkUsernameQuery);

if ($checkUsernameResult->num_rows > 0) {
    echo "<script>alert('Username already exists. Please choose a different username');</script>";
    echo "<script>window.location = 'register.html';</script>";
    exit();
}

// Check if the email already exists
$checkEmailQuery = "SELECT * FROM data WHERE email = '$email'";
$checkEmailResult = $conn->query($checkEmailQuery);

if ($checkEmailResult->num_rows > 0) {
    echo "<script>alert('Email already exists. Please use a different email address');</script>";
    echo "<script>window.location = 'register.html';</script>";
    exit();
}

// Insert user data into the database
$sql = "INSERT INTO data (username, email, password, phone) VALUES ('$username', '$email', '$password', '$phone')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Registration successful');</script>";
    echo "<script>window.location = 'login.html';</script>";
    exit();
} else {
    echo "<script>alert('Error during registration');</script>";
    echo "<script>window.location = 'register.html';</script>";
    exit();
}

$conn->close();
?>