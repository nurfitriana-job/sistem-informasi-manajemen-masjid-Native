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

// Ambil jadwal sholat untuk hari ini
$tanggalHariIni = date('Y-m-d');
$jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jadwal_sholat WHERE tanggal='$tanggalHariIni'"));

$waktuSekarang = date('H:i');
$nextSholat = "";
$highlight = "";

if ($jadwal) {
    if ($waktuSekarang < $jadwal['subuh']) {
        $nextSholat = "Subuh"; $highlight = "subuh";
    } elseif ($waktuSekarang < $jadwal['dzuhur']) {
        $nextSholat = "Dzuhur"; $highlight = "dzuhur";
    } elseif ($waktuSekarang < $jadwal['ashar']) {
        $nextSholat = "Ashar"; $highlight = "ashar";
    } elseif ($waktuSekarang < $jadwal['maghrib']) {
        $nextSholat = "Maghrib"; $highlight = "maghrib";
    } elseif ($waktuSekarang < $jadwal['isya']) {
        $nextSholat = "Isya"; $highlight = "isya";
    } else {
        $nextSholat = "Subuh (Besok)"; $highlight = "";
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Jadwal Sholat Hari Ini</h2>

    <?php if ($jadwal): ?>
        <div class="alert alert-info text-center fs-5 mb-4">
            <strong>Sebentar lagi masuk waktu <?= $nextSholat; ?></strong>
        </div>

        <div class="row text-center">
            <div class="col-md-2 mb-3">
                <div class="card shadow-sm <?= $highlight=='subuh'?'bg-primary text-white':''; ?>">
                    <div class="card-body">
                        <h6>Subuh</h6>
                        <h4><?= date('H:i', strtotime($jadwal['subuh'])); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card shadow-sm <?= $highlight=='dzuhur'?'bg-success text-white':''; ?>">
                    <div class="card-body">
                        <h6>Dzuhur</h6>
                        <h4><?= date('H:i', strtotime($jadwal['dzuhur'])); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card shadow-sm <?= $highlight=='ashar'?'bg-warning text-dark':''; ?>">
                    <div class="card-body">
                        <h6>Ashar</h6>
                        <h4><?= date('H:i', strtotime($jadwal['ashar'])); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card shadow-sm <?= $highlight=='maghrib'?'bg-danger text-white':''; ?>">
                    <div class="card-body">
                        <h6>Maghrib</h6>
                        <h4><?= date('H:i', strtotime($jadwal['maghrib'])); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb-3">
                <div class="card shadow-sm <?= $highlight=='isya'?'bg-dark text-white':''; ?>">
                    <div class="card-body">
                        <h6>Isya</h6>
                        <h4><?= date('H:i', strtotime($jadwal['isya'])); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Jadwal sholat untuk hari ini belum tersedia.</div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>
