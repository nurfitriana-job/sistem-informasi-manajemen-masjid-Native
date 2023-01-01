<?php
$title = "Daftar Acara";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Ambil data acara
$acaraList = mysqli_query($conn, "SELECT * FROM pengumuman_acara ORDER BY tanggal ASC");
?>

<div class="container-fluid">
    <h2 class="mb-4 text-center">📢 Pengumuman Acara Masjid</h2>

    <div class="row">
        <?php if (mysqli_num_rows($acaraList) > 0): ?>
            <?php while ($a = mysqli_fetch_assoc($acaraList)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 border-success">
                       <div class="card-header text-white text-center" style="background-color: #00a77dff;">
                            <h5 class="mb-0"><?= htmlspecialchars($a['judul']); ?></h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>📅 Tanggal:</strong> <?= date('d M Y', strtotime($a['tanggal'])); ?></p>
                            <p class="mb-2"><strong>⏰ Jam:</strong> <?= htmlspecialchars($a['jam']); ?> WIB</p>
                            <hr>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($a['isi'])); ?></p>
                        </div>
                        <div class="card-footer text-center bg-light">
                            <span class="text-success fw-bold">Mari hadiri dan raih keberkahannya!</span>
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

<?php include 'layout/footer.php'; ?>
