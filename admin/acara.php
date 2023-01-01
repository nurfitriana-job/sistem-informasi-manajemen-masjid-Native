<?php
$title = "Pengumuman Acara";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Fungsi kirim pesan Telegram
function kirimTelegram($chat_id, $pesan, $token) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $post = [
        'chat_id' => $chat_id,
        'text' => $pesan,
        'parse_mode' => 'HTML'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Tambah Acara + Broadcast Telegram
if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);

    // Simpan ke DB
    mysqli_query($conn, "INSERT INTO pengumuman_acara (judul, isi, tanggal, jam) VALUES ('$judul','$isi','$tanggal','$jam')");

    // Format pesan
    $pesan = "📢 <b>Pengumuman Acara Masjid</b>\n\n";
    $pesan .= "Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n";
    $pesan .= "<b>Judul:</b> $judul\n";
    $pesan .= "<b>Tanggal:</b> " . date('d-m-Y', strtotime($tanggal)) . "\n";
    $pesan .= "<b>Jam:</b> $jam WIB\n";
    $pesan .= "<b>Keterangan:</b>\n$isi\n\n";
    $pesan .= "Mari bersama-sama menghadiri acara ini.\n";
    $pesan .= "Wassalamu'alaikum Warahmatullahi Wabarakatuh 🙏";

    // Kirim ke semua user dengan chat_id Telegram
    $chatResult = mysqli_query($conn, "SELECT telegram_chat_id FROM users WHERE telegram_chat_id IS NOT NULL AND telegram_chat_id != ''");
    while ($row = mysqli_fetch_assoc($chatResult)) {
        $chatId = $row['telegram_chat_id'];
        kirimTelegram($chatId, $pesan, $GLOBALS['telegramToken']);
    }

    echo "<script>alert('Acara berhasil ditambahkan dan dikirim ke Telegram'); location.href='acara.php';</script>";
}

// Update Acara
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    mysqli_query($conn, "UPDATE pengumuman_acara SET judul='$judul', isi='$isi', tanggal='$tanggal', jam='$jam' WHERE id='$id'");
    echo "<script>alert('Acara berhasil diupdate'); location.href='acara.php';</script>";
}

// Delete Acara
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pengumuman_acara WHERE id='$id'");
    echo "<script>alert('Acara berhasil dihapus'); location.href='acara.php';</script>";
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Pengumuman Acara</h2>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Acara</div>
        <div class="card-body">
            <form method="POST">
                <input type="text" name="judul" placeholder="Judul Acara" class="form-control mb-2" required>
                <textarea name="isi" placeholder="Isi Acara" class="form-control mb-2" rows="3" required></textarea>
                <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" class="form-control mb-2" required>
                <input type="time" name="jam" value="<?= date('H:i'); ?>" class="form-control mb-2" required>
                <button class="btn btn-success" name="simpan">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Judul</th>
                <th>Isi</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $data = mysqli_query($conn, "SELECT * FROM pengumuman_acara ORDER BY tanggal DESC");
        while ($row = mysqli_fetch_assoc($data)) {
            echo "<tr>
                    <td>{$row['judul']}</td>
                    <td>{$row['isi']}</td>
                    <td>" . date('d-m-Y', strtotime($row['tanggal'])) . "</td>
                    <td>{$row['jam']} WIB</td>
                    <td>
                        <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#edit{$row['id']}'>Edit</button>
                        <a href='?hapus={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Hapus data ini?\")'>Hapus</a>
                    </td>
                  </tr>";

            // Modal Edit
            echo "<div class='modal fade' id='edit{$row['id']}' tabindex='-1'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'><h5>Edit Acara</h5></div>
                            <form method='POST'>
                                <div class='modal-body'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <input type='text' name='judul' value='{$row['judul']}' class='form-control mb-2' required>
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

<?php include 'layout/footer.php'; ?>
