<?php
// Fungsi untuk melakukan koneksi ke database
function connectDB()
{
    $host = "localhost"; // Host database (misalnya localhost)
    $username = "root"; // Nama pengguna database
    $password = ""; // Kata sandi database
    $database = "db_mukicik"; // Nama database

    // Membuat koneksi
    $conn = new mysqli($host, $username, $password, $database);

    // Memeriksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    //  else {
    //     echo "Koneksi Berhasil";
    // }

    return $conn;
}
