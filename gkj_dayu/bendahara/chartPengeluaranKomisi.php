<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : intval(date('Y'));
$start_month = isset($_GET['start_month']) ? intval($_GET['start_month']) : 1;
$end_month = isset($_GET['end_month']) ? intval($_GET['end_month']) : 12;
$id_bidang = isset($_GET['id_bidang']) ? intval($_GET['id_bidang']) : 11;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
$offset = ($page - 1) * $limit;

// Query untuk pengeluaran per komisi dengan filter status = 'Tervalidasi'
$sql = "
    SELECT 
        s.id_komisi,
        s.nama_komisi,
        SUM(COALESCE(pe.dana_gereja, 0)) AS dana_gereja,
        SUM(COALESCE(pe.dana_swadaya, 0)) AS dana_swadaya,
        SUM(COALESCE(pe.dana_gereja, 0) + COALESCE(pe.dana_swadaya, 0)) AS total_pengeluaran
    FROM realisasi_pengeluaran_komisi pe
    LEFT JOIN komisi s ON pe.id_komisi = s.id_komisi
    LEFT JOIN fiskal f ON pe.id_fiskal = f.id_fiskal
    WHERE f.tahun = ? 
      AND s.id_bidang = ? 
      AND MONTH(pe.tanggal_pengeluaran) BETWEEN ? AND ?
      AND pe.status = 'Tervalidasi'
    GROUP BY s.id_komisi, s.nama_komisi
    ORDER BY total_pengeluaran DESC
    LIMIT ? OFFSET ?;
";

// Query untuk menghitung total komisi dengan filter status = 'Tervalidasi'
$countSql = "
    SELECT COUNT(DISTINCT s.id_komisi) AS total
    FROM realisasi_pengeluaran_komisi pe
    LEFT JOIN komisi s ON pe.id_komisi = s.id_komisi
    LEFT JOIN fiskal f ON pe.id_fiskal = f.id_fiskal
    WHERE f.tahun = ? 
      AND s.id_bidang = ? 
      AND MONTH(pe.tanggal_pengeluaran) BETWEEN ? AND ?
      AND pe.status = 'Tervalidasi'
;";

// Persiapkan statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param('iiiiii', $tahun, $id_bidang, $start_month, $end_month, $limit, $offset);

$countStmt = $conn->prepare($countSql);
if ($countStmt === false) {
    die("Prepare failed: " . $conn->error);
}
$countStmt->bind_param('iiii', $tahun, $id_bidang, $start_month, $end_month);

// Eksekusi dan ambil data
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo json_encode(['error' => 'Error executing query: ' . $stmt->error]);
    exit();
}

// Hitung total pages
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalItems = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $limit);

// Output JSON
header("Content-Type: application/json");
echo json_encode([
    'data' => $data,
    'totalPages' => $totalPages,
]);
?>
