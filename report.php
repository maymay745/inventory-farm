<?php
session_start();

// Dummy session setup (replace with real login logic in production)
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "admin";
}

// Database connection
$host = "localhost";
$db = "inventory_systems";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper for status badge color
function getStatusColor($status) {
    return match ($status) {
        'Available' => 'success',
        'In Use' => 'primary',
        'Under Maintenance' => 'warning',
        'Retired' => 'secondary',
        default => 'dark',
    };
}

// Undo soft delete
if (isset($_GET['undo'])) {
    $undoId = (int)$_GET['undo'];
    $conn->query("UPDATE machinery SET deleted = 0 WHERE id = $undoId");
    header("Location: report.php?restored=1");
    exit();
}

// Filters
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$deleted_filter = "AND deleted = 0";

// Query with filters
$sql = "SELECT * FROM machinery WHERE 1=1 $deleted_filter";
if (!empty($type_filter)) {
    $sql .= " AND type = '" . $conn->real_escape_string($type_filter) . "'";
}
if (!empty($status_filter)) {
    $sql .= " AND status = '" . $conn->real_escape_string($status_filter) . "'";
}
$result = $conn->query($sql);

// Prepare data for display and computation
$machinery_data = [];
$total_items = 0;
$total_value = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $machinery_data[] = $row;
        $total_items++;
        $total_value += (float)$row['quantity'] * (float)$row['price'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 30px; background: #f8f9fa; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 1050; }
    </style>
</head>
<body>

<div class="container">
    <!-- Account Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">🚜 Farm Machinery Report</h2>
        <div class="text-end">
            <p class="mb-0">👤 Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
            <a href="logout.php" class="btn btn-sm btn-outline-secondary mt-1">Logout</a>
        </div>
    </div>

    <!-- Toasts -->
    <div class="toast-container">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="toast show align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">🗑️ Item deleted. <a href="report.php?undo=<?= $_GET['deleted'] ?>" class="text-white text-decoration-underline">Undo?</a></div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php elseif (isset($_GET['restored'])): ?>
            <div class="toast show align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">✅ Item restored successfully!</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <form method="GET" class="row gy-2 gx-3 mb-3">
        <div class="col-auto">
            <input type="text" class="form-control" name="type" placeholder="Type" value="<?= htmlspecialchars($type_filter) ?>">
        </div>
        <div class="col-auto">
            <select class="form-select" name="status">
                <option value="">-- Status --</option>
                <?php foreach (['Available', 'In Use', 'Under Maintenance', 'Retired'] as $status): ?>
                    <option value="<?= $status ?>" <?= $status_filter === $status ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
            <a href="report.php" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <!-- Summary -->
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>📊 <strong><?= $total_items ?></strong> Total Items</div>
        <div>💰 Total Estimated Value: <strong>₱<?= number_format($total_value ?? 0, 2) ?></strong></div>
    </div>

    <!-- Table -->
    <?php if (!empty($machinery_data)): ?>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th><th>Name</th><th>Type</th><th>Brand</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Status</th><th>Purchased</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($machinery_data as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['brand']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>₱<?= number_format((float)$row['price'], 2) ?></td>
                            <td>₱<?= number_format((float)$row['quantity'] * (float)$row['price'], 2) ?></td>
                            <td><span class="badge bg-<?= getStatusColor($row['status']) ?>"><?= $row['status'] ?></span></td>
                            <td><?= date("F j, Y", strtotime($row['purchase_date'])) ?></td>
                            <td>
                                <a href="edit_machinery.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                <a href="delete_machinery.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4">No machinery found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
