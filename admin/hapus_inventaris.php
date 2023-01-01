<?php
include '../config/db.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM inventaris WHERE id='$id'");
header("Location: inventaris.php");
