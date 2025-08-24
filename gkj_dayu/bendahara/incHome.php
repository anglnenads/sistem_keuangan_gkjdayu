<?php
include_once("../_function_i/inc_f_object.php");
?>


<style>
    .row-rekap {
        display: flex;
        height: 7%;
        margin-top: 1%;
        margin-left: auto;
        margin-right: auto;
        font-size: 22px;
        border-radius: 20px;
        font-weight: 600;
        width: 98%;
        background-color: #F3FBFF;
        padding: 10px;
        color: #0870B6;
    }

    .row-one {
        display: flex;
        height: 4%;
        margin-top: 2%;
        margin-left: auto;
        margin-right: auto;
        font-size: 20px;
        border-radius: 25px;
        font-weight: bold;
        width: 98%;
        color: #4C6271;
    }

    .total {
        background-color: #D6E5E7;
        width: fit-content;
        padding-left: 35px;
        padding-right: 35px;
        border-radius: 25px;
        margin-top: auto;
        margin-bottom: auto;
    }
</style>
<?php

$sql = "SELECT SUM(saldo_awal) as saldo_awal, SUM(jumlah_penerimaan) AS jumlah_penerimaan, SUM(jumlah_pengeluaran) AS jumlah_pengeluaran, SUM(saldo_akhir) AS saldo_akhir FROM `v_saldo_akun` WHERE tahun = '$tahun_aktif'";
$view = new cView();
$array = $view->vViewData($sql);

$saldo_awal = isset($array[0]['saldo_awal']) ? $array[0]['saldo_awal'] : 0;
$total_terima = isset($array[0]['jumlah_penerimaan']) ? $array[0]['jumlah_penerimaan'] : 0;
$total_keluar = isset($array[0]['jumlah_pengeluaran']) ? $array[0]['jumlah_pengeluaran'] : 0;
$saldo_akhir = isset($array[0]['saldo_akhir']) ? $array[0]['saldo_akhir'] : 0;


$view = new cView();
$array = $view->vViewData($sql);

?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("home", "Beranda", "Beranda");
            ?>
        </div>
    </div>
    

    <h3 style="color:#292c5f">Sistem Keuangan GKJ Dayu</h3>
    <h5 style="color:#8B8ABB">Selamat Datang, <?= $_SESSION["jabatan"] ?> !</h5>

    <div class="row mt-5" style="gap: 100px; height: 18%; padding-left: 50px;">
        <div class="col-md-4 " style="width:28%; background-color: #E5F7FF; border-radius: 15px;">
            <div class="row">
                <div class="col-md-9 d-flex flex-column justify-content-center ps-5 pt-4">
                    <h5 style="color:6F98C8; font-weight: 700;">Saldo</h5>
                    <h3 style="color:3F3E65; font-weight: 700;">Rp. <?= number_format($saldo_akhir, 0, ',', '.') ?></h3>

                </div>
                <div class="col-md-3 text-start pt-4 ps-4">
                    <ion-icon name="cash-outline" class="fs-1" style="color:566EA9"></ion-icon>
                </div>
            </div>
            <div class="row">
                <h5 style="color:8FA9C8; font-weight: 600; padding-left:50px;">Saldo Awal Tahun : <span style="color:557AA6;">Rp. <?= number_format($saldo_awal, 0, ',', '.') ?></span></h5>
            </div>
        </div>
        <div class="col-md-4 " style="width:28%; background-color: #E5F7FF; border-radius: 15px;">
            <div class="row">
                <div class="col-md-9 d-flex flex-column justify-content-center ps-5 pt-4">
                    <h5 style="color:6F98C8; font-weight: 700;">Kas Masuk</h5>
                    <h3 style="color:3F3E65; font-weight: 700;">Rp. <?= number_format($total_terima, 0, ',', '.') ?></h3>
                </div>
                <div class="col-md-3 text-start pt-4 ps-4">
                    <ion-icon name="cash-outline" class="fs-1" style="color:566EA9"></ion-icon>

                </div>
            </div>
            <div class="row ps-3">
                
            </div>
        </div>
        <div class="col-md-4 " style="width:28%; background-color: #E5F7FF; border-radius: 15px;">
            <div class="row">
                <div class="col-md-9 d-flex flex-column justify-content-center ps-5 pt-4">
                    <h5 style="color:6F98C8; font-weight: 700;">Kas Keluar</h5>
                    <h3 style="color:3F3E65; font-weight: 700;">Rp. <?= number_format($total_keluar, 0, ',', '.') ?></h3>
                </div>
                <div class="col-md-3 text-start pt-4 ps-4">
                    <ion-icon name="cash-outline" class="fs-1" style="color:566EA9"></ion-icon>
                </div>
            </div>
        </div>
        </div>
        &nbsp;
    </div>