<?php
session_start();
include '../db.php';

// Kiểm tra xem người dùng có đăng nhập và có vai trò admin không
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../home_store/home.php");
    exit();
}

// Lấy danh sách người dùng từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header-container h2{
            font-size: 40px;
            font-weight: 800;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
        }
        table{
            border: 1px solid;
            margin-top: 20px;
        }
        .admin-btn{
            text-decoration: none;
            height: fit-content;
            border-radius: 5px;
            color: aliceblue;
            padding: 10px;
            text-align: center;
            margin: 2px;
        }
        .add-btn {
            display: inline-block;
            background-color: cadetblue;
        }
        .edit-btn {
            width: 70px;
            display: inline-block;
            background-color: burlywood;
        }
        .del-btn {
            width: 70px;
            display: inline-block;
            background-color: red;
        }
        .container {
            border: 1px solid ghostwhite;
            padding: 60px;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.5);
        }
        .navbar-brand {
            font-weight: 700;
            padding-left: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand " href="../home_store/home.php">ZAUN</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
            <li class="nav-item"><a class="nav-link" href="../home_store/home.php">Visit Web Site</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_product.php">Product Management</a></li>
        </ul>
    </nav>

    <div class="container mt-4">
        <div class="header-container">
            <h2 style="color:  #e390fc;">USER MANAGEMENT</h2>

            <a href="add_user.php" class="add-btn admin-btn"><i class="fas fa-user-plus"></i>Add new user</a>
        </div>
        <div class="table-user">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>USERNAME</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>ADDRESS</th>
                        <th>PHONE</th>
                        <th>ROLE</th>
                        <th>CREATE DAY</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $row['user_id']; ?>" class="edit-btn admin-btn">Edit</a>
                                <a href="delete_user.php?id=<?php echo $row['user_id']; ?>" class="del-btn admin-btn" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>