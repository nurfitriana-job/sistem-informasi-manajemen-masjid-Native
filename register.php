<?php
session_start();
include 'config/db.php';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $telegram_id = mysqli_real_escape_string($conn, $_POST['telegram_id']); // Optional

    // Validasi kolom wajib
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom wajib diisi kecuali ID Telegram!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } else {
        // Cek apakah email sudah terdaftar
        $cekEmail = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($cekEmail) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Hash password MD5 agar sesuai dengan sistem login
            $hashedPassword = md5($password);

            // Insert user baru
            $query = mysqli_query($conn, "INSERT INTO users (nama, email, password, telegram_chat_id, role) VALUES ('$nama', '$email', '$hashedPassword', '$telegram_id', 'user')");
            if ($query) {
                $success = "Pendaftaran berhasil! Silakan login.";
                header("refresh:2; url=login.php");
            } else {
                $error = "Terjadi kesalahan, coba lagi!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Jamaah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .register-card {
            max-width: 420px;
            margin: 80px auto;
            padding: 25px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .register-card img { width: 150px; margin-bottom: 15px; }
        .register-card h3 { color: #198754; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="register-card">
    <!-- Logo -->
    <img src="assets/img/logo-masjid.png" alt="Logo Masjid">
    <h3 class="fw-bold">Register Jamaah</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama" required>
        </div>
        <div class="mb-3 text-start">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
        </div>
        <div class="mb-3 text-start">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <div class="mb-3 text-start">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password" required>
        </div>
        <div class="mb-3 text-start">
            <label>ID Chat Telegram (Opsional)</label>
            <input type="text" name="telegram_id" class="form-control" placeholder="Masukkan ID Chat Telegram">
            <small class="text-muted">Opsional. Digunakan untuk menerima notifikasi via Telegram.</small>
        </div>
        <button type="submit" name="register" class="btn btn-success w-100">Daftar</button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none">← Kembali ke Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
