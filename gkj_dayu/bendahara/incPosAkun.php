<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
// insert
if (!empty($_POST["savebtn"])) {
    $datafield = array("jenis_akun", "status_aktif");
    $datavalue = array("'" . $_POST["jenis_akun"] . "'", $_POST["status_aktif"]);

    $insert = new cInsert();
    $insert->vInsertData($datafield, "akunjenis", $datavalue);
}
// update
if (!empty($_POST["editbtn"])) {

    $datafield = array("jenis_akun", "status_aktif");
    $datavalue = array("'" . $_POST["jenis_akun"] . "'", $_POST["status_aktif"]);

    $datakey = ' id_jenisAkun =' . $_POST["id_jenisAkun"] . '';

    $update = new cUpdate();
    $update->vUpdateData($datafield, "akunjenis", $datavalue, $datakey, "");
}

// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    $delete->_dDeleteData($_POST["hiddendeletevalue0"], $_POST["hiddendeletevalue1"], $_POST["hiddendeletevalue2"]);
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php _myHeader("folder", "Pos Akun", "Data Pos Akun"); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <!-- Kiri: Modal Insert -->
                <div class="col-md-6">
                    <?php
                    $afield = array(
                        array("Nama Pos", "jenis_akun", "", 1, ""),
                        array("Status Aktif", "status_aktif", "", 7),
                    );
                    $caption = array("Data Pos Akun", "Entri Data Pos Akun");
                    // [1] $number, [2] $type, [3] $name, [4] $button, [5] $width, [6] $height, [7] $title, [8] $acaption, [9] $afield, [10] $value, [11] $linkurl, [12] $footer
                    _CreateModalInsert(0, "insert", "insert-form", "insert-button", 800, 550, "Tambah Data", $caption, $afield, "", "");
                    ?>

                </div>
                <!-- Kanan: POS AKUN -->
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <div style="display: flex; align-items: center; gap: 0px; color: #002e63;">
                        <p style="margin: 0;"><b>Daftar Akun:</b></p>
                        <a href="12"
                            style="display: inline-flex; align-items: center; justify-content: center; padding: 6px 15px; background-color: transparent; border-radius: 4px;  text-decoration: underline; font-weight: 600;">
                            Data Akun (COA)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

</div>
<br>
<div class="row">
    <div class="col-md-12">
        <?php
        $sql = "SELECT a.* FROM akunjenis a ORDER BY id_jenisAkun ASC";

        $view = new cView();
        $array = $view->vViewData($sql);
        ?>

        <div id="" class='table-responsive'>
            <table id='example' class='table table-condensed w-100'>
                <thead>
                    <tr class='small'>
                        <td width='5%' class="text-right">No</td>
                        <td width=''>Nama Pos</td>
                        <td width=''></td>
                        <td width=''></td>
                        <td width='' class="text-center">Status Aktif</td>
                        <td width=''></td>
                        <td width='5%' class="text-center">DETAIL</td>
                        <td width='5%' class="text-center">EDIT</td>
                        <td width='5%' class="text-center">HAPUS</td>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $cnourut = 0;
                    foreach ($array as $data) {
                        $cnourut = $cnourut + 1;
                    ?>
                        <tr class=''>
                            <td class="text-right"><?= $cnourut; ?></td>
                            <td><?= $data["jenis_akun"]; ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-center"><?= $data["status_aktif"] == 1 ? "Aktif" : "Tidak Aktif"; ?></td>
                            <td></td>
                            <td class="text-center">
                                <?php
                                $datadetail = array(
                                    array("Nama POS", ":", $data["jenis_akun"], 1, ""),
                                    array("Status Aktif", ":", $data["status_aktif"] == 1 ? "Aktif" : "Tidak Aktif", 1, ""),
                                );
                                _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Pos Akun#Data Pos Akun $cnourut : " . $data["jenis_akun"], "", $datadetail, "", "121", "");
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $dataupdate = array(
                                    array("id", "id_jenisAkun", $data["id_jenisAkun"], 2, ""),
                                    array("Nama POS", "jenis_akun", $data["jenis_akun"], 1, ""),
                                    array("Status Aktif", "status_aktif", $data["status_aktif"], 7, ""),
                                );

                                // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Pos Akun#Data Pos Akun $cnourut : " . $data["jenis_akun"], "", $dataupdate, "", "121");
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $datadelete = array(
                                    array("id_jenisAkun", $data["id_jenisAkun"], "akunjenis")
                                );
                                _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Pos Akun#Data Pos Akun  $cnourut : " . $data["jenis_akun"], "", $datadelete, "");
                                ?>
                            </td>
                        </tr>
                    <?php   } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>