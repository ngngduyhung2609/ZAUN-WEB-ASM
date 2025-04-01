<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Xử lý thêm/sửa sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
    $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT) ?: NULL;

    if ($price === false || $stock === false) {
        $error = "Price is not valid!";
    } else {
        $image = '';
        if ($_FILES['image']['name']) {
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], "../img/$image");
        }

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category_id, $image);
        $stmt->execute();
    }
}

// Xử lý sửa sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
    $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT) ?: NULL;

    if ($product_id === false || $price === false || $stock === false) {
        $error = "Information is not valid!";
    } else {
        $image = $_POST['current_image']; // Giữ ảnh cũ nếu không upload ảnh mới
        if ($_FILES['image']['name']) {
            $image = basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], "../img/$image");
        }

        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE product_id = ?");
        $stmt->bind_param("ssdissi", $name, $description, $price, $stock, $category_id, $image, $product_id);
        $stmt->execute();
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete_product'])) {
    $product_id = filter_var($_GET['delete_product'], FILTER_VALIDATE_INT);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

// Xử lý thêm/sửa danh mục
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = filter_var($_POST['category_name'], FILTER_SANITIZE_STRING);
    $category_description = filter_var($_POST['category_description'], FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_name, $category_description);
    $stmt->execute();
}

// Xử lý xóa danh mục
if (isset($_GET['delete_category'])) {
    $category_id = filter_var($_GET['delete_category'], FILTER_VALIDATE_INT);
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
}

// Lấy danh sách sản phẩm
$products = $conn->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id");

// Lấy danh sách danh mục
$categories = $conn->query("SELECT * FROM categories");

// Lấy thông tin sản phẩm để sửa (nếu có)
$edit_product = null;
if (isset($_GET['edit_product'])) {
    $edit_product_id = filter_var($_GET['edit_product'], FILTER_VALIDATE_INT);
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $edit_product_id);
    $stmt->execute();
    $edit_product = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Product & Category Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding-left: 10px;
        }
        .navbar-brand{
            font-weight: 700;
        }
        .formcontain {
            display: flex   ;
            justify-content: space-between;
        }
        form{
            border: 1px solid;
            padding: 60px 100px;
            height: fit-content;
            box-shadow: 5px 5px 10px rgba(255, 170, 252, 0.5);
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
    <div class="container mt-4">
        <h1>Product Manager</h1>

        <!-- Form thêm/sửa sản phẩm -->
         <div class="formcontain">
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <h3><?php echo $edit_product ? 'Edit Product' : 'Add Product' ?></h3>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($edit_product): ?>
                <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo $edit_product['image']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="name">Name Product</label>
                <input type="text" class="form-control" name="name" value="<?php echo $edit_product['name'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Discription</label>
                <textarea class="form-control" name="description"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" name="price" step="0.01" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" name="stock" value="<?php echo $edit_product['stock'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select class="form-control" name="category_id">
                    <option value="">No category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; $categories->data_seek(0); ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">IMAGE</label>
                <input type="file" class="form-control-file" name="image">
                <?php if ($edit_product && $edit_product['image']): ?>
                    <p>Current Image: <img src="../img/<?php echo htmlspecialchars($edit_product['image']); ?>" width="150"></p>
                <?php endif; ?>
            </div>
            <button type="submit" name="<?php echo $edit_product ? 'edit_product' : 'add_product'; ?>" class="btn btn-primary">
                <?php echo $edit_product ? 'Edit Product' : 'Add Product'; ?>
            </button>
            <?php if ($edit_product): ?>
                <a href="admin_product.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
        <!-- <h1>Category Manager</h1> -->
        <form method="POST" class="mb-4">
            <h3>Add Category</h3>
            <div class="form-group">
                <label for="category_name">Name Category</label>
                <input type="text" class="form-control" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="category_description">Descriptom</label>
                <textarea class="form-control" name="category_description"></textarea>
            </div>
            <button type="submit" name="add_category" class="btn btn-primary">Add</button>
        </form>
        </div>
        <!-- Danh sách sản phẩm -->
        <h3>Product List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Act</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['product_id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo number_format($product['price'], 0, ',', '.'); ?> $</td>
                        <td><?php echo $product['stock']; ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'Không có'); ?></td>
                        <td><img src="../img/<?php echo htmlspecialchars($product['image'] ?? 'default.png'); ?>" width="50"></td>
                        <td>
                            <a href="?edit_product=<?php echo $product['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete_product=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Form thêm danh mục
        <h1>Category Manager</h1>
        <form method="POST" class="mb-4">
            <h3>Thêm danh mục mới</h3>
            <div class="form-group">
                <label for="category_name">Tên danh mục</label>
                <input type="text" class="form-control" name="category_name" required>
            </div>
            <div class="form-group">
                <label for="category_description">Mô tả</label>
                <textarea class="form-control" name="category_description"></textarea>
            </div>
            <button type="submit" name="add_category" class="btn btn-primary">Thêm danh mục</button>
        </form> -->

        <!-- Danh sách danh mục -->
        <h3>Category list</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Descriptom</th>
                    <th>Act</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $category['category_id']; ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars($category['description'] ?? 'Not found'); ?></td>
                        <td>
                            <a href="?delete_category=<?php echo $category['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete this?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>