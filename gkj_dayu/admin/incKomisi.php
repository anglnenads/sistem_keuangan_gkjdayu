<?php
// insert
if (!empty($_POST["savebtn"])) {


    $datafield_ang = array("id_bidang", "nama_komisi", "deskripsi_komisi", "nama_ketuaKomisi");

    $datavalue_ang = array($_POST["id_bidang"], "'" . $_POST["nama_komisi"] . "'", "'" . $_POST["deskripsi_komisi"] . "'", "'" . $_POST["nama_ketuaKomisi"] . "'",);

    $insert = new cInsert();
    $insert->vInsertData($datafield_ang, "komisi", $datavalue_ang);
}
?>

<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = "";

    $datafield_ang =
        $datafield_ang = array("id_bidang", "nama_komisi", "deskripsi_komisi", "nama_ketuaKomisi");

    $datavalue_ang = array($_POST["id_bidang"], "'" . $_POST["nama_komisi"] . "'", "'" . $_POST["deskripsi_komisi"] . "'", "'" . $_POST["nama_ketuaKomisi"] . "'");

    $datakey = ' id_komisi =' . $_POST["id_komisi"];

    $update = new cUpdate();
    $update->vUpdateData($datafield_ang, "komisi", $datavalue_ang, $datakey, $linkurl);
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
        _myHeader("Folder", "Komisi", "Data Komisi");
        ?>
    </div>
</div>

    <div class="row">
        <div class="col-md-12">
            <?php
            $afield = array(
                array("Nama Komisi", "nama_komisi", "", 1),
                array("Nama Bidang", "id_bidang", "", 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                array("Nama Ketua", "nama_ketuaKomisi", "", 19),
                array("Deskripsi", "deskripsi_komisi", "", 17),
            );

            $caption = array("Data Komisi", "Entri Data Komisi");
            // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
            _CreateModalInsert(0, "insert", "insert-form", "insert-button", "lg", "", "Tambah Data", $caption, $afield, "", "16");

            ?>
        </div>
    </div>
    <p></p>

    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, b.* FROM komisi a LEFT OUTER JOIN bidang b ON a.id_bidang = b.id_bidang ORDER BY b.id_bidang, a.id_komisi ASC";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Nama Bidang</td>
                            <td width=''>Nama Komisi</td>
                            <td width=''>Nama Ketua </td>
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
                                <td><?= $data["nama_komisi"]; ?></td>
                                <td><?= $data["nama_ketuaKomisi"]; ?></td>
                                <td><?= $data["deskripsi_komisi"]; ?></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Nama Bidang", ":", $data["nama_bidang"], 1, ""),
                                        array("Nama Komisi", ":", $data["nama_komisi"], 1, ""),
                                        array("Nama Ketua", ":", $data["nama_ketuaKomisi"], 19, ""),
                                        array("Deskripsi", ":", $data["deskripsi_komisi"], 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Komisi#Data Komisi $cnourut : " . $data["nama_komisi"], "", $datadetail, "", "16", "");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $dataupdate = array(
                                        array("ID Komisi", "id_komisi", $data["id_komisi"], 2, ""),
                                        array("Nama Komisi", "nama_komisi", $data["nama_komisi"], 1, ""),
                                        array("Nama Bidang", "id_bidang", $data["id_bidang"], 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                                        array("Nama Ketua", "nama_ketuaKomisi", $data["nama_ketuaKomisi"], 1, ""),
                                        array("Deskripsi", "deskripsi_komisi", $data["deskripsi_komisi"], 17, ""),
                                    );

                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Komisi#Data Komisi $cnourut : " . $data["nama_komisi"], "", $dataupdate, "", "16");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $datadelete = array(
                                        array("id_komisi", $data["id_komisi"], "komisi")
                                    );
                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Komisi#Data Komisi $cnourut : " . $data["nama_komisi"], "", $datadelete, "16");
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

