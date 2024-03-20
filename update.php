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
    if (isset($_POST['productId'])) {
        $productId = $_POST['productId'];
    } else {
        $error = "Product ID is not set.";
    }

    if (isset($_POST['name'])) {
        $productName = $_POST['name'];
    } else {
        $error = "Product name is not set.";
    }

    if (isset($_POST['price'])) {
        $productPrice = $_POST['price'];
    } else {
        $error = "Product price is not set.";
    }

    if (isset($_POST['rate'])) {
        $productRating = $_POST['rate'];
    } else {
        $error = "Product rating is not set.";
    }

    if (isset($_POST['category'])) {
        $categoryId = $_POST['category'];
    } else {
        $error = "Product category is not set.";
    }

    // File upload
    $targetDirectory = "assets/uploads/"; // Direktori tempat file akan disimpan
    $fileName = $_FILES["fileFoto"]["name"];
    if (!empty($fileName)) {
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = $productName . '_' . date("Ymd") . '.' . $fileExt;
        $targetFile = $targetDirectory . $newFileName; // Nama file yang akan disimpan

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
    }

    // Jika tidak ada kesalahan, coba unggah file
    if (empty($error)) {
        if (!empty($fileName)) {
            if (move_uploaded_file($_FILES["fileFoto"]["tmp_name"], $targetFile)) {
                // Query untuk meng-update data produk di dalam tabel produk
                $sql = "UPDATE tb_product SET ProductName = '$productName', ProductPrice = $productPrice, ProductImage = '$targetFile', ProductRating = $productRating, CategoryId = $categoryId WHERE ProductId = $productId";

                if ($conn->query($sql) === TRUE) {
                    $success = "Product updated successfully.";
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                $error = "Maaf, terjadi kesalahan saat mengunggah file.";
            }
        } else {
            // Query untuk meng-update data produk di dalam tabel produk (tanpa mengubah gambar)
            $sql = "UPDATE tb_product SET ProductName = '$productName', ProductPrice = $productPrice, ProductRating = $productRating, CategoryId = $categoryId WHERE ProductId = $productId";

            if ($conn->query($sql) === TRUE) {
                $success = "Product updated successfully.";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
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
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
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
                <h2 class="mb-3">Modify Product</h2>
                <table class="table" id="productTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Rating</th>
                            <th>Category</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <!-- Data will be inserted here using AJAX -->
                    </tbody>
                </table>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script>
                    $(document).ready(function() {
                        // Function to fetch and display product data
                        function fetchProductData() {
                            $.ajax({
                                url: 'get_products.php',
                                type: 'GET',
                                success: function(response) {
                                    $('#productTableBody').html(response);
                                }
                            });
                        }

                        // Initial load of product data
                        fetchProductData();

                        // Event listener for delete buttons
                        $(document).on('click', '.deleteBtn', function() {
                            var productId = $(this).data('id');
                            if (confirm("Are you sure you want to delete this product?")) {
                                $.ajax({
                                    url: 'delete_product.php',
                                    type: 'POST',
                                    data: {
                                        id: productId
                                    },
                                    success: function(response) {
                                        // Refresh product data after deletion
                                        fetchProductData();
                                        // Show notification
                                        $('#notification').html('<div class="alert alert-success" role="alert">Product successfully deleted.</div>');
                                    },
                                    error: function(xhr, status, error) {
                                        // Show error notification
                                        $('#notification').html('<div class="alert alert-danger" role="alert">Failed to delete product. Please try again later.</div>');
                                        console.error(xhr.responseText);
                                    }
                                });
                            }
                        });

                        // Ketika tombol "Select" pada tabel diklik
                        $(document).on('click', '.selectProduct', function() {
                            // Ambil ID produk dari data-id atribut pada tombol "Select" yang diklik
                            var productId = $(this).data('id');

                            // Kirim permintaan AJAX untuk mendapatkan data produk berdasarkan ID produk yang dipilih
                            $.ajax({
                                url: 'get_product_by_id.php',
                                type: 'POST',
                                data: {
                                    productId: productId
                                },
                                dataType: 'json',
                                success: function(response) {
                                    // Periksa apakah data produk ditemukan
                                    if (response) {
                                        console.log(productId)
                                        // Isi nilai input dengan data produk yang ditemukan
                                        $('#productId').val(response.ProductId);
                                        $('#name').val(response.ProductName);
                                        $('#price').val(response.ProductPrice);
                                        $('#rate').val(response.ProductRating);
                                        $('#categoryFilter').val(response.CategoryId);

                                        // Tampilkan nama file gambar produk
                                        if (response.ProductImage) {
                                            // Mendapatkan nama file dari URL gambar produk
                                            var fileName = response.ProductImage.split('/').pop();
                                            // Tampilkan nama file di luar input file
                                            $('#fileFotoLabel').text(fileName);
                                        } else {
                                            // Jika gambar produk tidak tersedia, kosongkan nama file sebelumnya
                                            $('#fileFotoLabel').text('Choose file...');
                                        }
                                    } else {
                                        // Jika data produk tidak ditemukan, kosongkan nilai input
                                        $('#productId').val('');
                                        $('#name').val('');
                                        $('#price').val('');
                                        $('#rate').val('');
                                        $('#categoryFilter').val('');
                                        $('#fileFoto').attr('src', ''); // Jika gambar produk ditampilkan, hapus gambar sebelumnya
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Tangani kesalahan jika terjadi
                                    console.error(xhr.responseText);
                                }
                            });
                        });

                        // Ketika pengguna memilih gambar
                        $('#fileFoto').change(function() {
                            var fileName = $(this).val().split('\\').pop(); // Mendapatkan nama file dari path lengkap
                            $('#fileFotoLabel').text(fileName); // Mengubah teks label dengan nama file
                        });


                    });
                </script>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label for="productId" class="col-sm-2 col-form-label">Id Product</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="productId" name="productId" readonly />
                        </div>
                    </div>
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
                            <!-- Input file -->
                            <input type="file" class="form-control" id="fileFoto" name="fileFoto" style="display: none;" />

                            <!-- Label yang menampilkan nama file -->
                            <label for="fileFoto" id="fileFotoLabel" class="form-label">Choose file...</label>
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
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                    <div id="notification">
                        <?php
                        // Menampilkan pesan error jika ada
                        if (!empty($error)) {
                            echo '<div class="alert alert-danger">' . $error . '</div>';
                        }
                        ?>
                        <?php
                        // Menampilkan pesan keberhasilan jika ada
                        if (!empty($success)) {
                            echo '<div class="alert alert-success">' . $success . '</div>';
                        }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="pt-3 mt-4 text-body-secondary border-top">
        <div class="container">&copy; 2016 - Mukicik</div>
    </footer>
    <!-- Bootstrap JS and jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
</body>

</html>