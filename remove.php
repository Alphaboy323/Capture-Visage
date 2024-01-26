<?php
session_start();

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "userdata";

// Establish a connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate the user's session and get their username
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
        if (isset($_POST['deleteImages']) && is_array($_POST['deleteImages'])) {
            $deleteImages = $_POST['deleteImages'];
            $placeholders = rtrim(str_repeat('?, ', count($deleteImages)), ', ');

            // Delete entries associated with the user's username and the selected images
            $stmt = $conn->prepare("DELETE FROM imgs WHERE username = ? AND image IN ($placeholders)");
            $stmt->bind_param("s" . str_repeat('s', count($deleteImages)), $username, ...$deleteImages);
            $stmt->execute();

            // Redirect to profile.php after deletion
            header("Location: profile.php");
            exit();
        }
    }

    // Retrieve images associated with the user
    $stmt = $conn->prepare("SELECT image FROM imgs WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $imagePaths = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePaths[] = $row['image'];
        }
    }

    $conn->close();
} else {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Images</title>
    <style>
        /* Add your CSS styles here */
        body {
            margin: 0;
            padding: 0;
            background-color: #282828;
            font-family: 'Blancha', sans-serif;
        }

        .black-bar {
            background-color: black;
            color: goldenrod;
            padding: 10px;
            text-align: center;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 50px 20px 20px;
        }

        .image-container img {
            width: 200px;
            height: auto;
            border-radius: 20px;
        }

        .delete-button {
            text-align: center;
            margin-top: 20px;
            font-family: 'Arial', sans-serif;
        }

        .delete-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: goldenrod;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="black-bar">
    <h1>Remove Images</h1>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="image-container">
        <?php foreach ($imagePaths as $image): ?>
            <label>
                <input type="checkbox" name="deleteImages[]" value="<?php echo $image; ?>">
                <img src="<?php echo $image; ?>" alt="Image">
            </label>
        <?php endforeach; ?>
    </div>
    <div class="delete-button">
        <button type="submit" name="delete" class="delete-link">Delete</button>
    </div>
</form>

</body>
</html>
            