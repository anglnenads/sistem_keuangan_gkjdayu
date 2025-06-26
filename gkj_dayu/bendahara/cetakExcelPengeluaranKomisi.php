<?php
header("Content-Type: application/vnd.ms-excel");
echo "\xEF\xBB\xBF"; // UTF-8 BOM

session_start();
$db = mysqli_connect("localhost", "root", "", "gkj_dayu");
if (!$db) die("Koneksi gagal: " . mysqli_connect_error());

$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date("Y");
$start = isset($_GET['start']) ? intval($_GET['start']) : 1;
$end = isset($_GET['end']) ? intval($_GET['end']) : 12;
$bidang = isset($_GET['bidang']) ? intval($_GET['bidang']) : 0;

// Ambil nama bidang
$namaBidang = "-";
if ($bidang > 0) {
    $res = mysqli_query($db, "SELECT nama_bidang FROM bidang WHERE id_bidang = $bidang");
    if ($row = mysqli_fetch_assoc($res)) $namaBidang = $row['nama_bidang'];
}

$nama_bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$namaFile = "LimaPengeluaranTertinggiPerKomisi_{$namaBidang}_{$tahun}_{$nama_bulan[$start]}-{$nama_bulan[$end]}.xls";
header("Content-Disposition: attachment; filename=\"$namaFile\"");

// Query pengeluaran tervalidasi tertinggi
$query = "
    SELECT 
        b.nama_bidang,
        k.nama_komisi,
        SUM(p.dana_gereja) AS total_dg,
        SUM(p.dana_swadaya) AS total_ds,
        SUM(p.jumlah) AS total_pengeluaran
    FROM realisasi_pengeluaran_komisi p
    JOIN komisi k ON p.id_komisi = k.id_komisi
    JOIN bidang b ON k.id_bidang = b.id_bidang
    JOIN fiskal f ON p.id_fiskal = f.id_fiskal
    WHERE f.tahun = $tahun
    AND MONTH(p.tanggal_pengeluaran) BETWEEN $start AND $end
    AND p.status = 'Tervalidasi'
";

if ($bidang > 0) {
    $query .= " AND k.id_bidang = $bidang";
}

$query .= "
    GROUP BY k.id_komisi
    ORDER BY total_pengeluaran DESC
    LIMIT 5
";

$result = mysqli_query($db, $query);
if (!$result) die("Query gagal: " . mysqli_error($db));

// Tabel hasil
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th colspan='5'>LAPORAN LIMA PENGELUARAN TERTINGGI PER KOMISI</th></tr>";
echo "<tr><th colspan='5'>$namaBidang</th></tr>";
echo "<tr><th colspan='5'>Periode: {$nama_bulan[$start]} - {$nama_bulan[$end]} {$tahun}</th></tr>";

echo "<tr>
        <th>No</th>
        <th>Komisi</th>
        <th>Dana Gereja</th>
        <th>Dana Swadaya</th>
        <th>Total Pengeluaran</th>
      </tr>";

$no = 1;
$total_dg = 0;
$total_ds = 0;
$total_all = 0;

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$no}</td>
        <td>{$row['nama_komisi']}</td>
        <td>Rp " . number_format($row['total_dg'], 0, ',', '.') . "</td>
        <td>Rp " . number_format($row['total_ds'], 0, ',', '.') . "</td>
        <td>Rp " . number_format($row['total_pengeluaran'], 0, ',', '.') . "</td>
    </tr>";

    $total_dg += $row['total_dg'];
    $total_ds += $row['total_ds'];
    $total_all += $row['total_pengeluaran'];
    $no++;
}

// Total row
echo "<tr style='font-weight: bold'>
        <td colspan='2' align='center'>TOTAL</td>
        <td>Rp " . number_format($total_dg, 0, ',', '.') . "</td>
        <td>Rp " . number_format($total_ds, 0, ',', '.') . "</td>
        <td>Rp " . number_format($total_all, 0, ',', '.') . "</td>
      </tr>";
echo "</table>";

mysqli_close($db);
?>
