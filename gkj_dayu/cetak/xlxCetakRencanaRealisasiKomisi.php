<?php

session_start(); 

if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
    $title = 'Laporan_Rekapitulasi_Anggaran_Komisi_' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
    $title = 'Laporan_Rekapitulasi_Anggaran_Komisi_' . $tahun;
    
} else {
    $tahun = 0;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: ../");
    exit;
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"".$title.".xls\"");

$conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, (int)$this->dbPort);

$sql = "SELECT 
    fiskal.id_fiskal,
    fiskal.tahun,
    bidang.nama_bidang,
    komisi.nama_komisi,

    -- Rencana pengeluaran dan penerimaan
    (SELECT SUM(rnk.jumlah) 
     FROM v_rencanakomisi rnk 
     WHERE rnk.id_komisi = komisi.id_komisi 
       AND rnk.id_bidang = bidang.id_bidang 
       AND rnk.id_fiskal = fiskal.id_fiskal
       AND rnk.jenis COLLATE utf8mb4_unicode_ci = 'Rencana Penerimaan') AS jumlah_rencana_penerimaan,

    (SELECT SUM(rnk.jumlah) 
     FROM v_rencanakomisi rnk 
     WHERE rnk.id_komisi = komisi.id_komisi 
        AND rnk.id_bidang = bidang.id_bidang 
       AND rnk.id_fiskal = fiskal.id_fiskal
       AND rnk.jenis COLLATE utf8mb4_unicode_ci = 'Rencana Pengeluaran') AS jumlah_rencana_pengeluaran,

    -- Realisasi pengeluaran dan penerimaan
    (SELECT SUM(rlk.jumlah) 
     FROM v_realisasikomisi rlk 
     WHERE rlk.id_komisi = komisi.id_komisi 
       AND rlk.id_bidang = bidang.id_bidang 
       AND rlk.id_fiskal = fiskal.id_fiskal
       AND rlk.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan') AS jumlah_realisasi_penerimaan,

    (SELECT SUM(rlk.jumlah) 
        FROM v_realisasikomisi rlk
       WHERE rlk.id_komisi = komisi.id_komisi 
       AND rlk.id_bidang = bidang.id_bidang 
       AND rlk.id_fiskal = fiskal.id_fiskal
       AND rlk.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran') AS jumlah_realisasi_pengeluaran

    FROM fiskal
    CROSS JOIN bidang
    LEFT JOIN komisi ON bidang.id_bidang = komisi.id_bidang";
$laporan = 'TAHUN ' . $tahun_aktif;

 if (isset($_SESSION['tahun'])) {
            if ($tahun == 0) {
                $sql .= " WHERE fiskal.tahun = $tahun_aktif ORDER BY fiskal.tahun, bidang.id_bidang, komisi.id_komisi ";
            } else {
                $sql .= " WHERE fiskal.tahun = $tahun ORDER BY fiskal.tahun, bidang.id_bidang, komisi.id_komisi; ";
                $laporan = 'TAHUN ' . $tahun;
            }
        } else {
            $sql .= " WHERE fiskal.tahun = $tahun_aktif ORDER BY fiskal.tahun, bidang.id_bidang, komisi.id_komisi ";
        }

$result = mysqli_query($conn, $sql);

$dataall = "";
$tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="text-align: center; font-weight:bold">
                    <td rowspan="2" width="22%">Bidang</td>
                    <td rowspan="2" width="30%">Komisi</td>
                    <td colspan="2">Penerimaan</td>
                    <td colspan="2">Pengeluaran</td>
                </tr>
                
                <tr style="text-align: center; font-weight:bold">
                    <td width="12%">Rencana</td>
                    <td width="12%">Realisasi</td>
                  <td width="12%">Rencana</td>
                    <td width="12%">Realisasi</td>
                </tr>';


$total_rencanaPenerimaan = 0;
$total_realisasiPenerimaan = 0;
$total_rencanaPengeluaran = 0;
$total_realisasiPengeluaran = 0;

while ($row = mysqli_fetch_assoc($result)) {

    $rencana_penerimaan = ($row["jumlah_rencana_penerimaan"] == 0 || empty($row["jumlah_rencana_penerimaan"])) ? "-" : number_format($row["jumlah_rencana_penerimaan"], 0, '.', ',');
    $realisasi_penerimaan = ($row["jumlah_realisasi_penerimaan"] == 0 || empty($row["jumlah_realisasi_penerimaan"])) ? "-" : number_format($row["jumlah_realisasi_penerimaan"], 0, '.', ',');
    $rencana_pengeluaran = ($row["jumlah_rencana_pengeluaran"] == 0 || empty($row["jumlah_rencana_pengeluaran"])) ? "-" : number_format($row["jumlah_rencana_pengeluaran"], 0, '.', ',');
    $realisasi_pengeluaran = ($row["jumlah_realisasi_pengeluaran"] == 0 || empty($row["jumlah_realisasi_pengeluaran"])) ? "-" : number_format($row["jumlah_realisasi_pengeluaran"], 0, '.', ',');

    $total_rencanaPenerimaan = $total_rencanaPenerimaan + $row["jumlah_rencana_penerimaan"];
    $total_realisasiPenerimaan = $total_realisasiPenerimaan + $row["jumlah_realisasi_penerimaan"];
    $total_rencanaPengeluaran = $total_rencanaPengeluaran + $row["jumlah_rencana_pengeluaran"];
    $total_realisasiPengeluaran = $total_realisasiPengeluaran + $row["jumlah_realisasi_pengeluaran"];

    $dataall = $dataall .   '<tr>
                                <td>' . $row["nama_bidang"] . '</td>
                                <td>' . $row["nama_komisi"] . '</td>
                               <td style="text-align: right"> ' . $rencana_penerimaan .   '</td>
                                <td style="text-align: right"> ' . $realisasi_penerimaan .   '</td>
                                <td style="text-align: right">' . $rencana_pengeluaran .   '</td> 
                                <td style="text-align: right">' . $realisasi_pengeluaran .   '</td> 
                            </tr>';
}

$tabelfooter = '<tr style="text-align: right; font-weight:bold">
                    <td width="52%" style="text-align: left">Total</td>
                    <td></td>
                    <td width="12%">' . number_format($total_rencanaPenerimaan, 0, '.', ',') . '</td>
                    <td width="12%">' . number_format($total_realisasiPenerimaan, 0, '.', ',') . '</td>
                    <td width="12%">' . number_format($total_rencanaPengeluaran, 0, '.', ',') . '</td>
                    <td width="12%">' . number_format($total_realisasiPengeluaran, 0, '.', ',') . '</td>
                    </tr>
                </table>';

$title1 = '<h2 style="text-align: center;">GEREJA KRISTEN JAWA DAYU</h2>';
$title2 = '<h3 style="text-align: center;">RENCANA DAN REALISASI KOMISI</h3>';
$title3 = '<h3 style="text-align: center;">' . $laporan . '</h2>';
$titleLaporan = $title1 . $title2 . $title3;

$allshowdata = $titleLaporan . $tabelheader . $tabelcaption . $dataall .$tabelfooter;

echo $allshowdata;
