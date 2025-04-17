<?php
// DATABASE CONNECTION
$host = "localhost";
$db = "inventory_systems";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid ID.");
}

$message = "";

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM machinery WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$machinery = $result->fetch_assoc();
$stmt->close();

if (!$machinery) {
    die("Machinery not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $type = trim($_POST["type"]);
    $brand = trim($_POST["brand"]);
    $quantity = (int) $_POST["quantity"];
    $price = (float) $_POST["price"];
    $status = $_POST["status"];
    $purchase_date = $_POST["purchase_date"];

    $update = $conn->prepare("UPDATE machinery SET name=?, type=?, brand=?, quantity=?, price=?, status=?, purchase_date=? WHERE id=?");
    $update->bind_param("sssidssi", $name, $type, $brand, $quantity, $price, $status, $purchase_date, $id);

    if ($update->execute()) {
        $message = "✅ Machinery updated successfully.";
        header("Location: report.php");
        exit();
    } else {
        $message = "❌ Update failed.";
    }

    $update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Machinery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">✏️ Edit Machinery</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($machinery['name']) ?>">
        </div>
        <div class="mb-3">
            <label>Type</label>
            <input type="text" name="type" class="form-control" required value="<?= htmlspecialchars($machinery['type']) ?>">
        </div>
        <div class="mb-3">
            <label>Brand</label>
            <input type="text" name="brand" class="form-control" required value="<?= htmlspecialchars($machinery['brand']) ?>">
        </div>
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" required min="1" value="<?= htmlspecialchars($machinery['quantity']) ?>">
        </div>
        <div class="mb-3">
            <label>Price (₱)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?= htmlspecialchars($machinery['price']) ?>">
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <?php
                $statuses = ['Available', 'In Use', 'Under Maintenance', 'Retired'];
                foreach ($statuses as $status):
                    $selected = $machinery['status'] === $status ? 'selected' : '';
                    echo "<option value=\"$status\" $selected>$status</option>";
                endforeach;
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Purchase Date</label>
            <input type="date" name="purchase_date" class="form-control" required value="<?= htmlspecialchars($machinery['purchase_date']) ?>">
        </div>
        <button type="submit" class="btn btn-success">💾 Save Changes</button>
        <a href="report.php" class="btn btn-secondary">🔙 Back</a>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
