<?php
// Include database connection
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
$conn = connectDB();

// Query to fetch all categories
$sql = "SELECT * FROM tb_category";
$result = $conn->query($sql);

// Fetching categories into an array
$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[$row['CategoryId']] = $row['CategoryName'];
    }
}

// Query to fetch all products with category name
$sql = "SELECT p.*, c.CategoryName FROM tb_product p INNER JOIN tb_category c ON p.CategoryId = c.CategoryId";
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
    <title>Product | Mukicik</title>
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
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="product.php">Product</a>
                    </li>
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

    <div class="container mt-5">
        <h2>Our Product</h2>
        <!-- Category filter dropdown -->
        <div class="row mb-3">
            <label for="categoryFilter" class="col-sm-2 col-form-label">Category</label>
            <div class="col-sm-3">
                <select class="form-select" id="categoryFilter">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $categoryId => $categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-3">
                <button class="btn btn-primary mb-3" id="clearFilterBtn">Clear Filter</button>
            </div>
        </div>
        <!-- Clear Filter button -->

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
    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->

    <script>
        $(document).ready(function() {
            // Show all products on page load
            $('.product-card').show();

            // Event listener for category filter change
            $('#categoryFilter').change(function() {
                var categoryId = $(this).val();
                // Show products with selected category
                if (categoryId === 'all') {
                    $('.product-card').show();
                } else {
                    $('.product-card').hide().filter('[data-category="' + categoryId + '"]').show();
                }
                // Add 'active' class to Clear Filter button
                $('#clearFilterBtn').removeClass('btn-primary').addClass('btn-danger');
            });

            // Event listener for clear filter button click
            $('#clearFilterBtn').click(function() {
                // Show all products
                $('.product-card').show();
                // Reset category filter to default value (All Categories)
                $('#categoryFilter').val('all');
                // Remove 'active' class from Clear Filter button
                $('#clearFilterBtn').removeClass('btn-danger').addClass('btn-primary');
            });
        });
    </script>
</body>

</html>