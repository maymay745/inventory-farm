<?php
session_start();

// Admin check (assuming session variable `role` is set for the logged-in user)
if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// DB Connection
$conn = new mysqli("localhost", "root", "", "inventory_systems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle order approval or rejection
if (isset($_GET['approve'])) {
    $order_id = $_GET['approve'];
    $conn->query("UPDATE customer_orders SET status = 1 WHERE id = $order_id"); // Approve the order
    header("Location: admin_manage_orders.php"); // Redirect to the same page after approval
}

if (isset($_GET['reject'])) {
    $order_id = $_GET['reject'];
    $conn->query("DELETE FROM customer_orders WHERE id = $order_id"); // Reject the order (delete it)
    header("Location: admin_manage_orders.php"); // Redirect to the same page after rejection
}

// Fetch orders (including pending ones)
$orders = $conn->query("SELECT * FROM customer_orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-green-400 via-green-500 to-green-600 min-h-screen p-8 text-white">

    <!-- Admin Orders Table -->
    <div class="max-w-6xl mx-auto mt-12 bg-white p-6 rounded-lg shadow-lg text-gray-800">
        <h2 class="text-2xl font-bold mb-4">Manage Customer Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Customer</th>
                        <th class="px-4 py-2 border">Machinery</th>
                        <th class="px-4 py-2 border">Type</th>
                        <th class="px-4 py-2 border">Qty</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['machinery_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['machinery_type']) ?></td>
                            <td class="px-4 py-2 border"><?= $row['quantity'] ?></td>
                            <td class="px-4 py-2 border"><?= $row['order_date'] ?></td>
                            <td class="px-4 py-2 border">
                                <?php if ($row['status'] == 0): ?>
                                    <span class="text-red-500">Pending</span>
                                <?php else: ?>
                                    <span class="text-green-500">Approved</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <?php if ($row['status'] == 0): ?>
                                    <a href="?approve=<?= $row['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200">Approve</a>
                                    <a href="?reject=<?= $row['id'] ?>" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">Reject</a>
                                <?php else: ?>
                                    <span class="text-gray-500">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
