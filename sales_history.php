<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_systems";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Delete Sale
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM sales WHERE id = $id");
    header("Location: sales_history.php");
    exit();
}

// Filters
$where = "1=1";
if (!empty($_GET['from_date'])) {
    $from = $_GET['from_date'];
    $where .= " AND DATE(sale_date) = '$from'";
}
if (!empty($_GET['product_name'])) {
    $productName = $conn->real_escape_string($_GET['product_name']);
    $where .= " AND inventory.product_name LIKE '%$productName%'";
}

// Fetch Sales
$sales = $conn->query("
    SELECT sales.*, inventory.product_name AS item_name, inventory.price AS price
    FROM sales
    JOIN inventory ON sales.product_id = inventory.product_id
    WHERE $where
    ORDER BY sale_date DESC
");

// Total Sales Calculation
$totalQuery = $conn->query("
    SELECT SUM(s.total_price) AS total_sales 
    FROM sales s 
    JOIN inventory i ON s.product_id = i.product_id 
    WHERE $where
");
$totalSales = $totalQuery->fetch_assoc()['total_sales'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; color: #000; }
        }
    </style>
</head>
<body class="bg-gradient-to-r from-indigo-600 to-blue-500 min-h-screen p-8 text-white">

<div class="max-w-7xl mx-auto">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold">📈 Sales History Dashboard</h1>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-4 justify-center mb-6 no-print">
        <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>" class="p-2 rounded text-gray-800">
        <input type="text" name="product_name" placeholder="Search item..." value="<?= $_GET['product_name'] ?? '' ?>" class="p-2 rounded text-gray-800">
        <button type="submit" class="bg-white text-indigo-700 px-4 py-2 rounded hover:bg-gray-200 transition">Filter</button>
        <button type="button" onclick="window.print()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">🖨️ Print</button>
        <button onclick="exportTableToExcel('salesTable')" type="button" class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500">📥 Export to Excel</button>
    </form>

    <!-- Total Sales -->
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold">
            💰 Total Sales: <span class="text-green-300">₱<?= number_format($totalSales, 2) ?></span>
        </h2>
    </div>

    <!-- Sales Table -->
    <div class="bg-white text-gray-800 shadow-lg rounded-lg overflow-x-auto">
        <table id="salesTable" class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100">
                <tr class="text-left text-xs font-semibold text-gray-600 uppercase">
                    <th class="p-4">#</th>
                    <th class="p-4">Item</th>
                    <th class="p-4">Qty</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Date</th>
                    <th class="p-4 no-print">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sales->num_rows > 0): ?>
                    <?php while ($row = $sales->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4"><?= $row['id'] ?></td>
                            <td class="p-4"><?= htmlspecialchars($row['item_name']) ?></td>
                            <td class="p-4"><?= $row['quantity'] ?></td>
                            <td class="p-4">₱<?= number_format($row['price'], 2) ?></td>
                            <td class="p-4 font-bold text-green-600">₱<?= number_format($row['total_price'], 2) ?></td>
                            <td class="p-4"><?= htmlspecialchars($row['customer_name'] ?? $row['username']) ?></td>
                            <td class="p-4"><?= date("M d, Y", strtotime($row['sale_date'])) ?></td>
                            <td class="p-4 no-print">
                            <a href="edit_sale.php?id=<?= $row['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">Edit</a>
                                <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this record?')" class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="p-6 text-center text-gray-500">No sales found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Export to Excel -->
<script>
function exportTableToExcel(tableID, filename = 'Sales_Report.xls') {
    const dataType = 'application/vnd.ms-excel';
    const table = document.getElementById(tableID);
    const tableHTML = table.outerHTML.replace(/ /g, '%20');
    const downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);
    downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    downloadLink.download = filename;
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

</body>
</html>

<?php $conn->close(); ?>
