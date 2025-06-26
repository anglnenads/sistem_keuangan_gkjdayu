<?php
// insert
if (!empty($_POST["savebtn"])) {

    $datafield = array("tahun", "tanggal_mulai", "tanggal_selesai", "status_aktif");
    $datavalue = array($_POST["tahunn"], "'" . $_POST["tanggal_mulai"] . "'", "'" . $_POST["tanggal_selesai"] . "'", $_POST["status_aktif"]);

    $insert = new cInsert();
    $insert->vInsertData($datafield, "fiskal", $datavalue);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
      if ($_POST["status_aktif"] == 1) {
        $query = "UPDATE fiskal SET status_aktif = 0";
        mysqli_query($GLOBALS["conn"], $query);
    }

    $datafield = array("id_fiskal", "tahun", "tanggal_mulai", "tanggal_selesai", "status_aktif");
    $datavalue = array($_POST["id_fiskal"], $_POST["tahunn"], "'" . $_POST["tanggal_mulai"] . "'", "'" . $_POST["tanggal_selesai"] . "'", $_POST["status_aktif"]);

    $datakey = ' id_fiskal =' . $_POST["id_fiskal"] . '';

    $update = new cUpdate();
    $update->vUpdateData($datafield, "fiskal", $datavalue, $datakey, "");
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
            <?php
            _myHeader("Folder", "Fiskal", "Data Fiskal");
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $afield = array(
                array("Tahun", "tahunn", "", 111, ""),
                array("Tanggal Mulai", "tanggal_mulai", "", 14),
                array("Tanggal Selesai", "tanggal_selesai", "", 14),
                array("", "status_aktif", 0, 2),
            );

            $caption = array("Data Fiskal", "Entri Data Fiskal");
            // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
            _CreateModalInsert(0, "insert", "insert-form", "insert-button", "lg", "", "Tambah Data", $caption, $afield, "", "11");
            ?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT * FROM fiskal ORDER BY tahun ASC";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Tahun</td>
                            <td width=''>Tanggal Mulai</td>
                            <td width=''>Tanggal Selesai</td>
                            <td width=''>Status Aktif</td>
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
                                <td><?= $data["tahun"]; ?></td>
                                <td><?= date('d-m-Y', strtotime($data["tanggal_mulai"])) ?></td>
                                <td><?= date('d-m-Y', strtotime($data["tanggal_selesai"])) ?></td>
                                <td style=" font-weight: 650; color: <?= $data['status_aktif'] == 1 ? '#009e60' : '#808080'; ?>;">
                                    <?= $data["status_aktif"] == 1 ? "Aktif" : "Tidak Aktif"; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Tahun", ":", $data["tahun"], 1, ""),
                                        array("Tanggal Mulai", ":", date('d-m-Y', strtotime($data["tanggal_mulai"])), 1, ""),
                                        array("Tanggal Selesai", ":", date('d-m-Y', strtotime($data["tanggal_selesai"])), 1, ""),
                                        array("Status Aktif", ":", $data["status_aktif"] == 1 ? "Aktif" : "Tidak Aktif", 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Fiskal#Data Fiskal $cnourut" . " : Tahun " . $data["tahun"], "", $datadetail, "", "11", "");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $dataupdate = array(
                                        array("ID", "id_fiskal", $data["id_fiskal"], 2, ""),
                                        array("Tahun", "tahunn", $data["tahun"], 111, ""),
                                        array("Tanggal Mulai", "tanggal_mulai", $data["tanggal_mulai"], 14),
                                        array("Tanggal Selesai", "tanggal_selesai", $data["tanggal_selesai"], 14),
                                        array("Status Aktif", "status_aktif", $data["status_aktif"], 7),
                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Fiskal#Data Fiskal $cnourut" . " : Tahun " . $data["tahun"], "", $dataupdate, "", "11");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $datadelete = array(
                                        array("id_fiskal", $data["id_fiskal"], "fiskal")
                                    );
                                    //$number, $type, $name, $button, $width, $height, $title, $acaption, $value, $linkurl
                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200,  "Hapus Data Fiskal#Data Fiskal $cnourut" . " : Tahun " . $data["tahun"],  "", $datadelete,  "11");
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