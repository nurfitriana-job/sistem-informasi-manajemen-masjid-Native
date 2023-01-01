<?php
session_start();
include 'config/db.php';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // MD5 sesuai data awal

    // Ambil data user berdasarkan email & password
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        // Simpan session
        $_SESSION['id'] = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        // Redirect sesuai role
        if ($data['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-card {
            max-width: 400px;
            margin: 80px auto;
            padding: 25px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-card img {
            width: 150px;
            margin-bottom: 0.5px;
        }
        .login-card h3 {
            color: #198754; /* Hijau Bootstrap */
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Logo di atas login -->
    <img src="assets/img/logo-masjid.png" alt="Logo Masjid">
    <h3 class="fw-bold">Login</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label>Email</label>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
        </div>
        <div class="mb-3 text-start">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="login" class="btn btn-success w-100">Login</button>
        <div class="text-center mt-3">
  Belum punya akun?  <a href="register.php" class="text-decoration-none"> Daftar di sini</a>
</div>
    </form>

    <div class="text-center mt-3">
        <a href="index.php" class="text-decoration-none">← Kembali ke Beranda</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
