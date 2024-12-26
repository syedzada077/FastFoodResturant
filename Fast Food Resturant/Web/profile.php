<?php
session_start(); // Start session to access session variables

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page or handle unauthorized access
    header('Location: login.php');
    exit;
}

// Database connection parameters
$servername = "localhost"; // Change this to your database server
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$database = "feanedb"; // Replace with your database name

// Create connection
$connection = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Assuming your user table is named 'users' and has columns 'name', 'email', 'phone', 'address', 'profile_picture'
$user_email = $_SESSION['email']; // Adjust this based on your session variable for user ID
$query = "SELECT name, email, phone, address, profile_picture FROM users WHERE email = '$user_email'"; // Query to fetch user data

$result = mysqli_query($connection, $query);

if (!$result) {
    die('Error fetching user data: ' . mysqli_error($connection));
}

$row = mysqli_fetch_assoc($result); // Fetch user data into an associative array

// Close database connection (always good practice)
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<a href="index.php" id="edit_profile_btn">Back</a>
    <div class="profile_section">
        <div class="container">
            <div class="heading_container">
                <h2>User Profile</h2>
            </div>
            <div class="profile_container">
                <div class="profile_photo">
                    <?php if ($row['profile_picture']) { ?>
                        <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="User Photo">
                    <?php } else { ?>
                        <img src="images/client1.jpg" alt="User Photo">
                    <?php } ?>
                </div>
                <div class="profile_details">
                    <p><strong>Name:</strong> <span id="name"><?php echo htmlspecialchars($row['name']); ?></span></p>
                    <p><strong>Email:</strong> <span id="email"><?php echo htmlspecialchars($row['email']); ?></span></p>
                    <p><strong>Phone:</strong> <span id="phone"><?php echo htmlspecialchars($row['phone']); ?></span></p>
                    <p><strong>Address:</strong> <span id="address"><?php echo htmlspecialchars($row['address']); ?></span></p>
                    <a href="edit_profile.php" id="edit_profile_btn">Edit Profile</a>
                    <a href="logout.php" id="edit_profile_btn">Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
