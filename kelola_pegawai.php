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

// Fungsi untuk menambahkan atau mengedit data pegawai
if (isset($_POST['save'])) {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $pendidikan = $_POST['pendidikan'];
    $lama_bekerja = $_POST['lama_bekerja'];
    $kedisiplinan = $_POST['kedisiplinan'];
    $jurusan = $_POST['jurusan'];

    if ($_POST['action'] == 'add') {
        $query = "INSERT INTO pegawai (nama, jabatan, pendidikan, lama_bekerja, kedisiplinan, jurusan) 
                  VALUES ('$nama', '$jabatan', '$pendidikan', '$lama_bekerja', '$kedisiplinan', '$jurusan')";
        if ($conn->query($query)) {
            $message = 'Pegawai berhasil ditambahkan!';
        } else {
            $message = 'Terjadi kesalahan: ' . $conn->error;
        }
    } elseif ($_POST['action'] == 'edit') {
        $id_pegawai = $_POST['id_pegawai'];
        $query = "UPDATE pegawai SET nama='$nama', jabatan='$jabatan', pendidikan='$pendidikan', lama_bekerja='$lama_bekerja',
                  kedisiplinan='$kedisiplinan', jurusan='$jurusan' WHERE id_pegawai='$id_pegawai'";
        if ($conn->query($query)) {
            $message = 'Data pegawai berhasil diperbarui!';
        } else {
            $message = 'Terjadi kesalahan: ' . $conn->error;
        }
    }

    $show_form = false;
}

// Fungsi untuk menghapus data pegawai
if (isset($_GET['delete'])) {
    $id_pegawai = $_GET['delete'];
    try {
        $query = "DELETE FROM pegawai WHERE id_pegawai='$id_pegawai'";
        if ($conn->query($query)) {
            $message = 'Pegawai berhasil dihapus!';
        }
    } catch (mysqli_sql_exception $e) {
        // Tangkap kesalahan foreign key constraint
        $message = 'Data tidak dapat dihapus karena sudah diberikan penilaian. Jika Anda ingin menghapus, silakan hapus data penilaian terlebih dahulu.';
    }
}

// Fungsi untuk menampilkan data pegawai yang akan diedit
$edit_data = [];
if (isset($_GET['edit'])) {
    $id_pegawai = $_GET['edit'];
    $query = "SELECT * FROM pegawai WHERE id_pegawai='$id_pegawai'";
    $result_edit = $conn->query($query);
    if ($result_edit->num_rows > 0) {
        $edit_data = $result_edit->fetch_assoc();
        $show_form = true;
    }
}

// Ambil data pegawai dari database
$query = "SELECT * FROM pegawai";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pegawai - Penilaian Kinerja</title>
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
        .sidebar ul li.logout {
            margin-top: auto;
        }
        .sidebar ul li.logout a {
            background-color: #E74C3C;
            color: #fff;
            text-align: center;
            width: calc(100% - 40px);
            padding: 10px 20px;
            border-radius: 4px;
        }
        .sidebar ul li.logout a:hover {
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
        .add-form input[type="text"], .add-form select {
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
            <div class="title"><i class="fas fa-user-cog"></i> Admin Dashboard</div>
        </div>
        <h2><i class="fas fa-users-cog"></i> Kelola Pegawai</h2>

        <?php if ($message != ''): ?>
            <div class="message"><i class="fas fa-check-circle"></i> <?= $message; ?></div>
        <?php endif; ?>

        <div class="add-form-container">
            <button class="toggle-btn" onclick="toggleForm()">
                <i class="fas fa-plus-circle"></i> <?= isset($edit_data['id_pegawai']) ? 'Edit Pegawai' : 'Tambah Pegawai'; ?>
            </button>
            <form action="kelola_pegawai.php" method="post" class="add-form" style="<?= isset($edit_data['id_pegawai']) ? 'display:block;' : 'display:none;'; ?>">
                <input type="hidden" name="action" value="<?= isset($edit_data['id_pegawai']) ? 'edit' : 'add'; ?>">
                <?php if (isset($edit_data['id_pegawai'])): ?>
                    <input type="hidden" name="id_pegawai" value="<?= $edit_data['id_pegawai']; ?>">
                <?php endif; ?>
                <input type="text" name="nama" placeholder="Nama Pegawai" required value="<?= isset($edit_data['nama']) ? $edit_data['nama'] : ''; ?>">
                <input type="text" name="jabatan" placeholder="Jabatan" required value="<?= isset($edit_data['jabatan']) ? $edit_data['jabatan'] : ''; ?>">
                <input type="text" name="pendidikan" placeholder="Pendidikan" required value="<?= isset($edit_data['pendidikan']) ? $edit_data['pendidikan'] : ''; ?>">
                <input type="text" name="lama_bekerja" placeholder="Lama Bekerja" required value="<?= isset($edit_data['lama_bekerja']) ? $edit_data['lama_bekerja'] : ''; ?>">
                <input type="text" name="kedisiplinan" placeholder="Kedisiplinan" required value="<?= isset($edit_data['kedisiplinan']) ? $edit_data['kedisiplinan'] : ''; ?>">
                <input type="text" name="jurusan" placeholder="Jurusan" required value="<?= isset($edit_data['jurusan']) ? $edit_data['jurusan'] : ''; ?>">
                <button type="submit" name="save"><i class="fas fa-save"></i> Simpan</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>JABATAN</th>
                    <th>PENDIDIKAN</th>
                    <th>LAMA BEKERJA</th>
                    <th>KEDISIPLINAN</th>
                    <th>JURUSAN</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nama']; ?></td>
                        <td><?= $row['jabatan']; ?></td>
                        <td><?= $row['pendidikan']; ?></td>
                        <td><?= $row['lama_bekerja']; ?></td>
                        <td><?= $row['kedisiplinan']; ?></td>
                        <td><?= $row['jurusan']; ?></td>
                        <td>
                            <a href="kelola_pegawai.php?edit=<?= $row['id_pegawai']; ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
                            <a href="kelola_pegawai.php?delete=<?= $row['id_pegawai']; ?>" class="delete-btn" onclick="return confirm('Anda yakin ingin menghapus pegawai ini?')"><i class="fas fa-trash-alt"></i> Hapus</a>
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
