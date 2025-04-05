<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT) ?: 1;

    if ($product_id === false || $quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product information is not valid!']);
        exit();
    }

    $stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stock = $stmt->get_result()->fetch_assoc()['stock'] ?? 0;

    if ($stock >= $quantity) {
        $stmt = $conn->prepare("
            INSERT INTO cart (user_id, product_id, quantity) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + ?
        ");
        $stmt->bind_param("iiii", $user_id, $product_id, $quantity, $quantity);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Added to cart!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Not enough stock! (Còn: $stock)"]);
    }
    exit();
}

// Xử lý xóa khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_from_cart'])) {
    $cart_id = filter_var($_POST['cart_id'], FILTER_VALIDATE_INT);
    if ($cart_id === false) {
        echo json_encode(['status' => 'error', 'message' => 'ID Cart is not valid!']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Deleted from cart!']);
    exit();
}

// Lấy danh sách sản phẩm trong giỏ hàng
$stmt = $conn->prepare("
    SELECT c.cart_id, c.quantity, p.name, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Tính tổng tiền
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['quantity'] * $item['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../store/store.css">
    <style>
        a{
            text-decoration: none   ;
        }
        .navbar{
            padding-top: 70px;
        }
        .dropdown-item{
            z-index: 1000;
        }
    </style>
</head>
<body>
<div class="mM-header-contain">
        
        <a href="../home_store/home.php">Zaun</a>

      <div class="mM-header-footer">
        
        <!-- <button><i class='bx bx-search'></i></button> -->
        <form class="form-inline">
          <input class="form-control mr-sm-2 search-input" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0 search-btn" type="submit"><i class='bx bx-search'></i></button>
        </form>
        <button>
            <!-- <span id="cart-count" class="badge badge-light card-count"><?php echo $cart_count; ?></span> -->
            <a href="../backend/cart.php"><i class="bx bx-shopping-bag"></i></a>
        </button>
        <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bx bx-user"></i></button>
                <?php if (isset($_SESSION['user_id'])): ?>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">My Accout</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                  </div>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">User Management</a>
                        <a class="dropdown-item" href="#">Product Management</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                      </div>
                    <?php endif; ?>
                <?php else: ?>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="login.php">Login</a>
                        <a class="dropdown-item" href="register.php">Register</a>
                  </div>
                <?php endif; ?>
      </div>
    </div>

    <!------------------------------- Body ---------------------------->
    <!-- Breadcrum -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb"> 
        <li class="breadcrumb-item active" aria-current="page">Home</li>
      </ol>
    </nav>          

    <div class="container mt-4">
        <h1>Your shopping cart</h1>

        <?php if (empty($cart_items)): ?>
            <p class="text-center">Your cart is empty!</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>prodcut</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Total</th>
                        <th>Act</th>
                    </tr>
                </thead>
                <tbody id="cart-table">
                    <?php foreach ($cart_items as $item): ?>
                        <tr data-cart-id="<?php echo $item['cart_id']; ?>">
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['price'], 0, ',', '.') ?> $</td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo number_format($item['quantity'] * $item['price'], 0, ',', '.') ?> $</td>
                            <td>
                                <button class="btn btn-danger remove-item" data-cart-id="<?php echo $item['cart_id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total:</th>
                        <th colspan="2"><?php echo number_format($total_amount, 0, ',', '.') ?> $</th>
                    </tr>
                </tfoot>
            </table>
            <a href="checkout.php" class="btn btn-success">Continue to payment</a>
        <?php endif; ?>



    <script>
    $(document).ready(function() {
        // Xử lý thêm sản phẩm vào giỏ
        $('#add-to-cart-form').submit(function(e) {
            e.preventDefault();
            $.post('cart.php', $(this).serialize() + '&add_to_cart=1', function(response) {
                alert(response.message);
                if (response.status === 'success') {
                    location.reload();
                }
            }, 'json').fail(function() {
                alert('Som thing when wrong!');
            });
        });

        // Xử lý xóa sản phẩm khỏi giỏ
        $('.remove-item').click(function() {
            const cartId = $(this).data('cart-id');
            if (confirm('Are you sure?')) {
                $.post('cart.php', { cart_id: cartId, remove_from_cart: 1 }, function(response) {
                    alert(response.message);
                    if (response.status === 'success') {
                        location.reload();
                    }
                }, 'json').fail(function() {
                    alert('Some problem when delete product!');
                });
            }
        });
    });
    </script>
</body>
</html>