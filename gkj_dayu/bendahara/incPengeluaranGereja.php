<?php
// update
if (!empty($_POST["editbtn"])) {

    $targetDir = '../uploads/bukti_transfer/';

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $bukti_pengeluaranFileName = null;

    if (!empty($_FILES["bukti_pengeluaran"]["name"])) {
        $bukti_pengeluaranFileName = time() . "_" . basename($_FILES["bukti_pengeluaran"]["name"]);
        $targetFilePath = $targetDir . $bukti_pengeluaranFileName;

        if ($_FILES["bukti_pengeluaran"]["error"] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($_FILES["bukti_pengeluaran"]["tmp_name"], $targetFilePath)) {
                // echo "File berhasil diunggah ke: " . $targetFilePath;
            } else {
                $bukti_pengeluaranFileName = null;
            }
        } else {
            $bukti_pengeluaranFileName = null;
        }
    }

    $datafield = array("id_fiskal",  "id_akun", "tanggal_pengeluaran", "bukti_pengeluaran", "keterangan", "jenis_pengeluaran",  "jumlah");
    $datavalue = array($id_fiskal,  $_POST['id_akun'], $_POST['tanggal_pengeluaran'],  $bukti_pengeluaranFileName, $_POST['keterangan'], $_POST['jenis_pengeluaran'],  $_POST['jumlah']);

    $datakey = ' id_pengeluaran =' . $_POST["id_pengeluaran"];

    $update = new cUpdate();
    $update->fUpdateData($datafield, "realisasi_pengeluaran_gereja", $datavalue, $datakey, "");
}

// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    $delete->_dDeleteData($_POST["hiddendeletevalue0"], $_POST["hiddendeletevalue1"], $_POST["hiddendeletevalue2"]);
}

// valid
if (!empty($_POST["btnsetuju"])) {

    $datafield = array("status", "id_validator");
    $datavalue = array("'Tervalidasi'", $id_user);

    $update = new cUpdate();
    $update->_functionStatus($_POST["hiddenupdatevalue0"], $_POST["hiddenupdatevalue1"], $_POST["hiddenupdatevalue2"], $datafield, $datavalue);
} else if (!empty($_POST["btntolak"])) {

    $datafield = array("status", "id_validator");
    $datavalue = array("'Tidak Valid'", $id_user);

    $update = new cUpdate();
    $update->_functionStatus($_POST["hiddenupdatevalue0"], $_POST["hiddenupdatevalue1"], $_POST["hiddenupdatevalue2"], $datafield, $datavalue);
}

if (empty($_POST["tb_bulan"])) {
    $_POST["tb_bulan"] = 0;
} else {
    $bulan = $_POST["tb_bulan"];
}

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Pengeluaran Gereja", "Data pengeluaran");
            ?>
        </div>
    </div>
    <div class="row" style="width:12%">
        <?php if ($status_aktif_fiskal == 1): ?>
            <a href="281">
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
    <div class="second" style="height: 70px;">
        <div class="filter">
            <form action="" method="post">
                <label for="tb_bulan">Bulan</label>
                <select style="border-radius: 4px; border: 1px solid #676892;" name="tb_bulan">
                    <option value=""> -- Pilih -- </option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select> &nbsp;
                <button style="background-color: #49749C; color:white; border-radius:4px; border:none; width:5%" type="submit">Filter</button>
            </form>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, a.status AS status_pengeluaran,
             b.*,
             u1.*,
             u2.nama AS nama_validator, u2.jbtn AS jbtn_validator
             FROM realisasi_pengeluaran_gereja a 
             LEFT JOIN akun b ON a.id_akun = b.id_akun 
             LEFT JOIN user u1 ON a.id_user = u1.id_user 
             LEFT JOIN user u2 ON a.id_validator = u2.id_user ";

            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " ";

            if ($_POST["tb_bulan"] == 0) {
                $sql .= " ";
            } else {
                $sql .= " AND month(a.tanggal_pengeluaran) = " . intval($bulan) . "  ";
            }
            $sql .= " ORDER BY a.status, a.tanggal_pengeluaran ASC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='2%' class="text-right">No</td>
                            <td width='12%'>Tanggal Pengeluaran</td>
                            <td width=''>Jenis Pengeluaran</td>
                            <td width=''>Akun</td>
                            <td width='13%' class="text-end">Jumlah Pengeluaran</td>
                            <td width='10%' class="text-center">Status</td>
                            <td width='5%' class="text-center">DETAIL</td>
                            <td width='5%' class="text-center">EDIT</td>
                            <td width='5%' class="text-center">HAPUS</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $total_keseluruhan = 0;

                        $groupedData = [];

                        foreach ($array as $data) {
                            $status = $data["status"];
                            $groupedData[$status][] = $data;
                        }

                        foreach ($groupedData as $status => $statusList) {
                            $id_pengeluaran = [];
                            $total = 0;
                            foreach ($statusList as $data) {

                                $cnourut = $cnourut + 1;
                                $id_pengeluaran[] = $data["id_pengeluaran"];
                        ?>
                                <tr class=''>
                                    <td class="text-right"><?= $cnourut; ?></td>
                                    <td class=""><?= date('d-m-Y', strtotime($data["tanggal_pengeluaran"])); ?></td>
                                    <td><?= $data["jenis_pengeluaran"]; ?></td>
                                    <td><?= $data["nama_akun"]; ?></td>
                                    <td class="text-end"> <?= number_format($data["jumlah"], 0, ',', '.'); ?></td>
                                    <?php
                                    $color = "black"; 
                                    switch ($data["status"]) {
                                        case "Tervalidasi":
                                            $color = "#008000";
                                            break;
                                        case "Belum Tervalidasi":
                                            $color = "#808080";
                                            break;
                                        case "Tidak Valid":
                                            $color = "#a52a2a";
                                            break;
                                    }
                                    ?>
                                    <td class="text-center" style="font-weight:650; color: <?= $color; ?>;"><?= $data["status"]; ?></td>
                                    <td class="text-center">
                                        <?php
                                        $datadetail = array(
                                            array("Tanggal Pengeluaran", ":", date('d-m-Y', strtotime($data["tanggal_pengeluaran"])), 1),
                                            array("Jenis Pengeluaran", ":", $data["jenis_pengeluaran"], 1, ""),
                                            array("Akun", ":", $data["nama_akun"], 1, ""),
                                            array("Jumlah Pengeluaran", ":", "Rp. " . number_format($data["jumlah"], 0, ',', '.'), 1),
                                            array("Keterangan", ":", $data["keterangan"], 1),
                                            array("Bukti Pengeluaran", ":", "<a href='http://localhost:80/gkj_dayu/uploads/bukti_transfer/" . htmlspecialchars($data["bukti_pengeluaran"]) . "' target='_blank'>" . htmlspecialchars($data["bukti_pengeluaran"]) . "</a>", 1),
                                            array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                            array("Tanggal Pencatatan", ":", date('d-m-Y', strtotime($data["tanggal_catat"])), 1),
                                            array("Divalidasi oleh", ":", $data["nama_validator"] . " - " . $data["jbtn_validator"], 1),
                                        );
                                        _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Realisasi Pengeluaran Gereja#Data Pengeluaran  $cnourut", "", $datadetail, "", "271", "");
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                         $disabled = ($status_aktif_fiskal == 1 && $data['status'] != 'Tervalidasi') ? false : true;
                                        $dataupdate = array(
                                            array("ID", "id_pengeluaran", $data["id_pengeluaran"], 2, ""),
                                            array("Tanggal Pengeluaran", "tanggal_pengeluaran", $data["tanggal_pengeluaran"], 14),
                                            array("Akun", "id_akun", $data["id_akun"], 5, "select id_akun field1, nama_akun field2 from akun WHERE jenis_debitKredit = 'Debet' AND status_input = 1 ORDER BY kode_akun ASC"),
                                            array("Jenis Pengeluaran", "jenis_pengeluaran", $data["jenis_pengeluaran"], 1),
                                            array("Jumlah", "jumlah", $data["jumlah"], 111),
                                            array("Keterangan", "keterangan", $data["keterangan"], 17),
                                            array("Bukti Pengeluaran", "bukti_pengeluaran", $data["bukti_pengeluaran"], 13, ""),
                                        );
                                        // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                        _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "", "", 'Edit Realisasi Pengeluaran Gereja#Data Pengeluaran ' . $cnourut,  "", $dataupdate, "", "271", $disabled);
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                         $disabled = ($status_aktif_fiskal == 1 && $data['status'] != 'Tervalidasi') ? false : true;
                                        $datadelete = array(
                                            array("id_pengeluaran", $data["id_pengeluaran"], "realisasi_pengeluaran_gereja")
                                        );
                                        _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Realisasi Pengeluaran Gereja#Data Pengeluaran $cnourut", "", $datadelete, "271", $disabled);
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                $total += $data['jumlah'];
                            }
                            $total_keseluruhan += $total;
                            ?>
                            <tr>
                                <td width='2%' class="text-right"></td>
                                <td width=''></td>
                                <td style=" font-weight:bolder" width=''>Total</td>
                                <td width=''></td>
                                <td style=" font-weight:bolder" width='' class="text-end"><?= number_format($total, 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php
                                    $disabled = ($status_aktif_fiskal == 1 && $data['status'] != 'Tervalidasi') ? false : true;
                                    $caption = "Validasi Transaksi Pengeluaran Gereja";
                                    $all_ids_string = implode(',', $id_pengeluaran);
                                    $datavalid = array(
                                        array("id_pengeluaran", $all_ids_string, "realisasi_pengeluaran_gereja"),
                                    );
                                    _CreateWindowModalValid($cnourut, "val", "val-form", "val-button", "md", 200, "Validasi Transaksi Penerimaan# " . $data['nama_akun'] . " : Rp. " . number_format($data["jumlah"], 0, ',', '.'), $datavalid, "271", $disabled, "Validasi", $caption);
                                    ?>
                                </td>
                                <td width='5%' class="text-center"></td>
                                <td width='5%' class="text-center"></td>
                                <td width='5%' class="text-center"></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    <?php
                    $query = "SELECT SUM(jumlah) AS jumlah FROM realisasi_pengeluaran_gereja WHERE status = 'Tervalidasi' AND id_fiskal = $id_fiskal";
                    $view = new cView();
                    $array = $view->vViewData($query);
                    if (!empty($array)) {
                        $saldo_tervalidasi = $array[0]['jumlah']; 
                    }
                    ?>
                    <tr>
                        <td colspan="9"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:#5B90CD; font-weight:bolder">Total Pengeluaran Keseluruhan</td>
                        <td class="text-end" style="color:#483d8b; font-weight:bolder"><?= number_format($total_keseluruhan, 0, ',', '.') ?></td>
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:#2e8b57; font-weight:bolder">Total Pengeluaran Tervalidasi</td>
                        <td class="text-end" style="color:#2e8b57; font-weight:bolder"><?= number_format($saldo_tervalidasi, 0, ',', '.') ?></td>
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:#808080; font-weight:bolder">Total Pengeluaran Belum Tervalidasi</td>
                        <td class="text-end" style="color:#808080; font-weight:bolder"><?= number_format($total_keseluruhan - $saldo_tervalidasi, 0, ',', '.') ?></td>
                        <td colspan="5"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>