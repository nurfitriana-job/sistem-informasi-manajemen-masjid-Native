<?php
include '../config/db.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$kategori = $_GET['kategori'] ?? '';
$periode = $_GET['periode'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$query = "SELECT * FROM keuangan_pemasukan WHERE 1=1";
if (!empty($kategori)) $query .= " AND kategori='$kategori'";
if ($periode == 'hari' && !empty($tanggal)) $query .= " AND DATE(tanggal)='$tanggal'";
if ($periode == 'bulan' && !empty($bulan) && !empty($tahun)) $query .= " AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'";
if ($periode == 'tahun' && !empty($tahun)) $query .= " AND YEAR(tanggal)='$tahun'";
$query .= " ORDER BY tanggal DESC";

$result = mysqli_query($conn, $query);

// Buat Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Kategori');
$sheet->setCellValue('B1', 'Nominal');
$sheet->setCellValue('C1', 'Keterangan');
$sheet->setCellValue('D1', 'Tanggal');

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A'.$rowNum, $row['kategori']);
    $sheet->setCellValue('B'.$rowNum, $row['nominal']);
    $sheet->setCellValue('C'.$rowNum, $row['keterangan']);
    $sheet->setCellValue('D'.$rowNum, $row['tanggal']);
    $rowNum++;
}

$writer = new Xlsx($spreadsheet);
$filename = 'Laporan_Keuangan_'.date('Ymd_His').'.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer->save("php://output");
exit;
?>
