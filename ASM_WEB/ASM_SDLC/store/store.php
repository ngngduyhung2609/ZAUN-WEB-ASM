<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../db.php';

// Lấy số lượng sản phẩm trong giỏ hàng
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $cart_count = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();
    } else {
        error_log("Query preparation error cart: " . $conn->error);
    }
}

// Lấy danh sách danh mục từ database
$categories = [];
$categories_query = $conn->query("SELECT category_id, name FROM categories");
if ($categories_query) {
    while ($row = $categories_query->fetch_assoc()) {
        $categories[$row['category_id']] = $row['name'];
    }
} else {
    error_log("Query preparation error categories: " . $conn->error);
}

// Lấy danh sách sản phẩm
$products_by_category = [];
$products_query = $conn->prepare("
    SELECT p.product_id, p.name, p.price, p.image, p.category_id 
    FROM products p 
    WHERE p.stock > 0 
    ORDER BY p.product_id ASC
");
if ($products_query) {
    $products_query->execute();
    $products_result = $products_query->get_result();
    while ($product = $products_result->fetch_assoc()) {
        $category_id = $product['category_id'] ?? 0; // 0 cho sản phẩm không danh mục
        $products_by_category[$category_id][] = $product;
    }
    $products_query->close();
} else {
    error_log("Query preparation error products: " . $conn->error);
}


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

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
  $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
  $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT) ?: 1;

  if ($product_id === false || $quantity <= 0) {
      echo json_encode(['status' => 'error', 'message' => 'Thông tin sản phẩm hoặc số lượng không hợp lệ!']);
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
      echo json_encode(['status' => 'success', 'message' => 'Add to card success!']);
  } else {
      echo json_encode(['status' => 'error', 'message' => "No more product! (Stockx: $stock)"]);
  }
  exit();
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="store.css">
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
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
</head>
<body>

<!-------------------------- header --------------------------------->
    <div class="mM-header-contain">
        
        <a href="../home_store/home.php">Zaun</a>
      <div class="mM-sub-content" style="display: flex;">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="mB-sub-gallery">
         <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="navbarGallery">Gallery</button>
         <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="../store/store.php">All product</a>
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
        <button class="btn btn-secondary header-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="bx bx-user"></i></button>
                <?php if (isset($_SESSION['user_id'])): ?>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">My Accout</a>
                    <a class="dropdown-item" href="../backend/logout.php">Logout</a>
                  </div>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">User Management</a>
                        <a class="dropdown-item" href="#">Product Management</a>
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

    <!------------------------------- Body ---------------------------->
    <!-- Breadcrum -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb"> 
        <li class="breadcrumb-item active" aria-current="page">Home</li>
      </ol>
    </nav>

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a class="bread-crum-home" href="#">Zaun</a></li>
        <li class="breadcrumb-item active" aria-current="page">Explore more device</li>
      </ol>
    </nav>
    
    <!--  screens - prodcut slider -->
      <section class="product">
        <h2 class="product-category">ALL PRODUCTS</h2>
        <button class="pre-btn"><i class='bx bx-chevron-left'></i></button>
        <button class="next-btn"><i class='bx bx-chevron-right'></i></button>
        <div class="product-container">
          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Help me make a decision </h3>
            </div>
          </div>

          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Help me make a decision </h3>
            </div>
          </div>

          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Help me make a decision </h3>
            </div>
          </div>

          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Help me make a decision </h3>
            </div>
          </div>

          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Help me make a decision </h3>
            </div>
          </div>
          
          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Yellow Computer </h3>
            </div>
          </div>

          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Yellow Computer </h3>
            </div>
          </div>

          <div class="product-card">
            <div class="product-image">
              <img src="../img/computer_yellow.jpg" alt="">
            </div>
            <div class="product-infor">
              <h3>Yellow Computer </h3>
            </div>
          </div>
        </div>
      </section>
      <nav aria-label="breadcrumb" class="">
          <ol class="breadcrumb breadcrum-product">
            <li class="breadcrumb-item"><a class="bread-crum-home" href="#">Filter</a></li>
            <li class="breadcrumb-item active" aria-current="page">Result</li>
          </ol>
        </nav>

      <div class="body-container">
        <div class="body-sidebar-container">
        <!-- side bar -->
            <div class="sidebar-contain">
              <div class="sidebar">
                <div class="logo">
                  <img src="../img/image-removebg-preview (2).png" alt="Zaun-Logo">
                  <div class="logo-name">ZAUN</div>
                </div>
                <h3><i class='bx bx-chevron-right'></i>Screen Size</h3>
              </div>
              <ul class="nav-list">
                
                <li>
                    <input type="checkbox" class="link-name"> 27 inch</input>
                </li>
                <li>
                    <input type="checkbox" class="link-name"> 28 inch</input>
                </li>
                <li>
                    <input type="checkbox" class="link-name"> 29 inch</input>
                </li>
              </ul>

              

              
            </div>
          </div>
          <!-- Product Appear -->
          <div class="body-view">
            <div class="view">
            <?php if (empty($products_by_category)): ?>
                        <p class="text-center">Chưa có sản phẩm nào trong kho!</p>
                    <?php else: ?>
                        <?php foreach ($products_by_category as $category_id => $products): ?>
                            <section id="category-<?php echo $category_id; ?>" class="mt-5">
                                <h3><?php echo htmlspecialchars($categories[$category_id] ?? 'Sản phẩm khác'); ?></h3>
                                <div class="row">
                                    <?php foreach ($products as $product): ?>
                                        <div class="col-md-4" style = "height: 450px;">         
                                            <div class="appear-product-container">
                                              <div class="img-contain">
                                                <img src="../img/<?php echo htmlspecialchars($product['image'] ?? 'default.png'); ?>" 
                                                     class="card-img-top"   
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     >
                                              </div>
                                                    <div class="h3-contain">
                                                       <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                                    </div>
                                                    <div class="price-contain">
                                                      <p>Giá: <?php echo number_format($product['price'], 0, ',', '.') ?> $</p>
                                                    </div>
                                                    <div class="card-btn-container">
                                                      <button class="btn btn-success core-btn buynow-btn" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                                                      <a href="../adminManagement/product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-info core-btn">Details</a>
                                                    </div>
                                                
                                            </div>
                                        </div>  
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    <?php endif; ?>
                
                <!-- <div class="appear-product-container">
                    <img src="../img/computer_yellow.jpg" alt="">
                    <span class="product-sale">50% of</span>
                    <h3>Yellow Computer</h3>
                    <p>$200 <span class="ori-price">$400</span></p>
                    <div class="card-btn-container">
                      <button class="core-btn buynow-btn">Add to card</button>
                      <button class="core-btn viewmore-btn">View more</button>
                    </div>
                </div> -->

                <!-- <div class="appear-product-container">
                  
                </div>

                <div class="appear-product-container">
                  
                </div>

                <div class="appear-product-container">
                  
                </div>

                <div class="appear-product-container">
                  
                </div>
                
                <div class="appear-product-container">
                  
                </div> -->
            </div>
          </div>
        </div>



    <!--------------------------------- Footer --------------------------------->
    <footer style="background-color: #f8f8f8; padding: 40px 20px; color: #333;">
    <div class="footer-container" style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between;">
        <div style="flex: 1; min-width: 200px; margin: 10px;">
            <h4>Products & Services</h4>
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="#" style="text-decoration: none; color: #333;">Tablets</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Phones</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Cameras</a></li>
            </ul>
        </div>
        <div style="flex: 1; min-width: 200px; margin: 10px;">
            <h4>Online Shopping</h4>
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="#" style="text-decoration: none; color: #333;">Warranty</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Best Prices</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Customer Programs</a></li>
            </ul>
        </div>
        <div style="flex: 1; min-width: 200px; margin: 10px;">
            <h4>Exclusive Programs</h4>
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="#" style="text-decoration: none; color: #333;">Customer Benefits</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Preferred Stores</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">VIP Member</a></li>
            </ul>
        </div>
        <div style="flex: 1; min-width: 200px; margin: 10px;">
            <h4>Need Assistance?</h4>
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="#" style="text-decoration: none; color: #333;">Customer Support</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Service Center</a></li>
            </ul>
        </div>
        <div style="flex: 1; min-width: 200px; margin: 10px;">
            <h4>Account & Community</h4>
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="#" style="text-decoration: none; color: #333;">Login</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Join</a></li>
                <li><a href="#" style="text-decoration: none; color: #333;">Forum</a></li>
            </ul>
        </div>
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <p>&copy; 2025 Zaun. NO SALE TO PILTOVER.</p>
    </div>
    <div>
        <p>Privacy <br>Policy</p>
        <p>Contact US</p>
    </div>
</footer>
    <!-- JS -->
    <script src="store.js"></script>
    <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <script>
      let cartCount = <?php echo $cart_count; ?>;
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
              console.log("123");
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
                            alert('Add to cart!');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                <?php else: ?>
                    alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ!');
                    window.location.href = 'login.php';
                <?php endif; ?>
            });
        });




        function addToCart(productId) {
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
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi thêm sản phẩm!' + error.message);
            });
        <?php else: ?>
            alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ!');
            window.location.href = 'login.php';
        <?php endif; ?>
    }
    </script>
</body>
</html>