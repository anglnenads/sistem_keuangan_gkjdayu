<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

// Ambil parameter tahun aktif dan bulan dari query string, dengan fallback default
$tahun = $_GET['tahun_aktif'] ?? $_GET['tahun'] ?? date('Y');
$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? intval($_GET['bulan']) : null;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Pagination
$limit = 17;
$offset = ($page - 1) * $limit;

// --- Hitung total halaman berdasarkan jumlah data ---

$countSql = "
    SELECT COUNT(*) AS total FROM (
        SELECT k.id_komisi
        FROM komisi k
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
            WHERE f.tahun = ?" . ($bulan !== null ? " AND MONTH(pe.tanggal_pengeluaran) = ?" : "") . "
            GROUP BY pe.id_komisi
        ) pgr ON k.id_komisi = pgr.id_komisi
        WHERE aggr.id_komisi IS NOT NULL OR pgr.id_komisi IS NOT NULL
    ) AS subquery
";

$paramsCount = [$tahun, $tahun];
$typesCount = "ii";
if ($bulan !== null) {
    $paramsCount[] = $bulan;
    $typesCount .= "i";
}

$stmtCount = $conn->prepare($countSql);
if ($stmtCount === false) {
    die("Failed to prepare count query: " . $conn->error);
}
$stmtCount->bind_param($typesCount, ...$paramsCount);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalKomisi = $countResult->fetch_assoc()['total'] ?? 0;
$totalPages = ceil($totalKomisi / $limit);

// --- Query utama data realisasi per komisi ---

$sql = "
    SELECT 
        k.nama_komisi,
        COALESCE(aggr.total_dana_swadaya_anggaran, 0) AS total_dana_swadaya_anggaran,
        COALESCE(aggr.total_dana_gereja_anggaran, 0) AS total_dana_gereja_anggaran,
        COALESCE(aggr.total_anggaran, 0) AS total_anggaran,
        COALESCE(pgr.total_dana_swadaya_pengeluaran, 0) AS total_dana_swadaya_pengeluaran,
        COALESCE(pgr.total_dana_gereja_pengeluaran, 0) AS total_dana_gereja_pengeluaran,
        COALESCE(pgr.total_pengeluaran, 0) AS total_pengeluaran,
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
        WHERE f.tahun = ?" . ($bulan !== null ? " AND MONTH(pe.tanggal_pengeluaran) = ?" : "") . "
        GROUP BY pe.id_komisi
    ) pgr ON k.id_komisi = pgr.id_komisi
    WHERE aggr.total_anggaran IS NOT NULL OR pgr.total_pengeluaran IS NOT NULL
    ORDER BY k.id_komisi ASC
    LIMIT ? OFFSET ?
";

$paramsData = [$tahun, $tahun];
$typesData = "ii";
if ($bulan !== null) {
    $paramsData[] = $bulan;
    $typesData .= "i";
}
$paramsData[] = $limit;
$paramsData[] = $offset;
$typesData .= "ii";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Failed to prepare main query: " . $conn->error);
}
$stmt->bind_param($typesData, ...$paramsData);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

// Kirim response JSON
header("Content-Type: application/json");
echo json_encode([
    'data' => $data,
    'totalPages' => $totalPages
]);
?>
