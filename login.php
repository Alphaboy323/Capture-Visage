<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Random Image Gallery</title>
    <style>
        /* Add your CSS styles here */
        .xyz {
            position: absolute;
            top: 100px;
            width:100%;  
            color: grey;
            text-align: center;
        }
        body {
            margin: 0;
            margin-bottom: 400px;
            padding: 0;
            background-image: linear-gradient(#282828, #000);
        }

        .black-bar {
            position: sticky;
            background-color: black;
            color: goldenrod;
            font-family: 'Blancha', sans-serif;
            padding: 10px;
            text-align: center;
        }

        .top-right {
            position: absolute;
            top: 10px;
            right: 30px;
            color: goldenrod;
            font-family: 'Arial', sans-serif;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 100px 20px 20px;
            position: relative; /* Set the container as relative for z-index */
        }

        .image-container img {
            width: 15%; /* Set images to 15% of the original width */
            height: auto; /* Maintain aspect ratio */
            border-radius: 20px;
            transition: transform 0.5s ease; /* Smooth transition on transform */
            position: relative; /* Set the image as relative for z-index */
            z-index: 1; /* Initially set z-index to 1 */
        }

        .profile-button {
            position: absolute;
            top: 50px;
            right: 20px;
            background-color: goldenrod;
            color: black;
            border-radius: 20px;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
        }

        .logout-button {
            position: absolute;
            top: 50px;
            left: 20px; /* Position adjusted to the left */
            background-color: red;
            color: white;
            border-radius: 20px;
            padding: 8px 15px;
            text-decoration: none;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body>

<div class="black-bar">
    <h1>Capture Visage</h1>
    <div class="top-left">
        <?php
        session_start();
        // Display username if logged in
        if (isset($_SESSION['username'])) {
            echo '<a class="logout-button" href="logout.php">Logout</a>';
        } else {
            echo '<a class="logout-button" href="login.html">Login</a>';
        }
        ?>
    </div>
</div>
<div class="xyz">
    <div class="top-left">
        <?php
        // Display username if logged in
        if (isset($_SESSION['username'])) {
            echo 'Logged in as: ' . $_SESSION['username'];
        } else {
            echo 'Login!!';
        }
        ?>
    </div>
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

    $imagePaths = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePaths[] = $row['image'];
        }
        // Shuffle the array to display images randomly
        shuffle($imagePaths);

        // Display images
        foreach ($imagePaths as $image) {
            echo '<img src="' . $image . '" alt="Image">';
        }
    } else {
        echo "No images found.";
    }

    $conn->close();
    ?>
</div>

<a class="profile-button" href="profile.php">Profile</a>


<script>
    const images = document.querySelectorAll('.image-container img');
images.forEach(image => {
    image.addEventListener('mouseover', () => {
        images.forEach(otherImage => {
            if (otherImage !== image) {
                otherImage.style.transform = 'scale(0.9)';
                otherImage.style.zIndex = '0'; // Move shrinking images behind
                otherImage.style.opacity = '0.5'; // Set transparency to 50%
            }
        });
        image.style.transform = 'scale(1.5)';
        image.style.zIndex = '2'; // Bring hovered image to the front
        image.style.opacity = '1'; // Reset opacity
    });
    image.addEventListener('mouseout', () => {
        images.forEach(img => {
            img.style.transform = 'scale(1)';
            img.style.zIndex = '1'; // Reset z-index on mouseout
            img.style.opacity = '1'; // Reset opacity
        });
    });
});

</script>

</body>
</html>