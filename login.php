<?php
// Menghubungkan ke database
include 'config/koneksi.php';

// Mulai session untuk menyimpan informasi login
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Periksa apakah username dan password adalah admin
    if ($username == "admin" && $password == "admin") {
        // Set session admin
        $_SESSION['user'] = 'admin';
        
        // Redirect ke halaman menu admin
        header("Location: admin_menu.php");
        exit();
    } 
    // Periksa apakah username dan password adalah pimpinan
    elseif ($username == "pimpinan" && $password == "pimpinan") {
        // Set session pimpinan
        $_SESSION['user'] = 'pimpinan';
        
        // Redirect ke halaman menu pimpinan
        header("Location: home.php");
        exit();
    } 
    else {
        // Pesan kesalahan jika login gagal
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Penilaian Kinerja</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #000428, #004e92);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            color: #ffffff;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            animation: fadeIn 0.8s ease-in-out;
            backdrop-filter: blur(10px);
        }
        .login-title {
            margin-bottom: 25px;
            font-size: 30px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }
        .login-title::after {
            content: '';
            width: 60px;
            height: 4px;
            background-color: #00c6ff;
            display: block;
            margin: 10px auto 0;
        }
        .form-label {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }
        .login-container img {
            display: block;
            margin: 0 auto 10px;
            max-width: 150%;
        .form-control {
            border-radius: 50px;
            padding: 12px 20px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #00c6ff;
            box-shadow: 0 0 0 0.2rem rgba(0, 198, 255, 0.25);
        }
        .btn-primary {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            border: none;
            border-radius: 50px;
            padding: 12px 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 198, 255, 0.3);
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #ffffff;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
 <div class="login-container">
    <img src="img/logo.jpg" alt="Logo">
<div class="login-container">
    <h2 class="login-title">Login</h2>
    <form action="login.php" method="post">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <?php
    // Tampilkan pesan kesalahan jika ada
    if (isset($error)) {
        echo "<p class='text-danger text-center mt-3'>$error</p>";
    }
    ?>
    
    <div class="footer">
        <p>&copy; 2024 Misti Harita</p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
