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

// Fetch all products from inventory
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory List</title>
    <!-- Link to Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for better UI */
        .table-hover tbody tr:hover {
            background-color: #f0f0f0;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-8">

<div class="max-w-7xl mx-auto">
    <!-- Page Title -->
    <h1 class="text-4xl font-semibold text-center text-gray-800 mb-8">📦 Inventory Management</h1>

    <!-- Inventory Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full table-auto text-sm table-hover table-striped">
            <thead class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white">
                <tr class="text-left text-xs font-semibold uppercase">
                    <th class="p-4">ID</th>
                    <th class="p-4">Product Name</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Quantity</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4"><?= $row['product_id'] ?></td>
                            <td class="p-4"><?= htmlspecialchars($row['product_name']) ?></td>
                            <td class="p-4">₱<?= number_format($row['price'], 2) ?></td>
                            <td class="p-4"><?= $row['quantity'] ?></td>
                            <td class="p-4 flex gap-4">
                                <!-- Edit Action -->
                                <a href="edit_product.php?id=<?= $row['product_id'] ?>" class="text-blue-500 hover:text-blue-700 transition duration-200 font-semibold">Edit</a>
                                <!-- Delete Action -->
                                <a href="delete_product.php?id=<?= $row['product_id'] ?>" class="text-red-500 hover:text-red-700 transition duration-200 font-semibold" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add New Product Button -->
    <div class="text-center mt-8">
        <a href="add_product.php" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition duration-200 text-lg font-semibold shadow-md transform hover:scale-105">+ Add New Product</a>
    </div>
</div>

</body>
</html>

<?php
// Close DB connection
$conn->close();
?>
