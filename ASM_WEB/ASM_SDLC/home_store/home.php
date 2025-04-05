<?php
  session_start();
  include '../db.php';
  
  // Lấy số lượng sản phẩm trong giỏ hàng (nếu đã đăng nhập)
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $cart_count = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}
?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Zaun Chem-Baron</title>
    <link rel="stylesheet" href="mainshop.css" />
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
    <!-- 
    Option
    - mm: Main Menu
    - mB: Main Body
    - mF: Main Footer
    -->
    <!-- Header -->
    <div class="mM-header-contain">
        
        <a href="home.php">Zaun</a>
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
                      <a class="dropdown-item" href="../store/store.php">All Products</a>
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
    </div>

    <!-- Body -->
    <div class="mB-start-sale">
    <div class="mB-content-contain">
      <!-- <div class="mB-category-contain">
        <div class="mB-category">
          <p>Some plug you would like</p>
          <aside>
            <h2>Categories</h2>
            <div><a href="#">Tulip</a></div>
            <div><a href="#">Rose</a></div>
            <div><a href="#">Pearl</a></div>
            <div><a href="#">Gold</a></div>
            <br />
            <h2>Brands</h2>
            <div><a href="#">Brand 1</a></div>
            <div><a href="#">Brand 2</a></div>
            <div><a href="#">Brand 3</a></div>
            <div><a href="#">Brand 4</a></div>
            <div><a href="#">Brand 5</a></div>
          </aside>
        </div>
      </div> -->
      <div class="mB-ads-contain">
        <div class="ad-contain">
        <video autoplay muted loop id="myVideo">
            <source src="../vd/Galaxy Book3 Ultra- Unveiling - Samsung.mp4" type="video/mp4">
          </video>
          <div class="ads-content">
            <h1>Galaxy Book3 Ultra: Unveiling | Samsung</h1>
            <p>Bring your biggest ideas to life with the ultimate Galaxy experience.<br>Experience stunning clarity with a silky smooth 120hz refresh rate on a 16-inch, 3K AMOLED screen (2880 X 1800). This jaw-dropping laptop continues Samsung's rich legacy of innovative displays with a 16:10 aspect ratio, the largest ever for Galaxy.1</p>
            <!-- <button id="myBtn" onclick="myFunction()">Pause</button> -->
          </div>
        </div>
      </div>

      <!-- Popular product -->
       <div class="pop-contain">
          <h1>Some Featured Products</h1>
        <div class="sB-pop-container">
            <!-- Option in product -->
            <input type="radio" name="option" id="1" checked> 
            <label for="1">
                <div class="tab-name">OnSale</div>
                <div class="tab-content">
                    <span class="sale">Sale</span>
                    <img src="../img/image-removebg-preview.png" alt="pad">
                    <div class="mB-content-holder">
                        <label for="img">Galaxy Tab deal to 25%</label>
                        <button class="buy-now-btn">Buy now</button>
                    </div> 
                    
                </div>
            </label>

            <input type="radio" name="option" id="2">
            <label for="2">
                <div class="tab-name">Phone</div>
                <div class="tab-content">
                    <img src="../img/image-removebg-preview (1).png" alt="phone">
                    <div class="mB-content-holder">
                        <label for="img">New Phone Release</label>
                        <button class="buy-now-btn">Buy now</button>
                    </div> 
                </div>
            </label>

            <input type="radio" name="option" id="3">
            <label for="3">
                <div class="tab-name">Screens</div>
                <div class="tab-content">
                    <img src="../img/screen.png" alt="screen">
                    <div class="mB-content-holder">
                        <label for="img">Super Untra Vip Screen</label>
                        <button class="buy-now-btn">Buy now</button>
                    </div> 
                </div>
            </label>

            <input type="radio" name="option" id="4">
            <label for="4">
                <div class="tab-name">TV</div>
                <div class="tab-content">
                <img src="https://images.samsung.com/is/image/samsung/p6pim/vn/qa55qn70fakxxv/gallery/vn-qled-qn70f-qa55qn70fakxxv-545475792?$684_547_PNG$" alt="screen">
                    <div class="mB-content-holder">
                        <label for="img">TV QLED</label>
                        <button class="buy-now-btn">Buy now</button>
                    </div> 
                </div>
            </label> 

            <input type="radio" name="option" id="5">
            <label for="5">
                <div class="tab-name">Loudspeaker</div>
                <div class="tab-content">
                <img src="../img/loudspeaker.png" alt="screen">
                    <div class="mB-content-holder">
                        <label for="img">Loudspeaker</label>
                        <button class="buy-now-btn">Buy now</button>
                    </div> 
                </div>
            </label>
        </div>
        </div>
        <div class="slide-show">
          <h1>Some product you may like</h1>
          <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              <div class="slide carousel-item active">
                <img class=" d-block w-100" src="../img/96_pc_gaming_1.jpg" alt="First slide">
                <div class="carousel-caption">
                  <button class="btn btn-primary buy-now-btn">Learn More</button>
                </div>
              </div>
              <div class="slide carousel-item">
                <img class="d-block w-100" src="../img/pc-gaming.jpg" alt="Second slide">
                <div class="carousel-caption">
                  <button class="btn btn-primary buy-now-btn">Learn More</button>
                </div>
              </div>
              <div class="slide carousel-item">
                <div class="carousel-caption">
                  <button class="btn btn-primary buy-now-btn">Learn More</button>
                </div>
                <img class="d-block w-100" src="../img/pc-choi-game-intel.jpg" alt="Third slide">
              </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
        </div>

      <div class="mB-suggestion">
        <h1>Suggestions for you</h1>
      </div>
      <div class="mB-display-product">
        <section class="mB-flowers-grid">
          <div class="product-card">
            <img src="Iso_Computer.jpg" />
            <h3>Gray Computer</h3>
            <p>Price: $4000</p>
            <button class="buy-now-btn">Add to Cart</button>
          </div>
        </section>

        <section class="mB-flowers-grid">
          <div class="product-card">
            <img src="Iso_Computer.jpg" />
            <h3>Gray Computer</h3>
            <p>Price: $4000</p>
            <button>Add to Cart</button>
          </div>
        </section>

        <section class="mB-flowers-grid">
          <div class="product-card">
            <img src="Iso_Computer.jpg" />
            <h3>Gray Computer</h3>
            <p>Price: $4000</p>
            <button>Add to Cart</button>
          </div>
        </section>

        <section class="mB-flowers-grid">
          <div class="product-card">
            <img src="Iso_Computer.jpg" />
            <h3>Gray Computer</h3>
            <p>Price: $4000</p>
            <button>Add to Cart</button>
          </div>
        </section>

      </div>
    </div>
    </div>

    <!-- Footer -->
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
     <script src="home.js"></script>
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
  </body>
</html>
