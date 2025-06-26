<?php
// insert
if (!empty($_POST["savebtn"])) {

    $datafield = array("nama_rekening", "no_rekening", "nama_bank", "jabatan", "keterangan");
    $datavalue = array("'" . $_POST["nama_rekening"] . "'", "'" . $_POST["no_rekening"] . "'", "'" . $_POST["nama_bank"] . "'", "'" . $_POST["jabatan"] . "'", "'" . $_POST["keterangan"] . "'");

    $insert = new cInsert();
    $insert->vInsertData($datafield, "bank", $datavalue);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {

    $linkurl = "";

    $datafield = array("nama_rekening", "no_rekening", "nama_bank", "jabatan", "keterangan");

    $datavalue = array("'" . $_POST["nama_rekening"] . "'", "'" . $_POST["no_rekening"] . "'", "'" . $_POST["nama_bank"] . "'", "'" . $_POST["jabatan"] . "'", "'" . $_POST["keterangan"] . "'");

    $datakey = ' id_bank =' . $_POST["id_bank"];

    $update = new cUpdate();
    $update->vUpdateData($datafield, "bank", $datavalue, $datakey, $linkurl);
}
?>

<?php
// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    $delete->_dDeleteData($_POST["hiddendeletevalue0"], $_POST["hiddendeletevalue1"], $_POST["hiddendeletevalue2"]);
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("Folder", "Rekening Bank", "Data Rekening Bank");
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $afield = array(
                array("Nama Rekening", "nama_rekening", "", 19),
                array("Nomor Rekening", "no_rekening", "", 111),
                array("Nama Bank", "nama_bank", "", 1),
                array("Jabatan", "jabatan", "", 1),
                array("Deskripsi", "keterangan", "", 17),
            );

            $caption = array("Data Rekening Bank", "Entri Data Rekening Bank");
            // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
            _CreateModalInsert(0, "insert", "insert-form", "insert-button", "lg", "", "Tambah Data", $caption, $afield, "", "13");

            ?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.* FROM bank a ";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Nama Rekening</td>
                            <td width=''>Nomor Rekening</td>
                            <td width=''>Nama Bank</td>
                            <td width=''>Jabatan</td>
                            <td width=''>Deskripsi</td>
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
                                <td><?= $data["nama_rekening"]; ?></td>
                                <td><?= $data["no_rekening"]; ?></td>
                                <td><?= $data["nama_bank"]; ?></td>
                                <td><?= $data["jabatan"]; ?></td>
                                <td><?= $data["keterangan"]; ?></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Nama Rekening", ":", $data["nama_rekening"], 1, ""),
                                        array("Nomor Rekening", ":", $data["no_rekening"], 1, ""),
                                        array("Nama Bank", ":", $data["nama_bank"], 1, ""),
                                        array("Jabatan", ":", $data["jabatan"], 1, ""),
                                        array("Deskripsi", ":", $data["keterangan"], 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Rekening Bank#Nama Rekening : ". $data["nama_rekening"], "", $datadetail, "", "13", "");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $dataupdate = array(
                                        array("ID", "id_bank", $data["id_bank"], 2, ""),
                                        array("Nama Rekening", "nama_rekening", $data["nama_rekening"], 19, ""),
                                        array("Nomor Rekening", "no_rekening", $data["no_rekening"], 111, ""),
                                        array("Nama Bank", "nama_bank", $data["nama_bank"], 1, ""),
                                        array("Jabatan", "jabatan", $data["jabatan"], 1, ""),
                                        array("Deskripsi", "keterangan", $data["keterangan"], 17, ""),
                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Rekening Bank#Nama Rekening : ". $data["nama_rekening"], "", $dataupdate, "", "13");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $datadelete = array(
                                        array("id_bank", $data["id_bank"], "bank")
                                    );
                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Rekening Bank#Nama Rekening : ". $data["nama_rekening"], "", $datadelete, "13");
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
