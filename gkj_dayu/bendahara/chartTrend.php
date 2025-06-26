<?php
session_start();
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");

header("Content-Type: application/json");

$conn = new cConnect();
$conn->goConnect();

if (!isset($_SESSION['tahun_aktif'])) {
    echo json_encode(["error" => "Tahun fiskal tidak ditemukan di sesi."]);
    exit;
}

$tahunFiskalAktif = (int)$_SESSION['tahun_aktif'];
$tahunMulai = $tahunFiskalAktif - 2;
$tahunPrediksi = $tahunFiskalAktif;

$view = new cView();

// Ambil daftar fiskal dari tahunMulai sampai tahunPrediksi (3 tahun)
$sqlFiskal = "
    SELECT id_fiskal, tahun 
    FROM fiskal 
    WHERE tahun BETWEEN $tahunMulai AND $tahunPrediksi
    ORDER BY tahun ASC
";

$fiskalData = $view->vViewData($sqlFiskal);
if (!$fiskalData || count($fiskalData) < 2) {
    echo json_encode(["error" => "Tidak cukup data fiskal untuk prediksi. Minimal 2 tahun terakhir diperlukan."]);
    exit;
}

// Pisahkan id_fiskal historis dan id_fiskal tahun aktif
$idFiskalMap = [];
$years = [];
$idFiskalHistoris = [];
$idFiskalAktif = null;

foreach ($fiskalData as $row) {
    $idFiskalMap[$row['id_fiskal']] = $row['tahun'];
    if ($row['tahun'] < $tahunFiskalAktif) {
        $years[] = (int)$row['tahun'];
        $idFiskalHistoris[] = $row['id_fiskal'];
    } elseif ($row['tahun'] == $tahunFiskalAktif) {
        $idFiskalAktif = $row['id_fiskal'];
    }
}

if (count($idFiskalHistoris) < 2) {
    echo json_encode(["error" => "Minimal 2 tahun historis dibutuhkan untuk prediksi."]);
    exit;
}

$idFiskalHistorisIn = implode(',', array_map('intval', $idFiskalHistoris));

// Ambil data historis penerimaan & pengeluaran (2 tahun terakhir)
$sqlDataHistoris = "
    SELECT f.tahun,
           COALESCE(p.total_penerimaan, 0) AS penerimaan,
           COALESCE(pg.total_pengeluaran, 0) AS pengeluaran
    FROM fiskal f
    LEFT JOIN (
        SELECT id_fiskal, SUM(jumlah_penerimaan) AS total_penerimaan
        FROM realisasi_penerimaan_gereja
        GROUP BY id_fiskal
    ) AS p ON f.id_fiskal = p.id_fiskal
    LEFT JOIN (
        SELECT id_fiskal, SUM(jumlah) AS total_pengeluaran
        FROM realisasi_pengeluaran_gereja
        GROUP BY id_fiskal
    ) AS pg ON f.id_fiskal = pg.id_fiskal
    WHERE f.id_fiskal IN ($idFiskalHistorisIn)
    ORDER BY f.tahun ASC
";

$data = $view->vViewData($sqlDataHistoris);
$penerimaanData = [];
$pengeluaranData = [];

foreach ($data as $row) {
    $penerimaanData[] = (float)$row['penerimaan'];
    $pengeluaranData[] = (float)$row['pengeluaran'];
}

// Lakukan prediksi berdasarkan 2 tahun sebelumnya
function linearRegression($x, $y) {
    $n = count($x);
    if ($n != count($y)) return null;
    $x_sum = array_sum($x);
    $y_sum = array_sum($y);
    $xx_sum = 0;
    $xy_sum = 0;
    for ($i = 0; $i < $n; $i++) {
        $xx_sum += $x[$i] * $x[$i];
        $xy_sum += $x[$i] * $y[$i];
    }
    $slope = ($n * $xy_sum - $x_sum * $y_sum) / ($n * $xx_sum - $x_sum * $x_sum);
    $intercept = ($y_sum - $slope * $x_sum) / $n;
    return ['slope' => $slope, 'intercept' => $intercept];
}

$penerimaanReg = linearRegression($years, $penerimaanData);
$pengeluaranReg = linearRegression($years, $pengeluaranData);

$predictedPenerimaan = round($penerimaanReg['slope'] * $tahunPrediksi + $penerimaanReg['intercept']);
$predictedPengeluaran = round($pengeluaranReg['slope'] * $tahunPrediksi + $pengeluaranReg['intercept']);

// Ambil data aktual untuk tahun aktif (jika ada)
$lastPenerimaan = 0;
$lastPengeluaran = 0;
$statusAktual = "Data belum tersedia";

if ($idFiskalAktif) {
    $sqlAktual = "
        SELECT 
            COALESCE((
                SELECT SUM(jumlah_penerimaan) 
                FROM realisasi_penerimaan_gereja 
                WHERE id_fiskal = $idFiskalAktif
            ), 0) AS penerimaan,
            COALESCE((
                SELECT SUM(jumlah) 
                FROM realisasi_pengeluaran_gereja 
                WHERE id_fiskal = $idFiskalAktif
            ), 0) AS pengeluaran
    ";
    $aktual = $view->vViewData($sqlAktual);
    if ($aktual && count($aktual) > 0) {
        $lastPenerimaan = (float)$aktual[0]['penerimaan'];
        $lastPengeluaran = (float)$aktual[0]['pengeluaran'];
        if ($lastPenerimaan > 0 || $lastPengeluaran > 0) {
            $statusAktual = "Data tersedia";
        }
    }
}

$penerimaanNearTarget = abs($predictedPenerimaan - $lastPenerimaan) / max($predictedPenerimaan, 1) < 0.1;
$pengeluaranNearTarget = abs($predictedPengeluaran - $lastPengeluaran) / max($predictedPengeluaran, 1) < 0.1;

$persentasePenerimaan = round(($lastPenerimaan / max($predictedPenerimaan, 1)) * 100, 2);
$persentasePengeluaran = round(($lastPengeluaran / max($predictedPengeluaran, 1)) * 100, 2);

// Susun hasil
$result = [
    "tahunFiskalAktif" => $tahunFiskalAktif,
    "status_data_aktual" => $statusAktual,
    "penerimaan" => array_map(function($tahun, $nominal) {
        return ["tahun" => $tahun, "nominal" => $nominal];
    }, $years, $penerimaanData),
    "pengeluaran" => array_map(function($tahun, $nominal) {
        return ["tahun" => $tahun, "nominal" => $nominal];
    }, $years, $pengeluaranData),
    "prediksi" => [
        "tahun" => $tahunPrediksi,
        "penerimaan" => $predictedPenerimaan,
        "pengeluaran" => $predictedPengeluaran,
        "indikator" => [
            "penerimaan_mendekati_target" => $penerimaanNearTarget,
            "pengeluaran_mendekati_target" => $pengeluaranNearTarget
        ]
    ],
    "persentase_pencapaian" => [
        "penerimaan" => $persentasePenerimaan,
        "pengeluaran" => $persentasePengeluaran
    ],
    "model_regresi" => [
        "penerimaan" => "y = " . round($penerimaanReg['slope'], 2) . "x + " . round($penerimaanReg['intercept'], 2),
        "pengeluaran" => "y = " . round($pengeluaranReg['slope'], 2) . "x + " . round($pengeluaranReg['intercept'], 2)
    ],
    "model_regresi_nilai" => [
        "penerimaan" => $penerimaanReg,
        "pengeluaran" => $pengeluaranReg
    ],
    "nilai_aktual" => [
        "penerimaan" => $lastPenerimaan,
        "pengeluaran" => $lastPengeluaran
    ]
];

echo json_encode($result);
?>
