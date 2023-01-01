<?php
$title = "Informasi Masjid";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Ambil data kegiatan
$data = mysqli_query($conn, "SELECT * FROM pengumuman_kegiatan ORDER BY tanggal DESC, jam DESC");
?>

<div class="container-fluid">
    <h2 class="mb-4"> Informasi Kegiatan & Pengumuman</h2>

    <div class="row">
        <?php if (mysqli_num_rows($data) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <?php
                // Tentukan warna card berdasarkan jenis informasi
               $bgStyle = "background-color: #00a77dff; color: white;";


                $tanggal = date('d M Y', strtotime($row['tanggal']));
                $jam = date('H:i', strtotime($row['jam']));
                ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header" style="<?= $bgStyle; ?>">
                            <strong><?= htmlspecialchars($row['jenis']); ?></strong>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['judul']); ?></h5>
                            <p class="card-text"><?= nl2br(htmlspecialchars($row['isi'])); ?></p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">🗓 <?= $tanggal; ?> | ⏰ <?= $jam; ?> WIB</small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="alert alert-info">Belum ada informasi terbaru.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
