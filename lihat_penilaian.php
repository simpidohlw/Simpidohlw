<?php
session_start();

// Periksa apakah pengguna sudah login sebagai pimpinan
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'pimpinan') {
    header("Location: login.php");
    exit();
}

include 'config/koneksi.php';

// Ambil data penilaian dari database
$query = "SELECT p.id_penilaian, peg.nama, p.nilai, p.tanggal, h.hasil 
          FROM penilaian p 
          JOIN pegawai peg ON p.id_pegawai = peg.id_pegawai
          LEFT JOIN hasil_penilaian h ON p.id_penilaian = h.id_penilaian";
$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);
}

// Query untuk mendapatkan pegawai dengan nilai tertinggi
$top_performer_query = "SELECT peg.nama, MAX(p.nilai) as nilai_tertinggi 
                        FROM penilaian p 
                        JOIN pegawai peg ON p.id_pegawai = peg.id_pegawai
                        GROUP BY peg.nama 
                        ORDER BY nilai_tertinggi DESC 
                        LIMIT 1";
$top_performer_result = $conn->query($top_performer_query);
$top_performer = $top_performer_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Penilaian - Penilaian Kinerja</title>
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
            margin-top: auto;
            text-align: center;
            padding: 10px 20px;
            background-color: #E74C3C;
            border-radius: 4px;
            margin: 0 20px 10px 20px;
            transition: background-color 0.3s ease;
        }
        .sidebar .logout a {
            color: #fff;
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
        .table-container {
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2980B9;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2980B9;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            margin-bottom: 20px;
            cursor: pointer;
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
            <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="lihat_penilaian.php"><i class="fas fa-chart-line"></i> Lihat Penilaian</a></li>
            <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li> <!-- Menu Laporan -->
             <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a></li>
        </ul>
      </div>

    <div class="content">
        <h2>Lihat Penilaian Pegawai</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Penilaian</th>
                        <th>Nama Pegawai</th>
                        <th>Nilai</th>
                        <th>Tanggal</th>
                        <th>Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_penilaian']); ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['nilai']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal']); ?></td>
                        <td><?= htmlspecialchars($row['hasil']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="reward.php" class="action-button">Reward</a>
    </div>
</body>
</html>
