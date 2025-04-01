<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    header("Location: admin_user.php");
    exit();
}

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: admin_user.php");
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, phone = ?, address = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("ssssssi", $username, $full_name, $email, $phone, $address, $role, $user_id);
    
    if ($stmt->execute()) {
        header("Location: admin_user.php");
        exit();
    } else {
        $error = "Lỗi khi cập nhật người dùng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
    <style>
        .navbar-brand {
            font-weight: 700;
            padding-left: 10px;
        }
        .container {
            border: 1px solid ghostwhite;
            padding: 60px;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.5);
        }
        .nav{
            text-decoration: none;
            color: grey;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand " href="../home_store/home.php">ZAUN</a>
        <a class="nav" href="admin_user.php">User Management </a>
        <a class="nav" href="">/ Edit User</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
            <li class="nav-item"><a class="nav-link" href="../home_store/home.php">Visit Website</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_product.php">Product Management</a></li>
        </ul>
    </nav>

    <div class="container mt-4">
        <h2>EDIT ACCOUT</h2>
        <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label" >USERNAME</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">FULLNAME</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">EMAIL</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">PHONE</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ADDRESS</label>
                <textarea class="form-control" id="address" name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">ROLE</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Change</button>
            <a href="admin_user.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>