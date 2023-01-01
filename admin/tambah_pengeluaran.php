<?php
include '../config/db.php';
if($_POST){
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    mysqli_query($conn, "INSERT INTO keuangan_pengeluaran (nominal, keterangan, tanggal) VALUES ('$nominal','$keterangan',NOW())");
}
header('Location: keuangan.php');
