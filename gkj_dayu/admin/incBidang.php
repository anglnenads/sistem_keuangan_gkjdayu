<?php
// insert
if (!empty($_POST["savebtn"])) {

    $datafield = array("nama_bidang", "deskripsi_bidang", "nama_ketuaBidang");
    $datavalue = array("'" . $_POST["nama_bidang"] . "'", "'" . $_POST["deskripsi_bidang"] . "'", "'" . $_POST["nama_ketuaBidang"] . "'");

    $insert = new cInsert();
    $insert->vInsertData($datafield, "bidang", $datavalue);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {

    $linkurl = "";

    $datafield = array("nama_bidang", "deskripsi_bidang", "nama_ketuaBidang");
    $datavalue = array("'" . $_POST["nama_bidang"] . "'", "'" . $_POST["deskripsi_bidang"] . "'", "'" . $_POST["nama_ketuaBidang"] . "'");

    $datakey = ' id_bidang =' . $_POST["id_bidang"];

    $update = new cUpdate();
    $update->vUpdateData($datafield, "bidang", $datavalue, $datakey, $linkurl);
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
            _myHeader("Folder", "Bidang", "Data Bidang");
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $afield = array(
                array("Nama Bidang", "nama_bidang", "", 1),
                array("Koordinator", "nama_ketuaBidang", "", 19),
                array("Deskripsi", "deskripsi_bidang", "", 17),
            );

            $caption = array("COA", "Entri Data Bidang");
            // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
            _CreateModalInsert(0, "insert", "insert-form", "insert-button", "lg", "", "Tambah Data", $caption, $afield, "", "15");

            ?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.* FROM bidang a ";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Nama Bidang</td>
                            <td width=''>Koordinator</td>
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
                                <td><?= $data["nama_bidang"]; ?></td>
                                <td><?= $data["nama_ketuaBidang"]; ?></td>
                                <td><?= $data["deskripsi_bidang"]; ?></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Nama Bidang", ":", $data["nama_bidang"], 1, ""),
                                        array("Koordinator", ":", $data["nama_ketuaBidang"], 1, ""),
                                        array("Deskripsi", ":", $data["deskripsi_bidang"], 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Bidang #Data Bidang $cnourut : ". $data["nama_bidang"], "", $datadetail, "", "15", "");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $dataupdate = array(
                                        array("ID", "id_bidang", $data["id_bidang"], 2, ""),
                                        array("Nama Bidang", "nama_bidang", $data["nama_bidang"], 1, ""),
                                        array("Koordinator", "nama_ketuaBidang", $data["nama_ketuaBidang"], 19, ""),
                                        array("Deskripsi", "deskripsi_bidang", $data["deskripsi_bidang"], 17, ""),
                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Bidang #Data Bidang $cnourut : ". $data["nama_bidang"], "", $dataupdate, "", "15");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $datadelete = array(
                                        array("id_bidang", $data["id_bidang"], "bidang")
                                    );
                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Bidang #Data Bidang $cnourut : ". $data["nama_bidang"], "", $datadelete, "15");
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