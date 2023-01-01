<?php
require '../library/vendor1/autoload.php';
include '../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $periode = $_POST['periode'];
    $tanggal = $_POST['tanggal'] ?? '';
    $bulan = $_POST['bulan'] ?? '';
    $tahun = $_POST['tahun'] ?? '';

    // Query default
    $query = "SELECT * FROM inventaris";

    // Filter sesuai periode
    if ($periode == 'harian' && !empty($tanggal)) {
        $query .= " WHERE DATE(tanggal) = '$tanggal'";
    } elseif ($periode == 'bulanan' && !empty($bulan)) {
        $query .= " WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
    } elseif ($periode == 'tahunan' && !empty($tahun)) {
        $query .= " WHERE YEAR(tanggal) = '$tahun'";
    }

    $query .= " ORDER BY tanggal DESC";
    $result = mysqli_query($conn, $query);

    // Buat Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Inventaris Masjid');

    // Judul
    $sheet->setCellValue('A1', 'Laporan Inventaris Masjid');
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    // Header Tabel
    $header = ['No', 'Nama Barang', 'Asal', 'Kondisi', 'Jenis', 'Jumlah', 'Keterangan', 'Tanggal'];
    $col = 'A';
    foreach ($header as $head) {
        $sheet->setCellValue($col.'3', $head);
        $sheet->getStyle($col.'3')->getFont()->setBold(true);
        $sheet->getStyle($col.'3')->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Style Header: background biru muda + border
    $sheet->getStyle('A3:H3')->applyFromArray([
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'BDD7EE'], // biru muda
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
        'font' => [
            'bold' => true,
        ],
    ]);

    // Isi Data
    $rowNum = 4;
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A'.$rowNum, $no++);
        $sheet->setCellValue('B'.$rowNum, $row['nama_barang']);
        $sheet->setCellValue('C'.$rowNum, ucfirst($row['asal']));
        $sheet->setCellValue('D'.$rowNum, ucfirst($row['kondisi']));
        $sheet->setCellValue('E'.$rowNum, ucfirst($row['jenis']));
        $sheet->setCellValue('F'.$rowNum, $row['jumlah']);
        $sheet->setCellValue('G'.$rowNum, $row['keterangan']);
        $sheet->setCellValue('H'.$rowNum, date('d-m-Y', strtotime($row['tanggal'])));
        $rowNum++;
    }

    // Style Border isi tabel
    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];
    $sheet->getStyle('A4:H'.($rowNum-1))->applyFromArray($styleArray);

    // Export ke Excel
    $filename = 'Laporan_Inventaris_'.date('YmdHis').'.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
