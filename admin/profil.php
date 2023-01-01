<?php
include '../config/db.php';
include 'layout/header.php';  // Sudah ada session_start() di sini
include 'layout/sidebar.php';

// Pastikan user sudah login
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Silakan login dulu'); location.href='../login.php';</script>";
    exit();
}

$idUser = $_SESSION['id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$idUser'"));

// Update Profil
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email' WHERE id='$idUser'");
    echo "<script>alert('Profil berhasil diperbarui'); location.href='profil.php';</script>";
}

// Ganti Password
if (isset($_POST['update_password'])) {
    $pass_lama = md5($_POST['password_lama']); 
    $pass_baru = $_POST['password_baru'];
    $pass_konfirmasi = $_POST['password_konfirmasi'];

    if ($pass_lama === $user['password']) {
        if ($pass_baru === $pass_konfirmasi) {
            $hashBaru = md5($pass_baru);
            mysqli_query($conn, "UPDATE users SET password='$hashBaru' WHERE id='$idUser'");
            echo "<script>alert('Password berhasil diubah'); location.href='profil.php';</script>";
        } else {
            echo "<script>alert('Konfirmasi password tidak cocok');</script>";
        }
    } else {
        echo "<script>alert('Password lama salah');</script>";
    }
}
?>
<div class="container-fluid mt-4">
    <h3 class="mb-4">Profil Saya</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm mb-4">
                <h5>Edit Profil</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button class="btn btn-success" name="update_profil">Update Profil</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 shadow-sm mb-4">
                <h5>Ganti Password</h5>
                <form method="POST">
                    <div class="mb-3"><label>Password Lama</label><input type="password" name="password_lama" class="form-control" required></div>
                    <div class="mb-3"><label>Password Baru</label><input type="password" name="password_baru" class="form-control" required></div>
                    <div class="mb-3"><label>Konfirmasi Password</label><input type="password" name="password_konfirmasi" class="form-control" required></div>
                    <button class="btn btn-warning" name="update_password">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'layout/footer.php'; ?>
