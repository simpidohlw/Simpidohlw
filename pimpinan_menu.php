<?php
session_start();

// Periksa apakah pengguna sudah login sebagai pimpinan
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'pimpinan') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pimpinan - Penilaian Kinerja</title>
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
            justify-content: space-between;
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
            flex-grow: 1;
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
        .sidebar .logout {
            text-align: center;
            padding: 20px;
            background-color: #E74C3C;
            border-radius: 4px;
            margin: 0 20px;
        }
        .sidebar .logout a {
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
        h2 {
            color: #2980B9;
            margin-bottom: 20px;
        }
        .welcome-message {
            font-size: 1.4em;
            color: #2C3E50;
            margin-bottom: 40px;
        }
        .action-button {
            display: inline-block;
            padding: 15px 25px;
            background-color: #2980B9;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: bold;
        }
        .action-button:hover {
            background-color: #2573a6;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="upu.jpg" alt="Logo Penilaian Kinerja">
        </div>
        <ul>
            <li><a href="pimpinan_menu.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="lihat_penilaian.php"><i class="fas fa-chart-line"></i> Lihat Penilaian</a></li>
             <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a></li>
        </ul>
      
      
    </div>

    <div class="content">
        <h2>Selamat Datang, Pimpinan!</h2>
        <p class="welcome-message">Anda berada di halaman utama dashboard pimpinan. Di sini Anda dapat melihat hasil penilaian kinerja pegawai.</p>
        <a href="lihat_penilaian.php" class="action-button">Lihat Penilaian</a>
    </div>
</body>
</html>
