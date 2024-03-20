<?php
// Memanggil fungsi koneksi dari file koneksi.php
include "koneksi.php";
session_start();

// Check if user is logged in
if (isset($_SESSION['email'])) {
  // If logged in, set a variable to true
  $loggedIn = true;
} else {
  // If not logged in, set a variable to false
  $loggedIn = false;
}

// Membuat koneksi
$conn = connectDB();

// Mendapatkan 6 data product tertinggi
$sql = "SELECT p.*, c.CategoryName FROM tb_product p INNER JOIN tb_category c ON p.CategoryId = c.CategoryId ORDER BY p.ProductRating DESC LIMIT 6";
$result = $conn->query($sql);

// Fetching products into an array
$products = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Home | Mukicik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
    <div class="container">
      <a class="navbar-brand" href="home.php">Mukicik</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="home.php">Home</a>
          </li>
          <?php if ($loggedIn) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Product
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="insert.php">Insert</a></li>
                <li><a class="dropdown-item" href="update.php">Update</a></li>
              </ul>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link" href="product.php">Product</a>
            </li>
          <?php endif; ?>
        </ul>
        <ul class="navbar-nav d-flex">
          <?php if ($loggedIn) : ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Users Count: <?php echo isset($_SESSION['email']) ? 1 : 0; ?> | Hi, <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Guest'; ?>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </li>
          <?php else : ?>
            <li class="nav-item">
              <a class="nav-link" href="register.php">Register</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login.php">Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  <div class="container mt-4 mb-4">
    <div class="row">
      <div class="col-md-4">
        <img src="img/home.jpg" alt="Guitar" srcset="" class="img-home" />
      </div>
      <div class="col-md-8">
        <h1 class="font-title">Mukicik</h1>
        <h3>
          An innovative music selling store with competitive prices <br />
          It's never this cheap to shop in music !
        </h3>
        <a href="register.php" class="btn btn-primary btn-lg" type="button">
          Be a New Member
        </a>
      </div>
    </div>
  </div>

  <!-- Menampilkan data product tertinggi -->
  <div class="container">
    <h2 class="mb-3">Top 6 Products</h2>
    <!-- Product cards -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="productContainer">
      <?php foreach ($products as $product) : ?>
        <div class="col-md-2 mb-4 product-card" data-category="<?php echo $product['CategoryId']; ?>">
          <div class="card shadow-sm">
            <img src="assets/uploads/<?php echo $product['ProductImage']; ?>" class="card-img-top img-product" alt="<?php echo $product['ProductName']; ?>">
            <div class="card-body">
              <a href="#" class="text-decoration-none">
                <h5 class="card-title"><?php echo $product['ProductName']; ?></h5>
              </a>
              <p class="card-text"><?php echo $product['CategoryName']; ?></p>
              <p class="card-text color-star"><?php echo $product['ProductRating']; ?> <i class="bi bi-star-fill"></i></p>
              <p class="card-text">Rp. <?php echo $product['ProductPrice']; ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <footer class="pt-3 mt-4 text-body-secondary border-top">
    <div class="container">&copy; 2016 - Mukicik</div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>