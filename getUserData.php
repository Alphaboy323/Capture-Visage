<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userdata";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Count distinct users
$userCountQuery = "SELECT COUNT(DISTINCT username) AS total_users FROM data";
$userResult = $conn->query($userCountQuery);
$userData = $userResult->fetch_assoc();

// Retrieve image paths and count for each user
$imageCountQuery = "SELECT COUNT(DISTINCT username) AS users_with_images FROM imgs";
$imageResult = $conn->query($imageCountQuery);
$imageData = $imageResult->fetch_assoc();

// Combine data
$result = array(
    "total_users" => intval($userData['total_users']),
    "users_with_images" => intval($imageData['users_with_images'])
);

header('Content-Type: application/json');
echo json_encode($result);

$conn->close();
?>
