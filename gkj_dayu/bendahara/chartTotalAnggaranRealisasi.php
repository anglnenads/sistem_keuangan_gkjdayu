<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');
$bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? intval($_GET['bulan']) : null;

$sql = "
SELECT 
    b.id_bidang,
    b.nama_bidang,

    -- Anggaran
    (
        SELECT COALESCE(SUM(a.dana_gereja), 0)
        FROM rencana_pengeluaran_komisi a
        JOIN fiskal f ON a.id_fiskal = f.id_fiskal
        WHERE a.id_bidang = b.id_bidang
          AND f.tahun = ?
    ) AS anggaran_gereja,
    
    (
        SELECT COALESCE(SUM(a.dana_swadaya), 0)
        FROM rencana_pengeluaran_komisi a
        JOIN fiskal f ON a.id_fiskal = f.id_fiskal
        WHERE a.id_bidang = b.id_bidang
          AND f.tahun = ?
    ) AS anggaran_swadaya,

    -- Realisasi (Pengeluaran) dengan filter status = 'Tervalidasi'
    (
        SELECT COALESCE(SUM(p.dana_gereja), 0)
        FROM realisasi_pengeluaran_komisi p
        JOIN fiskal f ON p.id_fiskal = f.id_fiskal
        WHERE p.id_bidang = b.id_bidang
          AND f.tahun = ?" . ($bulan !== null ? " AND MONTH(p.tanggal_pengeluaran) = ?" : "") . "
          AND p.status = 'Tervalidasi'
    ) AS realisasi_gereja,

    (
        SELECT COALESCE(SUM(p.dana_swadaya), 0)
        FROM realisasi_pengeluaran_komisi p
        JOIN fiskal f ON p.id_fiskal = f.id_fiskal
        WHERE p.id_bidang = b.id_bidang
          AND f.tahun = ?" . ($bulan !== null ? " AND MONTH(p.tanggal_pengeluaran) = ?" : "") . "
          AND p.status = 'Tervalidasi'
    ) AS realisasi_swadaya

FROM bidang b
ORDER BY b.id_bidang
";


if ($bulan !== null) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii", $tahun, $tahun, $tahun, $bulan, $tahun, $bulan);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $tahun, $tahun, $tahun, $tahun);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $anggaran_gereja = (float) $row['anggaran_gereja'];
    $anggaran_swadaya = (float) $row['anggaran_swadaya'];
    $realisasi_gereja = (float) $row['realisasi_gereja'];
    $realisasi_swadaya = (float) $row['realisasi_swadaya'];

    $data[] = [
        'id_bidang' => $row['id_bidang'],
        'nama_bidang' => $row['nama_bidang'],
        'anggaran_gereja' => $anggaran_gereja,
        'anggaran_swadaya' => $anggaran_swadaya,
        'total_anggaran' => $anggaran_gereja + $anggaran_swadaya,
        'realisasi_gereja' => $realisasi_gereja,
        'realisasi_swadaya' => $realisasi_swadaya,
        'total_realisasi' => $realisasi_gereja + $realisasi_swadaya
    ];
}

header('Content-Type: application/json');
echo json_encode(['data' => $data]);
?>
