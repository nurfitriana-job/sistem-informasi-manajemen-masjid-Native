<?php
ob_start();
$title = "Jadwal Sholat";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

date_default_timezone_set('Asia/Jakarta');

// ✅ Cek jika hari ini belum ada jadwal
$tanggalHariIni = date('Y-m-d');
$cek = mysqli_query($conn, "SELECT * FROM jadwal_sholat WHERE tanggal='$tanggalHariIni'");
if (mysqli_num_rows($cek) == 0) {
    // Ambil dari API otomatis jika tidak ada
    $city = "Pekanbaru"; 
    $country = "Indonesia";
    $method = 11;
    $url = "https://api.aladhan.com/v1/timingsByCity?city=$city&country=$country&method=$method";

    $response = @file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['data']['timings'])) {
            $jadwal = $data['data']['timings'];
            $subuh = $jadwal['Fajr'];
            $dzuhur = $jadwal['Dhuhr'];
            $ashar = $jadwal['Asr'];
            $maghrib = $jadwal['Maghrib'];
            $isya = $jadwal['Isha'];

            mysqli_query($conn, "INSERT INTO jadwal_sholat (subuh,dzuhur,ashar,maghrib,isya,tanggal)
            VALUES ('$subuh','$dzuhur','$ashar','$maghrib','$isya','$tanggalHariIni')");
        }
    }
}

// ✅ Tambah manual
if (isset($_POST['simpan'])) {
    $subuh = $_POST['subuh'];
    $dzuhur = $_POST['dzuhur'];
    $ashar = $_POST['ashar'];
    $maghrib = $_POST['maghrib'];
    $isya = $_POST['isya'];
    $tanggal = $_POST['tanggal'];
    mysqli_query($conn, "INSERT INTO jadwal_sholat (subuh,dzuhur,ashar,maghrib,isya,tanggal)
    VALUES ('$subuh','$dzuhur','$ashar','$maghrib','$isya','$tanggal')");
    header("Location: jadwal-sholat.php");
}

// ✅ Update Jadwal
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $subuh = $_POST['subuh'];
    $dzuhur = $_POST['dzuhur'];
    $ashar = $_POST['ashar'];
    $maghrib = $_POST['maghrib'];
    $isya = $_POST['isya'];
    $tanggal = $_POST['tanggal'];
    mysqli_query($conn, "UPDATE jadwal_sholat SET subuh='$subuh',dzuhur='$dzuhur',ashar='$ashar',maghrib='$maghrib',isya='$isya',tanggal='$tanggal' WHERE id='$id'");
    header("Location: jadwal-sholat.php");
}

// ✅ Delete Jadwal
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM jadwal_sholat WHERE id='$id'");
    header("Location: jadwal-sholat.php");
}
ob_end_flush();

?>

<div class="container-fluid">
    <h2 class="mb-4">Jadwal Sholat</h2>
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" class="row g-3 align-items-center">
                <div class="col-md-2">
                    <label class="form-label">Subuh</label>
                    <input type="time" name="subuh" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dzuhur</label>
                    <input type="time" name="dzuhur" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ashar</label>
                    <input type="time" name="ashar" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Maghrib</label>
                    <input type="time" name="maghrib" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Isya</label>
                    <input type="time" name="isya" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" class="form-control" required>
                </div>
                <div class="col-12 mt-3">
                    <button class="btn btn-success" name="simpan">Simpan</button>
                    <button type="button" id="ambilJadwal" class="btn btn-primary">Ambil Otomatis</button>
                </div>
            </form>
        </div>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>Subuh</th><th>Dzuhur</th><th>Ashar</th><th>Maghrib</th><th>Isya</th><th>Tanggal</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php
        $data = mysqli_query($conn, "SELECT * FROM jadwal_sholat ORDER BY tanggal DESC");
        while ($row = mysqli_fetch_assoc($data)) {
            echo "<tr>
                    <td>{$row['subuh']}</td>
                    <td>{$row['dzuhur']}</td>
                    <td>{$row['ashar']}</td>
                    <td>{$row['maghrib']}</td>
                    <td>{$row['isya']}</td>
                    <td>{$row['tanggal']}</td>
                    <td>
                        <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#edit{$row['id']}'>Edit</button>
                        <a href='?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
                    </td>
                  </tr>";

            // Modal Edit
            echo "<div class='modal fade' id='edit{$row['id']}' tabindex='-1'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'><h5>Edit Jadwal</h5></div>
                            <form method='POST'>
                                <div class='modal-body'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <label>Subuh</label><input type='time' name='subuh' value='{$row['subuh']}' class='form-control mb-2'>
                                    <label>Dzuhur</label><input type='time' name='dzuhur' value='{$row['dzuhur']}' class='form-control mb-2'>
                                    <label>Ashar</label><input type='time' name='ashar' value='{$row['ashar']}' class='form-control mb-2'>
                                    <label>Maghrib</label><input type='time' name='maghrib' value='{$row['maghrib']}' class='form-control mb-2'>
                                    <label>Isya</label><input type='time' name='isya' value='{$row['isya']}' class='form-control mb-2'>
                                    <label>Tanggal</label><input type='date' name='tanggal' value='{$row['tanggal']}' class='form-control mb-2'>
                                </div>
                                <div class='modal-footer'>
                                    <button type='submit' name='update' class='btn btn-primary'>Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                  </div>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
// Ambil jadwal otomatis dan simpan ke DB
document.getElementById('ambilJadwal').addEventListener('click', function() {
    fetch('get_jadwal.php')
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                let formData = `simpan=1&subuh=${data.subuh}&dzuhur=${data.dzuhur}&ashar=${data.ashar}&maghrib=${data.maghrib}&isya=${data.isya}&tanggal=${data.tanggal}`;
                fetch('jadwal-sholat.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: formData
                }).then(() => location.reload());
            } else {
                alert('Gagal ambil jadwal: ' + data.error);
            }
        })
        .catch(err => alert('Koneksi gagal'));
});
</script>

<?php include 'layout/footer.php'; ?>
