<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Panel Masjid</title>
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
      background-color: #00a77dff;
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
      min-height: 100vh;
    }

    #mainContent.full {
      margin-left: 0;
    }

    #toggleSidebar {
      position: fixed;
      top: 15px;
      left: 260px;
      z-index: 1100;
      background-color: #00a77dff;
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
      background-color: rgba(255, 255, 255, 0.2);
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div id="sidebar">
    <div class="text-center mb-3">
      <img src="../assets/img/logo-masjid.png" alt="Logo Masjid" style="width:100px; height:80px; object-fit:cover; border-radius:50%; background:white; padding:5px;">
    </div>
    <h4 class="mb-4 text-center">User Panel</h4>

    <ul class="nav flex-column">
      <li><a href="dashboard.php" class="nav-link text-white">Dashboard</a></li>

      <li>
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#pengumumanUser" role="button" aria-expanded="false" aria-controls="pengumumanUser">
          Pengumuman <span class="float-end">▼</span>
        </a>
        <div class="collapse" id="pengumumanUser">
          <ul class="nav flex-column ms-3">
            <li><a href="acara.php" class="nav-link text-white">Acara</a></li>
            <li><a href="kegiatan.php" class="nav-link text-white">Kegiatan</a></li>
          </ul>
        </div>
      </li>

      <li><a href="jadwal-sholat.php" class="nav-link text-white">Jadwal Shalat</a></li>

      <li>
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#keuanganUser" role="button" aria-expanded="false" aria-controls="keuanganUser">
          Keuangan <span class="float-end">▼</span>
        </a>
        <div class="collapse" id="keuanganUser">
          <ul class="nav flex-column ms-3">
            <li><a href="pemasukan.php" class="nav-link text-white">Pemasukan</a></li>
            <li><a href="pengeluaran.php" class="nav-link text-white">Pengeluaran</a></li>
          </ul>
        </div>
      </li>

      <li><a href="profil.php" class="nav-link text-white">Profil</a></li>
      <li><a href="../logout.php" class="nav-link text-white">Logout</a></li>
    </ul>
  </div>

  <!-- Tombol Toggle -->
  <button id="toggleSidebar">☰</button>

  <!-- Konten Utama -->
  <div id="mainContent">
    <!-- Konten dinamis akan ditampilkan di sini -->
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
