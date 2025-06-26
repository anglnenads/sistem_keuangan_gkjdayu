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

    $saldo_awal = ($row["saldo_awal"] == 0 || empty($row["saldo_awal"])) ? "-" : number_format($row["saldo_awal"], 0, ',', '.');
    $jumlah_penerimaan = ($row["jumlah_penerimaan"] == 0 || empty($row["jumlah_penerimaan"])) ? "-" : number_format($row["jumlah_penerimaan"], 0, ',', '.');
    $jumlah_pengeluaran = ($row["jumlah_pengeluaran"] == 0 || empty($row["jumlah_pengeluaran"])) ? "-" : number_format($row["jumlah_pengeluaran"], 0, ',', '.');
    $saldo_akhir = ($row["saldo_akhir"] == 0 || empty($row["saldo_akhir"])) ? "-" : number_format($row["saldo_akhir"], 0, ',', '.');

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
                    <td width="12%">' . number_format($total_saldoAwal, 0, ',', '.') . '</td>
                    <td width="12%">' . number_format($total_penerimaan, 0, ',', '.') . '</td>
                    <td width="12%">' . number_format($total_pengeluaran, 0, ',', '.') . '</td>
                    <td width="12%">' . number_format($total_saldoAkhir, 0, ',', '.') . '</td>
                    </tr>
                </table>';

$date = date('d-m-Y');
$user = "<br><h4>Diunduh tanggal $date oleh $namaUser - $jbtnUser</h4>";

$allshowdata = $tabelheader . $tabelcaption .  $dataall . $tabelfooter . $user;


$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle('Laporan Rekapitulasi Komisi');

$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Start First Page Group
$pdf->startPageGroup();
// Tambahkan halaman
$pdf->AddPage('L');

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


// Tambah Judul
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'GEREJA KRISTEN JAWA DAYU', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'LAPORAN REKAPITULASI TAHUNAN KOMISI', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, $laporan, 0, 1, 'C');


// Tambah Tabel
$pdf->SetFont('helvetica', '', 10);

$pdf->writeHTML($allshowdata, true, false, true, false, '');

// Output PDF
$pdf->Output($title.'.pdf', 'I');
