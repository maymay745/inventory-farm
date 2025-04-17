<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "inventory_systems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle form submission for creating a maintenance schedule
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $machinery_name = $_POST["machinery_name"];
    $assigned_worker = $_POST["assigned_worker"];
    $maintenance_date = $_POST["maintenance_date"];
    $status = $_POST["status"];
    $notes = $_POST["notes"];

    $stmt = $conn->prepare("INSERT INTO maintenance_schedule (machinery_name, assigned_worker, maintenance_date, status, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $machinery_name, $assigned_worker, $maintenance_date, $status, $notes);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Maintenance Schedule Created Successfully');</script>";
}

// Handle deletion request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM maintenance_schedule WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Maintenance Schedule Deleted Successfully');</script>";
}

// Retrieve maintenance schedules
$result = $conn->query("SELECT * FROM maintenance_schedule ORDER BY maintenance_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Schedule</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Function to confirm deletion
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = url;
            }
        }
    </script>
</head>
<body class="bg-gradient-to-r from-green-500 to-blue-600 min-h-screen p-8 text-white">

    <div class="max-w-3xl mx-auto bg-white text-gray-800 p-8 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center">Add Maintenance Schedule</h1>
        <form action="" method="POST" class="space-y-4">
            <input type="text" name="machinery_name" placeholder="Machinery Name" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            <input type="text" name="assigned_worker" placeholder="Assigned Worker" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            <input type="date" name="maintenance_date" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            <select name="status" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            <textarea name="notes" placeholder="Notes (optional)" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">Create Schedule</button>
        </form>
    </div>

    <div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-lg text-gray-800">
        <h2 class="text-2xl font-bold mb-4">Maintenance Schedule List</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border text-left cursor-pointer" onclick="sortTable(0)">Machinery</th>
                        <th class="px-4 py-2 border text-left cursor-pointer" onclick="sortTable(1)">Assigned Worker</th>
                        <th class="px-4 py-2 border text-left cursor-pointer" onclick="sortTable(2)">Maintenance Date</th>
                        <th class="px-4 py-2 border text-left cursor-pointer" onclick="sortTable(3)">Status</th>
                        <th class="px-4 py-2 border text-left cursor-pointer" onclick="sortTable(4)">Notes</th>
                        <th class="px-4 py-2 border text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['machinery_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['assigned_worker']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['maintenance_date']) ?></td>
                            <td class="px-4 py-2 border">
                                <span class="px-2 py-1 rounded-full 
                                    <?= $row['status'] == 'Completed' ? 'bg-green-500 text-white' : ($row['status'] == 'In Progress' ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($row['notes']) ?></td>
                            <td class="px-4 py-2 border text-center">
                                <a href="javascript:void(0);" onclick="confirmDelete('?delete_id=<?= $row['id'] ?>')" class="text-red-600 hover:text-red-800">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($result->num_rows === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-4">No maintenance schedules found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sort function
        function sortTable(columnIndex) {
            const table = document.querySelector("table");
            const rows = Array.from(table.rows).slice(1); // Skip header row
            const isAscending = table.querySelectorAll("th")[columnIndex].classList.contains("asc");

            rows.sort((rowA, rowB) => {
                const cellA = rowA.cells[columnIndex].innerText.trim();
                const cellB = rowB.cells[columnIndex].innerText.trim();

                if (cellA < cellB) return isAscending ? -1 : 1;
                if (cellA > cellB) return isAscending ? 1 : -1;
                return 0;
            });

            rows.forEach(row => table.appendChild(row)); // Reorder the rows

            table.querySelectorAll("th").forEach((th, index) => {
                if (index === columnIndex) {
                    th.classList.toggle("asc", !isAscending);
                    th.classList.toggle("desc", isAscending);
                } else {
                    th.classList.remove("asc", "desc");
                }
            });
        }
    </script>

</body>
</html>

<?php $conn->close(); ?>
