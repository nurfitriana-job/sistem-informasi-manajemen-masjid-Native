<?php
session_start();
include '../config/db.php';
include 'layout/header.php';
include 'layout/sidebar.php';

// Pastikan user login
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'user') {
    echo "<script>alert('Akses ditolak'); location.href='../login.php';</script>";
    exit();
}

$userId = $_SESSION['id'];

// Ambil data user
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$userId'"));

// Update profil (nama & email)
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email' WHERE id='$userId'");
    if ($update) {
        $_SESSION['nama'] = $nama; // update session nama
        echo "<script>alert('Profil berhasil diperbarui'); location.href='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}

// Ganti password
if (isset($_POST['ganti_password'])) {
    $password_lama = md5($_POST['password_lama']);
    $password_baru = md5($_POST['password_baru']);
    $konfirmasi = md5($_POST['konfirmasi']);

    if ($password_lama != $user['password']) {
        echo "<script>alert('Password lama salah!');</script>";
    } elseif ($password_baru != $konfirmasi) {
        echo "<script>alert('Konfirmasi password baru tidak cocok!');</script>";
    } else {
        mysqli_query($conn, "UPDATE users SET password='$password_baru' WHERE id='$userId'");
        echo "<script>alert('Password berhasil diganti'); location.href='profil.php';</script>";
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Profil Saya</h2>

    <div class="row">
        <div class="col-md-6">
            <!-- Card Update Profil -->
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #00a77dff;">
  Update Profil
</div>

                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <button class="btn" name="update_profil" style="background-color: #00a77dff; border-color: #00a77dff; color: white;">
  Simpan Perubahan
</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Card Ganti Password -->
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background-color: #00a77dff;">
  Ganti Password
</div>

                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Password Lama</label>
                            <input type="password" name="password_lama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi" class="form-control" required>
                        </div>
                        <button class="btn" name="ganti_password" style="background-color: #00a77dff; border-color: #00a77dff; color: white;">
  Update Password
</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
