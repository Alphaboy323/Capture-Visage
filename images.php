<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <style>
        /* Add your CSS styles here */
        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .image-container img {
            width: 200px;
            height: auto;
        }
    </style>
</head>
<body>

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

// Retrieve all images from the database
$sql = "SELECT image FROM imgs";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="image-container">';
    while ($row = $result->fetch_assoc()) {
        $imageData = base64_encode($row['image']);
        echo '<img src="data:image/*;base64,' . $imageData . '">';
    }
    echo '</div>';
} else {
    echo "No images found.";
}

$conn->close();
?>

</body>
</html>
