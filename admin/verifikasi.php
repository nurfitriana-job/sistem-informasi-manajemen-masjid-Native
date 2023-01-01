<?php
session_start();
include '../config/db.php';
include '../config/telegram.php'; // Pastikan token Telegram ada di sini

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id']) && $_GET['aksi'] == 'terima') {
    $id = intval($_GET['id']);

    // Update status ke verified
    mysqli_query($conn, "UPDATE keuangan_pemasukan SET status='verified' WHERE id='$id'");

    // Ambil data untuk notifikasi
    $data = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT kp.kategori, kp.nominal, u.nama, u.telegram_chat_id
        FROM keuangan_pemasukan kp
        JOIN users u ON kp.user_id = u.id
        WHERE kp.id='$id'
    "));

    // Kirim Notifikasi Telegram
    if (!empty($data['telegram_chat_id'])) {
        $pesan = "Assalamu'alaikum warahmatullahi wabarakatuh,\n\n" .
                 "Alhamdulillah, donasi Anda telah *TERVERIFIKASI*.\n\n" .
                 "Kategori: *{$data['kategori']}*\n" .
                 "Nominal: Rp " . number_format($data['nominal'], 0, ',', '.') . "\n\n" .
                 "Jazakallahu khairan atas kebaikan Anda. Semoga Allah membalas dengan pahala berlipat.\n\n" .
                 "Wassalamu'alaikum warahmatullahi wabarakatuh.";

        $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=" . $data['telegram_chat_id'] . "&text=" . urlencode($pesan) . "&parse_mode=Markdown";
        file_get_contents($url);
    }

    echo "<script>alert('Pembayaran diterima dan notifikasi dikirim'); location.href='keuangan.php';</script>";
    exit;
}

// Jika POST (Tolak)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'tolak') {
    $id = intval($_POST['id']);
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);

    // Update status ke rejected + simpan alasan
    mysqli_query($conn, "UPDATE keuangan_pemasukan SET status='rejected', alasan_penolakan='$alasan' WHERE id='$id'");

    // Ambil data untuk notifikasi
    $data = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT kp.kategori, kp.nominal, u.nama, u.telegram_chat_id
        FROM keuangan_pemasukan kp
        JOIN users u ON kp.user_id = u.id
        WHERE kp.id='$id'
    "));

    // Kirim Notifikasi Telegram (Penolakan)
    if (!empty($data['telegram_chat_id'])) {
       $pesan = "Assalamu'alaikum warahmatullahi wabarakatuh,\n\n" .
                 "Mohon maaf, pembayaran Anda *DITOLAK* karena:\n" .
                 "\"$alasan\"\n\n" .
                 "Silakan upload kembali bukti pembayaran yang benar melalui sistem kami.\n\n" .
                 "Semoga Allah memudahkan segala urusan kita.\n\n" .
                 "Wassalamu'alaikum warahmatullahi wabarakatuh.";

        $url = "https://api.telegram.org/bot$telegramToken/sendMessage?chat_id=" . $data['telegram_chat_id'] . "&text=" . urlencode($pesan) . "&parse_mode=Markdown";
        file_get_contents($url);
    }

    echo "<script>alert('Pembayaran ditolak dan notifikasi dikirim'); location.href='keuangan.php';</script>";
    exit;
}

echo "<script>alert('Aksi tidak valid'); location.href='keuangan.php';</script>";
?>
