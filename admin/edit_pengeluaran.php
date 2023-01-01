<?php
include '../config/db.php';
if($_POST){
    $id = $_POST['id'];
    $nominal = $_POST['nominal'];
    $keterangan = $_POST['keterangan'];
    mysqli_query($conn, "UPDATE keuangan_pengeluaran SET nominal='$nominal', keterangan='$keterangan' WHERE id='$id'");
}
header('Location: keuangan.php');
