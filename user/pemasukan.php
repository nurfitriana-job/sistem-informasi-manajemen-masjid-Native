<?php
session_start();
include '../config/db.php';
include 'layout/header.php';
include 'layout/sidebar.php';

// Pastikan user login & role user
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'user') {
    echo "<script>alert('Akses ditolak'); location.href='../login.php';</script>";
    exit();
}

$userId = $_SESSION['id'];

// Ambil data pemasukan milik user
$pemasukanUser = mysqli_query($conn, "
    SELECT * FROM keuangan_pemasukan 
    WHERE user_id='$userId' 
    ORDER BY tanggal DESC
");

// Proses upload bukti
if (isset($_POST['upload_bukti'])) {
    $idPemasukan = $_POST['id_pemasukan'];
    $bukti = '';

    if (!empty($_FILES['bukti']['name'])) {
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $bukti = time() . '.' . $ext;
        move_uploaded_file($_FILES['bukti']['tmp_name'], "../uploads/" . $bukti);

        // Update status jadi pending lagi
        mysqli_query($conn, "UPDATE keuangan_pemasukan 
            SET bukti_pembayaran='$bukti', status='pending' 
            WHERE id='$idPemasukan' AND user_id='$userId'");

        echo "<script>alert('Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.'); location.href='pemasukan.php';</script>";
    }
}
?>

<div class="container-fluid mt-4">
    <h3 class="mb-4">Pemasukan Saya</h3>
    <div class="card shadow-sm">
<div class="card-header text-white" style="background-color: #00a77dff;">
  Daftar Donasi Anda
</div>

        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($pemasukanUser)): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                            <td><?= ucfirst($row['kategori']); ?></td>
                            <td>Rp <?= number_format($row['nominal'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td>
                                <?php
                                if ($row['status'] == 'verified') {
                                    echo "<span class='badge bg-success'>Terverifikasi</span>";
                                } elseif ($row['status'] == 'pending') {
                                    echo "<span class='badge bg-warning text-dark'>Menunggu</span>";
                                } elseif ($row['status'] == 'rejected') {
                                    echo "<span class='badge bg-danger'>Ditolak</span>";
                                } else {
                                    echo "<span class='badge bg-secondary'>Belum Upload</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($row['bukti_pembayaran'])): ?>
                                    <a href="../uploads/<?= $row['bukti_pembayaran']; ?>" target="_blank" class="btn btn-info btn-sm">Lihat</a>
                                <?php else: ?>
                                    Belum ada
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($row['bukti_pembayaran']) || $row['status'] == 'rejected'): ?>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadBukti<?= $row['id']; ?>">
                                        <?= $row['status'] == 'rejected' ? 'Upload Ulang' : 'Upload' ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if ($row['status'] == 'rejected' && !empty($row['alasan_penolakan'])): ?>
                            <tr>
                                <td colspan="7" class="text-danger">
                                    <strong>Alasan Penolakan:</strong> <?= htmlspecialchars($row['alasan_penolakan']); ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <!-- Modal Upload Bukti -->
                        <div class="modal fade" id="uploadBukti<?= $row['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5>Upload Bukti Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_pemasukan" value="<?= $row['id']; ?>">
                                            <div class="mb-3">
                                                <label>Bukti Pembayaran (jpg/png)</label>
                                                <input type="file" name="bukti" class="form-control" accept="image/*" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-success" name="upload_bukti">Upload</button>
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

<?php include 'layout/footer.php'; ?>
