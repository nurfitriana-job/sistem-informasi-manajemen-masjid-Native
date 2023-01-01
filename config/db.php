<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_masjid";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
$telegramToken = "792929666:AAEXIJ_t-xpR3hv8vJbz53qPKR6uI6NNzTk"; // Ganti dengan token bot kamu

?>
