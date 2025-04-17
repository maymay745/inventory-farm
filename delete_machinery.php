<?php
$host = "localhost";
$db = "inventory_systems";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    $stmt = $conn->prepare("UPDATE machinery SET deleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: report.php?deleted=$id");
    exit();
} else {
    echo "Invalid ID.";
}

$conn->close();
?>
