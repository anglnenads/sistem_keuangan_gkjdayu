<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

// Koneksi
$conn = new cConnect();
$conn->goConnect();

// Set header JSON
header("Content-Type: application/json");

$response = [
    'data' => [
        'penerimaan' => [],
        'pengeluaran' => [],
        'saldo' => [],
        'komisiList' => []
    ]
];

// Ambil parameter dari GET dengan validasi
$idBidang = isset($_GET['id_bidang']) ? intval($_GET['id_bidang']) : null;
$idKomisi = isset($_GET['id_komisi']) ? intval($_GET['id_komisi']) : null;
$startMonth = isset($_GET['start_month']) ? intval($_GET['start_month']) : 1;
$endMonth = isset($_GET['end_month']) ? intval($_GET['end_month']) : 12;
$tahun = isset($_GET["tahun"]) && !empty($_GET["tahun"]) ? intval($_GET["tahun"]) : date("Y");

// Fungsi bantu prepare and execute query with params
function fetchAll($conn, $sql, $types = null, $params = []) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return false;
    }
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

// Ambil daftar Komisi berdasarkan id_bidang jika ada
if ($idBidang) {
    $komisiList = fetchAll($conn, "SELECT * FROM komisi WHERE id_bidang = ?", "i", [$idBidang]);
    if ($komisiList !== false) {
        $response['data']['komisiList'] = $komisiList;
    }
}

if ($idKomisi) {
    // Query penerimaan dengan filter status='tervalidasi'
    $sqlPenerimaan = "
        SELECT 
            MONTH(p.tanggal_penerimaan) AS bulan, 
            SUM(p.dana_gereja) AS dana_gereja, 
            SUM(p.dana_swadaya) AS dana_swadaya, 
            SUM(p.dana_gereja + p.dana_swadaya) AS total 
        FROM realisasi_penerimaan_komisi p
        LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal
        WHERE p.id_komisi = ?
          AND f.tahun = ?
          AND MONTH(p.tanggal_penerimaan) BETWEEN ? AND ?
          AND p.status = 'tervalidasi'
        GROUP BY MONTH(p.tanggal_penerimaan)
        ORDER BY bulan ASC
    ";
    $penerimaanData = fetchAll($conn, $sqlPenerimaan, "iiii", [$idKomisi, $tahun, $startMonth, $endMonth]);

    if ($penerimaanData !== false) {
        foreach ($penerimaanData as $row) {
            $response['data']['penerimaan'][] = [
                'bulan' => (int)$row['bulan'],
                'dana_gereja' => (int)$row['dana_gereja'],
                'dana_swadaya' => (int)$row['dana_swadaya'],
                'total' => (int)$row['total']
            ];
        }
    }

    // Query pengeluaran dengan filter status='tervalidasi'
    $sqlPengeluaran = "
        SELECT 
            MONTH(p.tanggal_pengeluaran) AS bulan, 
            SUM(p.dana_gereja) AS dana_gereja, 
            SUM(p.dana_swadaya) AS dana_swadaya, 
            SUM(p.dana_gereja + p.dana_swadaya) AS total 
        FROM realisasi_pengeluaran_komisi p
        LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal
        WHERE p.id_komisi = ?
          AND f.tahun = ?
          AND MONTH(p.tanggal_pengeluaran) BETWEEN ? AND ?
          AND p.status = 'tervalidasi'
        GROUP BY MONTH(p.tanggal_pengeluaran)
        ORDER BY bulan ASC
    ";
    $pengeluaranData = fetchAll($conn, $sqlPengeluaran, "iiii", [$idKomisi, $tahun, $startMonth, $endMonth]);

    if ($pengeluaranData !== false) {
        foreach ($pengeluaranData as $row) {
            $response['data']['pengeluaran'][] = [
                'bulan' => (int)$row['bulan'],
                'dana_gereja' => (int)$row['dana_gereja'],
                'dana_swadaya' => (int)$row['dana_swadaya'],
                'total' => (int)$row['total']
            ];
        }
    }

    // Hitung saldo per bulan
    $penerimaan = $response['data']['penerimaan'];
    $pengeluaran = $response['data']['pengeluaran'];

    $saldoGereja = 0;
    $saldoSwadaya = 0;

    for ($bulan = $startMonth; $bulan <= $endMonth; $bulan++) {
        $penerimaanBulan = ['dana_gereja' => 0, 'dana_swadaya' => 0, 'total' => 0];
        $pengeluaranBulan = ['dana_gereja' => 0, 'dana_swadaya' => 0, 'total' => 0];

        foreach ($penerimaan as $p) {
            if ($p['bulan'] === $bulan) {
                $penerimaanBulan = $p;
                break;
            }
        }
        foreach ($pengeluaran as $p) {
            if ($p['bulan'] === $bulan) {
                $pengeluaranBulan = $p;
                break;
            }
        }

        $saldoGereja += $penerimaanBulan['dana_gereja'] - $pengeluaranBulan['dana_gereja'];
        $saldoSwadaya += $penerimaanBulan['dana_swadaya'] - $pengeluaranBulan['dana_swadaya'];
        $saldoTotal = $saldoGereja + $saldoSwadaya;

        $response['data']['saldo'][] = [
            'bulan' => $bulan,
            'saldo' => $saldoTotal,
            'dana_gereja' => $saldoGereja,
            'dana_swadaya' => $saldoSwadaya,
            'tambah_gereja' => $penerimaanBulan['dana_gereja'],
            'tambah_swadaya' => $penerimaanBulan['dana_swadaya'],
            'kurang_gereja' => $pengeluaranBulan['dana_gereja'],
            'kurang_swadaya' => $pengeluaranBulan['dana_swadaya']
        ];
    }
}

// Output JSON bersih
echo json_encode($response);
exit();
?>
