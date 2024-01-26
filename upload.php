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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            echo "File uploaded successfully!";
        } else {
            echo "Error uploading file.";
        }
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

    // If everything is okay, upload the file and store image path in the database
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;

            $sql = "INSERT INTO imgs (username, image) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $imagePath);

            if ($stmt->execute()) {
                $uploadMessage = "The file has been uploaded and the image path has been saved to the database.";
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
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <display type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="submit">Upload</button>
    </form>
    <div><?php echo $uploadMessage; ?></div>
</div>
<a class="profile-button" href="profile.php">Profile</a>
</body>
</html>
