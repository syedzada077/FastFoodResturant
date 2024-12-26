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

if (isset($_POST['update_profile'])) {
    $user_email = $_SESSION['email'];
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $profile_picture = '';

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Update user data in the database
    $query = "UPDATE users SET name='$name', email='$email', phone='$phone', address='$address'";
    if ($profile_picture) {
        $query .= ", profile_picture='$profile_picture'";
    }
    $query .= " WHERE email='$user_email'";

    if (mysqli_query($connection, $query)) {
        echo "Profile updated successfully.";
        header('Location: profile.php');
        exit;
    } else {
        echo "Error updating profile: " . mysqli_error($connection);
    }
}

// Fetch current user data
$user_email = $_SESSION['email'];
$query = "SELECT name, email, phone, address, profile_picture FROM users WHERE email = '$user_email'";
$result = mysqli_query($connection, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    die('Error fetching user data: ' . mysqli_error($connection));
}

// Close database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="edit_profile_section">
        <div class="container">
            <div class="heading_container">
                <h2>Edit Profile</h2>
            </div>
            <div class="edit_profile_form">
                <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="4" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture">
                        <?php if ($row['profile_picture']) { ?>
                            <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture" width="100">
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_profile">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
