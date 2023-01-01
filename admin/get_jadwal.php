<?php
header('Content-Type: application/json');

$city = "Pekanbaru";
$country = "Indonesia";
$method = 11; // Kemenag RI
$url = "https://api.aladhan.com/v1/timingsByCity?city=$city&country=$country&method=$method";

$response = null;

// ✅ Pakai cURL
if (function_exists('curl_version')) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);
}

// ✅ Fallback file_get_contents
if (!$response && ini_get('allow_url_fopen')) {
    $context = stream_context_create([
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);
    $response = @file_get_contents($url, false, $context);
}

// ✅ Jika gagal, kirim error JSON
if (!$response) {
    echo json_encode([
        "status" => false,
        "error" => "Tidak bisa mengambil data jadwal sholat. Pastikan koneksi internet tersedia."
    ]);
    exit;
}

// ✅ Decode JSON
$data = json_decode($response, true);

if (isset($data['data']['timings'])) {
    $jadwal = $data['data']['timings'];
    echo json_encode([
        "status" => true,
        "subuh" => $jadwal['Fajr'],
        "dzuhur" => $jadwal['Dhuhr'],
        "ashar" => $jadwal['Asr'],
        "maghrib" => $jadwal['Maghrib'],
        "isya" => $jadwal['Isha'],
        "tanggal" => date('Y-m-d')
    ]);
} else {
    echo json_encode(["status" => false, "error" => "Format API tidak sesuai"]);
}
