<?php

session_start(); // Mulai session

if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
      $title = 'Laporan Rekapitulasi Gereja ' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
      $title = 'Laporan Rekapitulasi Gereja ' . $tahun;
} else {
    $tahun = 0;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: http://localhost:80/gkj_dayu/");
    exit;
}


header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"".$title.".xls\"");

$conn = mysqli_connect("localhost", "root", "", "gkj_dayu", "3306");

$sql = 'SELECT * FROM v_akun WHERE tahun = ' . $tahun_aktif;

if (!empty($tahun)) {
  if ($tahun == 0) {
    $sql .= "";
  } else {
    $sql = " SELECT * FROM v_akun WHERE tahun = $tahun";
  }
}

$query = "SELECT jenis_akun, saldo_awal FROM v_saldo_akun WHERE tahun = $tahun_aktif GROUP BY jenis_akun";
$laporan = 'TAHUN ' . $tahun_aktif;

if (!empty($tahun)) {
  if ($tahun == 0) {
    $query .= '';
  } else {
    $query = ' SELECT jenis_akun, saldo_awal FROM v_saldo_akun WHERE tahun = ' . $tahun . ' GROUP BY jenis_akun';
    $laporan = 'TAHUN ' . $tahun;
  }
}

$result_data = mysqli_query($conn, $sql);
$result_saldo = mysqli_query($conn, $query);

$saldo_row = "";
$saldo_awal = 0;
$saldo_pasamuan = 0;
$saldo_diakonia = 0;
$saldo_pembangunan = 0;
$saldo_pangruktilaya = 0;
$saldo_emiritus = 0;
$saldo_pemanggilan = 0;
$saldo_cadangan = 0;

$dataall = "";
$tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="font-weight: bold; text-align:center">
                        <td width="8%" class="text-center">NAMA POS</td>
                         <td width="5.8%" class="text-center"></td>
                        <td width="5.8%" class="text-center">SALDO AWAL</td>
                        <td width="5.8%" class="text-center">JANUARI</td>
                        <td width="5.8%" class="text-center">FEBRUARI</td>
                        <td width="5.8%" class="text-center">MARET</td>
                        <td width="5.8%" class="text-center">APRIL</td>
                        <td width="5.8%" class="text-center">MEI</td>
                        <td width="5.8%" class="text-center">JUNI</td>
                        <td width="5.8%" class="text-center">JULI</td>
                        <td width="5.8%" class="text-center">AGUSTUS</td>
                        <td width="6%" class="text-center">SEPTEMBER</td>
                        <td width="5.8%" class="text-center">OKTOBER</td>
                        <td width="5.8%" class="text-center">NOVEMBER</td>
                        <td width="5.8%" class="text-center">DESEMBER</td>
                        <td width="5.8%" class="text-center">TOTAL</td>
                        <td width="5.8%" class="text-center">SALDO AKHIR</td>
                    </tr>';

while ($row = mysqli_fetch_assoc($result_data)) {

  $jenis = $row['jenis_akun'];

  if (!isset($dataGrouped[$jenis])) {
    $dataGrouped[$jenis] = [
      'jenis_akun' => $jenis,
      'bulan_penerimaan' => [],
      'bulan_pengeluaran' => [],
      'total_penerimaan' => 0,
      'total_pengeluaran' => 0,
    ];
  }

  $bulan = (int)$row['bulan'];
  $penerimaan = (float)$row['total_penerimaan'];
  $pengeluaran = (float)$row['total_pengeluaran'];

  $dataGrouped[$jenis]['bulan_penerimaan'][$bulan] =
    ($dataGrouped[$jenis]['bulan_penerimaan'][$bulan] ?? 0) + $penerimaan;

  $dataGrouped[$jenis]['bulan_pengeluaran'][$bulan] =
    ($dataGrouped[$jenis]['bulan_pengeluaran'][$bulan] ?? 0) + $pengeluaran;

  $dataGrouped[$jenis]['total_penerimaan'] += $penerimaan;
  $dataGrouped[$jenis]['total_pengeluaran'] += $pengeluaran;

  // Tambahkan ke total per bulan dari semua jenis_akun
  $totalPerBulan['penerimaan'][$bulan] =
    ($totalPerBulan['penerimaan'][$bulan] ?? 0) + $penerimaan;

  $totalPerBulan['pengeluaran'][$bulan] =
    ($totalPerBulan['pengeluaran'][$bulan] ?? 0) + $pengeluaran;
}
$saldoTahunLalu = [];
while ($row = mysqli_fetch_assoc($result_saldo)) {
  $jenis = $row['jenis_akun'];
  $saldoTahunLalu[$jenis] = (float)$row['saldo_awal'];
}

$totalSaldoAwal = 0;
$totalPenerimaanSemua = 0;
$totalPengeluaranSemua = 0;
$totalSaldoAkhir = 0;

foreach ($dataGrouped as $jenisAkun => $data) {
  $saldo_awal = $saldoTahunLalu[$jenisAkun] ?? 0;
  $saldo_akhir = $saldo_awal + $data['total_penerimaan'] - $data['total_pengeluaran'];

  // Akumulasi total
  $totalSaldoAwal += $saldo_awal;
  $totalPenerimaanSemua += $data['total_penerimaan'];
  $totalPengeluaranSemua += $data['total_pengeluaran'];
  $totalSaldoAkhir += $saldo_akhir;

  $dataall = $dataall  . '<tr>
                            <td rowspan="2" style="text-align: center; font-weight: bold;">' . htmlspecialchars($jenisAkun) . '</td>
                            <td style="text-align: center; font-weight: bold;">Penerimaan</td>
                            <td rowspan="2" style="text-align: right; font-weight: bold;">' . number_format($saldo_awal, 0, '.', ',') . '</td>';
  for ($b = 1; $b <= 12; $b++) {
    $val = $data['bulan_penerimaan'][$b] ?? 0;
    $dataall .= '<td style="text-align: right">' . ($val == 0 ? '-' : number_format($val, 0, '.', ',')) . '</td>';
  }
  $dataall .=  '<td style="text-align: right; font-weight: bold;">' . number_format($data['total_penerimaan'], 0, '.', ',') . '</td>
                                        <td rowspan="2" style="text-align: right; font-weight: bold;">' . number_format($saldo_akhir, 0, '.', ',') . '</td>
                            </tr>
                          <tr>
                              <td style="text-align: center; font-weight: bold;">Pengeluaran</td>';
  for ($b = 1; $b <= 12; $b++) {
    $val = $data['bulan_pengeluaran'][$b] ?? 0;
    $dataall .= '<td style="text-align: right">' . ($val == 0 ? '-' : number_format($val, 0, '.', ',')) . '</td>';
  }
  $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($data['total_pengeluaran'], 0, '.', ',') . '</td>
                          </tr>';
}
                          $dataall .=  '<tr>
                            <td style="text-align: center; font-weight: bold;" rowspan="2">TOTAL</td>
                            <td style="text-align: center; font-weight: bold;">Penerimaan</td>
                            <td rowspan="2" style="text-align: right; font-weight: bold;">' . number_format($totalSaldoAwal, 0, '.', ',') . '</td>';
  for ($b = 1; $b <= 12; $b++) {
    $totalPenerimaanPerBulan = $totalPerBulan['penerimaan'][$b] ?? 0;
    $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($totalPenerimaanPerBulan, 0, '.', ',') . '</td>';
  }
  $dataall .= '<td  style="text-align: right; font-weight: bold;">' . number_format($totalPenerimaanSemua, 0, '.', ',') . '</td>
                                    <td rowspan="2" style="text-align: right; font-weight: bold;">' .  number_format($totalSaldoAkhir, 0, '.', ',') . '</td>
                        </tr>
                        <tr>
                        <td style="text-align: center; font-weight: bold;">Pengeluaran</td>';
                        for ($b = 1; $b <= 12; $b++) {
                            $totalPengeluaranPerBulan = $totalPerBulan['pengeluaran'][$b] ?? 0;
                            $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($totalPengeluaranPerBulan, 0, '.', ',') . '</td>';
                        }
                        $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($totalPengeluaranSemua, 0, '.', ',') .'</td>

                    </tr>';
$tabelfooter = '</table>';

$title1 = '<h2 style="text-align: center;">Gereja Kristen Jawa Dayu</h2>';
$title2 = '<h2 style="text-align: center;">Laporan Rekapitulasi</h2>';
$title3 = '<h2 style="text-align: center;">' . $laporan . '</h2>';
$titleLaporan = $title1 . $title2 . $title3;

$allshowdata = $titleLaporan . $tabelheader . $tabelcaption .  $dataall . $tabelfooter;

echo $allshowdata;
