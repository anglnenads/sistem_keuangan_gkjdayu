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

if (isset($_SESSION['bulan'])) {
    $bulan = $_SESSION['bulan'];
} else {
    $bulan = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
} elseif (isset($_SESSION['tahun_aktif'])) {
    $tahun = $_SESSION['tahun_aktif'];
} else {
    $tahun = 0;
}

if ($tahun != 0 && $bulan != 0 && isset($nama_bulan[$bulan])) {
    $title = 'Laporan_Penerimaan_Gereja_' . $nama_bulan[$bulan] . '_' . $tahun;
} elseif ($tahun != 0) {
    $title = 'Laporan_Penerimaan_Gereja_' . $tahun;
} else {
    $title = '';
}


if (!isset($_SESSION['id_user'])) {
    header("Location: http://localhost:80/gkj_dayu/");
    exit;
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"" . $title . ".xls\"");

$conn = mysqli_connect("localhost", "root", "", "gkj_dayu", "3306");

$sql = "SELECT 
            p.tanggal_penerimaan AS tanggal_penerimaan,
            p.jenis_penerimaan AS jenis_penerimaan,
            p.jumlah_penerimaan AS jumlah_penerimaan,
            p.id_akun AS id_akun,
            a.nama_akun AS nama_akun,
            f.tahun AS tahun
            FROM 
            realisasi_penerimaan_gereja p
            LEFT JOIN akun a ON p.id_akun = a.id_akun
            LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal ";


if (!empty($bulan)) {
    if ($bulan == 0) {
        $sql .= "WHERE f.tahun = $tahun AND p.status = 'Tervalidasi'";
    } else {


        $nama_bulan = $nama_bulan[$bulan];

        $sql .= " WHERE f.tahun = $tahun AND month(tanggal_penerimaan) = $bulan AND p.status = 'Tervalidasi'";
        $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun;
    }
} else {
    $sql .= " WHERE f.tahun = $tahun AND p.status = 'Tervalidasi'";
    $laporan = 'TAHUN ' . $tahun;
}

$sql .= " ORDER BY p.tanggal_penerimaan ASC";

$result = mysqli_query($conn, $sql);

$tablebody = "";
$tabelheader = '<table border="1"  width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="font-weight:bold">
                    <td width="5%" style="text-align:center">Tanggal</td>
                    <td width="10%" style="text-align:center">Akun</td>
                    <td width="5%" style="text-align:center">Jumlah</td>        
                </tr>';

$cnourut = 0;
$total_penerimaan = 0;
$all_total = 0;

$groupedData = [];

foreach ($result as $data) {
    $jenis = $data["jenis_penerimaan"];
    $tanggal = $data["tanggal_penerimaan"];

    $groupedData[$tanggal][$jenis][] = $data;
}

foreach ($groupedData as $tanggal => $tanggalData) {
    $firstTanggal = true;

    foreach ($tanggalData as $jenis => $dataList) {
        $total_penerimaan = 0;

        if ($firstTanggal) {
            $tablebody .= '<tr>
                            <td style="text-align: center;">="' . date('d-m-Y', strtotime($tanggal)) . '"</td>
                            <td style="font-weight: bold;">' . htmlspecialchars($jenis) . '</td>
                            <td></td>
                        </tr>';
            $firstTanggal = false;
        } else {
            $tablebody .= '<tr>
                            <td></td>
                            <td style="font-weight: bold;">' . htmlspecialchars($jenis) . '</td>
                            <td></td>
                        </tr>';
        }

        foreach ($dataList as $data) {
            $jumlah = $data["jumlah_penerimaan"];
            $total_penerimaan += $jumlah;

            $tablebody .= '<tr>
                            <td></td>
                            <td style="padding-left: 20px;">' . htmlspecialchars($data["nama_akun"]) . '</td>
                            <td style="text-align: right;">' . number_format($jumlah, 0, '.', ',') . '</td>
                        </tr>';
        }

        // total per jenis penerimaan
        $tablebody .= '<tr>
                        <td></td>
                        <td style="font-weight: bold;">Total</td>
                        <td style="text-align: right; border-top: 1px solid black; font-weight: bold;">' . number_format($total_penerimaan, 0, '.', ',') . '</td>
                    </tr>';

        $all_total += $total_penerimaan;
    }
}

// total keseluruhan
$tablebody .= '<tr>
                <td colspan=3></td>
            </tr>
            <tr>
                <td></td>
                <td colspan="" style="font-weight: bold;">Total Keseluruhan:</td>
                <td style="text-align: right; font-weight: bold;">' . number_format($all_total, 0, '.', ',') . '</td>
            </tr>';

$tabelfooter = '</table>';

$title1 = '<h2 style="text-align: center;">GEREJA KRISTEN JAWA DAYU</h2>';
$title2 = '<h2 style="text-align: center;">LAPORAN PENERIMAAN</h2>';
$title3 = '<h2 style="text-align: center;">' . $laporan . '</h2>';
$titleLaporan = $title1 . $title2 . $title3;

$allshowdata = $titleLaporan . $tabelheader . $tabelcaption . $tablebody . $tabelfooter;

echo $allshowdata;
