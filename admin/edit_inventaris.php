<?php
include '../config/db.php';
$id = $_POST['id'];
$nama = $_POST['nama_barang'];
$asal = $_POST['asal'];
$kondisi = $_POST['kondisi'];
$jenis = $_POST['jenis'];
$jumlah = $_POST['jumlah'];
$keterangan = $_POST['keterangan'];

mysqli_query($conn, "UPDATE inventaris SET nama_barang='$nama', asal='$asal', kondisi='$kondisi', jenis='$jenis', jumlah='$jumlah', keterangan='$keterangan' WHERE id='$id'");
header("Location: inventaris.php");
