<?php

session_start(); 

if (isset($_SESSION['bulan'])) {
    $bulan = $_SESSION['bulan'];
} else {
    $bulan = 0;
}

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
    header("Location: http://localhost:80/gkj_dayu/");
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
$conn = mysqli_connect("localhost", "root", "", "gkj_dayu", "3306");

if (isset($_SESSION["id_user"])) {
  $id_user = $_SESSION['id_user'];
  $sql = "SELECT nama, jbtn FROM user WHERE id_user = $id_user";
  $result = mysqli_query($GLOBALS["conn"], $sql);

  if ($row = $result->fetch_assoc()) {
    $namaUser = $row['nama'];
    $jbtnUser = $row['jbtn'];
  }
}

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

        $nama_bulan = $nama_bulan[$bulan];

        $sql .= " WHERE tahun = $tahun_aktif AND  month(tanggal_pengeluaran) = $bulan AND p.status = 'Tervalidasi'";
        $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun_aktif;
        $title = 'Laporan Pengeluaran Gereja ' .$nama_bulan .' ' . $tahun_aktif;
    }
} else if (!empty($tahun)) {
    if ($tahun == 0) {
        $sql .= "WHERE tahun = " . $tahun_aktif . " AND p.status = 'Tervalidasi'";
    } else {
        $sql .= "WHERE tahun = " . $tahun . " AND p.status = 'Tervalidasi'";
        $laporan = 'TAHUN ' . $tahun;
        $title = 'Laporan Pengeluaran Gereja ' . $tahun;
    }
} else {
    $sql .= "WHERE tahun = " . $tahun_aktif . " AND p.status = 'Tervalidasi'";
    $laporan = 'TAHUN ' . $tahun_aktif;
    $title = 'Laporan Pengeluaran Gereja ' . $tahun_aktif;
}

$sql .= " ORDER BY tanggal_pengeluaran ASC";


$result = mysqli_query($conn, $sql);

$data1 = "";
$tabelheader = '<table border="" width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr>
                    <td width="16%"></td>
                    <td width="50%"></td>
                    <td width="17%"></td>
                    <td width="17%"></td>
                </tr>';

$cnourut = 0;
$total_pengeluaran = 0;
$all_total = 0;

$groupedData = [];

foreach ($result as $data) {
    $tanggal = $data["tanggal_pengeluaran"];

    $groupedData[$tanggal][] = $data;
}



foreach ($groupedData as $tanggal => $tanggalData) {

    $data1 = $data1 . '<tr>
                            <td style="text-align: center;">' . date('d-m-Y', strtotime($tanggal)) . "</td>
                            <td></td>
                            <td></td>
                        </tr>";

    $total_pengeluaran = 0;
    foreach ($tanggalData as $data) {

        $total_pengeluaran = $total_pengeluaran + $data["jumlah_pengeluaran"];
        $data1 = $data1 . "<tr>
                            <td></td>
                            <td style='padding-left: 10px;'>" . $data["jenis_pengeluaran"] . '</td>
                            <td style="text-align: right;">' . number_format($data["jumlah_pengeluaran"], 0, ',', '.') . "</td>
                            <td></td>
                        </tr>";
    }

    $data1 = $data1 . '<tr>
                        <td></td>
                        <td> Total </td>
                        <td style="border-top: 1px solid black;"></td>
                        <td style="text-align: right;">' . number_format($total_pengeluaran, 0, ',', '.') . '</td>
                        <td></td> 
                        <td></td> 
                    </tr>
                    <tr>
                        <td></td>
                    </tr>';
    $all_total = $all_total + $total_pengeluaran;
}


$tabelfooter = '<tr style="font-weight:bold">
                    <td width="33%">Total Pengeluaran</td>
                    <td></td>
                    <td style="text-align: right; border-top: 1px solid black;">' . number_format($all_total, 0, ',', '.') .   '</td>
                </tr>
                </table>';


$date = date('d-m-Y');
$user = "<br><h4>Diunduh tanggal $date oleh $namaUser - $jbtnUser</h4>";

$allshowdata = $tabelheader . $tabelcaption . $data1 . $tabelfooter . $user;

$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle($title);

$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Start First Page Group
$pdf->startPageGroup();
// Tambahkan halaman
$pdf->AddPage();

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Tambah Judul
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'GEREJA KRISTEN JAWA DAYU', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'LAPORAN PENGELUARAN', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, $laporan, 0, 1, 'C');


// Tambah Tabel
$pdf->SetFont('helvetica', '', 10);

$pdf->writeHTML($allshowdata, true, false, true, false, '');

// Output PDF
$pdf->Output($title.'.pdf', 'I');
