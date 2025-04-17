<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_systems";

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_id = intval($_POST['id']);
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $customer_name = $conn->real_escape_string($_POST['customer_name']);

    // Get product price
    $priceResult = $conn->query("SELECT price FROM inventory WHERE product_id = $product_id");
    $price = $priceResult->fetch_assoc()['price'] ?? 0;

    $total_price = $quantity * $price;

    // Prepare update query
    $update = $conn->prepare("UPDATE sales SET product_id = ?, quantity = ?, customer_name = ?, total_price = ? WHERE id = ?");
    $update->bind_param("iisdi", $product_id, $quantity, $customer_name, $total_price, $sale_id);
    $update->execute();

    // Redirect to sales history after update
    header("Location: sales_history.php");
    exit();
}

// Fetch sale data based on sale ID
if (!isset($_GET['id'])) {
    die("Sale ID is required.");
}
$sale_id = intval($_GET['id']);
$sale = $conn->query("SELECT * FROM sales WHERE id = $sale_id")->fetch_assoc();
if (!$sale) {
    die("Sale not found.");
}

// Fetch list of products
$products = $conn->query("SELECT product_id, product_name FROM inventory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Sale</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-6">
        <h1 class="text-2xl font-bold mb-4 text-center">✏️ Edit Sale</h1>

        <form method="POST" class="space-y-4">
            <!-- Hidden sale ID -->
            <input type="hidden" name="id" value="<?= $sale['id'] ?>">

            <!-- Product Dropdown -->
            <div>
                <label class="block font-medium mb-1">Product</label>
                <select name="product_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <?php while ($p = $products->fetch_assoc()): ?>
                        <option value="<?= $p['product_id'] ?>" <?= $p['product_id'] == $sale['product_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['product_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block font-medium mb-1">Quantity</label>
                <input type="number" name="quantity" value="<?= $sale['quantity'] ?>" min="1" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- Customer Name -->
            <div>
                <label class="block font-medium mb-1">Customer Name</label>
                <input type="text" name="customer_name" value="<?= htmlspecialchars($sale['customer_name']) ?>" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <!-- Submit -->
            <div class="flex justify-between items-center">
                <a href="sales_history.php" class="text-sm text-gray-600 hover:underline">← Back to Sales</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
