<?php
session_start();
include '../db.php';

// Kiểm tra xem người dùng có đăng nhập và có vai trò admin không
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Xử lý thêm người dùng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $full_name = filter_var($_POST['full_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

    // Kiểm tra các trường bắt buộc
    if (empty($username) || empty($email) || empty($password) || !in_array($role, ['customer', 'admin'])) {
        $error = "Please fill the missing information!";
    } else {
        // Kiểm tra username hoặc email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Username or Email is exist!";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO users (username, full_name, email, address, phone, password, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssss", $username, $full_name, $email, $address, $phone, $password, $role);
            if ($stmt->execute()) {
                $success = "Success!";
                header("Location: admin_user.php"); 
                exit();
            } else {
                $error = "Have some troble when add user!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add user</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../fe/style.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            /* font-weight: 700; */
            padding-left: 10px;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
            max-width: 700px;
        }
        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 500;
            color: #34495e;
        }
        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #2ecc71;
            border-color: #2ecc71;
        }
        .btn-secondary {
            background-color: #95a5a6;
            border-color: #95a5a6;
        }
        .btn i {
            margin-right: 5px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .navbar-brand{
            font-weight: 700;
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- <a class="navbar-brand" href="#">ZAUN</a> -->
            <a class="navbar-brand " href="../home_store/home.php">ZAUN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_product.php">Product Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_user.php">User Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="../home_store/home.php">Visit Website</a></li>
                    <li class="nav-item"><a class="nav-link" href="../backend/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container">
        <h2><i class="fas fa-user-plus me-2"></i>ADD NEW USER</h2>

        <!-- Hiển thị thông báo -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Form thêm người dùng -->
        <form method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">USERNAME <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="username" name="username" required>
                <div class="invalid-feedback">Please enter username!</div>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">FULLNAME</label>
                <input type="text" class="form-control" id="full_name" name="full_name">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">EMAIL <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Please valid email!</div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">ADDRESS</label>
                <input type="text" class="form-control" id="address" name="address">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">PHONE</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">PASSWORD<span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter password!</div>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">ROLE <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">Select value</option>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
                <div class="invalid-feedback">Select role for this accout</div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary btn-custom"><i class="fas fa-save"></i>Save</button>
                <a href="admin_user.php" class="btn btn-secondary btn-custom"><i class="fas fa-arrow-left"></i>Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xác thực form Bootstrap
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>