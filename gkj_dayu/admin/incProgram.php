<?php
include_once("../_function_i/inc_f_object.php");

// update
if (!empty($_POST["editbtn"])) {

    $id_komisi = isset($_POST['id_komisi']) && $_POST['id_komisi'] !== '' ? $_POST['id_komisi'] : 'NULL';
    $linkurl = "";
    $datafield = array("nama_program", "keterangan", "id_bidang", "id_komisi", "tgl_mulai", "tgl_selesai", "penanggung_jawab");
    $datavalue = array("'" . $_POST["nama_program"] . "'", "'" . $_POST["keterangan"] . "'", $_POST["id_bidang"],  $id_komisi, "'" . $_POST["tgl_mulai"] . "'", "'" . $_POST["tgl_selesai"] . "'", "'" . $_POST["penanggung_jawab"] . "'");

    $datakey = ' id_program =' . $_POST["id_program"] . '';

    $update = new cUpdate();
    $update->vUpdateData($datafield, "program", $datavalue, $datakey, $linkurl);
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
            <?php _myHeader("newspaper", "Program", "Data Program"); ?>
        </div>
    </div>

    <div class="row" style="width:12%">
        <?php if ($status_aktif_fiskal == 1): ?>
            <a href="31">
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
            $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.*, b.*, c.*, d.*, e.*
            FROM program a 
            LEFT JOIN bidang b ON a.id_bidang = b.id_bidang 
            LEFT JOIN komisi c ON a.id_komisi = c.id_komisi 
            LEFT JOIN fiskal d ON a.id_fiskal = d.id_fiskal 
            LEFT JOIN user e ON a.id_user = e.id_user ";
            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " ORDER BY b.id_bidang, c.id_komisi, a.id_program ASC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>

            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td class="text-right">No</td>
                            <td></td>
                            <td width=''>Nama Program</td>
                            <td width=''>Tanggal Mulai</td>
                            <td width=''>Tanggal Selesai</td>
                            <td width=''>Penanggung Jawab</td>
                            <td width='5%' class="text-center">DETAIL</td>
                            <td width='5%' class="text-center">EDIT</td>
                            <td width='5%' class="text-center">HAPUS</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $groupedData = [];

                        foreach ($array as $data) {
                            $bidang = $data["nama_bidang"];
                            $komisi = $data["nama_komisi"];

                            $groupedData[$bidang][$komisi][] = $data;
                        }

                        foreach ($groupedData as $bidang => $bidangList) {
                            $firstBidangRow = true;
                            foreach ($bidangList as $komisi => $komisiList) { ?>

                                <tr style="font-weight: bold;">
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;" width='10%' class="text-left"><?= $firstBidangRow ? $bidang : ""; ?></td>
                                    <td style="background-color: #f2f3f4;" width="28%"><?= $komisi ?></td>
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;" width='5%'></td>
                                    <td style="background-color: #f2f3f4;" width='5%'></td>
                                    <td style="background-color: #f2f3f4;" width='5%'></td>
                                </tr>

                                <?php
                                $countprogram = 0;
                                foreach ($komisiList as $data) {
                                    $cnourut = $cnourut + 1;
                                    $countprogram = $countprogram + 1;
                                ?>

                                    <tr class=''>
                                        <td class="text-right"><?= $countprogram; ?></td>
                                        <td></td>
                                        <td><?= $data["nama_program"]; ?></td>
                                        <td><?= date('d-m-Y', strtotime($data["tgl_mulai"])) ?></td>
                                        <td><?= date('d-m-Y', strtotime($data["tgl_selesai"])) ?></td>
                                        <td><?= $data["penanggung_jawab"]; ?></td>
                                        <td class="text-center">
                                            <?php
                                            $datadetail = array(
                                                array("Nama Program", ":", $data["nama_program"], "", 1),
                                                array("Tanggal Mulai", ":", date('d-m-Y', strtotime($data["tgl_mulai"])), "", 1),
                                                array("Tanggal Selesai", ":", date('d-m-Y', strtotime($data["tgl_selesai"])), "", 1),
                                                array("Bidang", ":", $data["nama_bidang"], "", 5, "select id_unit field1, nama_bidang field2 from unit"),
                                                array("Komisi", ":", !empty($data["nama_komisi"]) ? $data["nama_komisi"] : "-", "", 5, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                array("Keterangan", ":", !empty($data["keterangan"]) ? $data["keterangan"] : "-", "", 1),
                                                array("Penanggung Jawab", ":", $data["penanggung_jawab"], "", 1),
                                                array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], "", 1),
                                            );
                                            _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Program#Data Program $cnourut : " . $data["nama_program"], "", $datadetail, "", "21", "");
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                            $dataupdate = array(
                                                array("id", "id_program", $data["id_program"], 2, ""),
                                                array("Nama Program", "nama_program", $data["nama_program"], 1, ""),
                                                array("Tanggal Mulai", "tgl_mulai", $data["tgl_mulai"], 14, ""),
                                                array("Tanggal Selesai", "tgl_selesai", $data["tgl_selesai"], 14, ""),
                                                array("Bidang", "id_bidang", $data["bidang"], 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                                                array("Komisi", "id_komisi", $data["komisi"], 51, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                array("Keterangan", "keterangan", $data["keterangan"], 17, ""),
                                                array("Penanggung Jawab", "penanggung_jawab", $data["penanggung_jawab"], 1, "")
                                            );
                                            // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                            _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", "Edit Data Program#Data Program $cnourut : " . $data["nama_program"], "", $dataupdate, "", "21", $disabled);
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                            $datadelete = array(
                                                array("id_program", $data["id_program"], "program")
                                            );
                                            _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Program#Data Program $cnourut : " . $data["nama_program"], "", $datadelete, "", $disabled);
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                $countprogram = 0;
                                $firstBidangRow = false;
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>