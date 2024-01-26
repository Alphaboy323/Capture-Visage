<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <style>
        /* Add your CSS styles here */
        body {
            margin: 0;
            padding: 0;
            background-color: #282828;
            font-family: 'blancha', sans-serif;
        }

        .black-bar {
            background-color: black;
            color: goldenrod;
            padding: 10px;
            text-align: center;
        }

        .top-right {
            position: absolute;
            top: 10px;
            right: 10px;
            color: goldenrod;
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
            margin-top: 20px;
            border-radius: 20px;
        }

        .upload-button {
            text-align: center;
            margin-top: 20px;
            font-family: 'Arial', sans-serif;
        }

        .upload-link {
            display: inline-block;
            padding: 15px 30px;
            background-color: goldenrod;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
        }

        .delete-button {
            text-align: center;
            margin-top: 20px;
            font-family: 'Arial', sans-serif;
        }

        .delete-link {
            display: inline-block;
            padding: 15px 30px;
            background-color: red; /* Change color as needed */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
        }

        .home-link {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .home-button {
            padding: 10px 20px;
            background-color: goldenrod;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body>

<div class="black-bar">
    <h1>Capture Visage</h1>
    <div class="top-right">
        <?php
        session_start();
        // Display username if logged in
        if (isset($_SESSION['username'])) {
            echo 'Logged in as: ' . $_SESSION['username'];
        }
        ?>
    </div>
</div>

<div class="image-container">
    <?php
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbname = "userdata";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Assuming 'username' is the column in the table that stores usernames
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $sql = "SELECT image FROM imgs WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $image = $row['image'];
                echo '<img src="' . $image . '" alt="Image">';
            }
        } else {
            echo "No images found for this user.";
        }
    } else {
        echo "Please log in to view images.";
    }

    $conn->close();
    ?>
</div>

<div class="upload-button">
    <a class="upload-link" href="upload.php">Upload Your Photographs</a>
</div>

<div class="delete-button">
    <a class="delete-link" href="remove.php">Delete Photos</a>
</div>

<div class="home-link">
    <a class="home-button" href="login.php">Home</a>
</div>

</body>
</html>
    