<?php
session_start();

// Mengakhiri sesi
session_unset(); // Menghapus semua variabel sesi
session_destroy(); // Menghancurkan sesi

// Mengarahkan pengguna kembali ke halaman login
header("Location: login.php");
exit();
?>
