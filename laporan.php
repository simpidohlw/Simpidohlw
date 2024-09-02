<?php
session_start();

// Periksa apakah pengguna sudah login sebagai pimpinan
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'pimpinan') {
    header("Location: login.php");
    exit();
}

include 'config/koneksi.php';

// Dapatkan tahun dari parameter GET, atau gunakan tahun saat ini jika tidak ada
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Variabel untuk menyimpan jumlah pegawai dan jumlah reward
$jumlah_pegawai = 0;
$jumlah_reward = 0;
$pegawai = [];

if ($tahun) {
    // Dapatkan pegawai dengan nilai tertinggi untuk tahun tertentu
    $query_max = "SELECT id_pegawai, MAX(nilai) AS nilai_tertinggi 
                  FROM penilaian 
                  WHERE YEAR(tanggal) = '$tahun'
                  GROUP BY id_pegawai 
                  ORDER BY nilai_tertinggi DESC 
                  LIMIT 1";
    $result_max = $conn->query($query_max);

    if ($result_max && $result_max->num_rows > 0) {
        $pegawai_terbaik = $result_max->fetch_assoc();
        $id_pegawai_terbaik = $pegawai_terbaik['id_pegawai'];
    } else {
        $id_pegawai_terbaik = null;
    }

    // Ambil data pegawai untuk tahun tertentu
    $query = "SELECT peg.id_pegawai, peg.nama, peg.jabatan, peg.pendidikan, peg.lama_bekerja, 
                     IF(peg.id_pegawai = '$id_pegawai_terbaik', 'Ya', '-') as penerima_reward
              FROM pegawai peg
              LEFT JOIN penilaian p ON peg.id_pegawai = p.id_pegawai AND YEAR(p.tanggal) = '$tahun'
              WHERE p.id_penilaian IS NOT NULL";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pegawai[] = $row;
            $jumlah_pegawai++;
            if ($row['penerima_reward'] == 'Ya') {
                $jumlah_reward++;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tahunan - Penilaian Kinerja</title>
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
            background-color: #1ABC9C;
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
            background-color: #16A085;
            transition: background-color 0.3s ease;
        }
        .sidebar ul li a:hover {
            background-color: #148F77;
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
            background-color: #C0392B;
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
        .print-buttons {
            margin-top: 20px;
        }
        .print-buttons button {
            padding: 10px 20px;
            background-color: #2980B9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .print-buttons button:hover {
            background-color: #2573a6;
        }
        .no-data {
            color: #C0392B;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
    <script>
        function printPDF() {
            window.location.href = 'cetak_laporan.php?tahun=<?= htmlspecialchars($tahun); ?>&format=pdf';
        }

        function printExcel() {
            window.location.href = 'cetak_laporan.php?tahun=<?= htmlspecialchars($tahun); ?>&format=excel';
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <a href="admin_menu.php">
                <img src="upu.jpg" alt="Logo Penilaian Kinerja">
            </a>
        </div>
        <ul>
            <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="lihat_penilaian.php"><i class="fas fa-chart-line"></i> Lihat Penilaian</a></li>
            <li><a href="laporan.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
            <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Laporan Tahun <?= htmlspecialchars($tahun); ?></h2>

        <form method="get" action="">
            <label for="tahun">Pilih Tahun:</label>
            <input type="number" name="tahun" id="tahun" value="<?= htmlspecialchars($tahun); ?>" required>
            <button type="submit">Lihat Laporan</button>
        </form>

        <?php if ($tahun && empty($pegawai)): ?>
            <p class="no-data">Laporan tidak tersedia untuk tahun <?= htmlspecialchars($tahun); ?>.</p>
        <?php elseif (!empty($pegawai)): ?>
            <div class="report-summary">
                <h3>Laporan Tahun <?= htmlspecialchars($tahun); ?></h3>
                <p>Jumlah Pegawai: <?= $jumlah_pegawai; ?></p>
                <p>Jumlah Pegawai yang Mendapatkan Reward: <?= $jumlah_reward; ?></p>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Pegawai</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Pendidikan</th>
                                <th>Lama Bekerja</th>
                                <th>Penerima Reward</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pegawai as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id_pegawai']); ?></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                <td><?= htmlspecialchars($row['pendidikan']); ?></td>
                                <td><?= htmlspecialchars($row['lama_bekerja']); ?></td>
                                <td><?= htmlspecialchars($row['penerima_reward']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="print-buttons">
                <button onclick="printPDF()">Cetak PDF</button>
                <button onclick="printExcel()">Cetak Excel</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>
