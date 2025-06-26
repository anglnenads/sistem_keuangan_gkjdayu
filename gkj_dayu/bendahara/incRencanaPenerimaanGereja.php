<?php
// update
if (!empty($_POST["editbtn"])) {

    $datafield = array("id_akun", "jumlah_penerimaan");
    $datavalue = array($_POST['id_akun'], $_POST['jumlah_penerimaan']);

    $datakey = ' id_rencana =' . $_POST["id_rencana"];

    $update = new cUpdate();
    $update->vUpdateData($datafield, "rencana_penerimaan_gereja", $datavalue, $datakey, "");
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
            _myHeader("newspaper", "Rencana Penerimaan Gereja", "Data Rencana Penerimaan Gereja");
            ?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, b.*, e.*, u.* ";
            $sql .= "FROM rencana_penerimaan_gereja a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun  LEFT JOIN fiskal e ON a.id_fiskal = e.id_fiskal LEFT JOIN user u ON a.id_user = u.id_user ";
            $sql .= "WHERE (e.tahun) = $tahun_aktif ORDER BY a.id_rencana";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%'>No</td>
                            <td width='30%'>Akun</td>
                            <td width='15%' class="text-end">Jumlah</td>
                            <td width='20%'></td>
                            <td width='4%' class="text-center">DETAIL</td>
                            <td width='4%' class="text-center">EDIT</td>
                            <td width='4%' class="text-center">HAPUS</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $cnourut = 0;
                        $total = 0;

                        foreach ($array as $data) {
                            $cnourut++;
                        ?>
                            <tr>
                                <td class="text-right"><?= $cnourut; ?></td>
                                <td><?= $data["nama_akun"]; ?></td>
                                <td class="text-end"><?= number_format($data["jumlah_penerimaan"], 0, ',', '.'); ?></td>
                                <td></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Akun", ":", $data["nama_akun"], 1, ""),
                                        array("Jumlah Rencana Penerimaan", ":", number_format($data["jumlah_penerimaan"], 0, ',', '.'), 1, ""),
                                        array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Rencana Penerimaan Gereja#Data Rencana $cnourut : " . $data["nama_akun"], "", $datadetail, "", "221", "");
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                    $dataupdate = array(
                                        array("ID", "id_rencana", $data["id_rencana"], 2, ""),
                                        array("Akun", "id_akun", $data["id_akun"], 5, "select id_akun field1, nama_akun field2 from akun where status_input = 1 AND jenis_debitKredit = 'Kredit' ORDER BY kode_akun ASC"),
                                        array("Jumlah Rencana Penerimaan", "jumlah_penerimaan", $data["jumlah_penerimaan"], 111),

                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Rencana Penerimaan Gereja#Data Rencana $cnourut : " . $data["nama_akun"], "", $dataupdate, "", "221", $disabled);
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                    $datadelete = array(
                                        array("id_rencana", $data["id_rencana"], "rencana_penerimaan_gereja")
                                    );
                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Rencana Penerimaan Gereja#Data Rencana $cnourut : " . $data["nama_akun"], "", $datadelete, "221", $disabled);
                                    ?>
                                </td>
                            </tr>
                        <?php
                            $total = $total + $data["jumlah_penerimaan"];
                        }
                        ?>
                    </tbody>
                    <tr>
                        <td></td>
                        <td style="color:#5B90CD; font-weight:bolder">T O T A L</td>
                        <td class="text-end" style="color:#483d8b; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
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