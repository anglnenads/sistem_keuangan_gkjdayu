<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$start_month = isset($_GET['start_month']) ? intval($_GET['start_month']) : 1;
$end_month = isset($_GET['end_month']) ? intval($_GET['end_month']) : 12;
$id_bidang = isset($_GET['id_bidang']) ? $_GET['id_bidang'] : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 16;
$offset = ($page - 1) * $limit;

// Build main SQL query with filter status = "tervalidasi" pada realisasi_pengeluaran_komisi
$sql = "
    SELECT 
        k.nama_komisi AS komisi,
        COALESCE(aggr.total_dana_swadaya_anggaran, 0) AS dana_swadaya_anggaran,
        COALESCE(aggr.total_dana_gereja_anggaran, 0) AS dana_gereja_anggaran,
        COALESCE(aggr.total_anggaran, 0) AS anggaran,
        COALESCE(pgr.total_dana_swadaya_pengeluaran, 0) AS dana_swadaya_pengeluaran,
        COALESCE(pgr.total_dana_gereja_pengeluaran, 0) AS dana_gereja_pengeluaran,
        COALESCE(pgr.total_pengeluaran, 0) AS pengeluaran,
        CASE 
            WHEN COALESCE(aggr.total_anggaran, 0) > 0 THEN 
                (COALESCE(pgr.total_pengeluaran, 0) / aggr.total_anggaran) * 100
            ELSE 0
        END AS persentase_realisasi
    FROM komisi k
    LEFT JOIN (
        SELECT 
            a.id_komisi,
            SUM(COALESCE(a.dana_swadaya, 0)) AS total_dana_swadaya_anggaran,
            SUM(COALESCE(a.dana_gereja, 0)) AS total_dana_gereja_anggaran,
            SUM(COALESCE(a.dana_swadaya, 0) + COALESCE(a.dana_gereja, 0)) AS total_anggaran
        FROM rencana_pengeluaran_komisi a
        JOIN fiskal f ON a.id_fiskal = f.id_fiskal
        WHERE f.tahun = ?
        GROUP BY a.id_komisi
    ) aggr ON k.id_komisi = aggr.id_komisi
    LEFT JOIN (
        SELECT 
            pe.id_komisi,
            SUM(COALESCE(pe.dana_swadaya, 0)) AS total_dana_swadaya_pengeluaran,
            SUM(COALESCE(pe.dana_gereja, 0)) AS total_dana_gereja_pengeluaran,
            SUM(COALESCE(pe.dana_swadaya, 0) + COALESCE(pe.dana_gereja, 0)) AS total_pengeluaran
        FROM realisasi_pengeluaran_komisi pe
        JOIN fiskal f ON pe.id_fiskal = f.id_fiskal
        WHERE f.tahun = ? 
          AND MONTH(pe.tanggal_pengeluaran) BETWEEN ? AND ?
          AND pe.status = 'tervalidasi'
        GROUP BY pe.id_komisi
    ) pgr ON k.id_komisi = pgr.id_komisi
";

$params = [$tahun, $tahun, $start_month, $end_month];
$types = "iiii";

if ($id_bidang !== null) {
    $sql .= " WHERE k.id_bidang = ?";
    $params[] = $id_bidang;
    $types .= "i";
}

$sql .= " ORDER BY k.id_komisi ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare statement gagal: ' . $conn->error]);
    exit();
}

$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    echo json_encode(['error' => 'Query gagal: ' . $stmt->error]);
    exit();
}

$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total item untuk pagination
$countSql = "
    SELECT COUNT(*) AS total FROM komisi k
    LEFT JOIN (
        SELECT a.id_komisi
        FROM rencana_pengeluaran_komisi a
        JOIN fiskal f ON a.id_fiskal = f.id_fiskal
        WHERE f.tahun = ?
        GROUP BY a.id_komisi
    ) aggr ON k.id_komisi = aggr.id_komisi
    LEFT JOIN (
        SELECT pe.id_komisi
        FROM realisasi_pengeluaran_komisi pe
        JOIN fiskal f ON pe.id_fiskal = f.id_fiskal
        WHERE f.tahun = ? AND MONTH(pe.tanggal_pengeluaran) BETWEEN ? AND ? AND pe.status = 'tervalidasi'
        GROUP BY pe.id_komisi
    ) pgr ON k.id_komisi = pgr.id_komisi
";

$countParams = [$tahun, $tahun, $start_month, $end_month];
$countTypes = "iiii";

if ($id_bidang !== null) {
    $countSql .= " WHERE k.id_bidang = ?";
    $countParams[] = $id_bidang;
    $countTypes .= "i";
}

$countStmt = $conn->prepare($countSql);
if (!$countStmt) {
    echo json_encode(['error' => 'Prepare count statement gagal: ' . $conn->error]);
    exit();
}

$countStmt->bind_param($countTypes, ...$countParams);
if (!$countStmt->execute()) {
    echo json_encode(['error' => 'Count query gagal: ' . $countStmt->error]);
    exit();
}

$countResult = $countStmt->get_result();
$totalItems = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $limit);

header("Content-Type: application/json");
echo json_encode([
    'data' => array_map(function ($row) {
        return [
            'komisi' => $row['komisi'],
            'dana_swadaya_anggaran' => (float)$row['dana_swadaya_anggaran'],
            'dana_gereja_anggaran' => (float)$row['dana_gereja_anggaran'],
            'anggaran' => (float)$row['anggaran'],
            'dana_swadaya_pengeluaran' => (float)$row['dana_swadaya_pengeluaran'],
            'dana_gereja_pengeluaran' => (float)$row['dana_gereja_pengeluaran'],
            'pengeluaran' => (float)$row['pengeluaran'],
            'persentase_realisasi' => (float)$row['persentase_realisasi']
        ];
    }, $data),
    'totalPages' => $totalPages
]);
?>
