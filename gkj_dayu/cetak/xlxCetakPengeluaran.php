<?php

session_start();

$nama_bulan = [
    1 => 'JANUARI',
    2 => 'FEBRUARI',
    3 => 'MARET',
    4 => 'APRIL',
    5 => 'MEI',
    6 => 'JUNI',
    7 => 'JULI',
    8 => 'AGUSTUS',
    9 => 'SEPTEMBER',
    10 => 'OKTOBER',
    11 => 'NOVEMBER',
    12 => 'DESEMBER'
];


if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
    $title = 'Laporan_Pengeluaran_Gereja_' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
    $title = 'Laporan_Pengeluaran_Gereja_' . $tahun;
} else {
    $tahun = 0;
}

if (isset($_SESSION['bulan'])) {
    $bulan = $_SESSION['bulan'];
    $title = 'Laporan_Pengeluaran_Gereja_' . $nama_bulan[$bulan] . '_' . $tahun_aktif;
} else {
    $bulan = 0;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: http://localhost:80/gkj_dayu/");
    exit;
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"".$title.".xls\"");

$conn = mysqli_connect("localhost", "root", "", "gkj_dayu", "3306");

$sql = "SELECT 
            p.tanggal_pengeluaran,
            p.jenis_pengeluaran,
            p.jumlah AS jumlah_pengeluaran,
            p.id_akun AS id_akun,
            a.nama_akun AS nama_akun,
            f.tahun AS tahun
            FROM realisasi_pengeluaran_gereja p
            LEFT JOIN akun a ON p.id_akun = a.id_akun
            LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal ";


if (!empty($bulan)) {
    if ($bulan == 0) {
        $sql .= "WHERE tahun = $tahun_aktif  AND p.status = 'Tervalidasi'";
    } else {
        $nama_bulan = $nama_bulan[$bulan];

        $sql .= " WHERE tahun = $tahun_aktif AND  month(tanggal_pengeluaran) = $bulan AND p.status = 'Tervalidasi'";
        $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun_aktif;
    }
} else if (!empty($tahun)) {
    if ($tahun == 0) {
        $sql .= "WHERE tahun = " . $tahun_aktif . " AND p.status = 'Tervalidasi'";
    } else {
        $sql .= "WHERE tahun = " . $tahun . " AND p.status = 'Tervalidasi'";
        $laporan = 'TAHUN ' . $tahun;
    }
} else {
    $sql .= "WHERE tahun = " . $tahun_aktif . " AND p.status = 'Tervalidasi'";
    $laporan = 'TAHUN ' . $tahun_aktif;
}

$sql .= " ORDER BY tanggal_pengeluaran ASC";

$result = mysqli_query($conn, $sql);

$tablebody = "";
$tabelheader = '<table border="1"  width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="font-weight:bold">
                    <td width="5%" style="text-align:center">Tanggal</td>
                    <td width="10%" style="text-align:center">Keterangan</td>
                    <td width="5%" style="text-align:center">Jumlah</td>
                </tr>';

$cnourut = 0;
$total_pengeluaran = 0;

$groupedData = [];

$all_total = 0;

foreach ($result as $data) {
    $tanggal = $data["tanggal_pengeluaran"];

    $groupedData[$tanggal][] = $data;
}

foreach ($groupedData as $tanggal => $dataList) {
    $firstRow = true;
    $total_pengeluaran = 0;

    foreach ($dataList as $data) {
        $jumlah = $data["jumlah_pengeluaran"];
        $total_pengeluaran += $jumlah;

        $tablebody .= '<tr>';

        if ($firstRow) {
            $tablebody .= '<td style="text-align: center;">="' . date('d-m-Y', strtotime($tanggal)) . '"</td>';
            $firstRow = false;
        } else {
            $tablebody .= '<td></td>';
        }

        $tablebody .= '<td>' . htmlspecialchars($data["jenis_pengeluaran"]) . '</td>
                        <td style="text-align: right;">' . number_format($jumlah, 0, '.', ',') . '</td>
                    </tr>';
    }
    $tablebody .= '<tr>
                    <td></td>
                    <td style="font-weight: bold;">Total</td>
                    <td style="text-align: right; border-top: 1px solid black; font-weight: bold;">' . number_format($total_pengeluaran, 0, '.', ',') . '</td>
                </tr>
                <tr>
                    <td colspan=3></td>
                </tr>';

    $all_total += $total_pengeluaran;
}

$tablebody .= '<tr>
                <td colspan=3></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-weight: bold;">Total Keseluruhan:</td>
                <td style="text-align: right; font-weight: bold;">' . number_format($all_total, 0, '.', ',') . '</td>
            </tr>';

$tabelfooter = '</table>';

$title1 = '<h2 style="text-align: center;">GEREJA KRISTEN JAWA DAYU</h2>';
$title2 = '<h2 style="text-align: center;">LAPORAN PENGELUARAN GEREJA</h2>';
$title3 = '<h2 style="text-align: center;">' . $laporan . '</h2>';
$titleLaporan = $title1 . $title2 . $title3;

$allshowdata = $titleLaporan . $tabelheader . $tabelcaption . $tablebody . $tabelfooter;

echo $allshowdata;
