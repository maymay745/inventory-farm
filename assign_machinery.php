<?php
$conn = new mysqli("localhost", "root", "", "inventory_systems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle deletion
if (isset($_POST["delete_id"])) {
    $delete_id = (int)$_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM assigned_machinery WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Assignment deleted successfully');</script>";
    echo "<script>window.location.href = window.location.href;</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["machinery_name"])) {
    $machinery_name = $_POST["machinery_name"];
    $assigned_to = $_POST["assigned_to"];
    $assignment_date = $_POST["assignment_date"];
    $notes = $_POST["notes"];

    $stmt = $conn->prepare("INSERT INTO assigned_machinery (machinery_name, assigned_to, assignment_date, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $machinery_name, $assigned_to, $assignment_date, $notes);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Machinery Assigned Successfully');</script>";
    echo "<script>window.location.href = window.location.href;</script>";
    exit;
}

// Fetch all assignments
$result = $conn->query("SELECT * FROM assigned_machinery ORDER BY assignment_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Machinery</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-600 to-green-500 min-h-screen p-8 text-white">

    <div class="max-w-3xl mx-auto bg-white text-gray-800 p-8 rounded-xl shadow-lg mb-8">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Assign Machinery</h1>
        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="machinery_name" class="block text-lg font-medium">Machinery Name</label>
                <input type="text" id="machinery_name" name="machinery_name" placeholder="Enter machinery name" required class="w-full p-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="assigned_to" class="block text-lg font-medium">Assigned To</label>
                <input type="text" id="assigned_to" name="assigned_to" placeholder="Assigned to (Employee or Customer)" required class="w-full p-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="assignment_date" class="block text-lg font-medium">Assignment Date</label>
                <input type="date" id="assignment_date" name="assignment_date" required class="w-full p-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="notes" class="block text-lg font-medium">Notes</label>
                <textarea id="notes" name="notes" placeholder="Additional notes" class="w-full p-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">Assign Machinery</button>
        </form>
    </div>

    <div class="max-w-6xl mx-auto bg-white p-8 rounded-xl shadow-lg text-gray-800">
        <h2 class="text-2xl font-bold mb-4">Assignment History</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border text-left">Machinery</th>
                        <th class="px-4 py-2 border text-left">Assigned To</th>
                        <th class="px-4 py-2 border text-left">Date</th>
                        <th class="px-4 py-2 border text-left">Notes</th>
                        <th class="px-4 py-2 border text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['machinery_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['assigned_to']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['assignment_date']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['notes']) ?></td>
                            <td class="px-4 py-2 border text-center">
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">No assignments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

<?php $conn->close(); ?>
