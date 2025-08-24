<?php

session_start(); 

if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
    $title = 'Laporan_Rekapitulasi_Komisi_' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
    $title = 'Laporan_Rekapitulasi_Komisi_' . $tahun;
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

$sql = "SELECT * FROM _saldo_komisi where tahun = $tahun_aktif ";
$laporan = 'TAHUN ' . $tahun_aktif;

if ($tahun == 0) {
    $sql .= " ";
} else {
    $sql = "SELECT * FROM _saldo_komisi where tahun = $tahun ";
    $laporan = 'TAHUN ' . $tahun;
}

$result = mysqli_query($conn, $sql);

$dataall = "";
$tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="text-align: center; font-weight:bold">
                    <td width="22%">Bidang</td>
                    <td width="30%">Komisi</td>
                    <td width="12%">Saldo Awal</td>
                    <td width="12%">Penerimaan</td>
                    <td width="12%">Pengeluaran</td>
                    <td width="12%">Saldo Akhir</td>
                </tr>';


$total_saldoAwal = 0;
$total_penerimaan = 0;
$total_pengeluaran = 0;
$total_saldoAkhir = 0;

while ($row = mysqli_fetch_assoc($result)) {


    $saldo_awal = ($row["saldo_awal"] == 0 || empty($row["saldo_awal"])) ? "-" : number_format($row["saldo_awal"], 0, '.', ',');
    $jumlah_penerimaan = ($row["jumlah_penerimaan"] == 0 || empty($row["jumlah_penerimaan"])) ? "-" : number_format($row["jumlah_penerimaan"], 0, '.', ',');
    $jumlah_pengeluaran = ($row["jumlah_pengeluaran"] == 0 || empty($row["jumlah_pengeluaran"])) ? "-" : number_format($row["jumlah_pengeluaran"], 0, '.', ',');
    $saldo_akhir = ($row["saldo_akhir"] == 0 || empty($row["saldo_akhir"])) ? "-" : number_format($row["saldo_akhir"], 0, '.', ',');

    $total_saldoAwal = $total_saldoAwal + $row["saldo_awal"];
    $total_pengeluaran = $total_pengeluaran + $row["jumlah_pengeluaran"];
    $total_penerimaan = $total_penerimaan + $row["jumlah_penerimaan"];
    $total_saldoAkhir = $total_saldoAkhir + $row["saldo_akhir"];

    
    $dataall = $dataall .   '<tr>
                                <td>' . $row["nama_bidang"] . '</td>
                                <td>' . $row["nama_komisi"] . '</td>
                                <td style="text-align: right"> ' . $saldo_awal .   '</td>
                                <td style="text-align: right"> ' . $jumlah_penerimaan .   '</td>
                                <td style="text-align: right">' . $jumlah_pengeluaran .   '</td> 
                                <td style="text-align: right">' . $saldo_akhir .   '</td> 
                            </tr>';
}

$tabelfooter = '<tr style="text-align: right; font-weight:bold">
                    <td width="52%" style="text-align: left">Total</td>
                    <td></td>
                    <td width="12%">' . number_format($total_saldoAwal, 0, '.', ',') . '</td>
                    <td width="12%">' . number_format($total_penerimaan, 0, '.', ',') . '</td>
                    <td width="12%">' . number_format($total_pengeluaran, 0, '.', ',') . '</td>
                    <td width="12%">' . number_format($total_saldoAkhir, 0, '.', ',') . '</td>
                </tr>
                </table>';

$title1 = '<h2 style="text-align: center;">GEREJA KRISTEN JAWA DAYU</h2>';
$title2 = '<h3 style="text-align: center;">LAPORAN REKAPITULASI TAHUNAN KOMISI</h3>';
$title3 = '<h3 style="text-align: center;">' . $laporan . '</h2>';
$titleLaporan = $title1 . $title2 . $title3;

$allshowdata = $titleLaporan . $tabelheader . $tabelcaption . $dataall .$tabelfooter;

echo $allshowdata;
