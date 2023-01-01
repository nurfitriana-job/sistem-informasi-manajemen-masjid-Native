<?php
$title = "Manajemen Informasi Masjid";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Tambah Informasi + Kirim Telegram
if (isset($_POST['simpan'])) {
    $jenis = $_POST['jenis'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];

    mysqli_query($conn, "INSERT INTO pengumuman_kegiatan (jenis, judul, isi, tanggal, jam) 
                         VALUES ('$jenis','$judul','$isi','$tanggal','$jam')");

    // Ambil semua chat_id user
    $result = mysqli_query($conn, "SELECT telegram_chat_id FROM users WHERE telegram_chat_id IS NOT NULL AND telegram_chat_id != ''");
    
    // Format pesan Telegram
    $pesan = "📢 *Informasi $jenis Masjid*\n\n" .
             "📌 *Judul:* $judul\n" .
             "🗓️ *Tanggal:* " . date('d-m-Y', strtotime($tanggal)) . "\n" .
             "⏰ *Jam:* $jam WIB\n\n" .
             "📖 *Detail:* \n$isi\n\n" .
             "_Mari kita saling mengingatkan dalam kebaikan._\n\n" .
             "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

    // Kirim ke semua user yang punya telegram_chat_id
    while ($row = mysqli_fetch_assoc($result)) {
        $chat_id = $row['telegram_chat_id'];
        file_get_contents("https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=$chat_id&text=" . urlencode($pesan) . "&parse_mode=Markdown");
    }

    echo "<script>alert('Informasi berhasil ditambahkan dan dikirim ke Telegram!'); location.href='kegiatan.php';</script>";
}

// Update Informasi
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $jenis = $_POST['jenis'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];

    mysqli_query($conn, "UPDATE pengumuman_kegiatan SET jenis='$jenis', judul='$judul', isi='$isi', tanggal='$tanggal', jam='$jam' WHERE id='$id'");
    echo "<script>alert('Data berhasil diperbarui'); location.href='kegiatan.php';</script>";
}

// Hapus Informasi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pengumuman_kegiatan WHERE id='$id'");
    echo "<script>alert('Data berhasil dihapus'); location.href='kegiatan.php';</script>";
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Manajemen Informasi Masjid</h2>

    <!-- Form Tambah -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">Tambah Informasi</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Jenis Informasi</label>
                    <select name="jenis" class="form-select" required>
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Pengumuman">Pengumuman</option>
                        <option value="Takziah">Takziah</option>
                    </select>
                </div>
                <input type="text" name="judul" placeholder="Judul" class="form-control mb-2" required>
                <textarea name="isi" placeholder="Isi Informasi" class="form-control mb-2" rows="3" required></textarea>
                <div class="row">
                    <div class="col-md-6">
                        <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" class="form-control mb-2" required>
                    </div>
                    <div class="col-md-6">
                        <input type="time" name="jam" value="<?= date('H:i'); ?>" class="form-control mb-2" required>
                    </div>
                </div>
                <button class="btn btn-success" name="simpan">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Daftar Informasi</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Jenis</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Isi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query($conn, "SELECT * FROM pengumuman_kegiatan ORDER BY tanggal DESC");
                while ($row = mysqli_fetch_assoc($data)) {
                    echo "<tr>
                            <td>{$row['jenis']}</td>
                            <td>{$row['judul']}</td>
                            <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
                            <td>{$row['jam']} WIB</td>
                            <td>{$row['isi']}</td>
                            <td>
                                <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#edit{$row['id']}'>Edit</button>
                                <a href='?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
                            </td>
                          </tr>";

                    // Modal Edit
                    echo "<div class='modal fade' id='edit{$row['id']}' tabindex='-1'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header bg-warning'><h5>Edit Informasi</h5></div>
                                    <form method='POST'>
                                        <div class='modal-body'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <label>Jenis</label>
                                            <select name='jenis' class='form-select mb-2'>
                                                <option value='Kegiatan' ".($row['jenis']=='Kegiatan'?'selected':'').">Kegiatan</option>
                                                <option value='Pengumuman' ".($row['jenis']=='Pengumuman'?'selected':'').">Pengumuman</option>
                                                <option value='Takziah' ".($row['jenis']=='Takziah'?'selected':'').">Takziah</option>
                                            </select>
                                            <input type='text' name='judul' value='{$row['judul']}' class='form-control mb-2'>
                                            <textarea name='isi' class='form-control mb-2'>{$row['isi']}</textarea>
                                            <input type='date' name='tanggal' value='{$row['tanggal']}' class='form-control mb-2'>
                                            <input type='time' name='jam' value='{$row['jam']}' class='form-control mb-2'>
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
    </div>
</div>

<?php include 'layout/footer.php'; ?>
