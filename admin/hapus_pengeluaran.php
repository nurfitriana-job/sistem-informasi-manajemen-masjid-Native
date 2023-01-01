<?php
include '../config/db.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM keuangan_pengeluaran WHERE id='$id'");
header('Location: keuangan.php');
