<?php
// --- START OF CONSOLIDATED PHP LOGIC BLOCK ---

// STEP 1: Start the session immediately. This MUST be the first command.
session_start();

// STEP 3: Handle all session checks and redirects BEFORE any HTML is sent.

// Check if user is logged in and has the correct role. If not, destroy session and redirect.
// if (!isset($_SESSION["id_user"]) || $_SESSION["baseurl"] !== 'admin') {
//     session_unset();
//     session_destroy();
//     // Use a relative path for redirection, which is more portable than a hardcoded localhost URL.
//     header("Location: ../");
//     exit(); // Always exit after a redirect header.
// }

if (isset($_SESSION["id_user"])) {
    $id_user = $_SESSION['id_user'];

    if ($_SESSION["baseurl"] !== 'admin') {
        // Jika role tidak sesuai, hapus session dan arahkan ke halaman login
        session_unset();
        session_destroy();
         header("Location: ../");
        exit();
    }
} else {
    // Jika belum login, arahkan ke halaman login
     header("Location: ../");
    exit();
}


// Handle the logout action.
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header("Location: ../");
    exit();
}

// STEP 2: Include all necessary files and connect to the database.
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/cInsert.php");
include_once("../_function_i/cUpdate.php");
include_once("../_function_i/cDelete.php");
include_once("../_function_i/inc_f_object.php");

$conn = new cConnect();
$conn->goConnect();



// STEP 4: Process form submissions and fetch data needed for the page.
if (isset($_POST['tahun']) && !empty($_POST['tahun'])) {
    $_SESSION["tahun_aktif"] = $_POST['tahun'];
}
$tahun_aktif = isset($_SESSION["tahun_aktif"]) ? $_SESSION["tahun_aktif"] : NULL;

// Initialize variables to avoid errors if queries fail
$id_fiskal = null;
$status_aktif_fiskal = 0;

if ($tahun_aktif) {
    $sql = "SELECT id_fiskal, status_aktif, tahun FROM fiskal WHERE tahun = '$tahun_aktif'";
    $view = new cView();
    $array = $view->vViewData($sql);

    if (!empty($array)) {
        $row = $array[0];
        $id_fiskal = $row['id_fiskal'];
        $status_aktif_fiskal = $row['status_aktif'];
    } else {
        $sql_latest = "SELECT id_fiskal, tahun, status_aktif FROM fiskal WHERE status_aktif = 1 ORDER BY tahun DESC LIMIT 1";
        $array_latest = $view->vViewData($sql_latest);
        if (!empty($array_latest)) {
            $row_latest = $array_latest[0];
            $id_fiskal = $row_latest['id_fiskal'];
            $status_aktif_fiskal = $row_latest['status_aktif'];
            $tahun_aktif = $row_latest['tahun']; // Update the active year to the latest found
        }
    }
}

// Get URL segments for the page router
$request = $_SERVER['REQUEST_URI'];
$request = trim($request, '/');
$segments = explode('/', $request);

// --- END OF PHP LOGIC BLOCK ---
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Keuangan GKJ Dayu</title>

    <!-- Link CSS External -->
    <link rel="stylesheet" href="../styles.css?v=1.0">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tambahkan jQuery (pastikan jQuery sudah dimuat sebelumnya) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/ionicons@5.5.2/dist/css/ionicons.min.css">

    <!-- sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            overflow-x: hidden;
        }

        .navbar {
            background-color: #CCDEF4;
            color: #41406D;
            font-weight: bold;
        }

        .navbar-brand {
            color: #41406D
        }

        .nav-link {
            margin: 0px 0px 0px 30px;
        }

        .nav-link.active {
            color: #0870B6 !important;
        }

        .dropdown-item.active {
            color: red !important;
        }

        .nav-link:hover {
            color: #1F5ACD !important;
        }

        .dropdown-item {
            color: #504F82;
            font-weight: 500;
        }

        .dropdown-menu {
            margin-left: 19px;
        }

        .dropdown-menu a:hover {
            color: #1F5ACD !important;
        }

        .dropdown-menu a:active,
        .dropdown-menu a:focus {
            color: #FFFFFF !important;
        }

        .second {
            width: 100%;
            background-color: #F1F7FE;
            padding: 25px;
            border-radius: 8px;
            margin-left: auto;
            margin-right: auto;
        }

        .sub-title {
            width: 100%;
            background-color: #4079CE;
            padding: 10px;
            border-radius: 8px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            color: white;
            font-weight: 500;
            font-size: 23px;
        }

        .firstsection {
            width: 90%;
            background-color: #F1F7FE;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: auto;
            margin-right: auto;
        }

        .section {
            width: 90%;
            background-color: #F1F7FE;
            padding: 15px;
            border-radius: 8px;
            margin-left: auto;
            margin-right: auto;
        }

        .secondsection {
            width: 90%;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #5B8FCD;
            margin-left: auto;
            margin-right: auto;
        }

        .secondsection .horizontal-session {
            display: flex;
            width: 100%;
            gap: 10px;
            font-weight: bold;
            background-color: #e7feff;
            text-align: center;
            justify-content: center;
            padding: 6px;
            border-radius: 10px;
        }

        .firstsection .horizontal,
        .section .horizontal,
        .secondsection .horizontal {
            display: flex;
            gap: 60px;
        }

        .horizontal .form-group {
            display: flex;
            flex-direction: column;
            height: 80px;
            color: #334D74;
            font-weight: bold;
        }

        .horizontal .form-group1 {
            display: flex;
            color: #334D74;
            font-weight: bold;
        }

        .horizontal .form-group1 select {
            height: 35px;
            color: #334D74;
            width: 30px;
        }

        .horizontal .form-group input::placeholder {
            font-style: italic;
        }

        .horizontal label {
            display: block;
            margin-bottom: 8px;
            color: #334D74;
            font-weight: bold;
        }

        .horizontal input[type="text"],
        .horizontal input[type="email"],
        .horizontal input[type="date"],
        .horizontal input[type="number"],
        .horizontal textarea[type="text"] {
            width: 70%;
            height: 50%;
            padding: 10px;
            margin-bottom: 17px;
            border: 1px solid #99A4C7;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
            color: #334D74;
        }

        .horizontal select {
            padding: 6px;
            margin-bottom: 15px;
            border: 1px solid #99A4C7;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
            color: #334D74;
        }

        .filter input[type="number"],
        .filter select {
            border: 1px solid #676892;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
            color: #334D74;
        }

        .filter label {
            color: #334D74;
            font-weight: bold;
        }

        .horizontal textarea {
            color: #334D74;
        }

        .horizontal input[type="text"]:focus,
        .horizontal input[type="email"]:focus,
        .horizontal input[type="date"]:focus,
        .horizontal input[type="number"]:focus,
        .horizontal select:focus {
            border-color: #00796b;
            outline: none;
        }

        .firstsection .button,
        .secondsection .button,
        .section .button {
            width: 10%;
            height: 30px;
            background-color: #49749C;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
            font-size: 15px;
            font-weight: bold;
        }

        .secondsection .button2,
        .section .button2 {
            width: 10%;
            height: 30px;
            background-color: #008000;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
            font-size: 15px;
            font-weight: bold;
        }

        .firstsection button:hover,
        .secondsection button:hover {
            background-color: #004d40;
        }

        .firstsection label.required::after,
        .secondsection label.required::after {
            content: " *";
            color: red;
        }

        .section label.required::after {
            content: " *";
            color: red;
        }

        .firstsection ::placeholder,
        .secondsection ::placeholder {
            font-style: italic;
            color: grey;
        }

        .my-header {
            padding: 10px;
            border-radius: 5px;
            color: #41406D;
            font-weight: bold;
        }

        .my-icon {
            font-size: 24px;
            color: #007bff;
            vertical-align: middle;
            margin-right: 10px;
        }

        .my-text {
            font-size: 20px;
            color: #333;
        }

        .blockquote-footer {
            font-style: italic;
            color: #6c757d;
        }

        .row+.row+.row {
            background-color: #E7F3FE;
            color: #4485B1;
            font-weight: 500;
            justify-content: center;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .dataTables_wrapper .row:first-of-type {
            background-color: #F6F5F5;
            height: 50px;
            justify-content: center;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .small td {
            background-color: #5B90CD !important;
            color: #FFFCFC !important;
            font-weight: 500 !important;
            font-size: 16px;
        }

        .btn-primary:nth-of-type(1) {
            border: none;
            font-weight: bold;
        }

        .pagination {
            --bs-pagination-active-bg: #5B90CD !important;
            --bs-pagination-disabled-color: #4485B1;
            --bs-pagination-disabled-bg: white !important;
            display: flex;
            padding-left: 0;
            list-style: none;
        }

        .button-add {
            border-radius: 25px;
            background-color: #41406D;
            height: 40px;
            width: 180px;
            color: white;
            font-weight: bold;
            font-size: 14px;
            border: none;
        }

        .button-add:not(:disabled):hover {
            background-color: #1a233a;
        }
    </style>
</head>

<body>
    <nav class="navbar fixed-top navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> GKJ DAYU</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="0"><ion-icon name="home"></ion-icon>
                            &nbsp;
                            Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <ion-icon name="folder"></ion-icon>
                            &nbsp;
                            Data
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="11"><ion-icon name="folder"></ion-icon> &nbsp; Fiskal</a>
                            </li>
                            <li><a class="dropdown-item" href="12"><ion-icon name="folder"></ion-icon> &nbsp; Akun</a>
                            </li>
                            <li><a class="dropdown-item" href="13"><ion-icon name="folder"></ion-icon> &nbsp; Rekening
                                    Bank</a></li>
                            <li><a class="dropdown-item" href="14"><ion-icon name="folder"></ion-icon> &nbsp; User</a>
                            </li>
                            <li><a class="dropdown-item" href="15"><ion-icon name="folder"></ion-icon> &nbsp; Bidang</a>
                            </li>
                            <li><a class="dropdown-item" href="16"><ion-icon name="folder"></ion-icon> &nbsp; Komisi</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <ion-icon name="newspaper"></ion-icon>
                            &nbsp;
                            Transaksi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="21"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Program</a></l>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="221"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Rencana Penerimaan Gereja</a></li>
                            <li><a class="dropdown-item" href="22"><ion-icon name="newspaper"></ion-icon> &nbsp; Rencana
                                    Pengeluaran Gereja</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="28"><ion-icon name="newspaper"></ion-icon> &nbsp; Rencana
                                    Penerimaan Komisi</a></li>
                            <li><a class="dropdown-item" href="23"><ion-icon name="newspaper"></ion-icon> &nbsp; Rencana
                                    Pengeluaran Komisi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="24"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Pengajuan Dana</a></li>
                            <li><a class="dropdown-item" href="25"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Pencairan Dana</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="27"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Penerimaan Gereja</a></li>
                            <li><a class="dropdown-item" href="271"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Pengeluaran Gereja</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="29"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Penerimaan Komisi</a></li>
                            <li><a class="dropdown-item" href="26"><ion-icon name="newspaper"></ion-icon> &nbsp;
                                    Pengeluaran Komisi</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <ion-icon name="receipt"></ion-icon>
                            &nbsp;
                            Laporan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="42"><ion-icon name="receipt"></ion-icon> &nbsp; Laporan
                                    Penerimaan</a></li>
                            <li><a class="dropdown-item" href="43"><ion-icon name="receipt"></ion-icon> &nbsp; Laporan
                                    Pengeluaran</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="451"><ion-icon name="receipt"></ion-icon> &nbsp; Laporan
                                    Rekapitulasi Bulanan</a></li>
                            <li><a class="dropdown-item" href="441"><ion-icon name="receipt"></ion-icon> &nbsp;
                                    Rekapitulasi Penerimaan Gereja</a></li>
                            <li><a class="dropdown-item" href="442"><ion-icon name="receipt"></ion-icon> &nbsp;
                                    Rekapitulasi Pengeluaran Gereja</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="45"><ion-icon name="receipt"></ion-icon> &nbsp; Laporan
                                    Rencana Komisi</a></li>
                            <li><a class="dropdown-item" href="46"><ion-icon name="receipt"></ion-icon> &nbsp; Laporan
                                    Realisasi Komisi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="47"><ion-icon name="receipt"></ion-icon> &nbsp;
                                    Rekapiltulasi Komisi</a></li>
                            <li><a class="dropdown-item" href="48"><ion-icon name="receipt"></ion-icon> &nbsp; Rencana
                                    vs Realisasi Komisi</a></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown dropdown-menu-end" style="display: flex; align-items: center;">
                        <a class="nav-link active">
                            <ion-icon name="calendar"></ion-icon>
                        </a>
                        <form method="POST" action="" style="display: flex; align-items: center;">
                            <select name="tahun" id="selected_year" onchange="this.form.submit()"
                                style=" border: none; margin-top:14px; background: none; font-size: inherit; cursor: pointer; color: #0870B6; font-weight: bold;">
                                <?php
                                $sql = "SELECT DISTINCT tahun FROM fiskal ORDER BY tahun ASC";
                                $result = $GLOBALS["conn"]->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($tahun_aktif == $row['tahun']) ? 'selected' : '';
                                    echo "<option value='" . $row['tahun'] . "' $selected> Tahun " . $row['tahun'] . "</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </li>
                    <li class="nav-item dropdown dropdown-menu-end">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false" style="display: flex; align-items: center;">
                            <ion-icon name="person-circle-outline" size="large"></ion-icon>
                            &nbsp;
                            <?= $_SESSION["jabatan"] ?>
                            &nbsp;
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><?= $_SESSION["nama"]; ?></a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="51"><ion-icon name="lock-closed"></ion-icon>
                                    &nbsp;
                                    Ubah Password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="?logout=true"><ion-icon name="log-out"></ion-icon> &nbsp;
                                    LOGOUT</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div style="margin-top: 80px; padding: 20px;">
        <?php
        // $link = isset($segments[5]) && is_numeric($segments[5]) ? $segments[5] : 0;
        $segments = explode("/", trim($_SERVER["REQUEST_URI"], "/"));

        // cari posisi "admin" di URL
        $posAdmin = array_search("admin", $segments);

        // ambil angka setelah "admin" sebagai $link
        $link = ($posAdmin !== false && isset($segments[$posAdmin + 1]) && is_numeric($segments[$posAdmin + 1]))
            ? (int) $segments[$posAdmin + 1]
            : 0;

        switch ($link) {
            case 0:
                include("incHome.php");
                break;
            case 11:
                include("incFiskal.php");
                break;
            case 12:
                include("incAkun.php");
                break;
            case 121:
                include("incPosAkun.php");
                break;
            case 13:
                include("incBank.php");
                break;
            case 14:
                include("incUser.php");
                break;
            case 15:
                include("incBidang.php");
                break;
            case 16:
                include("incKomisi.php");
                break;
            case 21:
                include("incProgram.php");
                break;
            case 22:
                include("incRencanaPengeluaranGereja.php");
                break;
            case 23:
                include("incRencanaPengeluaranKomisi.php");
                break;
            case 24:
                include("incPengajuan.php");
                break;
            case 25:
                include("incPencairan.php");
                break;
            case 26:
                include("incPengeluaranKomisi.php");
                break;
            case 27:
                include("incPenerimaanGereja.php");
                break;
            case 28:
                include("incRencanaPenerimaanKomisi.php");
                break;
            case 29:
                include("incPenerimaanKomisi.php");
                break;
            case 221:
                include("incRencanaPenerimaanGereja.php");
                break;
            case 271:
                include("incPengeluaranGereja.php");
                break;
            case 31:
                if ($status_aktif_fiskal == 1) {
                    include("incFormProgram.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 32:
                if ($status_aktif_fiskal == 1) {
                    include("incFormRencanaPengeluaranGereja.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 33:
                if ($status_aktif_fiskal == 1) {
                    include("incFormRencanaPengeluaranKomisi.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 34:
                if ($status_aktif_fiskal == 1) {
                    include("incFormPengajuan.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 341:
                if ($status_aktif_fiskal == 1) {
                    include("incFormPengajuanInsidental.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 36:
                if ($status_aktif_fiskal == 1) {
                    include("incFormPengeluaranKomisi.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 37:
                if ($status_aktif_fiskal == 1) {
                    include("incFormPenerimaanGereja.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 38:
                if ($status_aktif_fiskal == 1) {
                    include("incFormRencanaPenerimaanKomisi.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 39:
                if ($status_aktif_fiskal == 1) {
                    include("incFormPenerimaanKomisi.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 222:
                if ($status_aktif_fiskal == 1) {
                    include("incFormRencanaPenerimaanGereja.php");
                } else {
                    include("incHome.php");
                }
                break;
            case 41:
                include("incKasUmum.php");
                break;
            case 42:
                include("incLaporanPenerimaan.php");
                break;
            case 43:
                include("incLaporanPengeluaran.php");
                break;
            case 44:
                include("incLaporanRekap.php");
                break;
            case 45:
                include("incLaporanRencanaKomisi.php");
                break;
            case 46:
                include("incLaporanRealisasiKomisi.php");
                break;
            case 47:
                include("incLaporanRekapKomisi.php");
                break;
            case 48:
                include("incLaporanKomisi.php");
                break;
            case 441:
                include("incLaporanPenerimaanAkun.php");
                break;
            case 442:
                include("incLaporanPengeluaranAkun.php");
                break;
            case 451:
                include("incLaporanRekapAkun.php");
                break;
            case 51:
                include("ubahPassword.php");
                break;
            default:
                include("incHome.php");
                break;
        }
        ?>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap 5 JS Proper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- ion icon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>


    <!-- tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        $("#selected_year").change(function () {
            var tahun_aktif = $("#selected_year").val();
            console.log("Tahun yang dikirim: " + tahun_aktif);

            if (tahun_aktif) {
                $.ajax({
                    url: "../_function_i/ambilData.php",
                    type: 'GET',
                    data: {
                        tahun_aktif: tahun_aktif
                    },
                    success: function (response) {
                        console.log("Tahun yang dipilih: " + response);
                    }
                });
            }
        });
    </script>

    <!-- Inisialisasi DataTable -->
    <script>
        $('#example').DataTable({
            "ordering": false,
            "autoWidth": false,
            "columnDefs": [{
                "orderable": false,
                "targets": [4, 5]
            }],
            "paging": true,
            "info": true,
            "searching": true
        });
    </script>
</body>

</html>