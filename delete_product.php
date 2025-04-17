<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_systems";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if an ID was passed
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM inventory WHERE product_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to inventory list with success message
        header("Location: inventory_list.php?message=deleted");
        exit();
    } else {
        echo "❌ Error deleting product: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "❌ Invalid product ID.";
}

$conn->close();
?>
