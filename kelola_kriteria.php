<?php
session_start();

// Periksa apakah pengguna sudah login sebagai admin
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'config/koneksi.php';

// Variabel untuk pesan keberhasilan atau kesalahan
$message = '';
$show_form = true;

// Fungsi untuk menambahkan atau mengedit kriteria penilaian
if (isset($_POST['save_kriteria'])) {
    $nama_kriteria = $_POST['nama_kriteria'];
    $deskripsi = $_POST['deskripsi'];
    $bobot = str_replace(',', '.', $_POST['bobot']); // Ubah koma menjadi titik untuk penyimpanan
    $kategori = $_POST['kategori'];

    if ($_POST['action'] == 'add') {
        // Tambahkan kriteria ke tabel kriteria_penilaian
        $query = "INSERT INTO kriteria_penilaian (nama_kriteria, deskripsi, bobot, kategori) 
                  VALUES ('$nama_kriteria', '$deskripsi', '$bobot', '$kategori')";
        if ($conn->query($query)) {
            $message = 'Kriteria berhasil ditambahkan!';
        } else {
            $message = 'Terjadi kesalahan: ' . $conn->error;
        }
    } elseif ($_POST['action'] == 'edit') {
        $id_kriteria = $_POST['id_kriteria'];
        // Perbarui data di tabel kriteria_penilaian
        $query = "UPDATE kriteria_penilaian SET nama_kriteria='$nama_kriteria', deskripsi='$deskripsi', bobot='$bobot', kategori='$kategori' 
                  WHERE id_kriteria='$id_kriteria'";
        if ($conn->query($query)) {
            $message = 'Kriteria berhasil diperbarui!';
        } else {
            $message = 'Terjadi kesalahan: ' . $conn->error;
        }
    }

    $show_form = false;
}

// Fungsi untuk menghapus kriteria
if (isset($_GET['delete'])) {
    $id_kriteria = $_GET['delete'];
    $query = "DELETE FROM kriteria_penilaian WHERE id_kriteria='$id_kriteria'";
    if ($conn->query($query)) {
        $message = 'Kriteria berhasil dihapus!';
    } else {
        $message = 'Terjadi kesalahan: ' . $conn->error;
    }
}

// Fungsi untuk menampilkan data kriteria yang akan diedit
$edit_data = [];
if (isset($_GET['edit'])) {
    $id_kriteria = $_GET['edit'];
    $query = "SELECT * FROM kriteria_penilaian WHERE id_kriteria='$id_kriteria'";
    $result_edit = $conn->query($query);
    if ($result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
        $show_form = true;
    }
}

// Ambil semua data kriteria dari database
$query = "SELECT * FROM kriteria_penilaian";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kriteria Penilaian - Admin Dashboard</title>
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
        .add-form input[type="text"], .add-form input[type="number"], .add-form select, .add-form textarea {
            padding: 10px;
            width: calc(100% - 20px);
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .add-form input[type="number"] {
            /* Menampilkan nilai desimal dengan koma */
            -moz-appearance: textfield;
        }
        .add-form input[type="number"]::-webkit-outer-spin-button,
        .add-form input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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
            <div class="title"><i class="fas fa-tasks"></i> Admin Dashboard - Kelola Kriteria Penilaian</div>
        </div>
        <h2><i class="fas fa-list-alt"></i> Kelola Kriteria Penilaian</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><i class="fas fa-check-circle"></i> <?= $message; ?></div>
        <?php endif; ?>

        <div class="add-form-container">
            <button class="toggle-btn" onclick="toggleForm()">
                <i class="fas fa-plus-circle"></i> <?= isset($edit_data['id_kriteria']) ? 'Edit Kriteria' : 'Tambah Kriteria'; ?>
            </button>
            <form action="kelola_kriteria.php" method="post" class="add-form" style="<?= isset($edit_data['id_kriteria']) ? 'display:block;' : 'display:none;'; ?>">
                <input type="hidden" name="action" value="<?= isset($edit_data['id_kriteria']) ? 'edit' : 'add'; ?>">
                <?php if (isset($edit_data['id_kriteria'])): ?>
                    <input type="hidden" name="id_kriteria" value="<?= $edit_data['id_kriteria']; ?>">
                <?php endif; ?>
                <label for="nama_kriteria">Nama Kriteria:</label>
                <input type="text" name="nama_kriteria" required value="<?= isset($edit_data['nama_kriteria']) ? $edit_data['nama_kriteria'] : ''; ?>">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi" rows="3"><?= isset($edit_data['deskripsi']) ? $edit_data['deskripsi'] : ''; ?></textarea>
                <label for="bobot">Bobot (%):</label>
                <input type="number" step="0.01" name="bobot" required value="<?= isset($edit_data['bobot']) ? str_replace('.', ',', $edit_data['bobot']) : ''; ?>">
                <label for="kategori">Kategori:</label>
                <select name="kategori" required>
                    <option value="Disiplin" <?= isset($edit_data['kategori']) && $edit_data['kategori'] == 'Disiplin' ? 'selected' : ''; ?>>Disiplin</option>
                    <option value="Sikap Kerja" <?= isset($edit_data['kategori']) && $edit_data['kategori'] == 'Sikap Kerja' ? 'selected' : ''; ?>>Sikap Kerja</option>
                    <option value="Potensi dan Kemampuan" <?= isset($edit_data['kategori']) && $edit_data['kategori'] == 'Potensi dan Kemampuan' ? 'selected' : ''; ?>>Potensi dan Kemampuan</option>
                    <option value="Hasil Kerja" <?= isset($edit_data['kategori']) && $edit_data['kategori'] == 'Hasil Kerja' ? 'selected' : ''; ?>>Hasil Kerja</option>
                </select>
                <button type="submit" name="save_kriteria"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Kriteria</th>
                    <th>Nama Kriteria</th>
                    <th>Deskripsi</th>
                    <th>Bobot (%)</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_kriteria']; ?></td>
                        <td><?= $row['nama_kriteria']; ?></td>
                        <td><?= $row['deskripsi']; ?></td>
                        <td><?= str_replace('.', ',', $row['bobot']); ?></td>
                        <td><?= $row['kategori']; ?></td>
                        <td>
                            <a href="kelola_kriteria.php?edit=<?= $row['id_kriteria']; ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a> |
                            <a href="kelola_kriteria.php?delete=<?= $row['id_kriteria']; ?>" class="delete-btn" onclick="return confirm('Anda yakin ingin menghapus kriteria ini?')"><i class="fas fa-trash-alt"></i> Hapus</a>
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
