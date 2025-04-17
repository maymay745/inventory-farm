<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = hash('sha256', $_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-cover bg-center min-h-screen" style="background-image: url('bg.jpeg');">

<div class="bg-black bg-opacity-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <!-- Logo & Title -->
        <div class="flex flex-col items-center mb-6">
            <img src="agri.jpg" alt="Logo" class="w-16 h-16 mb-2">
            <h2 class="text-2xl font-semibold text-gray-800">Farm Machinery Login</h2>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-600 px-4 py-2 mb-4 rounded text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post" class="space-y-5">
            <div>
                <label class="block text-gray-700 mb-1" for="username">Username</label>
                <input type="text" name="username" id="username" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-gray-700 mb-1" for="password">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition">
                Login
            </button>

            <p class="text-sm text-center mt-2">
                <a href="forgot_password_request.php" class="text-green-600 hover:underline">Forgot password?</a>
            </p>
        </form>

        <!-- Register Link -->
        <p class="text-sm text-gray-600 text-center mt-4">
            Don't have an account?
            <a href="register.php" class="text-green-600 hover:underline">Register here</a>
        </p>
    </div>
</div>

</body>
</html>
