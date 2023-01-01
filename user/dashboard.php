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

// ===== Ambil data keseluruhan untuk ringkasan (seperti admin) =====
$zakat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='zakat' AND status='verified'"));
$infaqmasjid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqmasjid' AND status='verified'"));
$infaqanakyatim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqanakyatim' AND status='verified'"));
$sedekah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='sedekah' AND status='verified'"));
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pengeluaran"));
$totalSaldo = ($zakat['total'] ?? 0) + ($infaqmasjid['total'] ?? 0) + ($infaqanakyatim['total'] ?? 0) + ($sedekah['total'] ?? 0) - ($pengeluaran['total'] ?? 0);

// ===== Ambil data donasi user untuk riwayat =====
$riwayatDonasi = mysqli_query($conn, "SELECT * FROM keuangan_pemasukan WHERE user_id='$userId' ORDER BY tanggal DESC LIMIT 5");
?>

<div class="container-fluid">
    <h2 class="mb-4">Dashboard</h2>

    <!-- Ringkasan Masjid -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Total Zakat</h6>
                    <h5 class="text-success">Rp <?= number_format($zakat['total'] ?? 0, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Total Infaq Masjid</h6>
                    <h5 class="text-success">Rp <?= number_format($infaqmasjid['total'] ?? 0, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
                <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Total Infaq Anak Yatim</h6>
                    <h5 class="text-success">Rp <?= number_format($infaqanakyatim['total'] ?? 0, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6>Total Sedekah</h6>
                    <h5 class="text-success">Rp <?= number_format($sedekah['total'] ?? 0, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center bg-light">
                <div class="card-body">
                    <h6>Pengeluaran</h6>
                    <h5 class="text-danger">Rp <?= number_format($pengeluaran['total'] ?? 0, 0, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo -->
    <div class="card mb-4 shadow-sm text-center bg-light">
        <div class="card-body">
            <h5>Total Saldo Masjid</h5>
            <h3 class="text-primary">Rp <?= number_format($totalSaldo, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <!-- Riwayat Donasi User -->
    <div class="card shadow-sm">
        <div class="card-header text-white" style="background-color: #00a77dff;">
    Riwayat Donasi Anda (Terbaru)
</div>

        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($riwayatDonasi) > 0): ?>
                        <?php while ($d = mysqli_fetch_assoc($riwayatDonasi)): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($d['tanggal'])); ?></td>
                                <td><?= ucfirst($d['kategori']); ?></td>
                                <td>Rp <?= number_format($d['nominal'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php if($d['status'] == 'verified'): ?>
                                        <span class="badge bg-success">Terverifikasi</span>
                                    <?php elseif($d['status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Belum ada donasi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
<a href="pemasukan.php" class="btn btn-sm" style="background-color: #00a77dff; border-color: #00a77dff; color: white;">Lihat Semua Donasi</a>

        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
