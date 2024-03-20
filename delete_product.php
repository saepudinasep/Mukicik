<?php
// Include database connection
include "koneksi.php";

// Membuat koneksi
$conn = connectDB();

// Memeriksa apakah ada parameter id yang dikirim melalui metode POST
if (isset($_POST['id'])) {
    // Mendapatkan id produk dari parameter POST
    $productId = $_POST['id'];

    // Query untuk menghapus data produk berdasarkan id
    $sql = "DELETE FROM tb_product WHERE ProductId = $productId";

    if ($conn->query($sql) === TRUE) {
        // Jika penghapusan berhasil, kirimkan respons berhasil
        echo "Product berhasil dihapus.";
    } else {
        // Jika terjadi kesalahan saat menghapus, kirimkan pesan kesalahan
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // Jika tidak ada parameter id, kirimkan pesan kesalahan
    echo "Product id tidak diberikan.";
}

// Menutup koneksi database
$conn->close();
