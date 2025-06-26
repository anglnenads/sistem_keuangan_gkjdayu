<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

$tahun = isset($_GET["tahun_aktif"]) ? intval($_GET["tahun_aktif"]) : date("Y");
$bulan = isset($_GET["bulan"]) ? intval($_GET["bulan"]) : null;

if ($bulan) {
    // Mode mingguan
    $query = "
        SELECT
            WEEK(transaksi.tanggal, 1) - WEEK(DATE_SUB(transaksi.tanggal, INTERVAL DAY(transaksi.tanggal)-1 DAY), 1) + 1 AS minggu_ke,
            SUM(transaksi.penerimaan) AS penerimaan,
            SUM(transaksi.pengeluaran) AS pengeluaran
        FROM (
            SELECT tanggal_penerimaan AS tanggal, jumlah_penerimaan AS penerimaan, 0 AS pengeluaran, id_fiskal
            FROM realisasi_penerimaan_gereja
            WHERE status = 'Tervalidasi'
            UNION ALL
            SELECT tanggal_pengeluaran AS tanggal, 0 AS penerimaan, jumlah AS pengeluaran, id_fiskal
            FROM realisasi_pengeluaran_gereja
            WHERE status = 'Tervalidasi'
        ) AS transaksi
        LEFT JOIN fiskal f ON transaksi.id_fiskal = f.id_fiskal
        WHERE f.tahun = ? AND MONTH(transaksi.tanggal) = ?
        GROUP BY minggu_ke ORDER BY minggu_ke ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $tahun, $bulan);
} else {
    // Mode bulanan (default)
    $query = "
        SELECT
            MONTH(transaksi.tanggal) AS bulan,
            SUM(transaksi.penerimaan) AS penerimaan,
            SUM(transaksi.pengeluaran) AS pengeluaran
        FROM (
            SELECT tanggal_penerimaan AS tanggal, jumlah_penerimaan AS penerimaan, 0 AS pengeluaran, id_fiskal
            FROM realisasi_penerimaan_gereja
            WHERE status = 'Tervalidasi'
            UNION ALL
            SELECT tanggal_pengeluaran AS tanggal, 0 AS penerimaan, jumlah AS pengeluaran, id_fiskal
            FROM realisasi_pengeluaran_gereja
            WHERE status = 'Tervalidasi'
        ) AS transaksi
        LEFT JOIN fiskal f ON transaksi.id_fiskal = f.id_fiskal
        WHERE f.tahun = ?
        GROUP BY bulan ORDER BY bulan ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $tahun);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($bulan) {
    // Init 5 minggu
    for ($i = 1; $i <= 5; $i++) {
        $data[$i] = ['minggu_ke' => $i, 'penerimaan' => 0, 'pengeluaran' => 0];
    }

    while ($row = $result->fetch_assoc()) {
        $data[intval($row['minggu_ke'])] = $row;
    }
} else {
    // Init 12 bulan
    for ($i = 1; $i <= 12; $i++) {
        $data[$i] = ['bulan' => $i, 'penerimaan' => 0, 'pengeluaran' => 0];
    }

    while ($row = $result->fetch_assoc()) {
        $data[intval($row['bulan'])] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode(['data' => array_values($data)]);
?>
