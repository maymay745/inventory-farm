<?php
require 'db.php';

$stmt = $pdo->prepare("SELECT * FROM roles WHERE role_name IN ('Sales Staff', 'Inventory Manager')");
$stmt->execute();
$roles = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    $role_id  = $_POST['role'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->rowCount() > 0) {
            $error = "Username already exists.";
        } else {
            $hashed = hash('sha256', $password);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed, $role_id]);
            $success = "Registration successful!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex justify-center items-center h-screen">

    <div class="w-full max-w-sm bg-white p-8 rounded-lg shadow-lg">
        <div class="flex justify-center mb-6">
            <img src="agri.jpg" alt="Logo" class="w-24 h-24"> 
        </div>

        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Register</h2>

        <form method="post">
            <!-- Display errors or success messages -->
            <?php if (!empty($error)) echo "<p class='text-red-500 text-sm mb-4'>$error</p>"; ?>
            <?php if (!empty($success)) echo "<p class='text-green-500 text-sm mb-4'>$success</p>"; ?>

            <div class="mb-4">
                <input type="text" name="username" placeholder="Username" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>

            <div class="mb-4">
                <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>

            <div class="mb-4">
                <input type="password" name="confirm" placeholder="Confirm Password" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
            </div>

            <div class="mb-4">
                <select name="role" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200">Register</button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">Already have an account? <a href="login.php" class="text-green-600 hover:underline">Login here</a></p>
    </div>

</body>
</html>
