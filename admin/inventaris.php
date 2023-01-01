<?php
$title = "Inventaris Masjid";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Ambil data inventaris
$inventarisList = mysqli_query($conn, "SELECT * FROM inventaris ORDER BY tanggal DESC");
?>

<div class="container-fluid">
    <h2 class="mb-4">Manajemen Inventaris Masjid</h2>

    <!-- Tombol Tambah + Export -->
    <div class="d-flex justify-content-between mb-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahInventaris">
        + Tambah Barang
    </button>
    <div>
        <a href="export_inventaris_pdf.php" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExport">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </button>
    </div>
</div>

    <!-- Tabel Inventaris -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">Data Inventaris</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Asal Barang</th>
                        <th>Kondisi</th>
                        <th>Jenis Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($inv = mysqli_fetch_assoc($inventarisList)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($inv['nama_barang']); ?></td>
                            <td><?= ucfirst($inv['asal']); ?></td>
                            <td><?= ucfirst($inv['kondisi']); ?></td>
                            <td><?= ucfirst($inv['jenis']); ?></td>
                            <td><?= $inv['jumlah']; ?></td>
                            <td><?= htmlspecialchars($inv['keterangan']); ?></td>
                            <td><?= date('d-m-Y', strtotime($inv['tanggal'])); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editInventaris<?= $inv['id']; ?>">Edit</button>
                                <a href="hapus_inventaris.php?id=<?= $inv['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        </tr>

                        <!-- Modal Edit Inventaris -->
                        <div class="modal fade" id="editInventaris<?= $inv['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5>Edit Inventaris</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="edit_inventaris.php">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $inv['id']; ?>">
                                            <div class="mb-3">
                                                <label>Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control" value="<?= $inv['nama_barang']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Asal Barang</label>
                                                <select name="asal" class="form-select">
                                                    <option value="sedekah" <?= $inv['asal']=='sedekah'?'selected':''; ?>>Sedekah</option>
                                                    <option value="pembelian" <?= $inv['asal']=='pembelian'?'selected':''; ?>>Pembelian</option>
                                                    <option value="hibah" <?= $inv['asal']=='hibah'?'selected':''; ?>>Hibah</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Kondisi</label>
                                                <select name="kondisi" class="form-select">
                                                    <option value="baik" <?= $inv['kondisi']=='baik'?'selected':''; ?>>Baik</option>
                                                    <option value="rusak" <?= $inv['kondisi']=='rusak'?'selected':''; ?>>Rusak</option>
                                                    <option value="perlu perbaikan" <?= $inv['kondisi']=='perlu perbaikan'?'selected':''; ?>>Perlu Perbaikan</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Jenis Barang</label>
                                                <select name="jenis" class="form-select">
                                                    <option value="elektronik" <?= $inv['jenis']=='elektronik'?'selected':''; ?>>Elektronik</option>
                                                    <option value="perabot" <?= $inv['jenis']=='perabot'?'selected':''; ?>>Perabot</option>
                                                    <option value="alat ibadah" <?= $inv['jenis']=='alat ibadah'?'selected':''; ?>>Alat Ibadah</option>
                                                    <option value="lainnya" <?= $inv['jenis']=='lainnya'?'selected':''; ?>>Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="mb-3"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" value="<?= $inv['jumlah']; ?>" required></div>
                                            <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"><?= $inv['keterangan']; ?></textarea></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-warning" type="submit">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Inventaris -->
<div class="modal fade" id="modalTambahInventaris" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5>Tambah Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="tambah_inventaris.php">
                <div class="modal-body">
                    <div class="mb-3"><label>Nama Barang</label><input type="text" name="nama_barang" class="form-control" required></div>
                    <div class="mb-3">
                        <label>Asal Barang</label>
                        <select name="asal" class="form-select" required>
                            <option value="sedekah">Sedekah</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="hibah">Hibah</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Kondisi</label>
                        <select name="kondisi" class="form-select" required>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="perlu perbaikan">Perlu Perbaikan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jenis Barang</label>
                        <select name="jenis" class="form-select" required>
                            <option value="elektronik">Elektronik</option>
                            <option value="perabot">Perabot</option>
                            <option value="alat ibadah">Alat Ibadah</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" required></div>
                    <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Export Excel -->
<div class="modal fade" id="modalExport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5>Export Data Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="export_inventaris_excel.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Pilih Periode</label>
                        <select name="periode" class="form-select" required>
                            <option value="harian">Harian</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="semua">Semua Data</option>
                        </select>
                    </div>
                    <div class="mb-3"><label>Tanggal</label><input type="date" name="tanggal" class="form-control"></div>
                    <div class="mb-3"><label>Bulan</label><input type="month" name="bulan" class="form-control"></div>
                    <div class="mb-3"><label>Tahun</label><input type="number" name="tahun" class="form-control" placeholder="Contoh: 2025"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
