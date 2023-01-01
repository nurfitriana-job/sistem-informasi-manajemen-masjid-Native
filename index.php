<?php
include 'config/db.php';
date_default_timezone_set('Asia/Jakarta');

// Ambil data About
$about = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM about_masjid LIMIT 1"));

// Ambil jadwal sholat
$tanggalHariIni = date('Y-m-d');
$jadwalQuery = mysqli_query($conn, "SELECT * FROM jadwal_sholat WHERE tanggal='$tanggalHariIni' LIMIT 1");

$subuh = $dzuhur = $ashar = $maghrib = $isya = "00:00";
if (mysqli_num_rows($jadwalQuery) > 0) {
    $jadwal = mysqli_fetch_assoc($jadwalQuery);
    $subuh = substr($jadwal['subuh'], 0, 5);
    $dzuhur = substr($jadwal['dzuhur'], 0, 5);
    $ashar = substr($jadwal['ashar'], 0, 5);
    $maghrib = substr($jadwal['maghrib'], 0, 5);
    $isya = substr($jadwal['isya'], 0, 5);
}

// Ambil zakat, infaq, sedekah
$zakat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='zakat'"))['total'] ?? 0;
$infaqmasjid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqmasjid'"))['total'] ?? 0;
$infaqanakyatim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqanakyatim'"))['total'] ?? 0;
$sedekah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='sedekah'"))['total'] ?? 0;

// Ambil pengeluaran
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pengeluaran"))['total'] ?? 0;

// Hitung saldo total
$totalSaldo = $zakat + $infaqmasjid + $infaqanakyatim + $sedekah - $pengeluaran;

// Filter acara & kegiatan yang belum lewat
$nowDate = date('Y-m-d');
$nowTime = date('H:i:s');

$acara = mysqli_query($conn, "
    SELECT judul, tanggal, jam 
    FROM pengumuman_acara 
    WHERE tanggal > '$nowDate' OR (tanggal = '$nowDate' AND jam >= '$nowTime') 
    ORDER BY tanggal ASC, jam ASC LIMIT 10
");

$kegiatan = mysqli_query($conn, "
    SELECT judul, tanggal, jam 
    FROM pengumuman_kegiatan 
    WHERE tanggal > '$nowDate' OR (tanggal = '$nowDate' AND jam >= '$nowTime') 
    ORDER BY tanggal ASC, jam ASC LIMIT 10
");

// Hari & tanggal
$hariIndo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$hari = $hariIndo[date('l')];
$tanggalMasehi = "$hari, " . date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masjid Baiturrahman</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
body { background:#f8f9fa; }
.navbar-brand img { width:80px; }
.jadwal-card { border-radius:15px; background:#e9f7ef; transition:0.3s; }
.jadwal-card.active { background:#a3e4d7; border:2px solid #198754; }
.jadwal-card:hover { transform:scale(1.05); }
.jadwal-card h5 { color:#198754; font-weight:bold; }
.jadwal-card p { font-size:1.6rem; font-weight:bold; color:#212529; }
.donasi-card { border-radius:12px; background:#f0f3f4; text-align:center; padding:10px; }
.donasi-card h6 { font-size:14px; color:#198754; margin-bottom:5px; }
.donasi-card h5 { font-size:18px; font-weight:bold; }
.marquee { background:#198754; color:white; padding:12px 0; font-size:1rem; font-weight:bold; white-space:nowrap; overflow:hidden; position:fixed; bottom:0; width:100%; z-index:999; }
.marquee span { display:inline-block; padding-left:100%; animation:marquee 20s linear infinite; }
@keyframes marquee { 0%{transform:translateX(0%);}100%{transform:translateX(-100%);} }
#clock { font-size:2.5rem; color:#198754; }
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

<!-- Navbar -->
 <body>
 <div class="background-blur"></div>

  <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand d-flex align-items-center fw-bold" href="#">
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
<!-- Tanggal -->
<div class="container text-center mt-3">
    <h4 style="color: #fffb00ff; font-weight: bold;">
        <?= $tanggalMasehi; ?>
    </h4>
</div>


<!-- Jam -->
<div class="container mt-4 text-center">
    <h2 id="clock" class="fw-bold"></h2>
<h4 id="nextPrayer" class="text-success mt-2"></h4>
    <audio id="adzanSubuh" src="assets/audio/adzan-subuh.mp3" preload="auto"></audio>
    <audio id="adzanBiasa" src="assets/audio/adzan-biasa.mp3" preload="auto"></audio>
</div>

<!-- Alert Fullscreen -->
<div id="adzanAlert" class="alert-adzan-fullscreen">
    <div id="alertText"></div>
    <button onclick="stopAdzan()" class="btn btn-danger">STOP</button>
</div>

<!-- Jadwal Sholat -->
<div class="container mt-4 mb-4">
<h3 class="text-center mb-3 fw-bold jadwal-title">Jadwal Shalat Hari Ini</h3>
<div class="row justify-content-center text-center">
<?php
$waktuShalat = ['Subuh'=>$subuh,'Dzuhur'=>$dzuhur,'Ashar'=>$ashar,'Maghrib'=>$maghrib,'Isya'=>$isya];
foreach($waktuShalat as $nama=>$jam){
    echo "<div class='col-6 col-md-2 mb-3'><div class='card jadwal-card p-2 shadow-sm' id='card-$nama'><div class='card-body'><h5>$nama</h5><p>$jam</p></div></div></div>";
}
?>
</div>
</div>

<!-- Info Donasi -->
<div class="container mb-4">
<h3 class="text-center mb-3 fw-bold donasi-title">Info Donasi</h3>
<div class="row justify-content-center text-center">
    <div class="col-4 col-md-2"><div class="donasi-card"><h6>Zakat</h6><h5>Rp <?= number_format($zakat,0,',','.'); ?></h5></div></div>
    <div class="col-4 col-md-2"><div class="donasi-card"><h6>Infaq Masjid</h6><h5>Rp <?= number_format($infaqmasjid,0,',','.'); ?></h5></div></div>
    <div class="col-4 col-md-2"><div class="donasi-card"><h6>Infaq Anak Yatim</h6><h5>Rp <?= number_format($infaqanakyatim,0,',','.'); ?></h5></div></div>
    <div class="col-4 col-md-2"><div class="donasi-card"><h6>Sedekah</h6><h5>Rp <?= number_format($sedekah,0,',','.'); ?></h5></div></div>
</div>
</div>

<!-- Running Text -->
<div class="marquee">
<span>
Total Saldo: Rp <?= number_format($totalSaldo, 0, ',', '.'); ?> |
<?php
$items = [];
while($a = mysqli_fetch_assoc($acara)) {
    $tgl = date('d M Y', strtotime($a['tanggal']));
    $jam = date('H:i', strtotime($a['jam']));
    $items[] = "📢 Acara: " . htmlspecialchars($a['judul']) . " pada $tgl jam $jam";
}
while($k = mysqli_fetch_assoc($kegiatan)) {
    $tgl = date('d M Y', strtotime($k['tanggal']));
    $jam = date('H:i', strtotime($k['jam']));
    $items[] = "📌 Kegiatan: " . htmlspecialchars($k['judul']) . " pada $tgl jam $jam";
}
echo implode(" &nbsp; • &nbsp; ", $items);
?>
</span>
</div>

<footer class="text-center mt-3 mb-4 text-muted">
&copy; <?= date('Y'); ?> Sistem Informasi Masjid Baiturrahman
</footer>

<!-- Modal About -->
<div class="modal fade" id="aboutModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Tentang Masjid</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h4 class="fw-bold"><?= htmlspecialchars($about['nama']); ?></h4>
        <p><strong>Alamat:</strong> <?= htmlspecialchars($about['alamat']); ?></p>
        <p><?= nl2br(htmlspecialchars($about['deskripsi'])); ?></p>
        <h5 class="mt-3 text-success">Visi</h5>
        <p><?= nl2br(htmlspecialchars($about['visi'])); ?></p>
        <h5 class="mt-3 text-success">Misi</h5>
        <p><?= nl2br(htmlspecialchars($about['misi'])); ?></p>
        <div id="map" style="height:300px; border-radius:10px;"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var lat = <?= $about['latitude'] ?: -6.200000 ?>;
    var lng = <?= $about['longitude'] ?: 106.816666 ?>;
    var map;

    var modal = document.getElementById('aboutModal');
    modal.addEventListener('shown.bs.modal', function () {
        if (!map) {
            map = L.map('map').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Masjid").openPopup();
        } else {
            map.invalidateSize();
        }
    });
});

const jadwal = {
    Subuh: "<?= $subuh; ?>",
    Dzuhur: "<?= $dzuhur; ?>",
    Ashar: "<?= $ashar; ?>",
    Maghrib: "<?= $maghrib; ?>",
    Isya: "<?= $isya; ?>"
};
const adzanAlert = document.getElementById('adzanAlert');
const alertText = document.getElementById('alertText');
const adzanSubuh = document.getElementById('adzanSubuh');
const adzanBiasa = document.getElementById('adzanBiasa');
let alreadyPlayed = false;

function updateClock(){
    const now=new Date();
    const jam=String(now.getHours()).padStart(2,'0');
    const menit=String(now.getMinutes()).padStart(2,'0');
    const detik=String(now.getSeconds()).padStart(2,'0');
    document.getElementById('clock').textContent=`${jam}:${menit}:${detik}`;

    const nowSec = now.getHours()*3600 + now.getMinutes()*60 + now.getSeconds();
    let nextShalat=null;
    const times = Object.entries(jadwal);

    document.querySelectorAll('.jadwal-card').forEach(card=>card.classList.remove('active'));

    for(let [nama,time] of times){
        const [h,m]=time.split(':').map(Number);
        const targetSec=h*3600+m*60;
        if(targetSec>nowSec && !nextShalat){
            nextShalat={nama,targetSec};
            document.getElementById('card-'+nama).classList.add('active');
        }
        if(targetSec===nowSec && !alreadyPlayed){
            adzanAlert.style.display='flex';
            alertText.innerHTML=`Waktu Sholat ${nama} sudah masuk!`;
            if(nama==='Subuh'){adzanSubuh.play();} else {adzanBiasa.play();}
            alreadyPlayed=true;
        }
    }

    if(!nextShalat){
        nextShalat={nama:'Subuh',targetSec:24*3600+(parseInt(jadwal['Subuh'].split(':')[0])*3600+parseInt(jadwal['Subuh'].split(':')[1])*60)};
        document.getElementById('card-Subuh').classList.add('active');
    }

    const selisih = nextShalat.targetSec - nowSec;
    const h = String(Math.floor(selisih/3600)).padStart(2,'0');
    const m = String(Math.floor((selisih%3600)/60)).padStart(2,'0');
    const s = String(selisih%60).padStart(2,'0');

    document.getElementById('nextPrayer').textContent=`Menuju Shalat ${nextShalat.nama}: ${h}:${m}:${s}`;
    let el = document.getElementById('nextPrayer');
    el.textContent = `Menuju Shalat ${nextShalat.nama}: ${h}:${m}:${s}`;
    el.style.setProperty('color', '#ffffffff', 'important'); // kuning emas
    el.style.fontWeight = 'bold';

}

function stopAdzan(){
    adzanSubuh.pause();adzanBiasa.pause();
    adzanSubuh.currentTime=0;adzanBiasa.currentTime=0;
    adzanAlert.style.display='none';
}
setInterval(updateClock,1000);updateClock();
</script>

</body>
</html>
