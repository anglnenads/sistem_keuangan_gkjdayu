<?php
header("Content-Type: application/vnd.ms-excel");
session_start();

$db = mysqli_connect("localhost", "root", "", "gkj_dayu");
if (!$db) die("Koneksi gagal: " . mysqli_connect_error());

$tahun = isset($_GET['tahun_aktif']) ? intval($_GET['tahun_aktif']) : ($_SESSION['tahun_aktif'] ?? date("Y"));
$bulan = isset($_GET['bulan']) && is_numeric($_GET['bulan']) ? intval($_GET['bulan']) : null;
$nama_bulan = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
    5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
    9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];

$namaFile = "laporanRencanaRealisasiPerBidang_{$tahun}" . ($bulan ? "_{$nama_bulan[$bulan]}" : "_Januari-Desember") . ".xls";
header("Content-Disposition: attachment; filename=\"$namaFile\"");
echo "\xEF\xBB\xBF";

function formatRupiah($angka) {
    return is_null($angka) ? 'Data tidak tersedia' : 'Rp ' . number_format(round($angka), 0, ',', '.');
}

// Ambil data ANGGARAN, grup by bidang & program
$q_anggaran = "SELECT b.nama_bidang, p.nama_program, ak.nama_akun,
               a.item, a.harga_satuan, a.volume, a.dana_gereja, a.dana_swadaya,
               (a.dana_gereja + a.dana_swadaya) AS total_anggaran
        FROM rencana_pengeluaran_komisi a
        LEFT JOIN program p ON a.id_program = p.id_program
        LEFT JOIN akun ak ON a.id_akun = ak.id_akun
        LEFT JOIN komisi k ON a.id_komisi = k.id_komisi
        LEFT JOIN bidang b ON k.id_bidang = b.id_bidang
        LEFT JOIN fiskal f ON a.id_fiskal = f.id_fiskal
        WHERE f.tahun = {$tahun}
        ORDER BY b.nama_bidang, p.nama_program";

$res_anggaran = $db->query($q_anggaran);
$data_anggaran = [];
while ($r = $res_anggaran->fetch_assoc()) {
    $data_anggaran[$r['nama_bidang']][$r['nama_program']][] = $r;
}

// Ambil data REALISASI PENGELUARAN, grup by bidang & program, hanya status = 'Tervalidasi'
$q_pengeluaran = "
    SELECT b.nama_bidang, p.nama_program, ak.nama_akun,
           pg.tanggal_pengeluaran, pg.item, pg.harga_satuan, pg.volume,
           pg.dana_gereja, pg.dana_swadaya,
           (pg.dana_gereja + pg.dana_swadaya) AS total_pengeluaran
    FROM realisasi_pengeluaran_komisi pg
    LEFT JOIN akun ak ON pg.id_akun = ak.id_akun
    LEFT JOIN komisi k ON pg.id_komisi = k.id_komisi
    LEFT JOIN bidang b ON k.id_bidang = b.id_bidang
    LEFT JOIN program p ON pg.id_program = p.id_program
    LEFT JOIN fiskal f ON pg.id_fiskal = f.id_fiskal
    WHERE f.tahun = ?
      AND pg.status = 'Tervalidasi'
";
$types = "i";
$params = [$tahun];

if ($bulan) {
    $q_pengeluaran .= " AND MONTH(pg.tanggal_pengeluaran) = ?";
    $types .= "i";
    $params[] = $bulan;
}

$q_pengeluaran .= " ORDER BY b.nama_bidang, p.nama_program, pg.tanggal_pengeluaran";

$stmt = $db->prepare($q_pengeluaran);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res_pengeluaran = $stmt->get_result();

$data_pengeluaran = [];
while ($r = $res_pengeluaran->fetch_assoc()) {
    $data_pengeluaran[$r['nama_bidang']][$r['nama_program']][] = $r;
}

// CETAK TABEL ANGGARAN
echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width:100%;'>";
echo "<tr><th colspan='7'>TOTAL ANGGARAN - Periode {$tahun}" . ($bulan ? " Bulan {$nama_bulan[$bulan]}" : " Januari–Desember") . "</th></tr>";
echo "<tr>
    <th>Akun</th>
    <th>Item</th>
    <th>Harga Satuan</th>
    <th>Vol</th>
    <th>Dana Gereja</th>
    <th>Dana Swadaya</th>
    <th>Total</th>
</tr>";

foreach ($data_anggaran as $bidang => $programs) {
    echo "<tr><td colspan='7' style='font-weight:bold; text-align:left;'>&nbsp;</td></tr>"; // Spasi antar bidang
    echo "<tr><td colspan='7' style='font-weight:bold; text-align:left;'>Bidang: " . htmlspecialchars($bidang) . "</td></tr>";

    $totalBidangG = 0; $totalBidangS = 0; $totalBidangT = 0;

    foreach ($programs as $program => $rows) {
        echo "<tr><td colspan='7' style='font-weight:bold; text-align:left;'>Program: " . htmlspecialchars($program) . "</td></tr>";

        $totalProgramG = 0; $totalProgramS = 0; $totalProgramT = 0;

        foreach ($rows as $r) {
            echo "<tr>
                <td>" . htmlspecialchars($r['nama_akun']) . "</td>
                <td>" . htmlspecialchars($r['item']) . "</td>
                <td>" . formatRupiah($r['harga_satuan']) . "</td>
                <td>" . htmlspecialchars($r['volume']) . "</td>
                <td>" . formatRupiah($r['dana_gereja']) . "</td>
                <td>" . formatRupiah($r['dana_swadaya']) . "</td>
                <td>" . formatRupiah($r['total_anggaran']) . "</td>
            </tr>";

            $totalProgramG += $r['dana_gereja'];
            $totalProgramS += $r['dana_swadaya'];
            $totalProgramT += $r['total_anggaran'];
        }

        echo "<tr style='font-weight:bold;'>
            <td colspan='4'>Total Keseluruhan Program " . htmlspecialchars($program) . "</td>
            <td>" . formatRupiah($totalProgramG) . "</td>
            <td>" . formatRupiah($totalProgramS) . "</td>
            <td>" . formatRupiah($totalProgramT) . "</td>
        </tr>";

        $totalBidangG += $totalProgramG;
        $totalBidangS += $totalProgramS;
        $totalBidangT += $totalProgramT;
    }

    echo "<tr style='font-weight:bold;'>
        <td colspan='4'>Total Anggaran Bidang " . htmlspecialchars($bidang) . "</td>
        <td>" . formatRupiah($totalBidangG) . "</td>
        <td>" . formatRupiah($totalBidangS) . "</td>
        <td>" . formatRupiah($totalBidangT) . "</td>
    </tr>";
}
echo "</table><br><br>";

// CETAK TABEL REALISASI PENGELUARAN
echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width:100%;'>";
echo "<tr><th colspan='8'>TOTAL PENGELUARAN - Periode {$tahun}" . ($bulan ? " Bulan {$nama_bulan[$bulan]}" : " Januari–Desember") . "</th></tr>";
echo "<tr>
    <th>Tanggal</th>
    <th>Akun</th>
    <th>Item</th>
    <th>Harga Satuan</th>
    <th>Vol</th>
    <th>Dana Gereja</th>
    <th>Dana Swadaya</th>
    <th>Total</th>
</tr>";

foreach ($data_pengeluaran as $bidang => $programs) {
    echo "<tr><td colspan='8'>&nbsp;</td></tr>"; // Spasi antar bidang
    echo "<tr><td colspan='8' style='font-weight:bold; text-align:left;'>Bidang: " . htmlspecialchars($bidang) . "</td></tr>";

    $totalBidangG = 0; $totalBidangS = 0; $totalBidangT = 0;

    foreach ($programs as $program => $rows) {
        echo "<tr><td colspan='8' style='font-weight:bold; text-align:left;'>Program: " . htmlspecialchars($program) . "</td></tr>";

        $totalProgramG = 0; $totalProgramS = 0; $totalProgramT = 0;

        foreach ($rows as $r) {
            $tgl = date("d-m-Y", strtotime($r['tanggal_pengeluaran']));
            echo "<tr>
                <td>{$tgl}</td>
                <td>" . htmlspecialchars($r['nama_akun']) . "</td>
                <td>" . htmlspecialchars($r['item']) . "</td>
                <td>" . formatRupiah($r['harga_satuan']) . "</td>
                <td>" . htmlspecialchars($r['volume']) . "</td>
                <td>" . formatRupiah($r['dana_gereja']) . "</td>
                <td>" . formatRupiah($r['dana_swadaya']) . "</td>
                <td>" . formatRupiah($r['total_pengeluaran']) . "</td>
            </tr>";

            $totalProgramG += $r['dana_gereja'];
            $totalProgramS += $r['dana_swadaya'];
            $totalProgramT += $r['total_pengeluaran'];
        }

        echo "<tr style='font-weight:bold;'>
            <td colspan='5'>Total Keseluruhan Program " . htmlspecialchars($program) . "</td>
            <td>" . formatRupiah($totalProgramG) . "</td>
            <td>" . formatRupiah($totalProgramS) . "</td>
            <td>" . formatRupiah($totalProgramT) . "</td>
        </tr>";

        $totalBidangG += $totalProgramG;
        $totalBidangS += $totalProgramS;
        $totalBidangT += $totalProgramT;
    }

    echo "<tr style='font-weight:bold;'>
        <td colspan='5'>Total Pengeluaran Bidang " . htmlspecialchars($bidang) . "</td>
        <td>" . formatRupiah($totalBidangG) . "</td>
        <td>" . formatRupiah($totalBidangS) . "</td>
        <td>" . formatRupiah($totalBidangT) . "</td>
    </tr>";
}
echo "</table>";
?>
