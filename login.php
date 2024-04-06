<?php 
session_start();
?>
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
            cursor: pointer; /* Add cursor pointer to indicate clickable */
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

        .orientation-buttons {
            margin-top: 20px;
            text-align: center;
        }

        .orientation-button {
            background-color: goldenrod;
            border: none;
            color: black;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .category-dropdown {
            margin-top: 20px;
            text-align: center;
        }

        .category-dropdown select {
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #f1f1f1;
            cursor: pointer;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            background-color: rgb(60, 60, 60); /* Light grey background */
            color: white;
            border-radius: 10px; /* Rounded corners */
            display: flex;
            align-items: center; /* Align content vertically */
            justify-content: space-between; /* Align content horizontally */
        }

    </style>
</head>
<body>

<div class="black-bar">
    <h1>Capture Visage</h1>
    <div class="top-left">
        <?php
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
<div class="orientation-buttons">
    <button class="orientation-button" id="portraitButton">Show Portrait Images</button>
    <button class="orientation-button" id="landscapeButton">Show Landscape Images</button>
    <button class="orientation-button" id="allButton">Show All Images</button>
</div>
<div class="category-dropdown">
    <select id="categorySelect">
        <option value="all">All Categories</option>
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
    </select>
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

    // Retrieve image paths, uploader, name, category, and orientation from the database
    $sql = "SELECT i.image, i.username, i.category, i.orientation, d.email, d.phone 
    FROM imgs i 
    INNER JOIN data d ON i.username = d.username";

    $result = $conn->query($sql);

    $images = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['name'] = basename($row['image']); // Extract the name from the image path
            $images[] = $row;
        }

        // Display images
        foreach ($images as $image) {
            echo '<img src="' . $image['image'] . '" alt="Image" 
            data-name="' . $image['name'] . '" 
            data-category="' . $image['category'] . '" 
            data-orientation="' . $image['orientation'] . '" 
            data-username="' . $image['username'] . '" 
            data-email="' . $image['email'] . '" 
            data-phone="' . $image['phone'] . '"
            data-uploader="' . $image['username'] . '">';
        }

    } else {
        echo "No images found.";
    }

    $conn->close();


    ?>
</div>

<a class="profile-button" href="profile.php">Profile</a>

<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2>Image Details</h2>
        </div>
        <div class="modal-body">
            <div style="display: flex;">
                <img id="modalImage" src="" alt="Modal Image" style="margin-right: 20px;">
                <div>
                    <p><strong>Name:</strong> <span id="imageName"></span></p><br>
                    <p><strong>Category:</strong> <span id="imageCategory"></span></p><br>
                    <p><strong>Orientation:</strong> <span id="imageOrientation"></span></p><br>
                    <p><strong>Uploaded By:</strong> <span id="uploader"></span></p><br>
                    <p><strong>Contact Email:</strong> <span id="email"></span></p><br>
                    <p><strong>Contact Phone:</strong> <span id="phone"></span></p><br>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on an image, open the modal
    const images = document.querySelectorAll('.image-container img');
    images.forEach(image => {
        image.addEventListener('click', () => {
            // Set modal content
            document.getElementById("modalImage").src = image.src;
            document.getElementById("imageName").innerText = image.getAttribute('data-name');
            document.getElementById("imageCategory").innerText = image.getAttribute('data-category');
            document.getElementById("imageOrientation").innerText = image.getAttribute('data-orientation');
            document.getElementById("uploader").innerText = image.getAttribute('data-uploader');
            document.getElementById("email").innerText = image.getAttribute('data-email');
            document.getElementById("phone").innerText = image.getAttribute('data-phone');
            // Display the modal
            modal.style.display = "block";
        });
    });

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Filter images based on orientation
    document.getElementById("portraitButton").addEventListener("click", function() {
        filterImages("portrait");
    });

    document.getElementById("landscapeButton").addEventListener("click", function() {
        filterImages("landscape");
    });

    document.getElementById("allButton").addEventListener("click", function() {
        filterImages("all");
    });

    document.getElementById("categorySelect").addEventListener("change", function() {
        filterImagesByCategory(this.value);
    });

    function filterImages(orientation) {
        images.forEach(image => {
            if (orientation === "portrait" && image.getAttribute('data-orientation') === "portrait") {
                image.style.display = "block";
            } else if (orientation === "landscape" && image.getAttribute('data-orientation') === "landscape") {
                image.style.display = "block";
            } else if (orientation === "all") {
                image.style.display = "block";
            } else {
                image.style.display = "none";
            }
        });
    }

    function filterImagesByCategory(category) {
        images.forEach(image => {
            if (category === "all" || image.getAttribute('data-category') === category) {
                image.style.display = "block";
            } else {
                image.style.display = "none";
            }
        });
    }
    images.forEach(image => {
        image.addEventListener('mouseover', () => {
            images.forEach(otherImage => {
                if (otherImage !== image) {
                    otherImage.style.transform = 'scale(0.9)';
                    otherImage.style.zIndex = '0'; // Move shrinking images behind
                    otherImage.style.opacity = '0.5'; // Set transparency to 50%
                }
            });
            image.style.transform = 'scale(1.2)';
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
