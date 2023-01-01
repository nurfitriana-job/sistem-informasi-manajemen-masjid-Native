<?php
// Autoload mPDF (tanpa Composer)
require_once __DIR__ . '/../library/vendor/autoload.php';
include '../config/db.php';

use Mpdf\Mpdf;

// Ambil info masjid
$qMasjid = mysqli_query($conn, "SELECT * FROM about_masjid LIMIT 1");
$masjid = mysqli_fetch_assoc($qMasjid);

$nama_masjid   = $masjid['nama_masjid'] ?? 'Masjid Baiturrahman';
$alamat_masjid = $masjid['alamat'] ?? 'Alamat belum tersedia';

// Ambil data inventaris
$data = mysqli_query($conn, "SELECT * FROM inventaris ORDER BY tanggal DESC");

// Awal HTML
$html = '
<style>
    body { font-family: Arial, sans-serif; }
    .kop-title {
        text-align: center;
        margin-bottom: 5px;
    }
    .kop-title h2 {
        margin: 0;
        font-size: 18pt;
    }
    .kop-title p {
        margin: 0;
        font-size: 10pt;
    }
    .line {
        border-top: 3px solid #000;
        margin: 10px 0 20px 0;
    }
    .sub-judul {
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        margin-bottom: 10px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 11pt;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
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
            <th>Nama Barang</th>
            <th>Asal</th>
            <th>Kondisi</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($inv = mysqli_fetch_assoc($data)) {
    $html .= '
        <tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars($inv['nama_barang']) . '</td>
            <td>' . ucfirst($inv['asal']) . '</td>
            <td>' . ucfirst($inv['kondisi']) . '</td>
            <td>' . ucfirst($inv['jenis']) . '</td>
            <td>' . $inv['jumlah'] . '</td>
            <td>' . htmlspecialchars($inv['keterangan']) . '</td>
            <td>' . date('d-m-Y', strtotime($inv['tanggal'])) . '</td>
        </tr>';
}

$html .= '</tbody></table>';

// Inisialisasi dan output PDF
$mpdf = new Mpdf(['orientation' => 'L']);
$mpdf->WriteHTML($html);
$mpdf->Output("inventaris_masjid_" . date('Ymd') . ".pdf", \Mpdf\Output\Destination::INLINE);
exit;
