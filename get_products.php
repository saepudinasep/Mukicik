<?php
// Include database connection
include "koneksi.php";

// Membuat koneksi
$conn = connectDB();

// Query untuk mengambil data produk dari tabel
$sql = "SELECT * FROM tb_product";
$result = $conn->query($sql);

// Menampilkan data produk dalam format HTML
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><span class='selectProduct' data-id='" . $row['ProductId'] . "'>Select</span></td>";
        echo "<td>" . $row['ProductId'] . "</td>";
        echo "<td>" . $row['ProductName'] . "</td>";
        echo "<td>" . $row['ProductPrice'] . "</td>";
        // echo "<td><img src='assets/uploads/" . $row['ProductImage'] . "' alt='" . $row['ProductName'] . "' class='img-table'></td>";
        echo "<td>" . $row['ProductImage'] . "</td>";
        echo "<td>" . $row['ProductRating'] . "</td>";
        echo "<td>" . $row['CategoryId'] . "</td>";
        echo "<td><button class='deleteBtn btn btn-danger' data-id='" . $row['ProductId'] . "'>Delete</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No products found</td></tr>";
}

// Menutup koneksi database
$conn->close();
