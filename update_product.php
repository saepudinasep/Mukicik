<?php
// Include database connection
include "koneksi.php";

// Memeriksa apakah data produk dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan nilai yang dikirimkan melalui form
    $productId = $_POST['productId'];
    $productName = $_POST['name'];
    $productPrice = $_POST['price'];
    $productRating = $_POST['rate'];
    $categoryId = $_POST['category'];

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
            echo json_encode(array("error" => "File yang diunggah bukan gambar."));
            exit;
        }

        // Cek apakah file sudah ada
        if (file_exists($targetFile)) {
            echo json_encode(array("error" => "Maaf, file sudah ada."));
            exit;
        }

        // Batasi ukuran file
        if ($_FILES["fileFoto"]["size"] > 5000000) {
            echo json_encode(array("error" => "Maaf, ukuran file terlalu besar."));
            exit;
        }

        // Jika tidak ada kesalahan, coba unggah file
        if (move_uploaded_file($_FILES["fileFoto"]["tmp_name"], $targetFile)) {
            // Query untuk meng-update data produk di dalam tabel produk
            $conn = connectDB();
            $sql = "UPDATE tb_product SET ProductName = ?, ProductPrice = ?, ProductImage = ?, ProductRating = ?, CategoryId = ? WHERE ProductId = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $productName, $productPrice, $targetFile, $productRating, $categoryId, $productId);
            if ($stmt->execute()) {
                echo json_encode(array("success" => "Produk berhasil diperbarui."));
            } else {
                echo json_encode(array("error" => "Gagal memperbarui produk. Error: " . $stmt->error));
            }
            $stmt->close();
            $conn->close();
        } else {
            echo json_encode(array("error" => "Maaf, terjadi kesalahan saat mengunggah file."));
        }
    } else {
        // Query untuk meng-update data produk di dalam tabel produk (tanpa mengubah gambar)
        $conn = connectDB();
        $sql = "UPDATE tb_product SET ProductName = ?, ProductPrice = ?, ProductRating = ?, CategoryId = ? WHERE ProductId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $productName, $productPrice, $productRating, $categoryId, $productId);
        if ($stmt->execute()) {
            echo json_encode(array("success" => "Produk berhasil diperbarui."));
        } else {
            echo json_encode(array("error" => "Gagal memperbarui produk. Error: " . $stmt->error));
        }
        $stmt->close();
        $conn->close();
    }
} else {
    // Jika tidak ada data POST, kirim respon error
    echo json_encode(array("error" => "Metode permintaan tidak valid."));
}
