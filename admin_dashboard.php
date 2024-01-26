<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        /* Add your CSS styles here */
        /* Sample styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #282828;
            margin: 0;
            padding: 20px;
        }

        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .back-to-home {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px;
            border: none;
            border-radius: 50%;
            background-color: #555;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        .back-to-home:hover {
            background-color: #333;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .user-image {
            max-width: 50px;
            max-height: 50px;
        }
        .popup-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9); /* Adjust the alpha value for translucency */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup {
            background-color: rgba(255, 255, 255, 0.7); /* Adjust the alpha value for translucency */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

    </style>
</head>
<body>

<a href="landing.html" class="back-to-home">&#127968;</a>
<div class="popup-background" id="popupBackground">
    <div class="popup">
        <h2>Enter Admin Access Code</h2>
        <input type="password" id="accessCode" placeholder="Access Code">
        <button onclick="checkAccessCode()">Submit</button>
    </div>
</div>
<div class="dashboard-container">
    <h1>Admin Dashboard</h1>
    <div class="user-info">
        <h3>Total Users and Images</h3>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "userdata";

        // Create connection asd
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Count distinct users
        $userCountQuery = "SELECT COUNT(DISTINCT username) AS total_users FROM data";
        $userResult = $conn->query($userCountQuery);
        $userData = $userResult->fetch_assoc();

        // Count total images uploaded
        $totalImagesQuery = "SELECT COUNT(image) AS total_images FROM imgs";
        $totalImagesResult = $conn->query($totalImagesQuery);
        $totalImagesData = $totalImagesResult->fetch_assoc();

        echo '<p>Total Users: ' . $userData['total_users'] . '</p>';
        echo '<p>Total Images: ' . $totalImagesData['total_images'] . '</p>';

        // Retrieve image paths and count for each user
        $imageCountQuery = "SELECT username, COUNT(image) AS image_count FROM imgs GROUP BY username";
        $imageResult = $conn->query($imageCountQuery);

        if ($imageResult->num_rows > 0) {
            echo '<h3>Images Uploaded by Each User</h3>';
            echo '<table>';
            echo '<tr><th>Username</th><th>Images Uploaded</th><th>Images</th></tr>';
            while ($row = $imageResult->fetch_assoc()) {
                $username = $row['username'];
                $imageCount = $row['image_count'];

                echo '<tr>';
                echo '<td>' . $username . '</td>';
                echo '<td>' . $imageCount . '</td>';
                echo '<td>';

                // Retrieve images for the current user
                $userImagesQuery = "SELECT image FROM imgs WHERE username = '$username'";
                $userImagesResult = $conn->query($userImagesQuery);

                while ($imageRow = $userImagesResult->fetch_assoc()) {
                    echo '<img class="user-image" src="' . $imageRow['image'] . '" alt="Image">';
                }

                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No images uploaded yet.</p>';
        }

        $conn->close();
        ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <div style="width: 200px; height: 200px;">
            <canvas id="userChart" width="200" height="200"></canvas>
        </div>
        <!-- ... (existing HTML) ... -->
        <button onclick="window.location.href = 'delete_users.php'">Delete Users</button>
        <!-- ... (existing HTML) ... -->


    </div>
    <script>
    function checkAccessCode() {
        const enteredCode = document.getElementById('accessCode').value;
        const popupBackground = document.getElementById('popupBackground');

        if (enteredCode === '142004') {
            popupBackground.style.display = 'none'; // Hide the popup background
        } else {
            window.location.href = 'landing.html';
        }
    }
</script>
</div>

</body>
</html>

<script>
    // Fetch data from the PHP backend
    fetch('getUserData.php') // Create a PHP file to handle this data retrieval
        .then(response => response.json())
        .then(data => {
            const registeredUsers = data.total_users;
            const usersWithImages = data.users_with_images;

            const ctx = document.getElementById('userChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'pie', // Change the chart type to pie
                data: {
                    labels: ['Registered Users', 'Users with Images'],
                    datasets: [{
                        label: 'User Count',
                        data: [registeredUsers, usersWithImages],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    // You can add options specific to the pie chart here
                }
            });
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
</script>
