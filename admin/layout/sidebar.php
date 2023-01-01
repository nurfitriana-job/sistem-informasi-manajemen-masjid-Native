<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel Masjid</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }

    #sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      min-height: 100vh;
      background-color: #198754;
      color: white;
      padding: 20px;
      transition: transform 0.3s ease;
      z-index: 1000;
      overflow-y: auto;
    }

    #sidebar.collapsed {
      transform: translateX(-100%);
    }

    #mainContent {
      margin-left: 250px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }

    #mainContent.full {
      margin-left: 0;
    }

    #toggleSidebar {
      position: fixed;
      top: 15px;
      left: 260px;
      z-index: 1100;
      background-color: #198754;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      transition: left 0.3s ease;
    }

    #sidebar.collapsed + #toggleSidebar {
      left: 15px;
    }

    .nav-link {
      color: white;
    }

    .nav-link:hover {
      background-color:rgba(255, 255, 255, 0.2);
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div id="sidebar">
    <!-- Logo Masjid -->
    <div class="text-center mb-3">
      <img src="../assets/img/logo-masjid.png" alt="Logo Masjid" style="width:100px; height:80px; object-fit:cover; border-radius:50%; background:white; padding:5px;">
    </div>
    <h4 class="mb-4 text-center">Admin Panel</h4>

    <ul class="nav flex-column">
      <!-- Dashboard -->
      <li><a href="dashboard.php" class="nav-link text-white">Dashboard</a></li>

      <!-- Pengumuman -->
      <li>
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#pengumumanMenu" role="button" aria-expanded="false" aria-controls="pengumumanMenu">
          Pengumuman <span class="float-end">▼</span>
        </a>
        <div class="collapse" id="pengumumanMenu">
          <ul class="nav flex-column ms-3">
            <li><a href="acara.php" class="nav-link text-white">Acara</a></li>
            <li><a href="kegiatan.php" class="nav-link text-white">Kegiatan</a></li>
          </ul>
        </div>
      </li>

      <!-- Jadwal Shalat -->
      <li><a href="jadwal-sholat.php" class="nav-link text-white">Jadwal Shalat</a></li>

      <!-- Inventaris -->
      <li><a href="inventaris.php" class="nav-link text-white">Inventaris</a></li>

      <!-- Keuangan -->
      <li><a href="keuangan.php" class="nav-link text-white">Keuangan</a></li>

      <!-- About Masjid -->
      <li><a href="about.php" class="nav-link text-white">About Masjid</a></li>

      <!-- Manajemen Akun -->
      <li>
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#akunMenu" role="button" aria-expanded="false" aria-controls="akunMenu">
          Manajemen Akun <span class="float-end">▼</span>
        </a>
        <div class="collapse" id="akunMenu">
          <ul class="nav flex-column ms-3">
            <li><a href="profil.php" class="nav-link text-white">Profil</a></li>
            <li><a href="user.php" class="nav-link text-white">User</a></li>
          </ul>
        </div>
      </li>

      <!-- Laporan -->
      <li>
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#laporanMenu" role="button" aria-expanded="false" aria-controls="laporanMenu">
          Laporan <span class="float-end">▼</span>
        </a>
        <div class="collapse" id="laporanMenu">
          <ul class="nav flex-column ms-3">
            <li><a href="laporan-keuangan.php" class="nav-link text-white">Laporan Keuangan</a></li>
          </ul>
        </div>
      </li>

      <!-- Logout -->
      <li><a href="../logout.php" class="nav-link text-white">Logout</a></li>
    </ul>
  </div>

  <!-- Tombol Toggle -->
  <button id="toggleSidebar">☰</button>

  <!-- Konten Utama -->
  <div id="mainContent">
    
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('full');
    });
  </script>
</body>
</html>
