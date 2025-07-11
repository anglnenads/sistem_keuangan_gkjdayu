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
    $title = 'Rekapitulasi_Realisasi_Komisi_' . $tahun_aktif;
} else {
    $tahun_aktif = 0;
}

if (isset($_SESSION['tahun'])) {
    $tahun = $_SESSION['tahun'];
    $title = 'Rekapitulasi_Realisasi_Komisi_' . $tahun;
} else {
    $tahun = 0;
}

if (isset($_SESSION['bulan'])) {
    $bulan = $_SESSION['bulan'];
    $title = 'Rekapitulasi_Realisasi_Komisi_' . $nama_bulan[$bulan] . '_' . $tahun_aktif;
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

    if ($row = $result->fetch_assoc()) {
        $namaUser = $row['nama'];
        $jbtnUser = $row['jbtn'];
    }
}

if (isset($_SESSION["komisi"])) {
    $id_komisi = $_SESSION["komisi"];
    $sql = "SELECT id_komisi, nama_komisi FROM komisi WHERE id_komisi = $id_komisi";
    $result = mysqli_query($GLOBALS["conn"], $sql);

    if ($row = $result->fetch_assoc()) {
        $bidangKomisi = $row['nama_komisi'];
    }
} elseif (isset($_SESSION["bidang"])) {
    $id_bidang = $_SESSION["bidang"];
    $sql = "SELECT id_bidang, nama_bidang FROM bidang WHERE id_bidang = $id_bidang";
    $result = mysqli_query($GLOBALS["conn"], $sql);

    if ($row = $result->fetch_assoc()) {
        $bidangKomisi = $row['nama_bidang'];
    }
}

if (isset($_SESSION['komisi']) || isset($_SESSION['bidang'])) {

    if (isset($_SESSION['komisi'])) {
        $sql_penerimaan = "SELECT * from v_realisasikomisi WHERE jenis COLLATE utf8mb4_general_ci='Realisasi Penerimaan'  AND id_komisi =" . $id_komisi . "";
        $sql_pengeluaran = "SELECT * from v_realisasikomisi WHERE jenis COLLATE utf8mb4_general_ci='Realisasi Pengeluaran' AND id_komisi =" . $id_komisi . "";
    } elseif (isset($_SESSION['bidang'])) {
        $sql_penerimaan = "SELECT * from v_realisasikomisi WHERE jenis COLLATE utf8mb4_general_ci='Realisasi Penerimaan'  AND id_bidang =" . $id_bidang . "";
        $sql_pengeluaran = "SELECT * from v_realisasikomisi WHERE jenis COLLATE utf8mb4_general_ci='Realisasi Pengeluaran' AND id_bidang =" . $id_bidang . "";
    }

    $laporan = 'TAHUN ' . $tahun_aktif;

    if (!empty($bulan)) {
        if ($bulan == 0) {
            $sql_penerimaan .= "";
            $sql_pengeluaran .= "";
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
            $sql_penerimaan .= " AND tahun = $tahun_aktif AND month(tanggal) = " . $bulan;
            $sql_pengeluaran .= " AND tahun = $tahun_aktif AND  month(tanggal) = " . $bulan;

            $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun_aktif;
        }
    } elseif (!empty($tahun)) {
        if ($tahun == 0) {
            $sql_penerimaan .= "";
            $sql_pengeluaran .= "";
        } else {
            $sql_penerimaan .= " AND tahun = $tahun ";
            $sql_pengeluaran .= " AND tahun = $tahun ";
            $laporan = 'TAHUN ' . $tahun;
        }
    } else {
        $sql_penerimaan .= " AND tahun = $tahun_aktif ";
        $sql_pengeluaran .= " AND tahun = $tahun_aktif ";
        $laporan = 'TAHUN ' . $tahun_aktif;
    }

    $sql_penerimaan .= " ORDER BY tanggal ASC";
    $sql_pengeluaran .= " ORDER BY tanggal ASC";

    $result_penerimaan = mysqli_query($conn, $sql_penerimaan);
    $result_pengeluaran = mysqli_query($conn, $sql_pengeluaran);

    $tabelheader1 = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
    $tabelcaption1 = '<tr style="text-align: center; font-weight:bold;">
                        <td rowspan="2" width="100%" class="text-center">REALISASI PENERIMAAN</td> 
                    </tr> 

                    <tr>
                        <td></td>
                    </tr> 

                    <tr style="text-align: center; font-weight:bold">
                        <td width="5%" class="text-center">No</td>
                        <td width="10%" class="text-center">Tanggal</td>
                        <td width="22%" class="text-center">Jenis Kegiatan</td>
                        <td width="5%" class="text-center">Vol</td>
                        <td width="14%" class="text-center">Satuan</td>
                        <td width="11%" class="text-center">Jumlah</td>
                        <td width="11%" class="text-center">Dana Gereja</td>
                        <td width="11%" class="text-center">Dana Swadaya</td>
                        <td width="11%" class="text-center">SubTotal</td>
                    </tr>';


    $data_per_program = [];
    while ($row = mysqli_fetch_assoc($result_penerimaan)) {
        $data_per_program[$row["nama_program"]][] = $row;
    }

    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;

    $dataall1 = "";
    foreach ($data_per_program as $program => $dataList) {
        $dataall1 .= '<tr style="font-weight: bold; background-color: #dcdcdc;">
                        <td colspan="9">Program: ' . $program . '</td>
                      </tr>';

        $number = 0;
        $total_jumlah = 0;
        $total_danaGereja = 0;
        $total_danaSwadaya = 0;
        $total_subtotal = 0;

        foreach ($dataList as $row) {
            $number++;
            $volume = ($row["volume"] == 0 || empty($row["volume"])) ? "-" : number_format($row["volume"], 0, ',', '.');
            $harga_satuan = ($row["harga_satuan"] == 0 || empty($row["harga_satuan"])) ? "-" : number_format($row["harga_satuan"], 0, ',', '.');
            $jumlah = ($row["jumlah"] == 0 || empty($row["jumlah"])) ? "-" : number_format($row["jumlah"], 0, ',', '.');
            $dana_gereja = ($row["dana_gereja"] == 0 || empty($row["dana_gereja"])) ? "-" : number_format($row["dana_gereja"], 0, ',', '.');
            $dana_swadaya = ($row["dana_swadaya"] == 0 || empty($row["dana_swadaya"])) ? "-" : number_format($row["dana_swadaya"], 0, ',', '.');
            $subtotal = $row["dana_gereja"] + $row["dana_swadaya"];

            $total_jumlah += $row["jumlah"];
            $total_danaGereja += $row["dana_gereja"];
            $total_danaSwadaya += $row["dana_swadaya"];
            $total_subtotal += $subtotal;

            $dataall1 .= '<tr>
                            <td style="text-align: center">' . $number . '</td>
                            <td style="text-align: center">' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
                            <td>' . $row["jenis_kegiatan"] . '</td>
                            <td style="text-align: center">' . $volume . '</td>
                            <td style="text-align: right">' . $harga_satuan . "/" . $row["satuan"] . '</td>
                            <td style="text-align: right">' . $jumlah . '</td>
                            <td style="text-align: right">' . $dana_gereja . '</td>
                            <td style="text-align: right">' . $dana_swadaya . '</td>
                            <td style="text-align: right">' . number_format($subtotal, 0, ',', '.') . '</td>
                        </tr>';
        }

        $dataall1 .= '<tr style="text-align: right; font-weight:bold;">
                        <td colspan="5" style="text-align: center">Total </td>
                        <td>' . number_format($total_jumlah, 0, ',', '.') . '</td>
                        <td>' . number_format($total_danaGereja, 0, ',', '.') . '</td>
                        <td>' . number_format($total_danaSwadaya, 0, ',', '.') . '</td>
                        <td>' . number_format($total_subtotal, 0, ',', '.') . '</td>
                      </tr>';

        $total1 += $total_jumlah;
        $total2 += $total_danaGereja;
        $total3 += $total_danaSwadaya;
        $total4 += $total_subtotal;
    }

    $tabelfooter1 = '<tr style="text-align: right; font-weight:bold; background-color: #d9edf7;">
                        <td colspan="5" style="text-align: center">Total Realisasi Penerimaan</td>
                        <td>' . number_format($total1, 0, ',', '.') . '</td>
                        <td>' . number_format($total2, 0, ',', '.') . '</td>
                        <td>' . number_format($total3, 0, ',', '.') . '</td>
                        <td>' . number_format($total4, 0, ',', '.') . '</td>
                      </tr>
                      </table>';


    $allshowdata1 = $tabelheader1 . $tabelcaption1 . $dataall1 . $tabelfooter1;


    $dataall = "";
    $tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
    $tabelcaption = '<tr style="text-align: center; font-weight:bold;">
                        <td rowspan="2" width="100%" class="text-center">REALISASI PENGELUARAN</td> 
                    </tr> 
                    <tr>
                        <td></td> 
                    </tr> 
                    <tr style="text-align: center; font-weight:bold">
                        <td width="5%" class="text-center">No</td>
                        <td width="10%" class="text-center">Tanggal</td>
                        <td width="22%" class="text-center">Jenis Kegiatan</td>
                        <td width="5%" class="text-center">Vol</td>
                        <td width="14%" class="text-center">Satuan</td>
                        <td width="11%" class="text-center">Jumlah</td>
                        <td width="11%" class="text-center">Dana Gereja</td>
                        <td width="11%" class="text-center">Dana Swadaya</td>
                        <td width="11%" class="text-center">SubTotal</td>
                    </tr>';


    $data_per_program = [];
    while ($row = mysqli_fetch_assoc($result_pengeluaran)) {
        $data_per_program[$row["nama_program"]][] = $row;
    }

    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;

    foreach ($data_per_program as $program => $dataList) {
        $dataall .= '<tr style="font-weight: bold; background-color: #dcdcdc;">
                        <td colspan="9">Program: ' . $program . '</td>
                    </tr>';

        $number = 0;
        $total_jumlah = 0;
        $total_danaGereja = 0;
        $total_danaSwadaya = 0;
        $total_subtotal = 0;

        foreach ($dataList as $row) {
            $number++;
            $volume = ($row["volume"] == 0 || empty($row["volume"])) ? "-" : number_format($row["volume"], 0, ',', '.');
            $harga_satuan = ($row["harga_satuan"] == 0 || empty($row["harga_satuan"])) ? "-" : number_format($row["harga_satuan"], 0, ',', '.');
            $jumlah = ($row["jumlah"] == 0 || empty($row["jumlah"])) ? "-" : number_format($row["jumlah"], 0, ',', '.');
            $dana_gereja = ($row["dana_gereja"] == 0 || empty($row["dana_gereja"])) ? "-" : number_format($row["dana_gereja"], 0, ',', '.');
            $dana_swadaya = ($row["dana_swadaya"] == 0 || empty($row["dana_swadaya"])) ? "-" : number_format($row["dana_swadaya"], 0, ',', '.');
            $subtotal = $row["dana_gereja"] + $row["dana_swadaya"];

            $total_jumlah += $row["jumlah"];
            $total_danaGereja += $row["dana_gereja"];
            $total_danaSwadaya += $row["dana_swadaya"];
            $total_subtotal += $subtotal;

            $dataall .= '<tr>
                            <td style="text-align: center">' . $number . '</td>
                            <td style="text-align: center">' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
                            <td>' . $row["jenis_kegiatan"] . '</td>
                            <td style="text-align: center">' . $volume . '</td>
                            <td style="text-align: right">' . $harga_satuan . "/" . $row["satuan"] . '</td>
                            <td style="text-align: right">' . $jumlah . '</td>
                            <td style="text-align: right">' . $dana_gereja . '</td>
                            <td style="text-align: right">' . $dana_swadaya . '</td>
                            <td style="text-align: right">' . number_format($subtotal, 0, ',', '.') . '</td>
                        </tr>';
        }

        $dataall .= '<tr style="text-align: right; font-weight:bold;">
                        <td colspan="5" style="text-align: center">Total</td>
                        <td>' . number_format($total_jumlah, 0, ',', '.') . '</td>
                        <td>' . number_format($total_danaGereja, 0, ',', '.') . '</td>
                        <td>' . number_format($total_danaSwadaya, 0, ',', '.') . '</td>
                        <td>' . number_format($total_subtotal, 0, ',', '.') . '</td>
                    </tr>';

        $total1 += $total_jumlah;
        $total2 += $total_danaGereja;
        $total3 += $total_danaSwadaya;
        $total4 += $total_subtotal;
    }

    $tabelfooter = '<tr style="text-align: right; font-weight:bold; background-color: #d9edf7;">
                        <td colspan="5" style="text-align: center">Total Realisasi Pengeluaran</td>
                        <td>' . number_format($total1, 0, ',', '.') . '</td>
                        <td>' . number_format($total2, 0, ',', '.') . '</td>
                        <td>' . number_format($total3, 0, ',', '.') . '</td>
                        <td>' . number_format($total4, 0, ',', '.') . '</td>
                    </tr>
                </table>';

    $date = date('d-m-Y');
    $user = "<br><h4>Diunduh tanggal $date oleh $namaUser - $jbtnUser</h4>";
    $allshowdata2 = $tabelheader . $tabelcaption . $dataall . $tabelfooter . $user;

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nama Anda');
    $pdf->SetTitle('Laporan Realisasi Komisi');

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
    $pdf->Cell(0, 10, 'LAPORAN REALISASI ' .  strtoupper($bidangKomisi), 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, $laporan, 0, 1, 'C');


    // Tambah Tabel
    $pdf->SetFont('helvetica', '', 10);

    $pdf->writeHTML($allshowdata1, true, false, true, false, '');
    $pdf->writeHTML($allshowdata2, true, false, true, false, '');

    // Output PDF
    $pdf->Output($title .'_'.$bidangKomisi. '.pdf', 'I');

} else {

    $sql = "SELECT
            b.nama_bidang,
            k.nama_komisi,
            SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN v.dana_gereja ELSE 0 END) AS penerimaan_dana_gereja,
            SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN v.dana_swadaya ELSE 0 END) AS penerimaan_dana_swadaya,
            SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN v.jumlah ELSE 0 END) AS jumlah_penerimaan,
            SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN v.dana_gereja ELSE 0 END) AS pengeluaran_dana_gereja,
            SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN v.dana_swadaya ELSE 0 END) AS pengeluaran_dana_swadaya,
            SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN v.jumlah ELSE 0 END) AS jumlah_pengeluaran
            FROM bidang b
               LEFT JOIN komisi k ON k.id_bidang = b.id_bidang
             LEFT JOIN v_realisasikomisi v 
                            ON v.id_bidang = b.id_bidang 
                             AND (
                            (v.id_komisi IS NULL AND (k.id_komisi IS NULL OR k.id_komisi = 0)) 
                            OR v.id_komisi = k.id_komisi) AND
            v.tahun = $tahun_aktif";
    
    $laporan = 'TAHUN ' . $tahun_aktif;

    if ($bulan == 0) {
        $sql .= "";
    } else {
        $nama_bulan = $nama_bulan[$bulan];

        $sql .= " AND  month(v.tanggal) = " . $bulan;
        $laporan = 'BULAN ' . $nama_bulan . ' TAHUN ' . $tahun_aktif;
    }

    if ($tahun == 0) {
        $sql .= "";
    } else {
        $sql = "SELECT
                        b.nama_bidang, k.nama_komisi,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN dana_gereja ELSE 0 END) AS penerimaan_dana_gereja,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN dana_swadaya ELSE 0 END) AS penerimaan_dana_swadaya,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN jumlah ELSE 0 END) AS jumlah_penerimaan,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN dana_gereja ELSE 0 END) AS pengeluaran_dana_gereja,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN dana_swadaya ELSE 0 END) AS pengeluaran_dana_swadaya,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN jumlah ELSE 0 END) AS jumlah_pengeluaran
                   FROM bidang b
                        LEFT JOIN komisi k ON k.id_bidang = b.id_bidang
                          LEFT JOIN v_realisasikomisi v 
                            ON v.id_bidang = b.id_bidang 
                             AND (
                            (v.id_komisi IS NULL AND (k.id_komisi IS NULL OR k.id_komisi = 0)) 
                            OR v.id_komisi = k.id_komisi) AND
                        v.tahun = $tahun";

        $laporan = 'TAHUN ' . $tahun;
    }

    $sql .= "  GROUP BY b.id_bidang, k.id_komisi ORDER BY b.id_bidang, k.id_komisi";

    $result = mysqli_query($conn, $sql);

    $dataall = "";
    $tabelheader = '<table border="1" width="100%" cellspacing="0" cellpadding="4">';
    $tabelcaption = '<tr style="text-align: center; font-weight:bold">
                        <td rowspan="2" width="20%" class="text-center">Bidang</td>
                        <td rowspan="2" width="20%" class="text-center">Komisi</td>
                        <td colspan="" width="30%" class="text-center">Realisasi Penerimaan</td>
                        <td colspan="" width="30%" class="text-center">Realisasi Pengeluaran</td>
                    </tr>
                
                <tr style="text-align: center; font-weight:bold">
                    <td width="10%" class="text-center">Dana Gereja</td>
                    <td width="10%" class="text-center">Dana Swadaya</td>
                    <td width="10%" class="text-center">Subtotal</td>
                    <td width="10%" class="text-center">Dana Gereja</td>
                    <td width="10%" class="text-center">Dana Swadaya</td>
                    <td width="10%" class="text-center">SubTotal</td>
                </tr>';


    $total1 = 0;
    $total2 = 0;
    $total3 = 0;
    $total4 = 0;
    $total5 = 0;
    $total6 = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        $penerimaan_danaGereja = ($row["penerimaan_dana_gereja"] == 0 || empty($row["penerimaan_dana_gereja"])) ? "-" : number_format($row["penerimaan_dana_gereja"], 0, ',', '.');
        $penerimaan_danaSwadaya = ($row["penerimaan_dana_swadaya"] == 0 || empty($row["penerimaan_dana_swadaya"])) ? "-" : number_format($row["penerimaan_dana_swadaya"], 0, ',', '.');
        $penerimaan_total = ($row["jumlah_penerimaan"] == 0 || empty($row["jumlah_penerimaan"])) ? "-" : number_format($row["jumlah_penerimaan"], 0, ',', '.');
        $pengeluaran_danaGereja = ($row["pengeluaran_dana_gereja"] == 0 || empty($row["pengeluaran_dana_gereja"])) ? "-" : number_format($row["pengeluaran_dana_gereja"], 0, ',', '.');
        $pengeluaran_danaSwadaya = ($row["pengeluaran_dana_swadaya"] == 0 || empty($row["pengeluaran_dana_swadaya"])) ? "-" : number_format($row["pengeluaran_dana_swadaya"], 0, ',', '.');
        $pengeluaran_total = ($row["jumlah_pengeluaran"] == 0 || empty($row["jumlah_pengeluaran"])) ? "-" : number_format($row["jumlah_pengeluaran"], 0, ',', '.');

        $total1 += $row["penerimaan_dana_gereja"];
        $total2 += $row["penerimaan_dana_swadaya"];
        $total3 += $row["jumlah_penerimaan"];
        $total4 += $row["pengeluaran_dana_gereja"];
        $total5 += $row["pengeluaran_dana_swadaya"];
        $total6 += $row["jumlah_pengeluaran"];

        $dataall = $dataall . '<tr>
                                    <td>' . $row["nama_bidang"] . "</td>
                                    <td>" . $row["nama_komisi"] . '</td>
                                    <td style="text-align: right">' . $penerimaan_danaGereja .   '</td>
                                    <td style="text-align: right">' . $penerimaan_danaSwadaya .   '</td>
                                    <td style="text-align: right">' . $penerimaan_total .   '</td>
                                    <td style="text-align: right">' . $pengeluaran_danaGereja .   '</td>
                                    <td style="text-align: right">' . $pengeluaran_danaSwadaya .   '</td>
                                    <td style="text-align: right">' . $pengeluaran_total .   '</td>
                                </tr>';
    }

    $tabelfooter = '<tr style="font-weight:bold; text-align:reight">
                        <td colspan="2" style="font-weight:bold; text-align:left">Total</td>
                        <td>' . number_format($total1, 0, ',', '.') . '</td>
                        <td>' . number_format($total2, 0, ',', '.') . '</td>
                        <td>' . number_format($total3, 0, ',', '.') . '</td>
                        <td>' . number_format($total4, 0, ',', '.') . '</td>
                        <td>' . number_format($total5, 0, ',', '.') . '</td>
                        <td>' . number_format($total6, 0, ',', '.') . '</td>
                    </tr>
                    </table>';

    $date = date('d-m-Y');
    $user = "<br><h4>Diunduh tanggal $date oleh $namaUser - $jbtnUser</h4>";
    $allshowdata = $tabelheader . $tabelcaption . $dataall . $tabelfooter . $user;

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nama Anda');
    $pdf->SetTitle('Laporan Realisasi Komisi');

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
    $pdf->Cell(0, 10, 'REKAPITULASI REALISASI KOMISI', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, $laporan, 0, 1, 'C');

    // Tambah Tabel
    $pdf->SetFont('helvetica', '', 10);

    $pdf->writeHTML($allshowdata, true, false, true, false, '');

    // Output PDF
    $pdf->Output($title . '.pdf', 'I');

};
