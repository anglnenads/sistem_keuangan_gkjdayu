<?php
// update
if (!empty($_POST["editbtn"])) {

    $linkurl = "";
    $datafield = array("nama_anggaran", "id_akun", "jumlah");
    $datavalue = array("'" .  $_POST['nama_anggaran'] . "'", $_POST["id_akun"],  $_POST['jumlah']);

    $datakey = ' id_anggaran =' . $_POST["id_anggaran"];

    $update = new cUpdate();
    $update->vUpdateData($datafield, "rencana_pengeluaran_gereja", $datavalue, $datakey, $linkurl);
}

// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    $delete->_dDeleteData($_POST["hiddendeletevalue0"], $_POST["hiddendeletevalue1"], $_POST["hiddendeletevalue2"]);
}

$pos = array_search('admin', $segments);
$link = isset($segments[$pos + 1]) ? $segments[$pos + 1] : null;
switch ($link) {
    case 32:
        include("incFormRencanaPengeluaranGereja.php");
        break;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Rencana Pengeluaran Gereja", "Data Rencana Pengeluaran Gereja");
            ?>
        </div>
    </div>

    <div class="row" style="width:12%">
        <?php if ($status_aktif_fiskal == 1): ?>
            <a href="32">
                <button type="button" class="button-add">
                    <ion-icon name="add-circle"></ion-icon> &nbsp; &nbsp; Tambah Data</button>
            </a>
        <?php else: ?>
            <a style="pointer-events: none;">
                <button type="button" class="button-add" style="background-color: #c4c3d0 " disabled>
                    <ion-icon name="add-circle"></ion-icon> &nbsp;&nbsp; Tambah Data
                </button>
            </a>
        <?php endif; ?>
    </div>

    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_fiskal as fiskal, a.*, b.*, e.*, u.* ";
            $sql .= "FROM rencana_pengeluaran_gereja a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun  LEFT JOIN fiskal e ON a.id_fiskal = e.id_fiskal LEFT JOIN user u ON a.id_user = u.id_user ";
            $sql .= "WHERE (e.tahun) = $tahun_aktif  ORDER BY a.id_anggaran";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width=''>No</td>
                            <td width=''>Jenis Pengeluaran</td>
                            <td width=''>Akun</td>
                            <td class='text-end' width=''>Jumlah</td>
                            <td width='14%'></td>
                            <td class='text-center' width='5%'>DETAIL</td>
                            <td class='text-center' width='5%'>EDIT</td>
                            <td class='text-center' width='5%'>HAPUS</td>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $total = 0;
                        foreach ($array as $data) {
                            $cnourut++
                        ?>
                            <tr>
                                <td class="text-right"><?= $cnourut; ?></td>
                                <td><?= $data["nama_anggaran"]; ?></td>
                                <td><?= $data["nama_akun"]; ?></td>
                                <td class='text-end'><?= number_format($data["jumlah"], 0, ',', '.'); ?></td>
                                <td></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Jenis Pengeluaran", ":", $data["nama_anggaran"], 1, ""),
                                        array("Akun", ":", $data["nama_akun"], 1, ""),
                                        array("Jumlah", ":", number_format($data["jumlah"], 0, ',', '.'), 1),
                                        array("Diinput oleh ", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Rencana Pengeluaran Gereja#Data Rencana $cnourut : " . $data["nama_anggaran"], "", $datadetail, "", "22", "");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                    $dataupdate = array(
                                        array("ID", "id_anggaran", $data["id_anggaran"], 2, ""),
                                        array("Jenis Pengeluaran", "nama_anggaran", $data["nama_anggaran"], 1, ""),
                                        array("Akun", "id_akun", $data["id_akun"], 5, "select id_akun field1, nama_akun field2 from akun where status_input = 1 AND jenis_debitKredit = 'Debet' ORDER BY kode_akun ASC"),
                                        array("Jumlah", "jumlah", $data["jumlah"], 1),
                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "lg", "", "Edit Data Rencana Pengeluaran Gereja#Data Rencana $cnourut : " . $data["nama_anggaran"], "", $dataupdate, "", "22", $disabled);
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                    $datadelete = array(
                                        array("id_anggaran", $data["id_anggaran"], "rencana_pengeluaran_gereja")
                                    );
                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Rencana Pengeluaran Gereja#Data Rencana $cnourut : " . $data["nama_anggaran"], "", $datadelete, "22", $disabled);
                                    ?>
                                </td>
                            </tr>
                        <?php
                            $total =  $total + ($data['jumlah']);
                        }
                        ?>
                    </tbody>
                    <tr>
                        <td></td>
                        <td style="color:#5B90CD; font-weight:bolder">Total</td>
                        <td></td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>