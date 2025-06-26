<?php
header("Content-Type: application/vnd.ms-excel");

// Mulai session
session_start();

// Koneksi ke database
$db = mysqli_connect("localhost", "root", "", "gkj_dayu");
if (!$db) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil tahun aktif dari GET atau SESSION
$tahun = isset($_GET['tahun_aktif']) && !empty($_GET['tahun_aktif']) ? intval($_GET['tahun_aktif']) : ($_SESSION['tahun_aktif'] ?? date("Y"));

// Ambil bulan dari GET
$bulan = isset($_GET['bulan']) && is_numeric($_GET['bulan']) && $_GET['bulan'] !== '' ? intval($_GET['bulan']) : null;

// Nama bulan
$nama_bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Nama file dinamis
$namaFile = "laporanKasGereja_" . $tahun;
if ($bulan && isset($nama_bulan[$bulan])) {
    $namaFile .= "_" . $nama_bulan[$bulan];
} elseif (!$bulan) {
    $namaFile .= "_Januari-Desember"; // Nama file jika "Semua Bulan"
}
$namaFile .= ".xls";
header("Content-Disposition: attachment; filename=\"$namaFile\"");

// UTF-8 BOM untuk mencegah karakter aneh di Excel
echo "\xEF\xBB\xBF";

// Query penerimaan dengan status "Tervalidasi"
$queryPenerimaan = "
    SELECT 
        p.tanggal_penerimaan AS tanggal,
        p.jenis_penerimaan AS keterangan,
        a.nama_akun AS akun,
        p.jumlah_penerimaan AS penerimaan,
        0 AS pengeluaran
    FROM realisasi_penerimaan_gereja p
    LEFT JOIN akun a ON p.id_akun = a.id_akun
    WHERE YEAR(p.tanggal_penerimaan) = ? 
      AND p.status = 'Tervalidasi'
";
$paramsP = [$tahun];
$typesP = "i";

if ($bulan) {
    $queryPenerimaan .= " AND MONTH(p.tanggal_penerimaan) = ?";
    $paramsP[] = $bulan;
    $typesP .= "i";
}

$stmtPenerimaan = $db->prepare($queryPenerimaan);
$stmtPenerimaan->bind_param($typesP, ...$paramsP);
$stmtPenerimaan->execute();
$resultPenerimaan = $stmtPenerimaan->get_result();

// Query pengeluaran dengan status "Tervalidasi"
$queryPengeluaran = "
    SELECT 
        pg.tanggal_pengeluaran AS tanggal,
        pg.jenis_pengeluaran AS keterangan,
        a.nama_akun AS akun,
        0 AS penerimaan,
        pg.jumlah AS pengeluaran
    FROM realisasi_pengeluaran_gereja pg
    LEFT JOIN akun a ON pg.id_akun = a.id_akun
    WHERE YEAR(pg.tanggal_pengeluaran) = ? 
      AND pg.status = 'Tervalidasi'
";
$paramsG = [$tahun];
$typesG = "i";

if ($bulan) {
    $queryPengeluaran .= " AND MONTH(pg.tanggal_pengeluaran) = ?";
    $paramsG[] = $bulan;
    $typesG .= "i";
}

$stmtPengeluaran = $db->prepare($queryPengeluaran);
$stmtPengeluaran->bind_param($typesG, ...$paramsG);
$stmtPengeluaran->execute();
$resultPengeluaran = $stmtPengeluaran->get_result();

// Gabungkan dan urutkan data
$data = array_merge(
    iterator_to_array($resultPenerimaan),
    iterator_to_array($resultPengeluaran)
);
usort($data, fn($a, $b) => strtotime($a['tanggal']) - strtotime($b['tanggal']));

// Tampilkan tabel Excel
echo "<table border='1' width='100%' cellspacing='0' cellpadding='4'>";
echo "<tr><th colspan='7'>LAPORAN KAS UMUM GEREJA</th></tr>";
echo "<tr><th colspan='7'>Periode: Tahun $tahun" . 
    ($bulan ? " - Bulan " . ($nama_bulan[$bulan] ?? 'Tidak Diketahui') : " - Januari sampai Desember") . "</th></tr>";
echo "<tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Akun</th>
        <th>Penerimaan</th>
        <th>Pengeluaran</th>
        <th>Saldo</th>
      </tr>";

$no = 1;
$saldo = 0;
$totalPenerimaan = 0;
$totalPengeluaran = 0;

if (empty($data)) {
    echo "<tr><td colspan='7' align='center'>-</td></tr>";
} else {
    foreach ($data as $row) {
        $penerimaan = (int)$row['penerimaan'];
        $pengeluaran = (int)$row['pengeluaran'];
        $saldo += $penerimaan - $pengeluaran;
        $totalPenerimaan += $penerimaan;
        $totalPengeluaran += $pengeluaran;

        echo "<tr>
                <td>{$no}</td>
                <td>" . htmlspecialchars($row['tanggal']) . "</td>
                <td>" . htmlspecialchars($row['keterangan']) . "</td>
                <td>" . htmlspecialchars($row['akun']) . "</td>
                <td>" . ($penerimaan > 0 ? 'Rp ' . number_format($penerimaan, 0, ',', '.') : '-') . "</td>
                <td>" . ($pengeluaran > 0 ? 'Rp ' . number_format($pengeluaran, 0, ',', '.') : '-') . "</td>
                <td>Rp " . number_format($saldo, 0, ',', '.') . "</td>
              </tr>";
        $no++;
    }

    // Baris total
    echo "<tr style='font-weight: bold;'>
            <td colspan='4' align='center'>Total</td>
            <td>Rp " . number_format($totalPenerimaan, 0, ',', '.') . "</td>
            <td>Rp " . number_format($totalPengeluaran, 0, ',', '.') . "</td>
            <td>Rp " . number_format($saldo, 0, ',', '.') . "</td>
          </tr>";
}

echo "</table>";

mysqli_close($db);
?>
