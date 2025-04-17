<?php
$conn = new mysqli("localhost", "root", "", "inventory_systems");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock = $_POST['stock'];
    $conn->query("UPDATE products SET stock=$stock WHERE id=$id");
    header("Location: dashboard.php");
    exit();
}

$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
?>

<form method="POST" style="margin: 40px;">
    <h2>Update Stock for <?= $product['name'] ?></h2>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
    <button type="submit">Update</button>
</form>
