<?php
include 'config/db.php';
date_default_timezone_set('Asia/Jakarta');

$title = "Kegiatan & Pengumuman";
include 'header.php';

// Ambil data kegiatan & pengumuman dari database
$acaraList = mysqli_query($conn, "SELECT * FROM pengumuman_acara ORDER BY tanggal ASC");
?>

<div class="container-fluid mt-4">
    <h2 class="mb-4" style="color: #ffea00ff;">Informasi Acara</h2>

    <div class="row">
        <?php if (mysqli_num_rows($acaraList) > 0): ?>
            <?php while ($a = mysqli_fetch_assoc($acaraList)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100" style="border: 1px solid rgba(13, 179, 102, 0.81);">
                        <div class="card-header text-white text-center" style="background-color: rgba(13, 179, 102, 0.81);">
                            <h5 class="mb-0"><?= htmlspecialchars($a['judul']); ?></h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>📅 Tanggal:</strong> <?= date('d M Y', strtotime($a['tanggal'])); ?></p>
                            <p class="mb-2"><strong>⏰ Jam:</strong> <?= htmlspecialchars($a['jam']); ?> WIB</p>
                            <hr>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($a['isi'])); ?></p>
                        </div>
                        <div class="card-footer text-center bg-light">
                            <span style="color: rgba(13, 179, 102, 0.81); font-weight: bold;">Mari hadiri dan raih keberkahannya!</span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">Belum ada acara yang diumumkan.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
