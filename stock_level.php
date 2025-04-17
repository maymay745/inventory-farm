<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$role_id = $_SESSION['role_id'];
$username = $_SESSION['username'];

// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "inventory_systems";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products and stock levels from inventory
$sql = "SELECT * FROM inventory";
$result = $conn->query($sql);

echo "<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Levels - Farm Machinery Inventory</title>
</head>
<body class="bg-gray-100 font-sans">

<!-- Sidebar -->
<div class="h-screen bg-green-700 text-white w-64 fixed top-0 left-0 p-6">
    <h2 class="text-2xl font-bold text-center mb-8">Welcome, <?= htmlspecialchars($username) ?></h2>
    <div class="space-y-4">
        <?php
        switch ($role_id) {
            case 1: // Admin
                echo "<a href='admin_view_users.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Manage Users</a>";
                echo "<a href='inventory.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Manage Inventory</a>";
                echo "<a href='assign_machinery.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Assign Machinery</a>";
                echo "<a href='maintenance_schedule.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Maintenance Schedule</a>";
                echo "<a href='reports.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>View Reports</a>";
                break;

            case 2: // Sales Staff
                echo "<a href='inventory.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Check Inventory</a>";
                echo "<a href='create_sales.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Create Sale</a>";
                echo "<a href='sales_history.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Sales History</a>";
                break;

            case 3: // Inventory Manager
                echo "<a href='inventory.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Manage Inventory</a>";
                echo "<a href='stock_level.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Stock Levels</a>";
                echo "<a href='add_machinery.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Add Machinery</a>";
                echo "<a href='track_usage.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Track Usage</a>";
                echo "<a href='low_stock_alerts.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Low Stock Alerts</a>";
                echo "<a href='log_maintenance.php' class='block py-2 px-4 rounded-md hover:bg-green-600'>Maintenance Log</a>";
                break;
        }
        ?>
        <a href="logout.php" class="block py-2 px-4 rounded-md bg-red-600 hover:bg-red-700 text-center">Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="ml-64 p-6">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Stock Levels</h2>

    <!-- Search Bar -->
    <div class="mb-6">
        <input type="text" id="searchInput" class="w-full p-3 rounded-md shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Search products..." />
    </div>

    <!-- Product Table -->
    <div class="bg-white shadow-lg rounded-lg p-6 overflow-x-auto">
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100">
                <tr class="text-left text-xs font-semibold text-gray-600 uppercase">
                    <th class="p-4">#</th>
                    <th class="p-4">Product Name</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Quantity</th>
                    <th class="p-4">Stock Status</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4"><?= $row['product_id'] ?></td>
                            <td class="p-4"><?= htmlspecialchars($row['product_name']) ?></td>
                            <td class="p-4">₱<?= number_format($row['price'], 2) ?></td>
                            <td class="p-4"><?= $row['quantity'] ?></td>
                            <td class="p-4">
                                <?php 
                                if ($row['quantity'] == 0) {
                                    echo "<span class='text-gray-500 font-semibold'>Out of Stock</span>";
                                } elseif ($row['quantity'] <= 5) {
                                    echo "<span class='text-red-500 font-semibold'>Low Stock</span>";
                                } else {
                                    echo "<span class='text-green-500 font-semibold'>In Stock</span>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination (if necessary) -->
    <div class="mt-6 text-center">
        <!-- Example Pagination -->
        <nav class="inline-flex">
            <a href="#" class="px-4 py-2 text-gray-600 border rounded-l-md hover:bg-gray-200">Previous</a>
            <a href="#" class="px-4 py-2 text-gray-600 border-t border-b hover:bg-gray-200">1</a>
            <a href="#" class="px-4 py-2 text-gray-600 border-t border-b hover:bg-gray-200">2</a>
            <a href="#" class="px-4 py-2 text-gray-600 border-t border-b hover:bg-gray-200">3</a>
            <a href="#" class="px-4 py-2 text-gray-600 border-t border-b hover:bg-gray-200">Next</a>
        </nav>
    </div>
</div>

<script>
    // JavaScript to filter the product table based on the search input
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#productTableBody tr');

        rows.forEach(row => {
            const productName = row.cells[1].textContent.toLowerCase();
            if (productName.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>

<?php
// Close DB connection
$conn->close();
?>
