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

$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? '';
    $purchase_date = $_POST['purchase_date'] ?? '';

    if ($name && $type && $brand && $quantity && $price && $status && $purchase_date) {
        $stmt = $conn->prepare("INSERT INTO machinery (name, type, brand, quantity, price, status, purchase_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssidsd", $name, $type, $brand, $quantity, $price, $status, $purchase_date);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Error inserting data: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Machinery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-success mb-4">➕ Add New Machinery</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">✅ Machinery added successfully! <a href="report.php">Back to Report</a></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger">❌ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Type</label>
            <input type="text" name="type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Brand</label>
            <input type="text" name="brand" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price (₱)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="Available">Available</option>
                <option value="In Use">In Use</option>
                <option value="Under Maintenance">Under Maintenance</option>
                <option value="Retired">Retired</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Purchase Date</label>
            <input type="date" name="purchase_date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">💾 Save Machinery</button>
        <a href="report.php" class="btn btn-secondary">↩️ Back to Report</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
