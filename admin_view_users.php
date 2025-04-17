<?php
session_start();
require 'db.php';

if ($_SESSION['role_id'] != 1) die("Access denied.");

$toast = '';

// Handle new user form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    if ($role_id == 1) die("Adding an administrator user is not allowed.");

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role_id]);

    $toast = 'User added successfully!';
}

if (isset($_POST['delete_user_id'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$_POST['delete_user_id']]);
    $toast = 'User deleted successfully!';
}

$users = $pdo->query("SELECT u.user_id, u.username, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id")->fetchAll();
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - User Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-300 min-h-screen font-sans">

<div class="container mx-auto p-6">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-4xl font-bold text-gray-800 tracking-tight">👥 User Management</h2>
    <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition-all duration-300">
      ➕ Add New User
    </button>
  </div>

  <!-- Search Bar -->
  <div class="mb-4">
    <input id="searchInput" type="text" placeholder="🔍 Search username or role..." class="w-full p-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
  </div>

  <!-- User Table -->
  <div class="overflow-x-auto bg-white rounded-xl shadow-xl border border-gray-200">
    <table class="min-w-full table-auto" id="userTable">
      <thead class="bg-green-600 text-white text-lg">
        <tr>
          <th class="py-3 px-4 text-left">Username</th>
          <th class="py-3 px-4 text-left">Role</th>
          <th class="py-3 px-4 text-left">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white text-gray-700 text-md" id="tableBody">
        <?php foreach ($users as $user): ?>
        <tr class="hover:bg-green-50 transition duration-200">
          <td class="py-3 px-4"><?= htmlspecialchars($user['username']) ?></td>
          <td class="py-3 px-4"><?= htmlspecialchars($user['role_name']) ?></td>
          <td class="py-3 px-4 flex space-x-4">
            <a href="edit_user.php?user_id=<?= $user['user_id'] ?>" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <input type="hidden" name="delete_user_id" value="<?= $user['user_id'] ?>">
              <button type="submit" onclick="return confirm('Delete this user?')" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="3" class="text-center py-4 text-gray-500">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex justify-center mt-6 gap-2" id="pagination"></div>

  <!-- Back Button -->
  <div class="mt-8 text-center">
    <a href="dashboard.php" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-full text-md font-bold transition">⬅ Back to Dashboard</a>
  </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black/50 flex justify-center items-center hidden z-50">
  <div class="bg-white bg-opacity-90 backdrop-blur-md rounded-xl shadow-2xl p-8 w-full max-w-md relative">
    <button onclick="closeModal()" class="absolute top-3 right-4 text-gray-600 text-xl hover:text-red-500">&times;</button>
    <h3 class="text-2xl font-bold mb-6 text-gray-800">Create New User</h3>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="add_user" value="1">
      <input type="text" name="username" required placeholder="Username" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input type="password" name="password" required placeholder="Password" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      <select name="role_id" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Select Role</option>
        <?php foreach ($roles as $role): ?>
          <?php if ($role['role_id'] != 1): ?>
            <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition-all">Create</button>
    </form>
  </div>
</div>

<!-- Toast Notification -->
<?php if (!empty($toast)): ?>
  <div id="toast" class="fixed bottom-5 right-5 flex items-center gap-3 bg-green-600 text-white px-5 py-3 rounded-lg shadow-md animate-fade-in z-50">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <span><?= $toast ?></span>
  </div>
  <script>
    setTimeout(() => { document.getElementById('toast').style.display = 'none'; }, 3000);
  </script>
<?php endif; ?>

<!-- JavaScript -->
<script>
  const modal = document.getElementById('addUserModal');
  function openModal() { modal.classList.remove('hidden'); }
  function closeModal() { modal.classList.add('hidden'); }

  document.getElementById('searchInput').addEventListener('input', function () {
    const search = this.value.toLowerCase();
    document.querySelectorAll('#tableBody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(search) ? '' : 'none';
    });
  });

  // Pagination
  const rowsPerPage = 8;
  const tbody = document.getElementById('tableBody');
  const rows = Array.from(tbody.getElementsByTagName('tr'));
  const pagination = document.getElementById('pagination');

  function showPage(page) {
    tbody.innerHTML = '';
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedItems = rows.slice(start, end);
    paginatedItems.forEach(row => tbody.appendChild(row));

    pagination.innerHTML = '';
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      btn.className = `px-4 py-1 rounded-lg ${i === page ? 'bg-green-600 text-white' : 'bg-white text-green-600 border border-green-600'} hover:bg-green-700 hover:text-white transition`;
      btn.onclick = () => showPage(i);
      pagination.appendChild(btn);
    }
  }

  if (rows.length > 0) showPage(1);
</script>

<!-- Styles -->
<style>
  @keyframes fade-in {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fade-in {
    animation: fade-in 0.5s ease-out;
  }
</style>

</body>
</html>
