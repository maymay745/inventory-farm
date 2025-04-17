<?php
$conn = new mysqli("localhost", "root", "", "inventory_systems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch settings
$result = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'];
    $admin_email = $_POST['admin_email'];
    $theme_color = $_POST['theme_color'];
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE settings SET site_name=?, admin_email=?, theme_color=?, maintenance_mode=? WHERE id=?");
    $stmt->bind_param("sssii", $site_name, $admin_email, $theme_color, $maintenance_mode, $settings['id']);
    
    if ($stmt->execute()) {
        echo "<script>alert('Settings updated successfully!'); window.location.href='settings.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to update settings.');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-600 to-purple-500 min-h-screen text-white p-10">

    <div class="max-w-2xl mx-auto bg-white text-gray-800 p-8 rounded-xl shadow-xl">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-600">⚙️ System Settings</h1>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Site Name</label>
                <input type="text" name="site_name" required value="<?= htmlspecialchars($settings['site_name']) ?>"
                       class="w-full p-3 border rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Admin Email</label>
                <input type="email" name="admin_email" required value="<?= htmlspecialchars($settings['admin_email']) ?>"
                       class="w-full p-3 border rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Theme Color</label>
                <select name="theme_color" class="w-full p-3 border rounded-lg">
                    <?php foreach (['blue', 'green', 'red', 'purple', 'gray'] as $color): ?>
                        <option value="<?= $color ?>" <?= $settings['theme_color'] == $color ? 'selected' : '' ?>>
                            <?= ucfirst($color) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center space-x-3">
                <input type="checkbox" name="maintenance_mode" value="1" <?= $settings['maintenance_mode'] ? 'checked' : '' ?>>
                <label>Enable Maintenance Mode</label>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition">
                💾 Save Settings
            </button>
        </form>
    </div>

</body>
</html>

<?php $conn->close(); ?>
