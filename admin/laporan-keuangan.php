<?php
session_start();

// =========================
// Load Library Tanpa Composer
// =========================
require_once __DIR__ . '/../library/vendor/autoload.php';  // mPDF + PhpSpreadsheet
require_once __DIR__ . '/../config/db.php';

// Gunakan namespace
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Mpdf\Mpdf;

// ========================================================
// PROSES EXPORT
// ========================================================
if (isset($_POST['export_excel']) || isset($_POST['export_pdf'])) {
    // Ambil filter
    $bulan    = $_POST['bulan']    ?? '';
    $tahun    = $_POST['tahun']    ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $jamaah   = $_POST['jamaah']   ?? '';

    // Query dasar
    $query = "SELECT k.*, u.nama AS nama_jamaah 
              FROM keuangan_pemasukan k
              LEFT JOIN users u ON k.user_id = u.id
              WHERE 1";

    // Filter kondisi
    if (!empty($bulan))    $query .= " AND MONTH(k.tanggal) = '" . intval($bulan) . "'";
    if (!empty($tahun))    $query .= " AND YEAR(k.tanggal) = '" . intval($tahun) . "'";
    if (!empty($kategori)) $query .= " AND k.kategori = '" . mysqli_real_escape_string($conn, $kategori) . "'";
    if (!empty($jamaah) && $jamaah != 'all') $query .= " AND k.user_id = '" . intval($jamaah) . "'";

    $query .= " ORDER BY k.tanggal ASC";
    $result = mysqli_query($conn, $query);

    // ================= EXPORT EXCEL =================
    if (isset($_POST['export_excel'])) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul laporan
        $sheet->setCellValue('A1', 'LAPORAN KEUANGAN MASJID');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header kolom
        $headers = ['No', 'Nama Jamaah', 'Kategori', 'Nominal', 'Keterangan', 'Tanggal', 'Status'];
        $col = 'A';
        $rowHeader = 3;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $rowHeader, $header);
            $sheet->getStyle($col . $rowHeader)->getFont()->setBold(true);
            $sheet->getStyle($col . $rowHeader)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $rowHeader)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($col . $rowHeader)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9D9D9'); // abu-abu muda
            $col++;
        }

        // Isi data
        $row = 4;
        $no = 1;
        while ($data = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue("A$row", $no++);
            $sheet->setCellValue("B$row", $data['nama_jamaah'] ?? 'Tidak Diketahui');
            $sheet->setCellValue("C$row", ucfirst($data['kategori']));
            $sheet->setCellValue("D$row", $data['nominal']);
            $sheet->setCellValue("E$row", $data['keterangan']);
            $sheet->setCellValue("F$row", date('d-m-Y', strtotime($data['tanggal'])));
            $sheet->setCellValue("G$row", ucfirst($data['status']));

            // Format angka (Rp)
            $sheet->getStyle("D$row")->getNumberFormat()->setFormatCode('#,##0');

            // Border untuk tiap cell
            foreach (range('A','G') as $col) {
                $sheet->getStyle($col.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            $row++;
        }

        // Auto size kolom
        foreach (range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output ke Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = "laporan_keuangan_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

   // ================= EXPORT PDF =================
if (isset($_POST['export_pdf'])) {
    // Ambil data masjid untuk kop
    $qMasjid = mysqli_query($conn, "SELECT * FROM about_masjid LIMIT 1");
    $masjid = mysqli_fetch_assoc($qMasjid);

    $nama_masjid   = $masjid['nama_masjid'] ?? 'Masjid Baiturrahman';
    $alamat_masjid = $masjid['alamat'] ?? 'Alamat belum tersedia';

// Awal HTML
$html = '
<style>
    body { font-family: Arial, sans-serif; }
    .kop-title { text-align: center; margin-bottom: 5px; }
    .kop-title h2 { margin: 0; font-size: 18pt; }
    .kop-title p { margin: 0; font-size: 10pt; }
    .line { border-top: 2px solid #000; margin: 8px 0 15px 0; }
    .sub-judul { text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 15px; }
    table { border-collapse: collapse; width: 100%; font-size: 11pt; }
    th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    th { background-color: #f2f2f2; }
</style>

<div class="kop-title">
    <h2>' . strtoupper($nama_masjid) . '</h2>
    <p>' . $alamat_masjid . '</p>
</div>

<div class="line"></div>

<div class="sub-judul">Laporan Inventaris Masjid</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Jamaah</th>
            <th>Kategori</th>
            <th>Nominal</th>
            <th>Keterangan</th>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';


    $no = 1;
    mysqli_data_seek($result, 0); // reset pointer
    while ($data = mysqli_fetch_assoc($result)) {
        $html .= '
        <tr>
            <td>' . $no++ . '</td>
            <td>' . ($data['nama_jamaah'] ?? 'Tidak Diketahui') . '</td>
            <td>' . ucfirst($data['kategori']) . '</td>
            <td style="text-align:right;">Rp ' . number_format($data['nominal'], 0, ',', '.') . '</td>
            <td>' . $data['keterangan'] . '</td>
            <td>' . date('d-m-Y', strtotime($data['tanggal'])) . '</td>
            <td>' . ucfirst($data['status']) . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';

    // Output PDF
    $mpdf = new Mpdf(['orientation' => 'L']); // Landscape
    $mpdf->WriteHTML($html);
    $fileName = "laporan_keuangan_" . date('Ymd_His') . ".pdf";
    $mpdf->Output($fileName, 'D');
    exit();
}
}
?>

<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div class="container-fluid">
    <h2 class="mb-4">Laporan Keuangan Masjid</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">Filter & Export Laporan</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label>Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">-- Pilih Bulan --</option>
                        <?php
                        for ($m = 1; $m <= 12; $m++) {
                            echo "<option value='$m'>" . date('F', mktime(0, 0, 0, $m, 10)) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tahun</label>
                    <select name="tahun" class="form-select">
                        <option value="">-- Pilih Tahun --</option>
                        <?php
                        $startYear = 2023;
                        $currentYear = date('Y');
                        for ($y = $startYear; $y <= $currentYear; $y++) {
                            echo "<option value='$y'>$y</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua</option>
                        <option value="zakat">Zakat</option>
                        <option value="infaqmasjid">Infaq Masjid</option>
                        <option value="infaqanakyatim">Infaq Anak Yatim</option>
                        <option value="sedekah">Sedekah</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Nama Jamaah</label>
                    <select name="jamaah" class="form-select">
                        <option value="all">Semua Jamaah</option>
                        <?php
                        $users = mysqli_query($conn, "SELECT id, nama FROM users WHERE role='user' ORDER BY nama ASC");
                        while ($u = mysqli_fetch_assoc($users)) {
                            echo "<option value='{$u['id']}'>{$u['nama']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-12 mt-3 d-flex gap-2">
                    <button type="submit" name="export_excel" class="btn btn-success w-50">
                        📥 Export Excel
                    </button>
                    <button type="submit" name="export_pdf" class="btn btn-danger w-50">
                        📄 Export PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
