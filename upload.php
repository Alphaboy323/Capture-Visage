<?php
session_start();
// Assuming the username is stored in $_SESSION['username']
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$commentFile = 'comments.txt';

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "userdata";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uploadMessage = ""; // Variable to hold upload message

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $targetDirectory = "uploads/"; // Directory where images will be stored on the server
    $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $uploadMessage = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        $uploadMessage = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadMessage = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // If everything is okay, upload the file and store image path, category, and orientation in the database
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
            $category = $_POST['category'];
            $orientation = $_POST['orientation'];

            $sql = "INSERT INTO imgs (username, image, category, orientation) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $imagePath, $category, $orientation);

            if ($stmt->execute()) {
                $uploadMessage = "The file has been uploaded and the image path, category, and orientation have been saved to the database.";
                header("Location: profile.php?message=" . urlencode($uploadMessage)); // Redirect to profile page with message
                exit();
            } else {
                $uploadMessage = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $uploadMessage = "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image Upload</title>
    <link rel="stylesheet" href="upload.css"> <!-- Link to your CSS file -->
    <style>
        /* Add your CSS styles here */
        .black-bar {
            background-color: black;
            color: goldenrod;
            font-family: 'Blancha', sans-serif;
            padding: 10px;
            text-align: center;
        }

        .top-right {
            color: goldenrod;
            font-family: 'Arial', sans-serif;
            position: absolute;
            top: 10px;
            right: 30px;
        }

        .upload-container {
            margin-top: 100px;
            padding: 50px;
            text-align: center;
        }

        .upload-container h2 {
            font-family: 'Arial', sans-serif;
        }

        .upload-container input[type="text"],
        .upload-container input[type="file"],
        .upload-container button {
            margin: 10px;
            padding: 8px;
            border-radius: 5px;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body background="white">

<div class="black-bar">
    <h1>Capture Visage</h1>
    <div class="top-right">
        <?php
        // Display username if logged in
        if (isset($_SESSION['username'])) {
            echo 'Logged in as: ' . $_SESSION['username'];
        }
        ?>
    </div>
</div>

<div class="upload-container">
    <h2>Upload Image</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="file" name="image" accept="image/*" required>
        <select name="category">
            <option value="Portrait Photography">Portrait Photography</option>
            <option value="Landscape Photography">Landscape Photography</option>
            <option value="Street Photography">Street Photography</option>
            <option value="Wildlife Photography">Wildlife Photography</option>
            <option value="Macro Photography">Macro Photography</option>
            <option value="Fashion Photography">Fashion Photography</option>
            <option value="Event Photography">Event Photography</option>
            <option value="Architectural Photography">Architectural Photography</option>
            <option value="Travel Photography">Travel Photography</option>
            <option value="Documentary Photography">Documentary Photography</option>
            <!-- Add more categories if needed -->
        </select>
        <select name="orientation">
            <option value="landscape">Landscape</option>
            <option value="portrait">Portrait</option>
            <!-- Add more orientations if needed -->
        </select>
        <button type="submit" name="submit">Upload</button>
        <div class="error-message" id="error-message"></div>
    </form>
</div>
<script>
    function validateForm() {
        var fileInput = document.querySelector('input[type="file"]');
        var errorMessage = document.getElementById('error-message');

        // Check file type
        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(fileInput.files[0].type)) {
            errorMessage.textContent = "Only JPEG, JPG, and PNG file types are allowed.";
            return false;
        }

        // Check file size (in bytes)
        var maxSize = 5000000; // Adjust as needed
        if (fileInput.files[0].size > maxSize) {
            errorMessage.textContent = "File size exceeds the maximum allowed size.";
            return false;
        }

        // Check image resolution (example: width and height both should be at least 100 pixels)
        var minWidth = 100;
        var minHeight = 100;
        var img = new Image();
        img.src = URL.createObjectURL(fileInput.files[0]);
        img.onload = function() {
            if (img.width < minWidth || img.height < minHeight) {
                errorMessage.textContent = "Image resolution should be at least " + minWidth + "x" + minHeight + " pixels.";
            } else {
                errorMessage.textContent = "";
            }
        };

        return true;
    }
</script>


<a class="profile-button" href="profile.php">Profile</a>
</body>
</html>
