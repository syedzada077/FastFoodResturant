<?php
session_start();

// Check if user is logged in
if (!(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)) {
    // Redirect to login page or handle as needed
    header('Location: login.php'); // Replace with your login page URL
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "feanedb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch user email from session
$user_email = $_SESSION['email'];

// Define drink options with their full names and prices
$drink_options = array(
    '1' => 'Water - $1',
    '3' => 'Coke - $3',
    '4' => 'Juice - $4'
);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $food_item = $_POST['food_item'];
    $food_choice_id = $_POST['food_choice']; // Assuming this is the ID of the choice
    $drink_id = $_POST['drink']; // This will be the value (1, 3, 4)
    $drink = isset($drink_options[$drink_id]) ? $drink_options[$drink_id] : 'Unknown Drink'; // Get full name based on value
    $quantity = $_POST['quantity'];
    $total_price_str = $_POST['total_price'];

    // Convert total_price_str to float for database insertion
    $total_price = floatval(str_replace('$', '', $total_price_str)); // Remove $ sign and convert to float

    // Retrieve the actual food choice name based on food_item and food_choice_id
    $food_choice_name = '';
    switch ($food_item) {
        case 'burger':
            switch ($food_choice_id) {
                case '5': $food_choice_name = 'Cheeseburger'; break;
                case '6': $food_choice_name = 'Chicken Burger'; break;
                case '4': $food_choice_name = 'Veggie Burger'; break;
                default: $food_choice_name = 'Unknown'; break;
            }
            break;
        case 'pizza':
            switch ($food_choice_id) {
                case '10': $food_choice_name = 'Pepperoni Pizza'; break;
                case '9': $food_choice_name = 'Vegetarian Pizza'; break;
                case '11': $food_choice_name = 'Hawaiian Pizza'; break;
                default: $food_choice_name = 'Unknown'; break;
            }
            break;
        case 'pasta':
            switch ($food_choice_id) {
                case '8': $food_choice_name = 'Spaghetti'; break;
                case '9': $food_choice_name = 'Chicken Alfredo'; break;
                case '10': $food_choice_name = 'Lasagna'; break;
                default: $food_choice_name = 'Unknown'; break;
            }
            break;
        case 'fries':
            switch ($food_choice_id) {
                case '3': $food_choice_name = 'Regular Fries'; break;
                case '4': $food_choice_name = 'Cheese Fries'; break;
                case '5': $food_choice_name = 'Chili Cheese Fries'; break;
                default: $food_choice_name = 'Unknown'; break;
            }
            break;
        default:
            $food_choice_name = 'Unknown';
            break;
    }

    // Prepare and execute SQL query to insert order
    $sql = "INSERT INTO orders (user_email, food_item, food_choice, drink, quantity, total_price) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssd", $user_email, $food_item, $food_choice_name, $drink, $quantity, $total_price);
    
    if ($stmt->execute()) {
        // Order successfully placed
        header("Location: cart.php");
        exit;
    } else {
        // Error in SQL execution
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Place Order</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Place Your Order</h2>
    <form action="placeorder.php" method="POST">
        
        <div class="form-group">
            <label for="food_item">Select Food Item:</label>
            <select class="form-control" id="food_item" name="food_item">
                <option value="">Select Food Item</option>
                <option value="burger">Burger</option>
                <option value="pizza">Pizza</option>
                <option value="pasta">Pasta</option>
                <option value="fries">Fries</option>
            </select>
        </div>

        <div class="form-group" id="food_options">
           
        </div>

        <div class="form-group">
            <label for="drink">Select Drink:</label>
            <select class="form-control" id="drink" name="drink">
                <option value="">No Drink</option>
                <option value="1">Water - $1</option>
                <option value="3">Coke - $3</option>
                <option value="4">Juice - $4</option>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1">
        </div>

        <div class="form-group">
            <label for="total_price">Total Price:</label>
            <input type="text" class="form-control" id="total_price" name="total_price" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Place Order</button>
    </form>
</div>

<script>
// Update food options based on food_item selection
document.getElementById('food_item').addEventListener('change', updateFoodOptions);
document.getElementById('drink').addEventListener('change', updateTotalPrice);
document.getElementById('quantity').addEventListener('input', updateTotalPrice);

function updateFoodOptions() {
    const foodItem = document.getElementById('food_item').value;
    const foodOptionsContainer = document.getElementById('food_options');
    let optionsHTML = '';

    switch (foodItem) {
        case 'burger':
            optionsHTML = `
                <label>Select Burger:</label>
                <select class="form-control" name="food_choice">
                    <option value="5">Cheeseburger - $5</option>
                    <option value="6">Chicken Burger - $6</option>
                    <option value="4">Veggie Burger - $4</option>
                </select>
            `;
            break;
        case 'pizza':
            optionsHTML = `
                <label>Select Pizza:</label>
                <select class="form-control" name="food_choice">
                    <option value="10">Pepperoni Pizza - $10</option>
                    <option value="9">Vegetarian Pizza - $9</option>
                    <option value="11">Hawaiian Pizza - $11</option>
                </select>
            `;
            break;
        case 'pasta':
            optionsHTML = `
                <label>Select Pasta:</label>
                <select class="form-control" name="food_choice">
                    <option value="8">Spaghetti - $8</option>
                    <option value="9">Chicken Alfredo - $9</option>
                    <option value="10">Lasagna - $10</option>
                </select>
            `;
            break;
        case 'fries':
            optionsHTML = `
                <label>Select Fries:</label>
                <select class="form-control" name="food_choice">
                    <option value="3">Regular Fries - $3</option>
                    <option value="4">Cheese Fries - $4</option>
                    <option value="5">Chili Cheese Fries - $5</option>
                </select>
            `;
            break;
        default:
            optionsHTML = '';
    }

    foodOptionsContainer.innerHTML = optionsHTML;
    updateTotalPrice();
}

function updateTotalPrice() {
    const foodPriceElement = document.querySelector('#food_options select[name="food_choice"]');
    const foodPrice = foodPriceElement ? parseFloat(foodPriceElement.value) : 0;

    const drinkPriceElement = document.getElementById('drink');
    const drinkPrice = drinkPriceElement ? parseFloat(drinkPriceElement.value) : 0; // Parse directly as float

    const quantity = parseInt(document.getElementById('quantity').value);

    console.log('Food Price:', foodPrice);
    console.log('Drink Price:', drinkPrice);
    console.log('Quantity:', quantity);

    if (!isNaN(foodPrice) && !isNaN(drinkPrice) && !isNaN(quantity)) {
        const totalPrice = (foodPrice + drinkPrice) * quantity;
        document.getElementById('total_price').value = '$' + totalPrice.toFixed(2);
    } else {
        document.getElementById('total_price').value = 'Invalid calculation';
    }
}
</script>

</body>
</html>
