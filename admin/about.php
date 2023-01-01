<?php
$title = "About Masjid";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Ambil data about
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_masjid LIMIT 1"));

// Update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $deskripsi = $_POST['deskripsi'];
    $visi = $_POST['visi'];
    $misi = $_POST['misi'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    mysqli_query($conn, "UPDATE about_masjid SET 
        nama='$nama', alamat='$alamat', deskripsi='$deskripsi', visi='$visi', misi='$misi', 
        latitude='$latitude', longitude='$longitude' WHERE id=".$data['id']);
    
    echo "<script>alert('Data berhasil diperbarui'); location.href='about.php';</script>";
}
?>

<div class="container-fluid">
    <h2 class="mb-4">About Masjid</h2>

    <div class="row">
        <!-- Form Edit -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">Informasi Masjid</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nama Masjid</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" required><?= htmlspecialchars($data['alamat']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Visi</label>
                            <textarea name="visi" class="form-control" rows="2"><?= htmlspecialchars($data['visi']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Misi</label>
                            <textarea name="misi" class="form-control" rows="2"><?= htmlspecialchars($data['misi']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control" value="<?= $data['latitude']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control" value="<?= $data['longitude']; ?>" readonly>
                        </div>
                        <button class="btn btn-success w-100">Update</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Peta -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-white fw-bold" style="background-color: #198754;">Peta Lokasi Masjid</div>
                <div class="card-body p-0">
                    <input type="text" id="search" class="form-control mb-2" placeholder="Cari lokasi...">
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var lat = <?= $data['latitude'] ?: -6.200000 ?>;
    var lng = <?= $data['longitude'] ?: 106.816666 ?>;

    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng], {draggable: true}).addTo(map)
        .bindPopup("Lokasi Masjid").openPopup();

    function updateAddress(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if(data && data.display_name){
                document.getElementById('alamat').value = data.display_name;
            }
        })
        .catch(err => console.error(err));
    }

    marker.on('dragend', function(e){
        var pos = e.target.getLatLng();
        document.getElementById('latitude').value = pos.lat.toFixed(6);
        document.getElementById('longitude').value = pos.lng.toFixed(6);
        updateAddress(pos.lat, pos.lng);
    });

    updateAddress(lat, lng);

    // Search lokasi
    document.getElementById('search').addEventListener('keypress', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
            var query = this.value;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
            .then(res => res.json())
            .then(data => {
                if(data.length > 0){
                    var newLat = data[0].lat;
                    var newLon = data[0].lon;
                    map.setView([newLat, newLon], 15);
                    marker.setLatLng([newLat, newLon]);
                    document.getElementById('latitude').value = parseFloat(newLat).toFixed(6);
                    document.getElementById('longitude').value = parseFloat(newLon).toFixed(6);
                    updateAddress(newLat, newLon);
                } else {
                    alert('Lokasi tidak ditemukan');
                }
            });
        }
    });
</script>

<?php include 'layout/footer.php'; ?>
