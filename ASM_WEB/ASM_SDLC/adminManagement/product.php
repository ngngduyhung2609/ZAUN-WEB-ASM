<?php
session_start();
include '../db.php';

// Lấy tất cả sản phẩm từ cơ sở dữ liệu cùng với danh mục
$stmt = $conn->prepare("
    SELECT p.product_id, p.name, p.description, p.price, p.image, c.name AS category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id
");
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Lấy số lượng sản phẩm trong giỏ hàng (nếu đã đăng nhập)
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $cart_count = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

// Lấy ID sản phẩm từ tham số URL
$product_id = intval($_GET['product_id']);

// Truy vấn thông tin sản phẩm
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="stylesheet" href="../store/store.css">
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Round|Material+Icons+Sharp|Material+Icons+Two+Tone"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="../home_store/mainshop.css">
    <link
      href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
      rel="stylesheet"
    />
    <style>
        body{
            margin: 0;
            padding: 0;
        }
        .parent {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(5, 1fr);
        grid-column-gap: 55px;
        }

        .div1 { grid-area: 1 / 1 / 4 / 4; 
        border: 5px dashed antiquewhite;
        border-radius: 10px;
        
        justify-content: center;
        display: flex;}
        .div2 { grid-area: 1 / 4 / 2 / 6; }
        .div3 { grid-area: 2 / 4 / 3 / 6; }
        .div4 { grid-area: 3 / 4 / 4 / 6; }
        .product-detail {
            padding: 50px;
            padding-top: 120px !important;
        }
        img{
            object-fit:contain;
            height: 690px;
            width: 550px;
        }
        .buy-now-btn{
          background: linear-gradient(to right, #eecaff, #e390fc) !important;
          color: white;
          padding: 10px 20px !important;
          border-radius: 25px !important;
          font-size: 15px !important;
          cursor: pointer;
          transition: 0.3s;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        }
    </style>
</head>
<body>
<div class="mM-header-contain">
        
        <a href="home.php">Zaun</a>
      <div class="mM-sub-content" style="display: flex;">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mB-sub-gallery">
         <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="navbarGallery">Gallery</button>
         <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="../store/store.php">Explore more device</a>
          <a class="dropdown-item" href="#">EXPERIENCE STORE</a>
          <a class="dropdown-item" href="#">Genuine Benefits</a>
        </div>
        </div>
      
        <div class="mB-phone">
          <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Phone</button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">Latest Release Products</a>
            <a class="dropdown-item" href="#">Galaxy AI</a>
            <a class="dropdown-item" href="#">App & Device</a>
          </div>
        </div>
        <div class="mB-tv">
          <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">TV & AV</button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">TV By Resolution</a>
            <a class="dropdown-item" href="#">TVs By Screen Size</a>
            <a class="dropdown-item" href="#">Projector</a>
          </div>
        </div>
        <div class="mB-it">
          <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">IT</button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">Explore more device</a>
            <a class="dropdown-item" href="#">All product</a>
            <a class="dropdown-item" href="#">On sale prodcut</a>
          </div>
        </div>
        <div class="mB-smart">
          <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Smart things</button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">Explore more device</a>
            <a class="dropdown-item" href="#">All product</a>
            <a class="dropdown-item" href="#">On sale prodcut</a>
          </div>
        </div>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <div class="mB-it">
          <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Management</button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="../adminManagement/admin_user.php">User Management</a>
              <a class="dropdown-item" href="../adminManagement/admin_product.php">Product Management</a>
            </div>
          </div>
              <?php endif; ?>
                <?php else: ?>
                  <div class="mB-sub-gallery">
                    <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="navbarGallery">Gallery</button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="../store/store.php">Explore more device</a>
                      <a class="dropdown-item" href="#">EXPERIENCE STORE</a>
                      <a class="dropdown-item" href="#">Genuine Benefits</a>
                    </div>
                    </div>
                  
                    <div class="mB-phone">
                      <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Phone</button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Latest Release Products</a>
                        <a class="dropdown-item" href="#">Galaxy AI</a>
                        <a class="dropdown-item" href="#">App & Device</a>
                      </div>
                    </div>
                    <div class="mB-tv">
                      <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">TV & AV</button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">TV By Resolution</a>
                        <a class="dropdown-item" href="#">TVs By Screen Size</a>
                        <a class="dropdown-item" href="#">Projector</a>
                      </div>
                    </div>
                    <div class="mB-it">
                      <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">IT</button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Explore more device</a>
                        <a class="dropdown-item" href="#">All product</a>
                        <a class="dropdown-item" href="#">On sale prodcut</a>
                      </div>
                    </div>
                    <div class="mB-smart">
                      <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Smart things</button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Explore more device</a>
                        <a class="dropdown-item" href="#">All product</a>
                        <a class="dropdown-item" href="#">On sale prodcut</a>
                      </div>
                    </div>
                  <?php endif; ?>
      </div>
      <div class="mM-header-footer">
        
        <!-- <button><i class='bx bx-search'></i></button> -->
        <form class="form-inline">
          <input class="form-control mr-sm-2 search-input" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0 search-btn" type="submit"><i class='bx bx-search'></i></button>
        </form>
        <button>
            <span id="cart-count" class="badge badge-light card-count"><?php echo $cart_count; ?></span>
            <a href="../backend/cart.php"><i class="bx bx-shopping-bag"></i></a>
        </button>
        <div class="mB-smart"></div>
          <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bx bx-user"></i></button>
                <?php if (isset($_SESSION['user_id'])): ?>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">My Accout</a>
                    <a class="dropdown-item" href="../backend/logout.php">Logout</a>
                  </div>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#"></a>
                        <a class="dropdown-item" href="../backend/logout.php">Logout</a>
                      </div>
                    <?php endif; ?>
                <?php else: ?>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="../backend/login.php">Login</a>
                        <a class="dropdown-item" href="../backend/register.php">Register</a>
                  </div>
                <?php endif; ?>
        </div>
      </div>
    <main>
    <div class="product-detail">
    <?php if ($product): ?>
        <div class="parent">
            <div class="div1">        
                 <img src="../img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="div2"> <h1><?php echo htmlspecialchars($product['name']); ?></h1> </div>
            <div class="div3"> <h3>Price: <?php echo htmlspecialchars($product['price']); ?> $</h3> <br> <button class="add-to-cart buy-now-btn">Add to Cart</button> </div>
            <div class="div4"> <p><?php echo htmlspecialchars($product['description']); ?></p> </div>
        </div>        
    <?php else: ?>
        <p>Product not exist.</p>
    <?php endif; ?>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
        let cartCount = <?php echo $cart_count; ?>;
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                <?php if (isset($_SESSION['user_id'])): ?>
                    fetch('../backend/cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `product_id=${productId}&add_to_cart=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            cartCount++;
                            document.getElementById('cart-count').innerText = cartCount;
                            alert('Sản phẩm đã được thêm vào giỏ hàng!');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                <?php else: ?>
                    alert('Please login before add product to cart!');
                    window.location.href = '../backend/login.php';
                <?php endif; ?>
            });
        });
    </script>
</body>
</html>