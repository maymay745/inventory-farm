<?php
// Start the session to handle user login and authorization if necessary
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_systems";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product data if ID is provided
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM inventory WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
    }
} else {
    die("Invalid product ID.");
}

// Handle form submission to update product details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    // Update product details
    $update_sql = "UPDATE inventory SET product_name = ?, price = ?, quantity = ? WHERE product_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sdii", $product_name, $price, $quantity, $product_id);

    if ($update_stmt->execute()) {
        header("Location: inventory_list.php");
        exit();
    } else {
        $error_message = "Failed to update product details.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Link to Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans p-8">

<div class="max-w-7xl mx-auto">
    <h1 class="text-4xl font-semibold text-center text-gray-800 mb-8">✏️ Edit Product</h1>

    <!-- Error Message -->
    <?php if (isset($error_message)): ?>
        <div class="bg-red-600 text-white p-4 mb-6 rounded-md text-center">
            <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- Edit Product Form -->
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
        <form method="POST" action="edit_product.php?id=<?= $product['product_id'] ?>" class="space-y-4">

            <!-- Product Name -->
            <div>
                <label for="product_name" class="block text-lg font-medium text-gray-700">Product Name</label>
                <input type="text" name="product_name" id="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required class="mt-2 block w-full p-3 border border-gray-300 rounded-md">
            </div>

            <!-- Price -->
            <div>
                <label for="price" class="block text-lg font-medium text-gray-700">Price</label>
                <input type="number" name="price" id="price" value="<?= $product['price'] ?>" step="0.01" required class="mt-2 block w-full p-3 border border-gray-300 rounded-md">
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-lg font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="<?= $product['quantity'] ?>" min="1" required class="mt-2 block w-full p-3 border border-gray-300 rounded-md">
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-6">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition duration-200">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-6">
        <a href="inventory_list.php" class="text-white bg-gray-700 hover:bg-gray-800 px-6 py-2 rounded-md font-semibold">Back to Inventory</a>
    </div>
</div>

</body>
</html>

<?php
// Close DB connection
$conn->close();
?>
