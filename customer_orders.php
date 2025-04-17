<?php
// DB Connection
$conn = new mysqli("localhost", "root", "", "inventory_systems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = $_POST['customer_name'];
    $name = $_POST['machinery_name'];
    $type = $_POST['machinery_type'];
    $quantity = $_POST['quantity'];
    $imageName = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO customer_orders (customer_name, machinery_name, machinery_type, quantity, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $customer, $name, $type, $quantity, $imageName);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Order placed successfully!');</script>";
}

// Fetch orders
$orders = $conn->query("SELECT * FROM customer_orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-green-400 via-green-500 to-green-600 min-h-screen p-8 text-white">

    <div class="max-w-4xl mx-auto bg-white text-gray-800 p-8 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold mb-6 text-center">Place a Machinery Order</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="customer_name" placeholder="Customer Name" required class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="text" name="machinery_name" placeholder="Machinery Name (e.g. Tractor)" required class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="text" name="machinery_type" placeholder="Type (e.g. Sprayer, Harvester)" class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="number" name="quantity" placeholder="Quantity" required class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="file" name="image" accept="image/*" class="w-full">
            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">Submit Order</button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="max-w-6xl mx-auto mt-12 bg-white p-6 rounded-lg shadow-lg text-gray-800">
        <h2 class="text-2xl font-bold mb-4">Customer Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Customer</th>
                        <th class="px-4 py-2 border">Machinery</th>
                        <th class="px-4 py-2 border">Type</th>
                        <th class="px-4 py-2 border">Qty</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Image</th>
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
                                <?php if ($row['image']): ?>
                                    <img src="<?= $row['image'] ?>" alt="Machinery" class="w-20 h-16 object-cover rounded">
                                <?php else: ?>
                                    <span class="text-gray-400">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <?php if ($row['status'] == 0): ?>
                                    <span class="text-red-500">Pending</span>
                                <?php else: ?>
                                    <span class="text-green-500">Approved</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <?php if ($row['status'] == 0): ?>
                                    <a href="admin_manage_orders.php?approve=<?= $row['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200">Approve</a>
                                    <a href="admin_manage_orders.php?reject=<?= $row['id'] ?>" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">Reject</a>
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
