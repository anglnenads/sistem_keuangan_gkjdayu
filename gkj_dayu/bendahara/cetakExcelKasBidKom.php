<?php
header("Content-Type: application/vnd.ms-excel");
echo "\xEF\xBB\xBF"; 
session_start();
$db = mysqli_connect("localhost", "root", "", "gkj_dayu");
if (!$db) die("Koneksi gagal: " . mysqli_connect_error());

// Parameter
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date("Y");
$start = isset($_GET['start']) ? max(1, intval($_GET['start'])) : 1;
$end = isset($_GET['end']) ? min(12, intval($_GET['end'])) : 12;
$bidang = isset($_GET['bidang']) ? intval($_GET['bidang']) : 0;
$komisi = isset($_GET['komisi']) ? intval($_GET['komisi']) : 0;

// Nama bidang dan komisi
$namaBidang = "-";
if ($bidang > 0) {
    $res = mysqli_query($db, "SELECT nama_bidang FROM bidang WHERE id_bidang = $bidang");
    if ($row = mysqli_fetch_assoc($res)) $namaBidang = $row['nama_bidang'];
}

$namaKomisi = "-";
if ($komisi > 0) {
    $res = mysqli_query($db, "SELECT nama_komisi FROM komisi WHERE id_komisi = $komisi");
    if ($row = mysqli_fetch_assoc($res)) $namaKomisi = $row['nama_komisi'];
}

$nama_bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$namaFile = "Laporan_Kas_{$namaBidang}_{$namaKomisi}_{$tahun}_{$nama_bulan[$start]}-{$nama_bulan[$end]}.xls";
header("Content-Disposition: attachment; filename=\"$namaFile\"");

// Filter SQL
$filter = "WHERE f.tahun = $tahun AND MONTH(p.tanggal_penerimaan) BETWEEN $start AND $end";
if ($bidang > 0) $filter .= " AND k.id_bidang = $bidang";
if ($komisi > 0) $filter .= " AND k.id_komisi = $komisi";

// Penerimaan
$queryPenerimaan = "
    SELECT 
        p.tanggal_penerimaan AS tanggal,
        pr.nama_program,
        a.nama_akun AS akun,
        p.jenis_penerimaan AS keterangan,
        NULL AS item,
        p.volume,
        p.harga_satuan,
        p.dana_gereja,
        p.dana_swadaya,
        p.jumlah_penerimaan AS jumlah,
        'penerimaan' AS tipe
    FROM realisasi_penerimaan_komisi p
    LEFT JOIN komisi k ON p.id_komisi = k.id_komisi
    LEFT JOIN bidang b ON k.id_bidang = b.id_bidang
    LEFT JOIN program pr ON p.id_program = pr.id_program
    LEFT JOIN akun a ON p.id_akun = a.id_akun
    LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal
    $filter
";

// Pengeluaran
$filterG = "WHERE f.tahun = $tahun AND MONTH(p.tanggal_pengeluaran) BETWEEN $start AND $end";
if ($bidang > 0) $filterG .= " AND k.id_bidang = $bidang";
if ($komisi > 0) $filterG .= " AND k.id_komisi = $komisi";

$queryPengeluaran = "
    SELECT 
        p.tanggal_pengeluaran AS tanggal,
        pr.nama_program,
        a.nama_akun AS akun,
        p.item AS keterangan,
        p.item,
        p.volume,
        p.harga_satuan,
        p.dana_gereja,
        p.dana_swadaya,
        p.jumlah AS jumlah,
        'pengeluaran' AS tipe
    FROM realisasi_pengeluaran_komisi p
    LEFT JOIN komisi k ON p.id_komisi = k.id_komisi
    LEFT JOIN bidang b ON k.id_bidang = b.id_bidang
    LEFT JOIN program pr ON p.id_program = pr.id_program
    LEFT JOIN akun a ON p.id_akun = a.id_akun
    LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal
    $filterG
";

$resultP = mysqli_query($db, $queryPenerimaan);
if (!$resultP) die("Query Penerimaan Gagal: " . mysqli_error($db));

$resultG = mysqli_query($db, $queryPengeluaran);
if (!$resultG) die("Query Pengeluaran Gagal: " . mysqli_error($db));

// Gabungkan data
$data = [];
while ($row = mysqli_fetch_assoc($resultP)) $data[] = $row;
while ($row = mysqli_fetch_assoc($resultG)) $data[] = $row;

// Urutkan berdasarkan tanggal
usort($data, fn($a, $b) => strtotime($a['tanggal']) - strtotime($b['tanggal']));

// Tabel
echo "<table border='1' width='100%' cellspacing='0' cellpadding='4'>";
echo "<tr><th colspan='11'>LAPORAN KAS</th></tr>";
echo "<tr><th colspan='11'>{$namaBidang} - {$namaKomisi}</th></tr>";
echo "<tr><th colspan='11'>Periode: {$nama_bulan[$start]} - {$nama_bulan[$end]} {$tahun}</th></tr>";

echo "<tr>
    <th>Tanggal</th>
    <th>Program</th>
    <th>Akun</th>
    <th>Keterangan</th>
    <th>Item</th>
    <th>Volume</th>
    <th>Harga Satuan</th>
    <th>Dana Gereja</th>
    <th>Dana Swadaya</th>
    <th>Jumlah Penerimaan</th>
    <th>Jumlah Pengeluaran</th>
</tr>";

$total_dg_penerimaan = $total_ds_penerimaan = 0;
$total_dg_pengeluaran = $total_ds_pengeluaran = 0;

if (empty($data)) {
    echo "<tr><td colspan='11' align='center'>Tidak ada data.</td></tr>";
} else {
    foreach ($data as $row) {
        $jumlah_penerimaan = $row['tipe'] === 'penerimaan' ? $row['jumlah'] : 0;
        $jumlah_pengeluaran = $row['tipe'] === 'pengeluaran' ? $row['jumlah'] : 0;

        if ($row['tipe'] === 'penerimaan') {
            $total_dg_penerimaan += $row['dana_gereja'];
            $total_ds_penerimaan += $row['dana_swadaya'];
        } else {
            $total_dg_pengeluaran += $row['dana_gereja'];
            $total_ds_pengeluaran += $row['dana_swadaya'];
        }

        echo "<tr>
            <td>{$row['tanggal']}</td>
            <td>{$row['nama_program']}</td>
            <td>{$row['akun']}</td>
            <td>{$row['keterangan']}</td>
            <td>" . ($row['item'] ?? '-') . "</td>
            <td>{$row['volume']}</td>
            <td>Rp " . number_format($row['harga_satuan'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['dana_gereja'], 0, ',', '.') . "</td>
            <td>Rp " . number_format($row['dana_swadaya'], 0, ',', '.') . "</td>
            <td>" . ($jumlah_penerimaan > 0 ? "Rp " . number_format($jumlah_penerimaan, 0, ',', '.') : '-') . "</td>
            <td>" . ($jumlah_pengeluaran > 0 ? "Rp " . number_format($jumlah_pengeluaran, 0, ',', '.') : '-') . "</td>
        </tr>";
    }

    $total_penerimaan = $total_dg_penerimaan + $total_ds_penerimaan;
    $total_pengeluaran = $total_dg_pengeluaran + $total_ds_pengeluaran;
    $saldo_dg = $total_dg_penerimaan - $total_dg_pengeluaran;
    $saldo_ds = $total_ds_penerimaan - $total_ds_pengeluaran;
    $total_saldo = $saldo_dg + $saldo_ds;

    echo "<tr style='font-weight:bold;'>
        <td colspan='7' align='center'>TOTAL PENERIMAAN</td>
        <td>Rp " . number_format($total_dg_penerimaan, 0, ',', '.') . "</td>
        <td>Rp " . number_format($total_ds_penerimaan, 0, ',', '.') . "</td>
        <td colspan='2'>Rp " . number_format($total_penerimaan, 0, ',', '.') . "</td>
    </tr>";

    echo "<tr style='font-weight:bold;'>
        <td colspan='7' align='center'>TOTAL PENGELUARAN</td>
        <td>Rp " . number_format($total_dg_pengeluaran, 0, ',', '.') . "</td>
        <td>Rp " . number_format($total_ds_pengeluaran, 0, ',', '.') . "</td>
        <td colspan='2'>Rp " . number_format($total_pengeluaran, 0, ',', '.') . "</td>
    </tr>";

    echo "<tr style='font-weight:bold;'>
        <td colspan='7' align='center'>SALDO</td>
        <td>Rp " . number_format($saldo_dg, 0, ',', '.') . "</td>
        <td>Rp " . number_format($saldo_ds, 0, ',', '.') . "</td>
        <td colspan='2'>Rp " . number_format($total_saldo, 0, ',', '.') . "</td>
    </tr>";
}
echo "</table>";
mysqli_close($db);
?>
