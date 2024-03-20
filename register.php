<?php
session_start();

// Include database connection
include "koneksi.php";

// Memeriksa apakah pengguna sudah login
if (isset($_SESSION['email'])) {
  // Jika belum login, maka arahkan kembali ke halaman login
  header("Location: login.php");
  exit();
}

// Membuat koneksi
$conn = connectDB();

// Membuat variabel targetFile
$targetFile = "";

// Inisialisasi variabel untuk menyimpan pesan error
$error = '';
$success = '';

// Memeriksa apakah ada pengiriman data melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Mendapatkan nilai yang dikirimkan melalui form
  $email = $_POST['email'];
  $name = $_POST['name'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm'];
  $gender = $_POST['gender'];
  $birthdate = $_POST['birthdate'];

  // File upload
  if (!empty($_FILES["fileFoto"]["name"])) { // Periksa apakah file telah diunggah
    // Lokasi penyimpanan file
    $targetDirectory = "assets/uploads/";

    // Mendapatkan ekstensi file
    $imageFileType = strtolower(pathinfo($_FILES["fileFoto"]["name"], PATHINFO_EXTENSION));

    // Membuat nama file unik berdasarkan nama pengguna dan tanggal upload
    $uniqueFileName = $name . "_" . date("YmdHis") . "." . $imageFileType;
    $targetFile = $targetDirectory . $uniqueFileName; // Nama file yang akan disimpan
    $uploadOk = 1; // Status pengunggahan

    // Cek apakah file adalah file gambar yang valid
    if (
      $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif"
    ) {
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
        // echo "File " . htmlspecialchars(basename($_FILES["fileFoto"]["name"])) . " telah diunggah.";
      } else {
        $error = "Maaf, terjadi kesalahan saat mengunggah file.";
      }
    }
  }


  // Query untuk memeriksa apakah email sudah digunakan
  $sqlCheckEmail = "SELECT * FROM tb_user WHERE UserEmail = '$email'";
  $resultCheckEmail = $conn->query($sqlCheckEmail);

  // Jika email sudah digunakan, tampilkan pesan kesalahan
  if ($resultCheckEmail->num_rows > 0) {
    $error = "Email sudah digunakan. Silakan gunakan email lain.";
  } elseif ($password !== $confirmPassword) {
    $error = "Password dan konfirmasi password tidak sama.";
  } elseif (strtotime($birthdate) >= strtotime('today')) {
    $error = "Tanggal lahir tidak valid.";
  } else {
    // Query untuk menambahkan data pengguna baru ke dalam database
    $sql = "INSERT INTO tb_user (Username, UserEmail, UserPassword, UserGender, UserDOB, UserProfilePicture) VALUES ('$name', '$email', '$password', '$gender', '$birthdate', '$uniqueFileName')";

    if ($conn->query($sql) === TRUE) {
      $success = "Registrasi berhasil. Silakan <a href='login.php'>login</a>.";
    } else {
      $error = "Error: " . $sql . "<br>" . $conn->error;
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
  <title>Register | Mukicik</title>
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
            <a class="nav-link active" aria-current="page" href="register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- End Navbar -->

  <div class="container mt-4 mb-4">
    <div class="row">
      <div class="col-md-8">
        <h2 class="mb-3">Register</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
          <div class="row mb-3">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-8">
              <input type="email" class="form-control" id="email" name="email" required />
            </div>
          </div>
          <div class="row mb-3">
            <label for="name" class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="name" name="name" required />
            </div>
          </div>
          <div class="row mb-3">
            <label for="password" class="col-sm-2 col-form-label">Password</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="password" name="password" required />
            </div>
          </div>
          <div class="row mb-3">
            <label for="confirm" class="col-sm-2 col-form-label">Confirm Password</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="confirm" name="confirm" required />
            </div>
          </div>
          <fieldset class="row mb-3">
            <legend class="col-form-label col-sm-2 pt-0">Gender</legend>
            <div class="col-sm-8">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked />
                <label class="form-check-label" for="male"> Male </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="female" value="female" />
                <label class="form-check-label" for="female"> Female </label>
              </div>
            </div>
          </fieldset>
          <div class="row mb-3">
            <label for="birthdate" class="col-sm-2 col-form-label">Birthdate</label>
            <div class="col-sm-8">
              <input type="date" class="form-control" id="birthdate" name="birthdate" required />
            </div>
          </div>
          <div class="row mb-3">
            <label for="file" class="col-sm-2 col-form-label">Choose File</label>
            <div class="col-sm-8">
              <input type="file" class="form-control" id="fileFoto" name="fileFoto" />
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-8 offset-sm-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms" required />
                <label class="form-check-label" for="terms">
                  I agree to terms and conditions
                </label>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-8 offset-sm-2">
              <button type="submit" class="btn btn-primary">Register</button>
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