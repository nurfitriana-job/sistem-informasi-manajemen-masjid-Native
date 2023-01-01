<?php
session_start();
include '../config/db.php';

$id = $_GET['id'];

// Ambil data lama
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM keuangan_pemasukan WHERE id='$id'"));

// Cek hak akses
if ($_SESSION['role'] != 'admin' && $data['user_id'] != $_SESSION['id']) {
    echo "<script>alert('Akses ditolak'); location.href='keuangan.php';</script>";
    exit();
}

// Hapus file bukti
if (!empty($data['bukti_pembayaran']) && file_exists("../uploads/".$data['bukti_pembayaran'])) {
    unlink("../uploads/".$data['bukti_pembayaran']);
}

// Hapus data
mysqli_query($conn, "DELETE FROM keuangan_pemasukan WHERE id='$id'");

echo "<script>alert('Data berhasil dihapus'); location.href='keuangan.php';</script>";
?>
