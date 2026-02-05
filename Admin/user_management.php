<?php
session_start();

// Auth check
if (!isset($_SESSION['email'])) {
    $_SESSION['login_error'] = "❌ Please login first!";
    header("Location: ../index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
    <title>PIMS - User Management</title>
    <style>
        body {
            background: url('../pnp2.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }

        .container {
            display: grid;
            grid-template-columns: 250px auto;
            gap: 20px;
            min-height: 100vh;
        }

        aside {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            padding: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        aside .logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        aside .logo img {
            width: 50px;
           width: 2rem;
    height: 2rem;
        }

        aside .logo h2 {
            font-size: 1.3em;
            color: #fff;
        }

        aside .logo .danger {
            color: #ff5252;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        .sidebar span.material-icons-sharp {
            margin-right: 10px;
        }

        main {
            padding: 20px;
        }

        .recent-orders {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .recent-orders table {
            width: 100%;
            border-collapse: collapse;
            color: #fff;
        }

        .recent-orders table th,
        .recent-orders table td {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .btn {
            padding: 6px 10px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: #fff;
        }

        .btn-primary {
            background-color: rgba(33, 150, 243, 0.8);
        }

        .btn-danger {
            background-color: rgba(166, 14, 3, 0.8);
        }

        .btn-add {
            margin-top: 15px;
            background-color: rgba(76, 175, 80, 0.8);
            padding: 8px 15px;
            border-radius: 6px;
        }

        /* === Modal === */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            width: 40%;
            margin: 8% auto;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-content input,
        .modal-content select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border-radius: 8px;
            border: none;
            outline: none;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: rgba(76, 175, 80, 0.8);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn {
            float: right;
            font-size: 24px;
            cursor: pointer;
            color: #fff;
        }
    </style>
</head>

<body>
<div class="container">
    <!-- Sidebar -->
    <aside>
        <div class="logo">
            <img src="img/logo.jpg" alt="logo">
            <h2>PI<span class="danger">MS</span></h2>
        </div>

        <div class="sidebar">
            <a href="../Admin/admin_page.php"><span class="material-icons-sharp">dashboard</span><h3>Dashboard</h3></a>
            <a href="../Admin/user_management.php" class="active"><span class="material-icons-sharp">person_outline</span><h3>Users</h3></a>
            <a href="#"><span class="material-icons-sharp">receipt_long</span><h3>Complaints</h3></a>
            <a href="#"><span class="material-icons-sharp">insights</span><h3>Evidence</h3></a>
            <a href="#"><span class="material-icons-sharp">report_gmailerrorred</span><h3>Reports</h3></a>
            <a href="#"><span class="material-icons-sharp">settings</span><h3>Settings</h3></a>
            <a href="../logout.php"><span class="material-icons-sharp">logout</span><h3>Logout</h3></a>
        </div>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="recent-orders">
            <h2>Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Email</th><th>Password</th><th>Role</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "users");
                    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
                    $result = $conn->query("SELECT * FROM users");
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['password']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                            <button class="btn btn-primary editBtn"
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= htmlspecialchars($row['name']) ?>"
                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                data-password="<?= htmlspecialchars($row['password']) ?>"
                                data-role="<?= htmlspecialchars($row['role']) ?>">Edit</button>
                            <a class="btn btn-danger" href="../Admin/delete.php?id=<?= $row['id'] ?>"
                                onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button class="btn-add" id="openAddModal">➕ Add User</button>
        </div>
    </main>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeAdd">&times;</span>
        <h3>🧊 Add User</h3>
        <form action="../Admin/create.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeEdit">&times;</span>
        <h3>✏️ Edit User</h3>
        <!-- ✅ FIXED PATH -->
        <form action="../Admin/edit.php" method="POST">
            <input type="hidden" name="id" id="editId">
            <input type="text" name="name" id="editName" required>
            <input type="email" name="email" id="editEmail" required>
            <input type="password" name="password" id="editPassword" required>
            <select name="role" id="editRole" required>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

<script>
    // --- Add Modal ---
    const addModal = document.getElementById("addUserModal");
    document.getElementById("openAddModal").onclick = () => addModal.style.display = "block";
    document.getElementById("closeAdd").onclick = () => addModal.style.display = "none";

    // --- Edit Modal ---
    const editModal = document.getElementById("editUserModal");
    document.getElementById("closeEdit").onclick = () => editModal.style.display = "none";

    document.querySelectorAll(".editBtn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("editId").value = btn.dataset.id;
            document.getElementById("editName").value = btn.dataset.name;
            document.getElementById("editEmail").value = btn.dataset.email;
            document.getElementById("editPassword").value = btn.dataset.password;
            document.getElementById("editRole").value = btn.dataset.role;
            editModal.style.display = "block";
        });
    });

    // --- AJAX Edit Submit ---
    document.querySelector("#editUserModal form").addEventListener("submit", async (e) => {
        e.preventDefault(); // prevent normal submit

        const form = e.target;
        const formData = new FormData(form);

        try {
            const res = await fetch("../Admin/edit.php", { // ✅ make sure this file exists
                method: "POST",
                body: formData
            });
            const text = await res.text();

            if (text.includes("success")) {
                alert("✅ User updated successfully!");
                editModal.style.display = "none";
                location.reload(); // reload table
            } else {
                alert("⚠️ Failed to update user:\n" + text);
            }
        } catch (err) {
            alert("❌ Error updating user!");
            console.error(err);
        }
    });

    // --- Close when clicking outside ---
    window.onclick = e => {
        if (e.target === addModal) addModal.style.display = "none";
        if (e.target === editModal) editModal.style.display = "none";
    };
</script>

</body>
</html>
