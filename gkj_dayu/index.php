<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Keuangan GKJ Dayu</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #E0ECF2;
            overflow-x: hidden;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #095F8E;
            font-size: 20px;
            font-weight:700;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .input-group {
            margin-left: auto;
            margin-right: auto;        
        }
    </style>

</head>

<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

include_once("_function_i/cConnect.php");
include_once("_function_i/cView.php");

$conn = new cConnect();
$conn->goConnect();

// Inisialisasi variabel $alert
$alert = "";

if (empty($_POST["btnpost"])) {
    $gu = 0;
    $gp = 0;
} else {
    $gp = 1;
    if (empty($_POST["unme"])) {
        $gu = 0;
        $gp = 0;
    } else {
        if (empty($_POST["pswd"])) {
            $gu = 1;
            $gp = 0;
        } else {
            // cleansing input post
            $un = strip_tags($_POST["unme"]);
            $up = md5(strip_tags($_POST["pswd"]));

            // username & role
            $sql = "SELECT a.* FROM user a ";
            $sql .= "WHERE a.username = '" . $un . "' AND a.password = '" . $up . "' ";

            $view = new cView();
            $array = $view->vViewData($sql);

            $gu = 0;
            $gp = 1;
            foreach ($array as $value) {
                $gu = 1;
                $gp = 1;
                $alert = "";
                $_SESSION["id_user"] = $value["id_user"];
                $_SESSION["nama"] = $value["nama"];
                $_SESSION["role"] = $value["role"];
                $_SESSION["baseurl"] = $value["urlbase"];
                $_SESSION["jabatan"] = $value["jbtn"];
                $_SESSION["aktif"] = $value["status_aktif"];
            }

            // tahun aktif
            $sqltahun = "SELECT a.* FROM fiskal a WHERE a.status_aktif = 1";
            $view = new cView();
            $aarraytahun = $view->vViewData($sqltahun);

            // Cek apakah ada hasil dari query
            if (!empty($aarraytahun)) {
                foreach ($aarraytahun as $datatahun) {
                    $_SESSION["tahun_aktif"] = $datatahun["tahun"];
                    $_SESSION["tanggal_mulai"] = $datatahun["tanggal_mulai"];
                    // Cek apakah 'tanggal_akhir' ada dalam array
                    if (isset($datatahun["tanggal_akhir"])) {
                        $_SESSION["tanggal_akhir"] = $datatahun["tanggal_akhir"];
                    } else {
                        // Berikan nilai default jika tidak ada
                        $_SESSION["tanggal_akhir"] = "Tanggal akhir tidak tersedia";
                    }
                }
            } else {
                // Jika query tidak menghasilkan data
                echo "Data fiskal aktif tidak ditemukan.";
            }
        }
    }
}
if ($gu == 0 and $gp == 1) {
    $alert = 'Username & Password Salah !';
} elseif ($gu == 0 and $gp == 0) {
    $alert = "";
} elseif ($gu == 1 and $gp == 1) {
    if ($_SESSION["aktif"] == 0) {
        $alert = "Akun tidak aktif atau dinonaktifkan. Hubungi Admin";
    } else {
        header('Location: ' . $_SESSION["baseurl"]);
    }
    //header('Location: ' . $_SESSION["baseurl"]);
}
?>

<body>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center" style="height: 200px; background-color:white; border:none">
                    <h6 class="card-title">
                        Sistem Keuangan Gereja Kristen Jawa Dayu
                    </h6>
                    <img src="_img/logo.png" alt="Logo Gereja" style="max-width: 400px; width: 100%; height: auto; margin-top: 10px;">
                </div>

                <div class="card-body">
                    <form action="" method="post">
                        <p>                        </p>
                        <div class="input-group mb-3" style="width:80%; margin-top: 50px;">
                            <span class="input-group-text" id="basic-addon1" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px; background-color:white; color:#00416a"><ion-icon name="person-circle" size="md"></ion-icon></span>
                            <input type="text" class="form-control" placeholder="Username" aria-label="username" name="unme" aria-describedby="basic-addon1" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;" >
                        </div>
                        <p></p>
                        <div class="input-group mb-3" style="width:80%">
                            <span class="input-group-text" id="basic-addon1" style="border-top-left-radius: 25px; border-bottom-left-radius: 25px; background-color:white; color:#00416a"><ion-icon name="lock-closed"></ion-icon></span>
                            <input type="password" class="form-control" placeholder="Password" aria-label="password" name="pswd" aria-describedby="basic-addon1" style="border-top-right-radius: 25px; border-bottom-right-radius: 25px;" >
                        </div>
                        <p></p>

                        <input class="btn btn-primary btn-sm" name="btnpost" type="submit" value=" L O G I N " 
                            style="border-radius: 25px; width: 80%; text-align: center; background-color: #095F8E; color: white; font-weight: bold; display: block; margin: 0 auto; margin-top: 30px; height:37px; border: none;">


                        <p></p>
                        <p class="text-danger text-center small">
                            <?= $alert; ?>
                        </p>
                    </form>
                </div>

                <div class="card-footer text-center small" style="background-color:white; border:none">
                    GKJ Dayu &copy;2025
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ion icon -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- tooltips -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aktifkan semua elemen dengan tooltip di dalam dokumen
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <!-- Inisialisasi DataTable -->
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
</body>
</html>