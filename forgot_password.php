<?php
// Memanggil fungsi koneksi dari file koneksi.php
include "koneksi.php";

// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (isset($_SESSION['email'])) {
    // Jika sudah login, maka arahkan kembali ke halaman home atau halaman lain yang sesuai
    header("Location: home.php");
    exit();
}

// Inisialisasi variabel untuk menyimpan pesan error
$error = '';
$success = '';

// Cek apakah form reset password sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data yang dikirimkan oleh form
    $email = $_POST['email'];
    $newPassword = $_POST['Newpassword'];
    $confirmPassword = $_POST['confirm'];

    // Melakukan validasi form
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Email dan password tidak boleh kosong.";
    } elseif ($newPassword != $confirmPassword) {
        $error = "Password baru dan konfirmasi password tidak cocok.";
    } else {
        // Membuat koneksi
        $conn = connectDB();

        // Melakukan query untuk mencari user berdasarkan email
        $sql = "SELECT * FROM tb_user WHERE UserEmail='$email'";
        $result = $conn->query($sql);

        // Memeriksa apakah query menghasilkan baris data
        if ($result->num_rows > 0) {
            // Jika ada baris data, maka update password user
            // $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Enkripsi password baru
            $updateSql = "UPDATE tb_user SET UserPassword='$newPassword' WHERE UserEmail='$email'";
            if ($conn->query($updateSql) === TRUE) {
                // Jika update password berhasil
                $success = "Password berhasil direset. Silakan login dengan password baru Anda.";
            } else {
                // Jika terjadi kesalahan saat update password
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        } else {
            // Jika tidak ada baris data yang sesuai dengan email yang dimasukkan
            $error = "Email tidak terdaftar.";
        }

        // Menutup koneksi
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Mukicik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand" href="#">Mukicik</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product.php">Product</a>
                    </li>
                </ul>
                <ul class="navbar-nav d-flex">
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container mt-4 mb-4">
        <div class="row">
            <!-- Form login -->
            <div class="col-md-6">
                <h2 class="mb-3">Forgot Password</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="row mb-3">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="email" name="email" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="Newpassword" class="col-sm-2 col-form-label">New Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="Newpassword" name="Newpassword" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="confirm" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="confirm" name="confirm" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-8 offset-sm-2">
                            <button type="submit" class="btn btn-primary">Reset</button>
                        </div>
                    </div>
                    <?php
                    // Menampilkan pesan error jika ada
                    if (!empty($error)) {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }
                    if (!empty($success)) {
                        echo '<div class="alert alert-primary">' . $success . '</div>';
                    }
                    ?>
                </form>
            </div>
            <div class="col-md-2">
                <img src="img/home.jpg" alt="Guitar" srcset="" class="img-home" />
            </div>
        </div>
    </div>

    <footer class="pt-3 mt-4 text-body-secondary border-top">
        <div class="container">&copy; 2016 - Mukicik</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>