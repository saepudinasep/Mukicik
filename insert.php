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

// Inisialisasi variabel untuk pesan kesalahan dan pesan keberhasilan
$error = '';
$success = '';

// Memeriksa apakah data produk dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan nilai yang dikirimkan melalui form
    $productName = $_POST['name'];
    $productPrice = $_POST['price'];
    $productRating = $_POST['rate'];
    $categoryId = $_POST['category'];

    // File upload
    $targetDirectory = "assets/uploads/"; // Direktori tempat file akan disimpan
    $fileName = $_FILES["fileFoto"]["name"];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = $productName . '_' . date("Ymd") . '.' . $fileExt;
    $targetFile = $targetDirectory . $newFileName; // Nama file yang akan disimpan
    $uploadOk = 1; // Status pengunggahan

    // Cek apakah file adalah file gambar yang valid
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error = "File yang diunggah bukan gambar.";
        $uploadOk = 0;
    }

    // Cek apakah file sudah ada
    if (file_exists($targetFile)) {
        $error = "Maaf, file sudah ada.";
        $uploadOk = 0;
    }

    // Batasi ukuran file
    if ($_FILES["fileFoto"]["size"] > 5000000) {
        $error = "Maaf, ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Jika tidak ada kesalahan, coba unggah file
    if ($uploadOk == 0) {
        $error = "Maaf, file tidak diunggah.";
    } else {
        if (move_uploaded_file($_FILES["fileFoto"]["tmp_name"], $targetFile)) {
            // Query untuk menyisipkan data produk ke dalam tabel produk
            $sql = "INSERT INTO tb_product (ProductName, ProductPrice, ProductImage, ProductRating, CategoryId) VALUES ('$productName', $productPrice, '$newFileName', $productRating, $categoryId)";

            if ($conn->query($sql) === TRUE) {
                $success = "Produk berhasil disisipkan.";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error = "Maaf, terjadi kesalahan saat mengunggah file.";
        }
    }

    // Tutup koneksi database
    $conn->close();
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Product
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="insert.php">Insert</a></li>
                            <li><a class="dropdown-item" href="update.php">Update</a></li>
                        </ul>
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

    <div class="container mt-4 mb-4">
        <div class="row">
            <div class="col-md-8">
                <h2 class="mb-3">Insert New Product</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="name" class="col-sm-2 col-form-label">Product Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="price" class="col-sm-2 col-form-label">Product Price</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="price" name="price" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="file" class="col-sm-2 col-form-label">Product Image</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control" id="fileFoto" name="fileFoto" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="rate" class="col-sm-2 col-form-label">Product Rating</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="rate" name="rate" required />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="categoryFilter" class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-8">
                            <select class="form-select" id="categoryFilter" name="category">
                                <option value="all">All Categories</option>
                                <?php foreach ($categories as $categoryId => $categoryName) : ?>
                                    <option value="<?php echo $categoryId; ?>"><?php echo $categoryName; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-8 offset-sm-2">
                            <button type="submit" class="btn btn-primary">Insert</button>
                        </div>
                    </div>
                    <?php
                    // Menampilkan pesan error jika ada
                    if (!empty($error)) {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }
                    ?>
                    <?php
                    // Menampilkan pesan error jika ada
                    if (!empty($success)) {
                        echo '<div class="alert alert-success">' . $success . '</div>';
                    }
                    ?>
                </form>
            </div>
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
</body>

</html>