<?php
include 'config/db.php';
date_default_timezone_set('Asia/Jakarta');

// Ambil data About (untuk modal dan peta)
$about = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_masjid LIMIT 1"));

// Hari & tanggal
$hariIndo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$hari = $hariIndo[date('l')];
$tanggalMasehi = "$hari, " . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'Masjid Baiturrahman' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="style.css">

  <style>
body {
      background-color: rgba(14, 70, 51, 0.3); /* hijau tua dengan opacity 30% */
      position: relative;
      z-index: 1;
    }
    .background-blur {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('assets/img/background-masjid.png') center center / cover no-repeat;
      background-attachment: fixed;
      filter: blur(0.5px);
      opacity: 0.5;
      z-index: -1;
    }
    .navbar-custom {
      background-color: rgba(13, 179, 102, 0.81); 
      backdrop-filter: blur(1px);
    }
    .marquee {
      background: rgba(5, 116, 64, 0.81);
      backdrop-filter: blur(1px);
      color: white;
      padding: 12px 0;
      font-size: 1rem;
      font-weight: bold;
      white-space: nowrap;
      overflow: hidden;
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 999;
    }
    .marquee span {
      display: inline-block;
      padding-left: 100%;
      animation: marquee 20s linear infinite;
    }
    @keyframes marquee {
      0% { transform: translateX(0%); }
      100% { transform: translateX(-100%); }
    }
    .jadwal-card {
      border-radius: 15px;
      background: #ffffffff;
      transition: 0.3s;
    }
    .jadwal-card.active {
      background: #FFD45A;
      border: 2px solid #FFD45A;
    }
    .jadwal-card:hover {
      transform: scale(1.05);
    }
    .jadwal-card h5 {
      color: #2b9803ff;
      font-weight: bold;
    }
    .jadwal-card p {
      font-size: 1.6rem;
      font-weight: bold;
      color: #005300ff;
    }
    .donasi-card {
      border-radius: 12px;
      background: #ffffffff;
      text-align: center;
      padding: 10px;
    }
    .donasi-card h6 {
      font-size: 14px;
      font-weight: bold;
      color: #bea808ff;
      margin-bottom: 5px;
    }
    .donasi-card h5 {
      font-size: 18px;
      font-weight: bold;
      color: #544d0eff;
    }
    #clock {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 48px;
      color: #ffe209ff; /* emas oranye */
    }
    .countdown-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-size: 20px;
        color: #EECF87; /* emas pucat */
    }
    h3 {
    color: #faf601ff; /* emas */
    }
    .alert-adzan-fullscreen {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.85);
      color: white;
      text-align: center;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      font-size: 2rem;
      font-weight: bold;
      z-index: 9999;
    }
    .alert-adzan-fullscreen button {
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 1.2rem;
    }
  .menu-link {
    text-decoration: none;
    color: #ffffffff; /* Ubah jika ingin warna lain */
    margin-right: 20px;
  }
  .menu-link:hover {
    text-decoration: none;
    color: #fffb00ff; /* Warna saat hover (opsional) */
    margin-right: 20px;
  }
.btn-outline-warning {
    color: #FFD45A !important;        /* teks kuning */
    border-color: #FFD45A !important; /* border kuning */
}

.btn-outline-warning:hover {
    background-color: #ffef0fff !important; /* kuning saat hover */
    color: #0E4633 !important;            /* teks hijau tua */
}
#nextPrayer {
    font-weight: bold; /* biar tegas */
}
.jadwal-title {
    color: #0b5d41ff; /* Kuning emas */
}

.donasi-title {
    color: #044222ff; /* Hijau tua */
}
.tanggal-masehi {
    color: #FFD45A; /* warna emas */
    font-weight: bold;
}

  </style>
</head>
<body>
  <div class="background-blur"></div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
        <img src="assets/img/logo.png" alt="Logo Masjid" style="width: 95px;" class="me-2">
        <span class="text-white fs-7">Masjid Baiturrahman</span>
      </a>
      <div>
        <a href="index.php" class="menu-link">Home</a>
        <a href="tentang.php" class="menu-link">About</a>
        <a href="kegiatan.php" class="menu-link">Kegiatan</a>
        <a href="acara.php" class="menu-link">Acara</a>
        <a href="login.php" class="btn btn-outline-warning btn-sm">Login</a>
      </div>
    </div>
  </nav>

