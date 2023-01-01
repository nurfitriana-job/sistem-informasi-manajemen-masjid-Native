<?php
include 'config/db.php';
date_default_timezone_set('Asia/Jakarta');

$title = "Kegiatan & Pengumuman";
include 'header.php';

// Ambil data kegiatan & pengumuman dari database
$data = mysqli_query($conn, "SELECT * FROM pengumuman_kegiatan ORDER BY tanggal DESC, jam DESC");
?>

<div class="container-fluid mt-4">
    <h2 class="mb-4" style="color: #ffea00ff;">Informasi Kegiatan & Pengumuman</h2>

    <div class="row">
        <?php if (mysqli_num_rows($data) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <?php
                    // Set warna header sesuai jenis dengan style inline
                    if ($row['jenis'] == 'Kegiatan') {
                        $bgStyle = 'background-color: rgba(13, 179, 102, 0.81); color: white;';  // hijau bootstrap
                    } elseif ($row['jenis'] == 'Pengumuman' || $row['jenis'] == 'Takziah') {
                        $bgStyle = 'background-color: rgba(13, 179, 102, 0.81); color: white;';  // hijau tosca
                    } else {
                        $bgStyle = 'background-color: #6c757d; color: white;';  // abu abu (secondary)
                    }

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

<?php include 'footer.php'; ?>
