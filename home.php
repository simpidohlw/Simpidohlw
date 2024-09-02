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
            background-color: #1ABC9C; /* Warna sidebar lebih segar */
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
            background-color: #16A085; /* Warna item menu sidebar */
            transition: background-color 0.3s ease;
        }
        .sidebar ul li a:hover {
            background-color: #148F77; /* Warna hover */
        }
        .sidebar ul li a i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .sidebar .logout {
            text-align: center;
            padding: 10px 20px;
            background-color: red;
            border-radius: 4px;
            margin: 0 20px 10px 20px;
            transition: background-color 0.3s ease;
        }
        .sidebar .logout a {
            color: darkred;
            text-decoration: none;
            font-weight: bold;
            display: block;
        }
        .sidebar .logout a:hover {
            background-color: #C0392B; /* Warna hover untuk tombol logout */
        }
        .sidebar .logout a i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
            background-color: #ECF0F1;
            min-height: 100vh;
        }
        h2 {
            color: #2980B9; /* Warna judul yang segar */
            margin-bottom: 20px;
        }
        p {
            color: #34495E; /* Warna teks konten */
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="upu.jpg" alt="Logo Penilaian Kinerja">
        </div>
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
              <li><a href="kelola_penilaian.php"><i class="fas fa-chart-line"></i> Kelola Penilaian</a></li>
              
            <li><a href="lihat_penilaian.php"><i class="fas fa-chart-line"></i> Lihat Penilaian</a></li>
            <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>

            <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Selamat Datang, Pimpinan!</h2>
        <p>Di sini Anda dapat melihat hasil penilaian pegawai, menentukan reward, dan melihat laporan tahunan.</p>
    </div>
</body>
</html>
