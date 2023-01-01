<?php
$title = "Manajemen Keuangan";
include 'layout/header.php';
include 'layout/sidebar.php';
include '../config/db.php';

// Ringkasan saldo
$zakat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='zakat' AND status='verified'"));
$infaqmasjid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqmasjid' AND status='verified'"));
$infaqanakyatim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='infaqanakyatim' AND status='verified'"));
$sedekah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pemasukan WHERE kategori='sedekah' AND status='verified'"));
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM keuangan_pengeluaran"));
$totalSaldo = ($zakat['total'] ?? 0) + ($infaqmasjid['total'] ?? 0) + ($infaqanakyatim['total'] ?? 0) + ($sedekah['total'] ?? 0) - ($pengeluaran['total'] ?? 0);

// Ambil list pemasukan
$pemasukanList = mysqli_query($conn, "
    SELECT kp.*, u.nama AS nama_user 
    FROM keuangan_pemasukan kp 
    LEFT JOIN users u ON kp.user_id = u.id 
    ORDER BY kp.tanggal DESC
");

// Ambil list pengeluaran
$pengeluaranList = mysqli_query($conn, "SELECT * FROM keuangan_pengeluaran ORDER BY tanggal DESC");
?>

<div class="container-fluid">
    <h2 class="mb-4">Manajemen Keuangan Masjid</h2>

    <!-- Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-3"><div class="card shadow-sm text-center"><div class="card-body"><h6>Zakat</h6><h5 class="text-success">Rp <?= number_format($zakat['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-2"><div class="card shadow-sm text-center"><div class="card-body"><h6>Infaq Masjid</h6><h5 class="text-success">Rp <?= number_format($infaqmasjid['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-2"><div class="card shadow-sm text-center"><div class="card-body"><h6>Infaq Anak Yatim</h6><h5 class="text-success">Rp <?= number_format($infaqanakyatim['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-2"><div class="card shadow-sm text-center"><div class="card-body"><h6>Sedekah</h6><h5 class="text-success">Rp <?= number_format($sedekah['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm text-center"><div class="card-body"><h6>Pengeluaran</h6><h5 class="text-danger">Rp <?= number_format($pengeluaran['total'] ?? 0, 0, ',', '.'); ?></h5></div></div></div>
    </div>

    <!-- Saldo -->
    <div class="card mb-4 shadow-sm text-center bg-light">
        <div class="card-body">
            <h5>Total Saldo</h5>
            <h3 class="text-primary">Rp <?= number_format($totalSaldo, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <!-- Tombol -->
    <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPemasukan">+ Tambah Pemasukan</button>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalPengeluaran">+ Tambah Pengeluaran</button>
    </div>

    <!-- Tabel Pemasukan -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">Daftar Pemasukan</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th><th>Kategori</th><th>Nominal</th><th>Keterangan</th><th>Jamaah</th><th>Status</th><th>Bukti</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = mysqli_fetch_assoc($pemasukanList)): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($p['tanggal'])); ?></td>
                            <td><?= ucfirst($p['kategori']); ?></td>
                            <td>Rp <?= number_format($p['nominal'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($p['keterangan']); ?></td>
                            <td><?php   if ($p['user_id'] == 0) {
                                        echo "Hamba Allah";
                                        } else {
                                        echo $p['nama_user'] ?: '-';
                                        } ?></td>
                            <td>
                                <?php if($p['status'] == 'pending'): ?>
                                    <span class="badge bg-warning">Menunggu</span>
                                <?php elseif($p['status'] == 'verified'): ?>
                                    <span class="badge bg-success">Terverifikasi</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['bukti_pembayaran']): ?>
                                    <a href="../uploads/<?= $p['bukti_pembayaran']; ?>" target="_blank">Lihat</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($p['status'] == 'pending'): ?>
                                    <a href="verifikasi.php?id=<?= $p['id']; ?>&aksi=terima" class="btn btn-success btn-sm">Terima</a>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tolak<?= $p['id']; ?>">Tolak</button>
                                <?php endif; ?>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPemasukan<?= $p['id']; ?>">Edit</button>
                                <a href="hapus_pemasukan.php?id=<?= $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        </tr>

                        <!-- Modal Tolak -->
                        <div class="modal fade" id="tolak<?= $p['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="verifikasi.php">
                                        <div class="modal-header bg-danger text-white">
                                            <h5>Tolak Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                            <input type="hidden" name="aksi" value="tolak">
                                            <div class="mb-3">
                                                <label>Alasan Penolakan</label>
                                                <textarea name="alasan" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-danger" type="submit">Kirim</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit Pemasukan -->
                        <div class="modal fade" id="editPemasukan<?= $p['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5>Edit Pemasukan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="edit_pemasukan.php" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $p['id']; ?>">
                                            <div class="mb-3">
                                                <label>Kategori</label>
                                                <select name="kategori" class="form-select">
                                                    <option value="zakat" <?= $p['kategori']=='zakat'?'selected':''; ?>>Zakat</option>
                                                    <option value="infaqmasjid" <?= $p['kategori']=='infaqmasjid'?'selected':''; ?>>Infaq Masjid</option>
                                                    <option value="infaqanakyatim" <?= $p['kategori']=='infaqanakyatim'?'selected':''; ?>>Infaq Anak Yatim</option>
                                                    <option value="sedekah" <?= $p['kategori']=='sedekah'?'selected':''; ?>>Sedekah</option>
                                                </select>
                                            </div>
                                            <div class="mb-3"><label>Nominal</label><input type="number" name="nominal" class="form-control" value="<?= $p['nominal']; ?>"></div>
                                            <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"><?= $p['keterangan']; ?></textarea></div>
                                            <div class="mb-3">
                                                <label>Jamaah</label>
                                                <select name="user_id" class="form-select">
                                                    <?php
                                                    $userQuery = mysqli_query($conn, "SELECT id, nama FROM users WHERE role='user'");
                                                    while ($u = mysqli_fetch_assoc($userQuery)): ?>
                                                        <option value="<?= $u['id']; ?>" <?= $u['id']==$p['user_id']?'selected':''; ?>><?= $u['nama']; ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Bukti Pembayaran</label>
                                                <input type="file" name="bukti" class="form-control">
                                                <?php if ($p['bukti_pembayaran']): ?>
                                                    <small>File lama: <a href="../uploads/<?= $p['bukti_pembayaran']; ?>" target="_blank"><?= $p['bukti_pembayaran']; ?></a></small>
                                                <?php endif; ?>
                                            </div>
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

    <!-- Tabel Pengeluaran -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-danger text-white">Daftar Pengeluaran</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Tanggal</th><th>Nominal</th><th>Keterangan</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($pg = mysqli_fetch_assoc($pengeluaranList)): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($pg['tanggal'])); ?></td>
                            <td>Rp <?= number_format($pg['nominal'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($pg['keterangan']); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPengeluaran<?= $pg['id']; ?>">Edit</button>
                                <a href="hapus_pengeluaran.php?id=<?= $pg['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        </tr>

                        <!-- Modal Edit Pengeluaran -->
                        <div class="modal fade" id="editPengeluaran<?= $pg['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5>Edit Pengeluaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="edit_pengeluaran.php">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $pg['id']; ?>">
                                            <div class="mb-3"><label>Nominal</label><input type="number" name="nominal" class="form-control" value="<?= $pg['nominal']; ?>"></div>
                                            <div class="mb-3"><label>Keterangan</label><textarea name="keterangan" class="form-control"><?= $pg['keterangan']; ?></textarea></div>
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

<!-- Modal Tambah Pemasukan -->
<div class="modal fade" id="modalPemasukan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="tambah_pemasukan.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="zakat">Zakat</option>
                            <option value="infaqmasjid">Infaq Masjid</option>
                            <option value="infaqanakyatim">Infaq Anak Yatim</option>
                            <option value="sedekah">Sedekah</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Nominal</label>
                        <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Jamaah</label>
                        <select name="user_id" class="form-select" required>
                            <option value="0">Hamba Allah</option> <!-- Tambahan -->
                            <?php
                            $userList = mysqli_query($conn, "SELECT * FROM users WHERE role='user'");
                            while ($u = mysqli_fetch_assoc($userList)) {
                                echo "<option value='{$u['id']}'>{$u['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Bukti Pembayaran</label>
                        <input type="file" name="bukti" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="modalPengeluaran" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tambah Pengeluaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="tambah_pengeluaran.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nominal</label>
                        <input type="number" name="nominal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
