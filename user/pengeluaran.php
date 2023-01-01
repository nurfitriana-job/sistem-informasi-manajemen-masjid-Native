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

// Ambil semua pengeluaran
$pengeluaranList = mysqli_query($conn, "SELECT * FROM keuangan_pengeluaran ORDER BY tanggal DESC");

// Total pengeluaran
$totalPengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pengeluaran"));
?>

<div class="container-fluid">
    <h2 class="mb-4">Data Pengeluaran Masjid</h2>

    <!-- Total Pengeluaran -->
    <div class="card mb-4 shadow-sm text-center bg-light">
        <div class="card-body">
            <h5>Total Pengeluaran Masjid</h5>
            <h3 class="text-danger">Rp <?= number_format($totalPengeluaran['total'] ?? 0, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <!-- Tabel Pengeluaran -->
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #00a77dff;">
  Daftar Pengeluaran
</div>

        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($pengeluaranList) > 0): ?>
                        <?php while ($pg = mysqli_fetch_assoc($pengeluaranList)): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($pg['tanggal'])); ?></td>
                                <td>Rp <?= number_format($pg['nominal'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($pg['keterangan']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data pengeluaran</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
