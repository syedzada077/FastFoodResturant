<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection script
    $servername = "localhost"; // Change if your MySQL server is on a different host
    $username = "root"; // Default XAMPP username
    $password = ""; // Default XAMPP password
    $dbname = "feanedb"; // Your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = $_POST['password']; 

    // Prepare SQL insert statement
    $sql = "INSERT INTO users (name, email,phone, address, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $password);

    // Execute the statement
    if ($stmt->execute()) {
        // Successful signup
        // Redirect to a success page
        header("Location: login.php"); // Adjust path as needed
        exit();
    } else {
        // Error in execution
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth_section">
        <div class="container">
            <div class="heading_container">
                <h2>Sign Up</h2>
            </div>
            <form action="signup.php" method="POST" class="form_container">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" name="phone" class="form-control" placeholder="Phone" required>
                </div>
                <div class="form-group">
                    <input type="text" name="address" class="form-control" placeholder="Address" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="btn-box">
                    <button type="submit">Sign Up</button>
                </div>
                <p class="switch_form">Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
