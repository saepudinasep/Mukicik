<?php
// Include database connection
include "koneksi.php";

// Mendapatkan ID produk yang dikirimkan melalui metode POST
$productId = $_POST['productId'];

// Membuat koneksi
$conn = connectDB();

// Query untuk mengambil data produk dari tabel berdasarkan ID
$sql = "SELECT * FROM tb_product WHERE ProductId = $productId";
$result = $conn->query($sql);

// Mengembalikan data produk dalam format JSON
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(null); // Jika produk tidak ditemukan, kembalikan null
}

// Menutup koneksi database
$conn->close();
