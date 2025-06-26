<?php

include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

// Koneksi ke database
$conn = new cConnect();
$conn->goConnect();

// Mulai session jika belum dimulai (pastikan session aktif)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika tahun_aktif diteruskan lewat GET, simpan dalam session
if (isset($_GET["tahun_aktif"])) {
    $_SESSION["tahun_aktif"] = intval($_GET["tahun_aktif"]);
}

// Ambil tahun dari session, jika tidak ada, ambil tahun sekarang
$tahun = isset($_SESSION["tahun_aktif"]) ? $_SESSION["tahun_aktif"] : date("Y");

// Ambil bulan dari GET jika ada
$bulan = isset($_GET["bulan"]) && !empty($_GET["bulan"]) ? intval($_GET["bulan"]) : null;

// Query untuk mengambil data penerimaan dan pengeluaran dengan filter status = 'Tervalidasi'
$query = "
    SELECT
        f.tahun,
        MONTH(transaksi.tanggal) AS bulan,
        SUM(transaksi.penerimaan) AS penerimaan,
        SUM(transaksi.pengeluaran) AS pengeluaran
    FROM (
        SELECT 
            tanggal_penerimaan AS tanggal, 
            jumlah_penerimaan AS penerimaan, 
            0 AS pengeluaran,
            id_fiskal
        FROM realisasi_penerimaan_gereja
        WHERE status = 'Tervalidasi'
        UNION ALL
        SELECT 
            tanggal_pengeluaran AS tanggal, 
            0 AS penerimaan, 
            jumlah AS pengeluaran,
            id_fiskal
        FROM realisasi_pengeluaran_gereja
        WHERE status = 'Tervalidasi'
    ) AS transaksi
    LEFT JOIN fiskal f ON transaksi.id_fiskal = f.id_fiskal
    WHERE f.tahun = ?";

// Siapkan parameter query
$queryParams = [$tahun];

// Tambahkan filter bulan jika ada
if ($bulan) {
    $query .= " AND MONTH(transaksi.tanggal) = ?";
    $queryParams[] = $bulan;
}

// Kelompokkan per bulan
$query .= " GROUP BY f.tahun, bulan ORDER BY bulan ASC";

// Eksekusi query
$stmt = $conn->prepare($query);

if ($stmt === false) {
    // Jika query tidak bisa dipersiapkan, tampilkan error
    die('Query preparation failed: ' . $conn->error);
}

// Bind parameters sesuai jumlah
if ($bulan) {
    $stmt->bind_param("ii", $queryParams[0], $queryParams[1]);
} else {
    $stmt->bind_param("i", $queryParams[0]);
}

// Jalankan query
$stmt->execute();

// Ambil hasil query
$result = $stmt->get_result();

// Pastikan bulan 1-12 tetap muncul
$bulanArray = range(1, 12);
$dataMap = [];
foreach ($bulanArray as $bln) {
    $dataMap[$bln] = [
        'tahun' => $tahun,
        'bulan' => $bln,
        'penerimaan' => 0,
        'pengeluaran' => 0,
        'saldo' => 0
    ];
}

// Mengisi data dari hasil query
while ($row = $result->fetch_assoc()) {
    $dataMap[$row['bulan']]['penerimaan'] = $row['penerimaan'];
    $dataMap[$row['bulan']]['pengeluaran'] = $row['pengeluaran'];
    $dataMap[$row['bulan']]['saldo'] = $row['penerimaan'] - $row['pengeluaran'];
}

// Total Penerimaan, Pengeluaran, dan Saldo
$totalPenerimaan = 0;
$totalPengeluaran = 0;
$totalSaldo = 0;

// Menghitung total dari setiap bulan
foreach ($dataMap as $row) {
    $totalPenerimaan += $row['penerimaan'];
    $totalPengeluaran += $row['pengeluaran'];
    $totalSaldo += $row['saldo'];
}

// Kirimkan data total dan data per bulan dalam format JSON
header("Content-Type: application/json");
echo json_encode([
    'penerimaan' => $totalPenerimaan,
    'pengeluaran' => $totalPengeluaran,
    'saldo' => $totalSaldo,
    'data' => array_values($dataMap)
]);

?>
