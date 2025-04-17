<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "inventory_systems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle form submission for logging usage
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['log_usage'])) {
    $machinery_name = $_POST["machinery_name"];
    $worker_name = $_POST["worker_name"];
    $usage_date = $_POST["usage_date"];
    $hours_used = $_POST["hours_used"];
    $notes = $_POST["notes"];

    // Insert usage log into the database
    $stmt = $conn->prepare("INSERT INTO machinery_usage (machinery_name, worker_name, usage_date, hours_used, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $machinery_name, $worker_name, $usage_date, $hours_used, $notes);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Usage Logged Successfully');</script>";
}

// Handle form submission for deleting usage
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_usage'])) {
    $usage_id = $_POST['usage_id']; // Get the ID of the usage log to delete

    // Delete usage log from the database
    $stmt = $conn->prepare("DELETE FROM machinery_usage WHERE id = ?");
    $stmt->bind_param("i", $usage_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Usage Log Deleted Successfully');</script>";
}

// Retrieve usage logs
$result = $conn->query("SELECT * FROM machinery_usage ORDER BY usage_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Machinery Usage</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS CDN -->
</head>
<body class="bg-gradient-to-r from-green-500 to-blue-600 min-h-screen p-8 text-white">

    <!-- Machinery Usage Form Section -->
    <div class="max-w-3xl mx-auto bg-white text-gray-800 p-8 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center">Log Machinery Usage</h1>
        <form action="" method="POST" class="space-y-4">
            <input type="text" name="machinery_name" placeholder="Machinery Name" required class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="text" name="worker_name" placeholder="Worker Name" required class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="date" name="usage_date" required class="w-full p-3 border border-gray-300 rounded-lg">
            <input type="number" name="hours_used" placeholder="Hours Used" required step="0.01" class="w-full p-3 border border-gray-300 rounded-lg">
            <textarea name="notes" placeholder="Notes (optional)" class="w-full p-3 border border-gray-300 rounded-lg"></textarea>
            <button type="submit" name="log_usage" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">Log Usage</button>
        </form>
    </div>

    <!-- Machinery Usage List Section -->
    <div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-lg text-gray-800">
        <h2 class="text-2xl font-bold mb-4">Machinery Usage Log</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Machinery</th>
                        <th class="px-4 py-2 border">Worker</th>
                        <th class="px-4 py-2 border">Usage Date</th>
                        <th class="px-4 py-2 border">Hours Used</th>
                        <th class="px-4 py-2 border">Notes</th>
                        <th class="px-4 py-2 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['machinery_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['worker_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['usage_date']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['hours_used']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['notes']) ?></td>
                            <td class="px-4 py-2 border text-center">
                                <form action="" method="POST" class="inline">
                                    <input type="hidden" name="usage_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete_usage" class="bg-red-600 text-white py-1 px-4 rounded-lg hover:bg-red-700 transition">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4">No usage logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
