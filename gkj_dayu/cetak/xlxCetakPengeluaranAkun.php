<?php

session_start(); 

$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
    $title = 'Rekapitulasi_Pengeluaran_Gereja_' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
    $title = 'Rekapitulasi_Pengeluaran_Gereja_' . $tahun;
} else {
    $tahun = 0;
}

if (isset($_SESSION['bulan'])) {
    $bulan = $_SESSION['bulan'];
    $title = 'Rekapitulasi_Pengeluaran_Gereja_' . $nama_bulan[$bulan] . '_' . $tahun_aktif;
} else {
    $bulan = 0;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: ../");
    exit;
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"".$title.".xls\"");

$conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, (int)$this->dbPort);

$sql = "SELECT 
            f.tahun,
            a.nama_akun,
            COALESCE(SUM(r.jumlah), 0) AS jumlah_rencana,
            COALESCE(SUM(r.dana_gereja), 0) AS dana_gereja_rencana,
            COALESCE(SUM(r.dana_swadaya), 0) AS dana_swadaya_rencana,
            (SELECT COALESCE(SUM(pg.jumlah), 0) FROM realisasi_pengeluaran_gereja pg 
            WHERE pg.id_akun = a.id_akun AND pg.id_fiskal = f.id_fiskal) AS realisasi,
            (SELECT COALESCE(SUM(pg.jumlah), 0) FROM realisasi_pengeluaran_gereja pg
            WHERE pg.id_akun = a.id_akun AND pg.id_fiskal = f.id_fiskal AND pg.status = 'Tervalidasi') AS dana_gereja_realisasi,
            0 AS dana_swadaya_realisasi
            FROM akun a
            CROSS JOIN fiskal f
            LEFT JOIN (
                -- Dari rencana komisi (jumlah = total)
                SELECT id_akun, id_fiskal, jumlah, dana_gereja, dana_swadaya FROM rencana_pengeluaran_komisi
                UNION ALL
    
                -- Dari rencana gereja (jumlah dianggap dana_gereja, lainnya 0)
                SELECT id_akun, id_fiskal, jumlah AS jumlah, jumlah AS dana_gereja, 0 AS dana_swadaya FROM rencana_pengeluaran_gereja) r 
                ON a.id_akun = r.id_akun AND f.id_fiskal = r.id_fiskal ";

if (!empty($bulan)) {
    if ($bulan == 0) {
        $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = $tahun_aktif
                                GROUP BY f.tahun, a.kode_akun ";
    } else {
        $sql = " SELECT a.nama_akun AS nama_akun, 0 AS jumlah_rencana, 0 AS dana_gereja_rencana, 0 AS dana_swadaya_rencana,
                COALESCE(SUM(p.jumlah), 0) AS realisasi,
                COALESCE(SUM(p.jumlah), 0) AS dana_gereja_realisasi,
                0 AS dana_swadaya_realisasi
                FROM akun a
                LEFT JOIN realisasi_pengeluaran_gereja p ON a.id_akun = p.id_akun AND MONTH(p.tanggal_pengeluaran) = $bulan AND id_fiskal IN (SELECT id_fiskal FROM fiskal WHERE tahun = $tahun_aktif)
                WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1
                GROUP BY a.kode_akun ORDER BY a.kode_akun";

        $nama_bulan = $nama_bulan[$bulan];
        $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun_aktif;
    }
} else if (!empty($tahun)) {
    if ($tahun == 0) {
        $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = $tahun_aktif
                GROUP BY f.tahun, a.kode_akun ";
    } else {
        $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = " . $tahun . " GROUP BY f.tahun, a.kode_akun";
        $laporan = 'TAHUN ' . $tahun;
    }
} else {
    $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = $tahun_aktif
            GROUP BY f.tahun, a.kode_akun ";

    $laporan = 'TAHUN ' . $tahun_aktif;
}

$result = mysqli_query($conn, $sql);

$dataall = "";
$tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="text-align: center; font-weight:bold">
                    <td rowspan="2" width="40%">Akun</td>
                    <td colspan="3">Rencana Pengeluaran</td>
                    <td width="20%">Realisasi Pengeluaran</td>
                </tr>

                <tr style="text-align: center; font-weight:bold">
                    <td width="14%">Dana Gereja</td>
                    <td width="14%">Dana Swadaya</td>
                    <td width="14%">Jumlah</td>
                    <td width="18%">Dana Gereja</td>
                </tr>';


$total_danaGereja = 0;
$total_danaSwadaya = 0;
$total_rencana = 0;
$total_realisasi = 0;

while ($row = mysqli_fetch_assoc($result)) {


    $dana_gereja = ($row["dana_gereja_rencana"] == 0 || empty($row["dana_gereja_rencana"])) ? "-" : number_format($row["dana_gereja_rencana"], 0, '.', ',');
    $dana_swadaya = ($row["dana_swadaya_rencana"] == 0 || empty($row["dana_swadaya_rencana"])) ? "-" : number_format($row["dana_swadaya_rencana"], 0, '.', ',');
    $rencana = ($row["jumlah_rencana"] == 0 || empty($row["jumlah_rencana"])) ? "-" : number_format($row["jumlah_rencana"], 0, '.', ',');
    $realisasi = ($row["dana_gereja_realisasi"] == 0 || empty($row["dana_gereja_realisasi"])) ? "-" : number_format($row["dana_gereja_realisasi"], 0, '.', ',');

    $total_danaGereja = $total_danaGereja + $row["dana_gereja_rencana"];
    $total_danaSwadaya = $total_danaSwadaya + $row["dana_swadaya_rencana"];
    $total_rencana = $total_rencana + $row["dana_gereja_rencana"] - $row["dana_swadaya_rencana"];
    $total_realisasi = $total_realisasi + $row["dana_gereja_realisasi"];


    $dataall = $dataall .   '<tr>
                                <td>' . $row["nama_akun"] . '</td>
                                <td style="text-align: right"> ' . $dana_gereja .   '</td>
                                <td style="text-align: right">' . $dana_swadaya .   '</td> 
                                 <td style="text-align: right"> ' . $rencana .   '</td>
                                <td style="text-align: right">' . $realisasi .   '</td> 
                            </tr>';
}

$tabelfooter = '<tr style="text-align: right; font-weight:bold">
                    <td width="40%" style="text-align: left">Total</td>
                    <td width="14%">' . number_format($total_danaGereja, 0, '.', ',') . '</td>
                    <td width="14%">' . number_format($total_danaSwadaya, 0, '.', ',') . '</td>
                    <td width="14%">' . number_format($total_rencana, 0, '.', ',') . '</td>
                    <td width="18%">' . number_format($total_realisasi, 0, '.', ',') . '</td>
                    </tr>
                </table>';


$title1 = '<h2 style="text-align: center;">GEREJA KRISTEN JAWA DAYU</h2>';
$title2 = '<h2 style="text-align: center;">REKAPITULASI PENGELUARAN GEREJA</h2>';
$title3 = '<h2 style="text-align: center;">' . $laporan . '</h2>';
$titleLaporan = $title1 . $title2 . $title3;

$allshowdata = $titleLaporan . $tabelheader . $tabelcaption . $dataall .$tabelfooter;

echo $allshowdata;
