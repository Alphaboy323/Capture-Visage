<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Check if input is email or username
    if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT username FROM data WHERE email=? AND password=?";
    } else {
        $query = "SELECT username FROM data WHERE username=? AND password=?";
    }

    // Establish hardcoded admin credentials
    $admin_username = "admin";
    $admin_password = "admin@123";

    // Check if the entered credentials match admin credentials
    if ($username_or_email == $admin_username && $password == $admin_password) {
        header("Location: admin_dashboard.php");
        exit();
    }

    // If not admin, proceed with regular user login check
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbname = "userdata";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username_or_email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['username'] = $result->fetch_assoc()['username'];
        header("Location: login.php");
        exit();
    } else {
        // Display alert using JavaScript
        echo "<script>alert('Invalid username or password. Please try again.');</script>";
        echo "<script>window.location = 'login.html';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
