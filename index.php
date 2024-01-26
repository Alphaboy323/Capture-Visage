<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Random Image Gallery</title>
    <style>
        /* Add your CSS styles here */
        body {
            margin: 0;
            padding: 0;
        }

        .black-bar {
            background-color: black;
            color: goldenrod;
            font-family: 'Blancha', sans-serif;
            padding: 10px;
            text-align: center;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
        }

        .image-container img {
            width: 200px;
            height: auto;
        }
    </style>
</head>
<body>

<div class="black-bar">
    <h1>Capture Visage</h1>
</div>

<div class="image-container">
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

    // Retrieve image paths from the database
    $sql = "SELECT image FROM imgs";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $image = $row['image'];
            echo '<img src="' . $image . '" alt="Image">';
        }
    } else {
        echo "No images found.";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
