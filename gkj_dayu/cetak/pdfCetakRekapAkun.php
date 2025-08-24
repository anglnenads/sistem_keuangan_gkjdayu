<?php

session_start(); 

if (isset($_SESSION['tahun'])) {
  $tahun = $_SESSION['tahun'];
} else {
  $tahun = 0;
}

if (isset($_SESSION['tahun_aktif'])) {
  $tahun_aktif = $_SESSION['tahun_aktif'];
} else {
  $tahun_aktif = 0;
}

if (!isset($_SESSION['id_user'])) {
    header("Location: ../");
  exit;
}

?>
<?php

require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF
{

  //Page header
  public function Header()
  {
    // Logo
    $image_file = K_PATH_IMAGES . 'logo_example.jpg';
    $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    // Set font
    $this->SetFont('helvetica', 'B', 20);
    // Title
    $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
  }

  // Page footer
  public function Footer()
  {
    // Position at 15 mm from bottom
    $this->SetY(-15);
    // Set font
    $this->SetFont('helvetica', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
  }
}


// connection & query
$conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, (int)$this->dbPort);

if (isset($_SESSION["id_user"])) {
  $id_user = $_SESSION['id_user'];
  $sql = "SELECT nama, jbtn FROM user WHERE id_user = $id_user";
  $result = mysqli_query($GLOBALS["conn"], $sql);

  if ($row = $result->fetch_assoc()) {
    $namaUser = $row['nama'];
    $jbtnUser = $row['jbtn'];
  }
}

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
$title = 'Laporan Rekapitulasi Gereja ' . $tahun_aktif;

if (!empty($tahun)) {
  if ($tahun == 0) {
    $query .= '';
  } else {
    $query = ' SELECT jenis_akun, saldo_awal FROM v_saldo_akun WHERE tahun = ' . $tahun . ' GROUP BY jenis_akun';
    $laporan = 'TAHUN ' . $tahun;
    $title = 'Laporan Rekapitulasi Gereja ' . $tahun;
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
                            <td rowspan="2" style="text-align: right; vertical-align: middle; font-weight: bold;">' . number_format($saldo_awal, 0, ',', '.') . '</td>';
  
                            for ($b = 1; $b <= 12; $b++) {
                              $val = $data['bulan_penerimaan'][$b] ?? 0;
                              $dataall .= '<td style="text-align: right">' . ($val == 0 ? '-' : number_format($val, 0, ',', '.')) . '</td>';
                            }

              $dataall .=  '<td style="text-align: right; font-weight: bold;">' . number_format($data['total_penerimaan'], 0, ',', '.') . '</td>
                            <td rowspan="2" style="text-align: right; font-weight: bold;">' . number_format($saldo_akhir, 0, ',', '.') . '</td>
                          </tr>

                          <tr>
                              <td style="text-align: center; font-weight: bold;">Pengeluaran</td>';
  
                              for ($b = 1; $b <= 12; $b++) {
                                $val = $data['bulan_pengeluaran'][$b] ?? 0;
                                $dataall .= '<td style="text-align: right">' . ($val == 0 ? '-' : number_format($val, 0, ',', '.')) . '</td>';
                              }
          
              $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($data['total_pengeluaran'], 0, ',', '.') . '</td>
                          </tr>';

}

$dataall .=  '<tr>
                <td style="text-align: center; font-weight: bold;" rowspan="2">TOTAL</td>
                <td style="text-align: center; font-weight: bold;">Penerimaan</td>
                <td rowspan="2" style="text-align: right; font-weight: bold;">' . number_format($totalSaldoAwal, 0, ',', '.') . '</td>';

                for ($b = 1; $b <= 12; $b++) {
                  $totalPenerimaanPerBulan = $totalPerBulan['penerimaan'][$b] ?? 0;
                  $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($totalPenerimaanPerBulan, 0, ',', '.') . '</td>';
                }

    $dataall .= '<td  style="text-align: right; font-weight: bold;">' . number_format($totalPenerimaanSemua, 0, ',', '.') . '</td>
                <td rowspan="2" style="text-align: right; font-weight: bold;">' .  number_format($totalSaldoAkhir, 0, ',', '.') . '</td>
              </tr>

              <tr>
                <td style="text-align: center; font-weight: bold;">Pengeluaran</td>';

                for ($b = 1; $b <= 12; $b++) {
                  $totalPengeluaranPerBulan = $totalPerBulan['pengeluaran'][$b] ?? 0;
                  $dataall .= '<td style="text-align: right; font-weight: bold;">' . number_format($totalPengeluaranPerBulan, 0, ',', '.') . '</td>';
                }

    $dataall .= '<td style="text-align: right; vertical-align: middle;  font-weight: bold;">' . number_format($totalPengeluaranSemua, 0, ',', '.') . '</td>
                </tr>';
$tabelfooter = '</table>';

$date = date('d-m-Y');
$user = "<br><h4>Diunduh tanggal $date oleh $namaUser - $jbtnUser</h4>";

$allshowdata = $tabelheader . $tabelcaption . $dataall . $tabelfooter . $user;


$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle($title );

$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Start First Page Group
$pdf->startPageGroup();
// Tambahkan halaman
// $pdf->AddPage('L');
$pdf->AddPage('L', array(400, 210)); 


// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


// Tambah Judul
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'GEREJA KRISTEN JAWA DAYU', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'LAPORAN REKAPITULASI BULANAN', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, $laporan, 0, 1, 'C');


// Tambah Tabel
$pdf->SetFont('helvetica', '', 9);

$pdf->writeHTML($allshowdata, true, false, true, false, '');

// Output PDF
$pdf->Output($title.'.pdf', 'I');
