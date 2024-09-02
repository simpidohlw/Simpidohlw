<?php
session_start();

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Penilaian Kinerja</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2C3E50;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
        }
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar .logo img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 50%;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            padding: 10px 20px;
            text-align: left;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px;
            border-radius: 4px;
        }
        .sidebar ul li a:hover {
            background-color: #34495E;
        }
        .sidebar ul li a i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .logout {
            margin-top: 10px;
            text-align: center;
            padding: 10px 20px;
            background-color: #E74C3C;
            border-radius: 4px;
        }
        .logout a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            display: block;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
            background-color: #ECF0F1;
            min-height: 100vh;
        }
        .header {
            background-color: #ECF0F1;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2980B9;
            margin-bottom: 20px;
        }
        .header .title {
            font-size: 1.8em;
            color: #2980B9;
            padding-left: 20px;
        }
        h2 {
            color: #2980B9;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">

            <img src="upu.jpg" alt="Logo Penilaian Kinerja" >
        </div>
        <ul>
            <li><a href="kelola_pegawai.php"><i class="fas fa-users"></i> Kelola Pegawai</a></li>
            <li><a href="kelola_penilaian.php"><i class="fas fa-chart-line"></i> Kelola Penilaian</a></li>
            <li><a href="kelola_kriteria.php"><i class="fas fa-list-alt"></i> Kelola Kriteria Penilaian</a></li>
            <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <div class="title">Admin Dashboard</div>
        </div>
        <h2>Selamat datang, Admin!</h2>
        <p>Di sini Anda dapat mengelola data pegawai, kriteria, dan penilaian kinerja mereka. Gunakan menu di sebelah kiri untuk mulai bekerja.</p>
    </div>
    
</body>
</html>
