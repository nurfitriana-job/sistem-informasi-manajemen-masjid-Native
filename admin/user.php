<?php
include '../config/db.php';
include 'layout/header.php';
include 'layout/sidebar.php';

// Proteksi halaman hanya untuk admin
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses ditolak'); location.href='../login.php';</script>";
    exit();
}

// Ambil data users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");

// Tambah User
if (isset($_POST['tambah_user'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // MD5 sesuai sistem lama
    $role = $_POST['role'];
    $telegram_id = mysqli_real_escape_string($conn, $_POST['telegram_id']);

    mysqli_query($conn, "INSERT INTO users (nama, email, password, role, telegram_chat_id) VALUES ('$nama','$email','$password','$role','$telegram_id')");
    echo "<script>alert('User berhasil ditambahkan'); location.href='user.php';</script>";
}

// Edit User
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];
    $telegram_id = mysqli_real_escape_string($conn, $_POST['telegram_id']);

    mysqli_query($conn, "UPDATE users SET nama='$nama', email='$email', role='$role', telegram_chat_id='$telegram_id' WHERE id='$id'");
    echo "<script>alert('User berhasil diperbarui'); location.href='user.php';</script>";
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    echo "<script>alert('User berhasil dihapus'); location.href='user.php';</script>";
}
?>

<div class="container-fluid mt-4">
    <h3 class="mb-4">Manajemen User</h3>

    <!-- Tombol Tambah -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahUser">+ Tambah User</button>

    <!-- Tabel Users -->
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #198754;">Daftar User</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Telegram ID</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= $u['id']; ?></td>
                            <td><?= htmlspecialchars($u['nama']); ?></td>
                            <td><?= htmlspecialchars($u['email']); ?></td>
                            <td><?= ucfirst($u['role']); ?></td>
                            <td><?= $u['telegram_chat_id'] ? htmlspecialchars($u['telegram_chat_id']) : '-'; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditUser<?= $u['id']; ?>">Edit</button>
                                <a href="user.php?hapus=<?= $u['id']; ?>" onclick="return confirm('Hapus user ini?')" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        </tr>

                        <!-- Modal Edit User -->
                        <div class="modal fade" id="modalEditUser<?= $u['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $u['id']; ?>">
                                            <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($u['nama']); ?>" required></div>
                                            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']); ?>" required></div>
                                            <div class="mb-3">
                                                <label>Role</label>
                                                <select name="role" class="form-select">
                                                    <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="user" <?= $u['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                </select>
                                            </div>
                                            <div class="mb-3"><label>Telegram Chat ID</label><input type="text" name="telegram_id" class="form-control" value="<?= htmlspecialchars($u['telegram_chat_id']); ?>"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-warning" name="edit_user">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3"><label>Nama</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="admin">Admin</option>
                            <option value="user" selected>User</option>
                        </select>
                    </div>
                    <div class="mb-3"><label>Telegram Chat ID</label><input type="text" name="telegram_id" class="form-control" placeholder="Masukkan Chat ID Telegram (Opsional)"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" name="tambah_user">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
