<?php
session_start();

// Ensure the user has the correct role to access this page (Sales Staff role, role_id = 2)
if ($_SESSION['role_id'] != 2) {
    header("Location: dashboard.php");
    exit();
}

// Sales success message
$sale_success_message = "Sale successfully created! Your sale has been recorded, and inventory has been updated.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Success - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<!-- Main Content -->
<div class="container mx-auto p-8">
    <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Sale Success</h2>

    <!-- Success Message -->
    <div class="bg-green-600 text-white p-6 rounded-lg shadow-lg mb-6">
        <p class="text-xl"><?= htmlspecialchars($sale_success_message) ?></p>
    </div>

    <!-- Options -->
    <div class="flex justify-center gap-6 mt-6">
        <a href="create_sales.php" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-300">Create Another Sale</a>
        <a href="dashboard.php" class="bg-gray-700 text-white px-6 py-3 rounded-md hover:bg-gray-800 transition duration-300">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
