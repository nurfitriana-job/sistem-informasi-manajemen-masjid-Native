<?php
$title = "Dashboard";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses ditolak'); location.href='../login.php';</script>";
    exit();
}
// Ambil total pemasukan & pengeluaran
$pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan"));
// Ringkasan saldo
$zakat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='zakat' AND status='verified'"));
$infaqmasjid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqmasjid' AND status='verified'"));
$infaqanakyatim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqanakyatim' AND status='verified'"));
$sedekah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='sedekah' AND status='verified'"));
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pengeluaran"));
$totalSaldo = ($zakat['total'] ?? 0) + ($infaqmasjid['total'] ?? 0) + ($infaqanakyatim['total'] ?? 0) + ($sedekah['total'] ?? 0) - ($pengeluaran['total'] ?? 0);

// Ambil list pemasukan
$pemasukanList = mysqli_query($conn, "
    SELECT kp.*, u.nama AS nama_user 
    FROM keuangan_pemasukan kp 
    LEFT JOIN users u ON kp.user_id = u.id 
    ORDER BY kp.tanggal DESC
");

// Ambil list pengeluaran
$pengeluaranList = mysqli_query($conn, "SELECT * FROM keuangan_pengeluaran ORDER BY tanggal DESC");

// Ambil jadwal shalat hari ini
$tanggalHariIni = date('Y-m-d');
$jadwalQuery = mysqli_query($conn, "SELECT * FROM jadwal_sholat WHERE tanggal='$tanggalHariIni' LIMIT 1");

// Ambil pengumuman acara & kegiatan (10 terbaru)
$pengumumanAcara = mysqli_query($conn, "SELECT judul, tanggal FROM pengumuman_acara ORDER BY tanggal DESC LIMIT 5");
$pengumumanKegiatan = mysqli_query($conn, "SELECT judul, tanggal FROM pengumuman_kegiatan ORDER BY tanggal DESC LIMIT 5");
?>
<div class="container-fluid">
    <h2 class="mb-4"><?= ($_SESSION['role'] == 'admin') ? 'Dashboard ' : 'Dashboard '; ?></h2>

   

    <!-- Jadwal Shalat -->
    <h4 class="mb-3">Jadwal Shalat Hari Ini</h4>
    <div class="row">
        <?php if(mysqli_num_rows($jadwalQuery) > 0): 
            $data = mysqli_fetch_assoc($jadwalQuery);
            $nama_waktu = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
            $isi = [$data['subuh'], $data['dzuhur'], $data['ashar'], $data['maghrib'], $data['isya']];
        ?>
            <?php for($i = 0; $i < 5; $i++): ?>
                <div class="col-6 col-md-2 mb-3">
                    <div class="card p-2 text-center shadow-sm" style="background:#e9f7ef; border-radius:10px;">
                        <h6 class="text-success fw-bold"><?= $nama_waktu[$i]; ?></h6>
                        <p class="fw-bold" style="font-size:1.3rem;">
                            <?= isset($isi[$i]) ? substr($isi[$i], 0, 5) : '--:--'; ?>
                        </p>
                    </div>
                </div>
            <?php endfor; ?>
        <?php else: ?>
            <p class="text-muted">Belum ada jadwal shalat hari ini.</p>
        <?php endif; ?>
    </div>
     <!-- Ringkasan Keuangan -->
    <?php if ($_SESSION['role'] == 'admin'): ?>
    <!-- Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-3"><div class="card shadow-sm text-center"><div class="card-body"><h6>Zakat</h6><h5 class="text-success">Rp <?= number_format($zakat['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-2"><div class="card shadow-sm text-center"><div class="card-body"><h6>Infaq Masjid</h6><h5 class="text-success">Rp <?= number_format($infaqmasjid['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-2"><div class="card shadow-sm text-center"><div class="card-body"><h6>Infaq Anak Yatim</h6><h5 class="text-success">Rp <?= number_format($infaqanakyatim['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-2"><div class="card shadow-sm text-center"><div class="card-body"><h6>Sedekah</h6><h5 class="text-success">Rp <?= number_format($sedekah['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm text-center"><div class="card-body"><h6>Pengeluaran</h6><h5 class="text-danger">Rp <?= number_format($pengeluaran['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
    </div>

    <!-- Saldo -->
    <div class="card mb-4 shadow-sm text-center bg-light">
        <div class="card-body">
            <h5>Total Saldo</h5>
            <h3 class="text-primary">Rp <?= number_format($totalSaldo, 0, ',', '.'); ?></h3>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pengumuman -->
    <h4 class="mt-4 mb-3">Pengumuman</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">Acara</div>
                <ul class="list-group list-group-flush">
                    <?php while($row = mysqli_fetch_assoc($pengumumanAcara)): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($row['judul']); ?> 
                            <span class="text-muted float-end"><?= date('d M Y', strtotime($row['tanggal'])); ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-success text-white">Kegiatan</div>
                <ul class="list-group list-group-flush">
                    <?php while($row = mysqli_fetch_assoc($pengumumanKegiatan)): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($row['judul']); ?> 
                            <span class="text-muted float-end"><?= date('d M Y', strtotime($row['tanggal'])); ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
