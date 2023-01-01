<!-- Tambahkan ini di dalam <head> -->
<style>
  .marquee {
    background-color: #f9faf8ff;
    padding: 8px 0;
    overflow: hidden;
    white-space: nowrap;
  }
  .marquee span {
    display: inline-block;
    padding-left: 100%;
    animation: marquee 15s linear infinite;
  }
  @keyframes marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
  }

  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  main {
    flex: 1;
  }

  footer {
    flex-shrink: 0;
  }
</style>
<!-- Di dalam body -->
<body>
  <!-- Marquee -->


  <!-- Konten utama -->
  <main class="container my-4">
    <!-- Konten dinamis di sini -->
  </main>

  <!-- Footer -->
  <footer class="bg-light py-3 text-center text-muted">
    <div class="container">
      <small>&copy; <?= date('Y'); ?> Sistem Informasi Masjid Baiturrahman</small>
    </div>
  </footer>

  <!-- Script Bootstrap & Leaflet -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</body>
</html>
