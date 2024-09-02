<?php
session_start();

// Periksa apakah pengguna sudah login sebagai admin atau pimpinan
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], ['admin', 'pimpinan'])) {
    header("Location: login.php");
    exit();
}

include 'config/koneksi.php';

// Variabel untuk pesan keberhasilan atau kesalahan
$message = '';
$show_form = true;

// Fungsi untuk menentukan hasil penilaian berdasarkan nilai akhir
function tentukanHasilPenilaian($nilai_total) {
    if ($nilai_total >= 90) {
        return "Sangat Baik";
    } elseif ($nilai_total >= 75) {
        return "Baik";
    } else {
        return "Cukup";
    }
}

// Fungsi untuk menambahkan atau mengedit data penilaian
if (isset($_POST['save'])) {
    $id_pegawai = $_POST['id_pegawai'];
    $nilai_total = 0;
    $tanggal = $_POST['tanggal'];

    // Ambil kriteria dan bobot dari database
    $query_kriteria = "SELECT id_kriteria, bobot FROM kriteria_penilaian";
    $result_kriteria = $conn->query($query_kriteria);

    // Hitung nilai total berdasarkan kriteria dan bobotnya
    while ($kriteria = $result_kriteria->fetch_assoc()) {
        $id_kriteria = $kriteria['id_kriteria'];
        $bobot = $kriteria['bobot'];
        $nilai_kriteria = $_POST['nilai_kriteria_' . $id_kriteria]; // Nilai untuk setiap kriteria

        // Hitung nilai akhir dengan bobot
        $nilai_total += $nilai_kriteria * ($bobot / 100);
    }

    if ($_POST['action'] == 'add') {
        // Tambahkan data ke tabel penilaian
        $query = "INSERT INTO penilaian (id_pegawai, kinerja, nilai, tanggal) 
                  VALUES ('$id_pegawai', 'Kinerja', '$nilai_total', '$tanggal')";
        if ($conn->query($query)) {
            $id_penilaian = $conn->insert_id; // Mendapatkan ID penilaian yang baru ditambahkan
            $hasil = tentukanHasilPenilaian($nilai_total);

            // Tambahkan data ke tabel hasil_penilaian
            $query_hasil = "INSERT INTO hasil_penilaian (id_penilaian, id_pegawai, nilai_total, tanggal, hasil) 
                            VALUES ('$id_penilaian', '$id_pegawai', '$nilai_total', '$tanggal', '$hasil')";
            $conn->query($query_hasil);
            $message = 'Penilaian berhasil ditambahkan!';
        } else {
            $message = 'Terjadi kesalahan: ' . $conn->error;
        }
    } elseif ($_POST['action'] == 'edit') {
        $id_penilaian = $_POST['id_penilaian'];
        // Perbarui data di tabel penilaian
        $query = "UPDATE penilaian SET id_pegawai='$id_pegawai', nilai='$nilai_total', tanggal='$tanggal' 
                  WHERE id_penilaian='$id_penilaian'";
        if ($conn->query($query)) {
            $hasil = tentukanHasilPenilaian($nilai_total);

            // Perbarui data di tabel hasil_penilaian
            $query_hasil = "UPDATE hasil_penilaian SET nilai_total='$nilai_total', tanggal='$tanggal', hasil='$hasil' 
                            WHERE id_penilaian='$id_penilaian'";
            $conn->query($query_hasil);
            $message = 'Data penilaian berhasil diperbarui!';
        } else {
            $message = 'Terjadi kesalahan: ' . $conn->error;
        }
    }

    $show_form = false;
}

// Fungsi untuk menghapus data penilaian
if (isset($_GET['delete'])) {
    $id_penilaian = $_GET['delete'];
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Hapus data dari tabel hasil_penilaian terlebih dahulu
        $query_hasil = "DELETE FROM hasil_penilaian WHERE id_penilaian='$id_penilaian'";
        if (!$conn->query($query_hasil)) {
            throw new Exception('Terjadi kesalahan saat menghapus dari tabel hasil_penilaian: ' . $conn->error);
        }

        // Hapus data dari tabel penilaian
        $query = "DELETE FROM penilaian WHERE id_penilaian='$id_penilaian'";
        if (!$conn->query($query)) {
            throw new Exception('Terjadi kesalahan saat menghapus dari tabel penilaian: ' . $conn->error);
        }

        // Commit transaksi
        $conn->commit();
        $message = 'Penilaian berhasil dihapus!';
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        $message = $e->getMessage();
    }
}

// Fungsi untuk menampilkan data penilaian yang akan diedit
$edit_data = [];
if (isset($_GET['edit'])) {
    $id_penilaian = $_GET['edit'];
    $query = "SELECT * FROM penilaian WHERE id_penilaian='$id_penilaian'";
    $result_edit = $conn->query($query);
    if ($result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
        $show_form = true;
    }
}

// Ambil data penilaian dari database
$query = "SELECT p.id_penilaian, peg.nama, p.nilai, p.tanggal, h.hasil 
          FROM penilaian p 
          JOIN pegawai peg ON p.id_pegawai = peg.id_pegawai
          LEFT JOIN hasil_penilaian h ON p.id_penilaian = h.id_penilaian";
$result = $conn->query($query);

// Ambil data pegawai untuk dropdown
$query_pegawai = "SELECT id_pegawai, nama FROM pegawai";
$result_pegawai = $conn->query($query_pegawai);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penilaian - Penilaian Kinerja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            flex-grow: 1;
            display: flex;
            flex-direction: column;
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
        .sidebar ul li.logout {
            margin-top: 1px;
            background-color: #E74C3C;
            border-radius: 4px;
            text-align: center;
        }
        .sidebar ul li.logout a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            display: block;
            padding: 3px;
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
        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .add-form-container {
            margin-bottom: 20px;
        }
        .add-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .add-form input[type="number"], .add-form input[type="date"], .add-form select {
            padding: 10px;
            width: calc(100% - 20px);
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .add-form button {
            padding: 10px 20px;
            background-color: #2980B9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .add-form button i {
            margin-right: 5px;
        }
        .add-form button:hover {
            background-color: #2573a6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        .delete-btn, .edit-btn {
            color: #E74C3C;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }
        .edit-btn i, .delete-btn i {
            margin-right: 5px;
        }
        .edit-btn {
            color: #2980B9;
        }
        .delete-btn:hover, .edit-btn:hover {
            text-decoration: underline;
        }
        .toggle-btn {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #2980B9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .toggle-btn i {
            margin-right: 5px;
        }
        .toggle-btn:hover {
            background-color: #2573a6;
        }
    </style>
    <script>
        function toggleForm() {
            var form = document.querySelector('.add-form');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
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
            <li><a href="kelola_pegawai.php"><i class="fas fa-users"></i> Kelola Pegawai</a></li>
            <li><a href="kelola_penilaian.php"><i class="fas fa-chart-line"></i> Kelola Penilaian</a></li>
            <li><a href="kelola_kriteria.php"><i class="fas fa-list-alt"></i> Kelola Kriteria Penilaian</a></li>
            <li class="logout"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> LOG OUT</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <div class="title"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</div>
        </div>
        <h2><i class="fas fa-chart-line"></i> Kelola Penilaian</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><i class="fas fa-check-circle"></i> <?= $message; ?></div>
        <?php endif; ?>

        <div class="add-form-container">
            <button class="toggle-btn" onclick="toggleForm()">
                <i class="fas fa-plus-circle"></i> <?= isset($edit_data['id_penilaian']) ? 'Edit Penilaian' : 'Tambah Penilaian'; ?>
            </button>
            <form action="kelola_penilaian.php" method="post" class="add-form" style="<?= isset($edit_data['id_penilaian']) ? 'display:block;' : 'display:none;'; ?>">
                <input type="hidden" name="action" value="<?= isset($edit_data['id_penilaian']) ? 'edit' : 'add'; ?>">
                <?php if (isset($edit_data['id_penilaian'])): ?>
                    <input type="hidden" name="id_penilaian" value="<?= $edit_data['id_penilaian']; ?>">
                <?php endif; ?>
                <label for="id_pegawai">Pegawai:</label>
                <select name="id_pegawai" required>
                    <?php while ($pegawai = $result_pegawai->fetch_assoc()): ?>
                        <option value="<?= $pegawai['id_pegawai']; ?>" <?= isset($edit_data['id_pegawai']) && $edit_data['id_pegawai'] == $pegawai['id_pegawai'] ? 'selected' : ''; ?>><?= $pegawai['nama']; ?></option>
                    <?php endwhile; ?>
                </select>

                <!-- Menambahkan input untuk setiap kriteria penilaian -->
                <?php
                $query_kriteria = "SELECT * FROM kriteria_penilaian";
                $result_kriteria = $conn->query($query_kriteria);
                while ($kriteria = $result_kriteria->fetch_assoc()):
                ?>
                    <label for="nilai_kriteria_<?= $kriteria['id_kriteria']; ?>"><?= $kriteria['nama_kriteria']; ?> (Bobot: <?= $kriteria['bobot']; ?>%):</label>
                    <input type="number" name="nilai_kriteria_<?= $kriteria['id_kriteria']; ?>" placeholder="Nilai <?= $kriteria['nama_kriteria']; ?>" required value="<?= isset($edit_data['nilai']) ? $edit_data['nilai'] : ''; ?>">
                <?php endwhile; ?>

                <input type="date" name="tanggal" required value="<?= isset($edit_data['tanggal']) ? $edit_data['tanggal'] : ''; ?>">
                <button type="submit" name="save"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Penilaian</th>
                    <th>Nama Pegawai</th>
                    <th>Nilai</th>
                    <th>Tanggal</th>
                    <th>Hasil</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_penilaian']; ?></td>
                        <td><?= $row['nama']; ?></td>
                        <td><?= $row['nilai']; ?></td>
                        <td><?= $row['tanggal']; ?></td>
                        <td><?= $row['hasil']; ?></td>
                        <td>
                            <a href="kelola_penilaian.php?edit=<?= $row['id_penilaian']; ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
                            <a href="kelola_penilaian.php?delete=<?= $row['id_penilaian']; ?>" class="delete-btn" onclick="return confirm('Anda yakin ingin menghapus penilaian ini?')"><i class="fas fa-trash-alt"></i> Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>
