<?php
session_start();

// Hapus semua variabel session
session_unset();

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login setelah logout
header("Location: login.php");
exit();
