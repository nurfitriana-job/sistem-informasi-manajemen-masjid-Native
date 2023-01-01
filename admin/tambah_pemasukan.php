<?php
session_start();
include '../config/db.php';
include '../config/telegram.php'; // Pastikan token Telegram ada di sini

$kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
$nominal = mysqli_real_escape_string($conn, $_POST['nominal']);
$keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
$user_id = intval($_POST['user_id']);

// Ambil data user (nama + telegram_chat_id)
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, telegram_chat_id FROM users WHERE id='$user_id'"));

// Upload bukti jika ada
$bukti = '';
if (!empty($_FILES['bukti']['name'])) {
    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $bukti = time() . '.' . $ext;
    move_uploaded_file($_FILES['bukti']['tmp_name'], "../uploads/" . $bukti);
}

// Simpan ke database
mysqli_query($conn, "INSERT INTO keuangan_pemasukan (kategori, nominal, keterangan, bukti_pembayaran, user_id, tanggal, status) 
VALUES ('$kategori','$nominal','$keterangan','$bukti','$user_id', NOW(), 'pending')");

// Kirim Notifikasi Telegram
if (!empty($user['telegram_chat_id'])) {
    $namaUser = $user['nama'];
$pesan = "Assalamu'alaikum warahmatullahi wabarakatuh,\n\n" .
             "Jazakallahu khairan kepada *{$namaUser}* atas donasi yang telah diberikan.\n\n" .
             "📌 *Kategori:* {$kategori}\n" .
             "💰 *Nominal:* Rp " . number_format($nominal, 0, ',', '.') . "\n\n" .
             "Segera lakukan transfer pembayaran dan upload bukti melalui sistem kami agar transaksi dapat diverifikasi.\n\n" .
             "Semoga Allah SWT membalas dengan keberkahan dan rezeki yang melimpah.\n" .
             "Barakallahu fiikum.\n\n" .
             "Wassalamu'alaikum warahmatullahi wabarakatuh.";

    $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=" . $user['telegram_chat_id'] . "&text=" . urlencode($pesan) . "&parse_mode=Markdown";
    file_get_contents($url);
}

echo "<script>alert('Pemasukan berhasil ditambahkan dan notifikasi dikirim'); location.href='keuangan.php';</script>";
?>
