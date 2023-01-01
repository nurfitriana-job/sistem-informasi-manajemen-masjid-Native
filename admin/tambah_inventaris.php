<?php
include '../config/db.php';
$nama = $_POST['nama_barang'];
$asal = $_POST['asal'];
$kondisi = $_POST['kondisi'];
$jenis = $_POST['jenis'];
$jumlah = $_POST['jumlah'];
$keterangan = $_POST['keterangan'];
$tanggal = date('Y-m-d');

mysqli_query($conn, "INSERT INTO inventaris (nama_barang, asal, kondisi, jenis, jumlah, keterangan, tanggal) VALUES ('$nama','$asal','$kondisi','$jenis','$jumlah','$keterangan','$tanggal')");
header("Location: inventaris.php");
