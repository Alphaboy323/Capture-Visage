<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Images</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            margin-bottom: 20px;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #333;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select {
            padding: 8px;
            font-size: 16px;
            border-radius: 4px;
        }

        button[type="submit"] {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            margin-top: 10px;
        }

        /* Popup styles */
        .popup {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            z-index: 999;
        }
    </style>
    <script>
        function closeMsg() {
            var msg = document.getElementById('successMsg');
            setTimeout(function () {
                msg.style.display = 'none';
            }, 3000);
        }
    </script>
</head>
<body>

<h1>Delete User and Data</h1>

<a href="admin_dashboard.php" class="back-btn">Back to Admin Dashboard</a>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "userdata";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $userQuery = "SELECT username FROM data";
    $userResult = $conn->query($userQuery);

    if ($userResult->num_rows > 0) {
        echo '<label for="users">Select User:</label>';
        echo '<select name="userToDelete" id="users">';

        while ($row = $userResult->fetch_assoc()) {
            echo '<option value="' . $row['username'] . '">' . $row['username'] . '</option>';
        }

        echo '</select>';
    } else {
        echo "No users found";
    }

    $conn->close();
    ?>

    <button type="submit" name="deleteImages">Delete Selected User's Data</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteImages'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "userdata";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['userToDelete'])) {
        $selectedUser = $_POST['userToDelete'];

        $deleteImagesQuery = "DELETE FROM imgs WHERE username = '$selectedUser'";
        if ($conn->query($deleteImagesQuery) === TRUE) {
            $deleteDataQuery = "DELETE FROM data WHERE username = '$selectedUser'";
            if ($conn->query($deleteDataQuery) === TRUE) {
                echo "<div class='popup' id='successMsg'>User '$selectedUser' data deleted successfully.</div>";
                echo "<script>document.getElementById('successMsg').style.display = 'block'; closeMsg();</script>";
            } else {
                echo "Error deleting user's data: " . $conn->error;
            }
        } else {
            echo "Error deleting user's images: " . $conn->error;
        }
    } else {
        echo "No user selected for deletion";
    }

    $conn->close();
}
?>
</body>
</html>
