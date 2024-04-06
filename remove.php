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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle image deletion
        if (isset($_POST['delete'])) {
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

        // Handle updating user profile
        if (isset($_POST['update_profile'])) {
            // Fetch new details from the form
            $newUsername = $_POST['newUsername'];
            $newEmail = $_POST['newEmail'];
            $newPassword = $_POST['newPassword'];
            $newPhone = $_POST['newPhone'];

            // Fetch current user details
            $stmt = $conn->prepare("SELECT username, email, phone FROM data WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $currentUsername = $row['username'];
                $currentEmail = $row['email'];
                $currentPhone = $row['phone'];

                // Check if the entered data is different from the current data or not null
                if (($newUsername != $currentUsername && !empty($newUsername)) ||
                    ($newEmail != $currentEmail && !empty($newEmail)) ||
                    !empty($newPassword) ||
                    ($newPhone != $currentPhone && !empty($newPhone))) {

                    // Update user profile in the database
                    $stmt = $conn->prepare("UPDATE data SET username=?, email=?, password=?, phone=? WHERE username=?");
                    $stmt->bind_param("sssss", $newUsername, $newEmail, $newPassword, $newPhone, $username);
                    $stmt->execute();

                    // If the username is changed, update the session variable
                    if ($newUsername != $username) {
                        $_SESSION['username'] = $newUsername;
                    }
                    echo "<script>alert('Data updated.');
                    window.location.href = 'profile.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('No changes or empty fields detected. Nothing updated.')</script>";
                }
            }
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

    // Fetch current user details
    $stmt = $conn->prepare("SELECT username, email, phone FROM data WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $currentUsername = $row['username'];
        $currentEmail = $row['email'];
        $currentPhone = $row['phone'];
    }
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

        .delete-link,
        .back-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: goldenrod;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            margin-right: 20px;
        }
        .update-details {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            width: 300px;
            margin-left: auto;
            margin-right: auto;
        }

        .update-details input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .update-details button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: goldenrod;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-details button:hover {
            background-color: #d4af37;
        }
    </style>
</head>
<body>

<div class="black-bar">
    <h1>Remove Images</h1>
</div>

<a href="profile.php" class="back-link">Back to Profile</a>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
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

<!-- Form for updating user profile -->
<div class="update-details">
    <h2>Update Profile</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateProfileForm()">
        <label for="newUsername">New Username:</label><br>
        <input type="text" id="newUsername" name="newUsername" value="<?php echo $currentUsername; ?>"><br>

        <label for="newEmail">New Email:</label><br>
        <input type="text" id="newEmail" name="newEmail" value="<?php echo $currentEmail; ?>"><br>

        <label for="newPassword">New Password:</label><br>
        <input type="password" id="newPassword" name="newPassword"><br>

        <label for="newPhone">New Phone Number:</label><br>
        <input type="text" id="newPhone" name="newPhone" value="<?php echo $currentPhone; ?>"><br>

        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>

<script>
    function validateForm() {
        var checkboxes = document.querySelectorAll('input[name="deleteImages[]"]:checked');
        if (checkboxes.length === 0) {
            alert("Please select at least one image to delete.");
            return false;
        }
        return true;
    }

    function validateProfileForm() {
        var newUsername = document.getElementById('newUsername').value;
        var newEmail = document.getElementById('newEmail').value;
        var newPassword = document.getElementById('newPassword').value;
        var newPhone = document.getElementById('newPhone').value;

        if (newUsername === "") {
            alert("Username must be filled out");
            return false;
        }

        // Username conventions check
        if (!/^[a-zA-Z0-9_]+$/.test(newUsername)) {
            alert("Username can only contain letters, numbers, and underscores");
            return false;
        }

        if (newEmail === "") {
            alert("Email must be filled out");
            return false;
        }

        if (newPassword === "") {
            alert("Password must be filled out");
            return false;
        }

        // Password conventions check
        if (newPassword.length < 8) {
            alert("Password must be at least 8 characters long");
            return false;
        }

        if (!/[a-z]/.test(newPassword) || !/[A-Z]/.test(newPassword) || !/[0-9]/.test(newPassword) || !/[@#$%^&*]/.test(newPassword)) {
            alert("Password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character");
            return false;
        }

        if (newPhone !== "" && !/^\d{10}$/.test(newPhone)) {
            alert("Please enter a valid 10-digit phone number");
            return false;
        }

        return true;
    }
</script>

</body>
</html>

