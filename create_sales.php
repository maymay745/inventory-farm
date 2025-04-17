<?php
session_start();
require 'db.php';  // DB connection file

// Ensure the user is sales staff
if ($_SESSION['role_id'] != 2) {
    header("Location: dashboard.php");
    exit();
}

// Fetch products for dropdown
$products = $pdo->query("SELECT * FROM inventory")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $username = $_SESSION['username'];
    $sale_date = date('Y-m-d H:i:s');

    // Fetch product details
    $product = $pdo->prepare("SELECT * FROM inventory WHERE product_id = ?");
    $product->execute([$product_id]);
    $product = $product->fetch();

    if ($product && $product['quantity'] >= $quantity) {
        $total_price = $product['price'] * $quantity;

        // Insert sale into database
        $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, total_price, sale_date, username, customer_name) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $quantity, $total_price, $sale_date, $username, $customer_name]);

        // Update inventory stock
        $new_quantity = $product['quantity'] - $quantity;
        $pdo->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?")->execute([$new_quantity, $product_id]);

        header("Location: sales_history.php");
        exit();
    } else {
        $error_message = "Insufficient stock. Please choose a lower quantity.";
    }
}

echo "<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Sale</title>
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto p-8">
    <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Create Sale</h2>

    <!-- Error -->
    <?php if (isset($error_message)): ?>
        <div class="bg-red-600 text-white p-4 mb-6 rounded-md text-center">
            <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
        <form method="POST" action="create_sales.php" class="space-y-4">

            <!-- Customer Name -->
            <div>
                <label for="customer_name" class="block text-lg font-medium text-gray-700">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" required class="mt-2 block w-full p-3 border border-gray-300 rounded-md">
            </div>

            <!-- Product Selection -->
            <div>
                <label for="product_id" class="block text-lg font-medium text-gray-700">Select Product</label>
                <select name="product_id" id="product_id" required class="mt-2 block w-full p-3 border border-gray-300 rounded-md">
                    <option value="">-- Choose Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['product_id'] ?>">
                            <?= htmlspecialchars($product['product_name']) ?> - ₱<?= number_format($product['price'], 2) ?> (Stock: <?= $product['quantity'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-lg font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" min="1" required class="mt-2 block w-full p-3 border border-gray-300 rounded-md">
            </div>

            <!-- Submit -->
            <div class="text-center mt-6">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Create Sale</button>
            </div>
        </form>
    </div>

    <!-- Back -->
    <div class="text-center mt-6">
        <a href="dashboard.php" class="text-white bg-gray-700 hover:bg-gray-800 px-6 py-2 rounded-md font-semibold">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
