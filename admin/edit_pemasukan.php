<?php
session_start();
include '../config/db.php';

$id = $_POST['id'];
$kategori = $_POST['kategori'];
$nominal = $_POST['nominal'];
$keterangan = $_POST['keterangan'];

// Ambil data lama
$old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM keuangan_pemasukan WHERE id='$id'"));

// Cek hak akses
if ($_SESSION['role'] != 'admin' && $old['user_id'] != $_SESSION['id']) {
    echo "<script>alert('Akses ditolak'); location.href='keuangan.php';</script>";
    exit();
}

// Jika admin bisa ganti user
$user_id = ($_SESSION['role'] == 'admin') ? $_POST['user_id'] : $old['user_id'];

// Upload bukti baru jika ada
$bukti = $old['bukti_pembayaran'];
if (!empty($_FILES['bukti']['name'])) {
    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $bukti = time() . '.' . $ext;
    move_uploaded_file($_FILES['bukti']['tmp_name'], "../uploads/" . $bukti);

    // Hapus bukti lama jika ada
    if (!empty($old['bukti_pembayaran']) && file_exists("../uploads/".$old['bukti_pembayaran'])) {
        unlink("../uploads/".$old['bukti_pembayaran']);
    }
}

// Update
mysqli_query($conn, "UPDATE keuangan_pemasukan SET kategori='$kategori', nominal='$nominal', keterangan='$keterangan', bukti_pembayaran='$bukti', user_id='$user_id' WHERE id='$id'");

echo "<script>alert('Data berhasil diperbarui'); location.href='keuangan.php';</script>";
?>
