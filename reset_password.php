<?php
require 'db.php'; // Ensure PDO is connected

// Check if the token is valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    // Check if token is valid and not expired
    if ($user && $user['reset_token_expires'] > time()) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = password_hash(trim($_POST['new_password']), PASSWORD_BCRYPT);

            // Update the password
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?");
            $stmt->execute([$new_password, $token]);

            $message = "Your password has been successfully reset. You can now <a href='login.php'>login</a>.";
        }
    } else {
        $message = "Invalid or expired token.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-green-700">Reset Your Password</h2>

    <?php if (isset($message)): ?>
        <div class="bg-blue-100 text-blue-700 p-3 rounded mb-4 text-sm">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <label class="block text-gray-700 font-medium">New Password:</label>
        <input type="password" name="new_password" required
               class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Reset Password</button>
    </form>
</div>
</body>
</html>
