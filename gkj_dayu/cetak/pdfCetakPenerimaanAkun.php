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
    $title = 'Rekapitulasi_Penerimaan_Gereja_' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
    $title = 'Rekapitulasi_Penerimaan_Gereja_' . $tahun;
} else {
    $tahun = 0;
}

if (isset($_SESSION['bulan'])) {
    $bulan = $_SESSION['bulan'];
    $title = 'Rekapitulasi_Penerimaan_Gereja_' . $nama_bulan[$bulan] . '_' . $tahun_aktif;
} else {
    $bulan = 0;
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

  // Cek apakah ada data
  if ($row = $result->fetch_assoc()) {
    $namaUser = $row['nama'];
    $jbtnUser = $row['jbtn'];
  }
}

$sql = "SELECT 
            fiskal.tahun AS tahun,
            akun.jenis_akun AS jenis_akun,
            akun.kode_akun AS kode_akun,
            akun.nama_akun AS nama_akun,
            COALESCE(rencana_rencana.jumlah_penerimaan, 0) AS jumlah_rencana,
            COALESCE(SUM(realisasi_penerimaan_gereja.jumlah_penerimaan), 0) AS jumlah_realisasi
          FROM (( fiskal JOIN akun ON (1 = 1))
            LEFT JOIN (
                SELECT r.id_fiskal AS id_fiskal, r.id_akun AS id_akun, SUM(r.jumlah_penerimaan) AS jumlah_penerimaan
                FROM rencana_penerimaan_gereja r
                GROUP BY r.id_fiskal, r.id_akun
              ) AS rencana_rencana 
              ON (akun.id_akun = rencana_rencana.id_akun AND fiskal.id_fiskal = rencana_rencana.id_fiskal))
            LEFT JOIN realisasi_penerimaan_gereja 
            ON ( akun.id_akun = realisasi_penerimaan_gereja.id_akun AND fiskal.id_fiskal = realisasi_penerimaan_gereja.id_fiskal AND realisasi_penerimaan_gereja.status = 'Tervalidasi')";

if (!empty($bulan)) {
    if ($bulan == 0) {
        $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND fiskal.tahun = $tahun_aktif
                    GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";
    } else {

        $sql = "SELECT a.nama_akun AS nama_akun, 0 AS jumlah_rencana, COALESCE(SUM(p.jumlah_penerimaan), 0) AS jumlah_realisasi ";
        $sql .= "FROM akun a ";
        $sql .= "LEFT JOIN (SELECT id_akun, jumlah_penerimaan FROM realisasi_penerimaan_gereja  WHERE MONTH(tanggal_penerimaan) = $bulan 
                            AND id_fiskal IN (SELECT id_fiskal FROM fiskal WHERE tahun = $tahun_aktif)) p ON a.id_akun = p.id_akun ";
        $sql .= " WHERE a.jenis_debitKredit = 'Kredit' AND a.statusAktif = 1 GROUP BY a.id_akun";

        $nama_bulan = $nama_bulan[$bulan];
        $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun_aktif;
    }
} else if (!empty($tahun)) {
    if ($tahun == 0) {
        $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND fiskal.tahun = $tahun_aktif
                    GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";
    } else {
        $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND tahun =  $tahun 
                            GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";

        $laporan = 'TAHUN ' . $tahun;
    }
} else {
    $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND fiskal.tahun = $tahun_aktif
                GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";

    $laporan = 'TAHUN ' . $tahun_aktif;
}

$result = mysqli_query($conn, $sql);

$dataall = "";
$tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
$tabelcaption = '<tr style="text-align: center; font-weight:bold">
                    <td width="50%">Akun</td>
                    <td width="25%">Rencana Penerimaan</td>
                    <td width="25%">Realisasi Penerimaan</td>
                </tr>';


$total_rencana = 0;
$total_realisasi = 0;

while ($row = mysqli_fetch_assoc($result)) {

    $jumlah_rencana = ($row["jumlah_rencana"] == 0 || empty($row["jumlah_rencana"])) ? "-" : number_format($row["jumlah_rencana"], 0, ',', '.');
    $jumlah_realisasi = ($row["jumlah_realisasi"] == 0 || empty($row["jumlah_realisasi"])) ? "-" : number_format($row["jumlah_realisasi"], 0, ',', '.');

    $total_rencana = $total_rencana + $row["jumlah_rencana"];
    $total_realisasi = $total_realisasi + $row["jumlah_realisasi"];


    $dataall = $dataall .   '<tr>
                                <td>' . $row["nama_akun"] . '</td>
                                <td style="text-align: right"> ' . $jumlah_rencana .   '</td>
                                <td style="text-align: right">' . $jumlah_realisasi .   '</td> 
                            </tr>';
}

$tabelfooter = '<tr style="text-align: right; font-weight:bold">
                    <td width="50%" style="text-align: left">Total</td>
                    <td width="25%">' . number_format($total_rencana, 0, ',', '.') . '</td>
                    <td width="25%">' . number_format($total_realisasi, 0, ',', '.') . '</td>
                </tr>
                </table>';

$date = date('d-m-Y');
$user = "<br><h4>Diunduh tanggal $date oleh $namaUser - $jbtnUser</h4>";

$allshowdata = $tabelheader . $tabelcaption .  $dataall . $tabelfooter . $user;



$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle('Laporan Rekapitulasi Anggaran Penerimaan Gereja');

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
$pdf->Cell(0, 10, 'REKAPITULASI PENERIMAAN GEREJA ', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, strtoupper($laporan), 0, 1, 'C');

// Tambah Tabel
$pdf->SetFont('helvetica', '', 10);

$pdf->writeHTML($allshowdata, true, false, true, false, '');

// Output PDF
$pdf->Output($title.'.pdf', 'I');
