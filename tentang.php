<?php
include 'config/db.php';
date_default_timezone_set('Asia/Jakarta');

$title = "Tentang Masjid";
$about = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_masjid LIMIT 1"));
include 'header.php';

// Variabel warna hijau yang dipakai
$warnaHijau = "#ffff00ff";
$warnaTosca = "#08a66cff";
?>

<div class="container mt-4">
    <h2 class="mb-4" style="color: <?= $warnaHijau; ?>;">Tentang Masjid</h2>
    <h4 class="fw-bold"><?= htmlspecialchars($about['nama']); ?></h4>
    <p><strong>Alamat:</strong> <?= htmlspecialchars($about['alamat']); ?></p>
    <p><?= nl2br(htmlspecialchars($about['deskripsi'])); ?></p>

    <h5 class="mt-3" style="color: <?= $warnaHijau; ?>;">Visi</h5>
    <p><?= nl2br(htmlspecialchars($about['visi'])); ?></p>

    <h5 class="mt-3" style="color: <?= $warnaHijau; ?>;">Misi</h5>
    <p><?= nl2br(htmlspecialchars($about['misi'])); ?></p>

    <h5 class="mt-3" style="color: <?= $warnaHijau; ?>;">Lokasi</h5>
    <div id="map" style="height:300px; border-radius:10px;"></div>
    <br><br>
    <a href="index.php" class="btn" style="background-color: <?= $warnaTosca; ?>; border-color: <?= $warnaTosca; ?>; color: white;" class="mt-4">Kembali ke Beranda</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var lat = <?= $about['latitude'] ?: -6.200000 ?>;
    var lng = <?= $about['longitude'] ?: 106.816666 ?>;
    var map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Masjid").openPopup();
});
</script>

<?php include 'footer.php'; ?>
