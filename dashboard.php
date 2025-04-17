<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$role_id = $_SESSION['role_id'];
$username = $_SESSION['username'];

// Connect to the database
require 'db.php';

// Count total users
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$user_count = $stmt->fetchColumn();

// Count machinery reports
$stmt = $pdo->prepare("SELECT COUNT(*) FROM machinery");
$stmt->execute();
$report_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false, sidebarOpen: true }" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Farm Machinery Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-all duration-300">

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="h-screen bg-green-800 text-white fixed flex flex-col transition-all duration-300">
    <div class="flex items-center justify-between p-4 border-b border-green-700">
        <h2 x-show="sidebarOpen" class="text-xl font-bold whitespace-nowrap">🌾 Dashboard</h2>
        <button @click="sidebarOpen = !sidebarOpen">
            <i data-feather="menu" class="w-6 h-6"></i>
        </button>
    </div>
    <p x-show="sidebarOpen" class="text-sm text-green-200 text-center mt-1">Hi, <?= htmlspecialchars($username) ?></p>
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        <?php
        function menuItem($href, $label, $icon) {
            $activeClass = ($_SERVER['PHP_SELF'] === '/' . $href || basename($_SERVER['PHP_SELF']) === $href) ? 'bg-green-700' : '';
            return "
            <a href='{$href}' class='flex items-center space-x-3 py-2 px-3 rounded-md hover:bg-green-700 {$activeClass} transition-all duration-200'>
                <i data-feather='{$icon}' class='w-5'></i>
                <span x-show='sidebarOpen'>{$label}</span>
            </a>";
        }

        switch ($role_id) {
            case 1:
                echo menuItem('admin_view_users.php', 'Manage Users', 'users');
                echo menuItem('inventory.php', 'Manage Inventory', 'box');
                echo menuItem('assign_machinery.php', 'Assign Machinery', 'tool');
                echo menuItem('maintenance_schedule.php', 'Maintenance', 'calendar');
                echo menuItem('report.php', 'Reports', 'bar-chart-2');
                echo menuItem('settings.php', 'Settings', 'settings');
                break;
            case 2:
                echo menuItem('inventory.php', 'Check Inventory', 'search');
                echo menuItem('create_sales.php', 'Create Sale', 'file-plus');
                echo menuItem('sales_history.php', 'Sales History', 'clock');
                break;
            case 3:
                echo menuItem('inventory.php', 'Manage Inventory', 'settings');
                echo menuItem('stock_level.php', 'Stock Levels', 'trending-up');
                echo menuItem('add_machinery.php', 'Add Machinery', 'plus-circle');
                echo menuItem('track_usage.php', 'Track Usage', 'activity');
                echo menuItem('low_stock_alerts.php', 'Low Stock Alerts', 'alert-triangle');
                echo menuItem('log_maintenance.php', 'Maintenance Log', 'clipboard');
                break;
        }
        ?>
    </nav>
    <div class="p-4 border-t border-green-700 flex justify-between items-center">
        <button @click="darkMode = !darkMode" class="text-sm flex items-center gap-2">
            <i data-feather="moon" class="w-4 h-4"></i>
            <span x-show="sidebarOpen">Dark Mode</span>
        </button>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-md text-sm">Logout</a>
    </div>
</aside>

<!-- Main Content -->
<main class="transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-16'">
    <div class="p-8">
        <h2 class="text-3xl font-semibold mb-6">Dashboard</h2>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <?php if ($role_id == 1): // Admin ?>
                <!-- Admin: Total Users -->
                <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-red-200 dark:bg-red-700 text-red-600 dark:text-red-300 p-3 rounded-full">
                            <i data-feather="users" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Total Users</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm"><?= $user_count ?> Users</p>
                        </div>
                    </div>
                </div>

                <!-- Admin: Machinery Reports -->
                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-yellow-200 dark:bg-yellow-700 text-yellow-600 dark:text-yellow-300 p-3 rounded-full">
                            <i data-feather="file-text" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Machinery Reports</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm"><?= $report_count ?> Reports</p>
                        </div>
                    </div>
                </div>

                <!-- Admin: System Management -->
                <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-200 dark:bg-green-700 text-green-600 dark:text-green-300 p-3 rounded-full">
                            <i data-feather="settings" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">System Management</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">Admins can manage all records</p>
                        </div>
                    </div>
                </div>

            <?php elseif ($role_id == 2): // Sales ?>
                <!-- Sales: Check Inventory -->
                <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-200 dark:bg-green-700 text-green-600 dark:text-green-300 p-3 rounded-full">
                            <i data-feather="search" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Check Inventory</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">Real-time stock lookup</p>
                        </div>
                    </div>
                </div>

                <!-- Sales: Create Sale -->
                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-yellow-200 dark:bg-yellow-700 text-yellow-600 dark:text-yellow-300 p-3 rounded-full">
                            <i data-feather="file-plus" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Create Sale</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">Log new machinery sales</p>
                        </div>
                    </div>
                </div>

                <!-- Sales: Sales History -->
                <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-red-200 dark:bg-red-700 text-red-600 dark:text-red-300 p-3 rounded-full">
                            <i data-feather="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Sales History</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">Review past sales records</p>
                        </div>
                    </div>
                </div>

            <?php elseif ($role_id == 3): // Inventory ?>
                <!-- Inventory: Manage Stock -->
                <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-200 dark:bg-green-700 text-green-600 dark:text-green-300 p-3 rounded-full">
                            <i data-feather="layers" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Stock Level</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">Monitor stock levels</p>
                        </div>
                    </div>
                </div>

                <!-- Inventory: Low Stock Alerts -->
                <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-red-200 dark:bg-red-700 text-red-600 dark:text-red-300 p-3 rounded-full">
                            <i data-feather="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Low Stock Alerts</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">Stay ahead of shortages</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 shadow-lg rounded-lg p-6 transition hover:shadow-xl">
                    <div class="flex items-center space-x-4">
                        <div class="bg-yellow-200 dark:bg-yellow-700 text-yellow-600 dark:text-yellow-300 p-3 rounded-full">
                            <i data-feather="clipboard" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Maintenance Log</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm">View scheduled maintenance</p>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    feather.replace();
</script>
</body>
</html>
