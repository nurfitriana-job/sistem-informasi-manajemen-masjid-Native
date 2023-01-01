<?php
$title = "Manajemen Pengumuman";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Tambah pengumuman
if (isset($_POST['simpan'])) {
    $jenis = $_POST['jenis'];
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $tanggal = $_POST['tanggal'];

    $query = "INSERT INTO pengumuman (jenis, judul, isi, tanggal) VALUES ('$jenis','$judul','$isi','$tanggal')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pengumuman berhasil ditambahkan!'); location.href='pengumuman.php';</script>";
    }
}

// Hapus pengumuman
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pengumuman WHERE id='$id'");
    header("Location: pengumuman.php");
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Manajemen Pengumuman</h2>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Pengumuman</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Jenis</label>
                    <select name="jenis" class="form-select" id="jenis" required>
                        <option value="">Pilih Jenis</option>
                        <option value="acara">Acara</option>
                        <option value="kegiatan">Kegiatan</option>
                        <option value="jadwal_sholat">Jadwal Sholat</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Isi (Untuk jadwal shalat pisahkan dengan koma, contoh: 04:30,12:00,15:15,18:20,19:30)</label>
                    <textarea name="isi" id="isi" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                <button type="button" class="btn btn-primary" id="ambilJadwal" style="display:none;">Ambil Jadwal Otomatis</button>
            </form>
        </div>
    </div>

    <!-- Tabel Pengumuman -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Daftar Pengumuman</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Judul</th>
                        <th>Isi</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY created_at DESC");
                    while ($row = mysqli_fetch_assoc($data)) :
                    ?>
                    <tr>
                        <td><?= ucfirst($row['jenis']); ?></td>
                        <td><?= $row['judul']; ?></td>
                        <td><?= $row['isi']; ?></td>
                        <td><?= $row['tanggal']; ?></td>
                        <td>
                            <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Tampilkan tombol Ambil Jadwal jika jenis jadwal_sholat
document.getElementById('jenis').addEventListener('change', function() {
    if (this.value === 'jadwal_sholat') {
        document.getElementById('ambilJadwal').style.display = 'inline-block';
    } else {
        document.getElementById('ambilJadwal').style.display = 'none';
    }
});

// Ambil jadwal sholat otomatis (API MyQuran)
document.getElementById('ambilJadwal').addEventListener('click', function() {
    fetch('https://api.myquran.com/v1/sholat/jadwal/1104/<?= date('Y-m-d'); ?>')
        .then(response => response.json())
        .then(data => {
            let jadwal = data.data.jadwal;
            let isi = `${jadwal.subuh},${jadwal.dzuhur},${jadwal.ashar},${jadwal.maghrib},${jadwal.isya}`;
            document.getElementById('isi').value = isi;
        })
        .catch(err => alert('Gagal ambil jadwal'));
});
</script>
<?php include 'layout/footer.php'; ?>
