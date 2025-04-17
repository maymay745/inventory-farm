<?php
require 'db.php'; // Ensure this connects with PDO as $pdo

$step = 1;
$error = '';
$success = '';
$user = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        // Step 1: Get user and question
        $username = trim($_POST['username']);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && !empty($user['security_question'])) {
            $step = 2;
        } else {
            $error = "❌ Username not found or no question set.";
        }

    } elseif (isset($_POST['username_hidden'], $_POST['answer'], $_POST['new_password'])) {
        // Step 2: Check answer, reset password
        $username = $_POST['username_hidden'];
        $answer = hash('sha256', trim($_POST['answer']));
        $new_password = hash('sha256', trim($_POST['new_password']));

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND security_answer = ?");
        $stmt->execute([$username, $answer]);
        $user = $stmt->fetch();

        if ($user) {
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $update->execute([$new_password, $username]);

            $success = "✅ Password reset. <a href='login.php' class='text-green-600 underline'>Login now</a>";
            $step = 3;
        } else {
            $error = "❌ Incorrect security answer.";
            $step = 2;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-green-700">Forgot Password</h2>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm text-center">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if ($step === 1): ?>
        <form method="POST" class="space-y-4">
            <label class="block text-gray-700 font-medium">Enter Username:</label>
            <input type="text" name="username" required
                   class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">
            <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Next</button>
        </form>

    <?php elseif ($step === 2): ?>
        <form method="POST" class="space-y-4">
            <p class="text-gray-700 font-medium">Security Question:</p>
            <p class="italic bg-green-50 text-green-800 px-3 py-2 rounded">
                <?= htmlspecialchars($user['security_question']) ?>
            </p>

            <input type="hidden" name="username_hidden" value="<?= htmlspecialchars($user['username']) ?>">

            <label class="block text-gray-700 font-medium">Answer:</label>
            <input type="text" name="answer" required
                   class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">

            <label class="block text-gray-700 font-medium">New Password:</label>
            <input type="password" name="new_password" required
                   class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500">

            <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
