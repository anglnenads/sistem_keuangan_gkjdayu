<?php
// update
if (!empty($_POST["editbtn"])) {

    $datafield = array( "id_bidang", "id_komisi", "id_program", "jumlah_penerimaan", "tanggal_penerimaan", "jenis_penerimaan",  "volume", "satuan", "harga_satuan", "dana_gereja", "dana_swadaya");
    $datavalue = array(  $_POST["id_bidang"], $_POST["id_komisi"], $_POST["id_program"],  $_POST["jumlah"],  $_POST["tanggal_penerimaan"], $_POST["jenis_penerimaan"], $_POST["volume"], $_POST["satuan"], $_POST["harga_satuan"], $_POST["dana_gereja"], $_POST["dana_swadaya"]);

    $datakey = ' id_penerimaan =' . $_POST["id_penerimaan"];

    $update = new cUpdate();
    $update->fUpdateData($datafield, "realisasi_penerimaan_komisi", $datavalue, $datakey, "");
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
            _myHeader("newspaper", "Penerimaan Komisi", "Data Penerimaan");
            ?>
        </div>
    </div>

    <div class="row" style="width:12%">
        <?php if ($status_aktif_fiskal == 1): ?>
            <a href="39">
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
    <div class="second">
        <div class="section" style="height: 20px;">
            <form action="" method="post">
                <div class="horizontal" style="margin-top: -15px; margin-left: -90px;">
                    <div class="form-group1" style="width:50%;">
                        <label for="bidang">Bidang</label>
                        <select style="width:80%; margin-left: 30px;" id="bidang" name="bidang">
                            <option value="">-- Pilih Bidang --</option>
                            <?php
                            $sql = "SELECT id_bidang, nama_bidang FROM bidang";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = '';
                                    if (isset($_POST['bidang']) && $_POST['bidang'] == $row['id_bidang']) {
                                        $selected = 'selected';
                                    }
                                    echo "<option value='" . $row['id_bidang'] . "' $selected>" . $row['nama_bidang'] . "</option>";
                                }
                            } else {
                                echo "<option value=''>Data tidak tersedia</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group1" style="width:50%">
                        <label for="komisi">Komisi</label>
                        <select style="width:80%; margin-left: 30px;" name="komisi" id="komisi">
                            <option value="">-- Pilih Komisi --</option>
                            <?php
                            $query = "SELECT id_komisi, nama_komisi FROM komisi";
                            $result = mysqli_query($conn, $query);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = '';
                                    if (isset($_POST['komisi']) && $_POST['komisi'] == $row['id_komisi']) {
                                        $selected = 'selected';
                                    }
                                    echo "<option value='" . $row['id_komisi'] . "' $selected>" . $row['nama_komisi'] . "</option>";
                                }
                            } else {
                                echo "<option value=''>Data tidak tersedia</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button name="filterKomisi" style="background-color: #49749C; color:white; border-radius:4px; border:none; width:8%; height: 30px;" type="submit">Pilih</button>
                </div>
            </form>
        </div>
    </div>
    <p></p>


    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.*, b.*,c.*, d.*, f.*, f1.*, f2.nama AS nama_validator, f2.jbtn AS jbtn_validator ";
            $sql .= "FROM realisasi_penerimaan_komisi a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun LEFT JOIN bidang c ON a.id_bidang = c.id_bidang LEFT JOIN komisi d ON a.id_komisi = d.id_komisi  LEFT JOIN program f ON a.id_program = f.id_program LEFT JOIN user f1 ON a.id_user = f1.id_user  LEFT JOIN user f2 ON a.id_validator = f2.id_user ";
            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " ";

            if (!empty($_POST['bidang']) || !empty($_POST['komisi'])) {
                $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.*, b.*,c.*, d.*, f.*, f1.*, f2.nama AS nama_validator, f2.jbtn AS jbtn_validator ";
                $sql .= "FROM realisasi_penerimaan_komisi a ";
                $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun LEFT JOIN bidang c ON a.id_bidang = c.id_bidang LEFT JOIN komisi d ON a.id_komisi = d.id_komisi LEFT JOIN program f ON a.id_program = f.id_program LEFT JOIN user f1 ON a.id_user = f1.id_user  LEFT JOIN user f2 ON a.id_validator = f2.id_user ";

                if (!empty($_POST['komisi'])) {
                    $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " AND a.id_komisi = " . $_POST['komisi'];
                } else {
                    $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " AND a.id_bidang = " . $_POST['bidang'];
                }
            }
            $sql .= " ORDER BY c.id_bidang, d.id_komisi, a.tanggal_penerimaan ASC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='' class='table table-condensed-border w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='3%' class="text-right">No</td>
                            <td width='10%'>Tanggal Penerimaan</td>
                            <td width=''>Jenis Penerimaan/Kegiatan</td>
                            <td width='3%'>Vol</td>
                            <td width='12%' class="text-center">Satuan</td>
                            <td width='7%' class="text-end">Jumlah</td>
                            <td width='7%' class="text-end">Dana Gereja</td>
                            <td width='7%' class="text-end">Dana Swadaya</td>
                            <td width='7%' class="text-end">SubTotal</td>
                            <td width='10%' class="text-center">Status</td>
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
                            $program = $data["nama_program"];
                            $status = $data["status"];
                            $groupedData[$bidang][$komisi][$program][$status][] = $data;
                        }
                        foreach ($groupedData as $bidang => $bidangList) {
                            foreach ($bidangList as $komisi => $komisiList) {  ?>
                                <tr style="font-weight: bold;">
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td colspan="4" style="background-color: rgb(223, 240, 248);" width='5%'><?= $bidang . " - " . $komisi ?></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                </tr>
                                <?php
                                $count = 0;
                                $lastStatus = null;
                                $total = 0;
                                $total_danaGereja = 0;
                                $total_danaSwadaya = 0;
                                $subTotal = 0;

                                $number = 0;

                                $firstBidangRow = true;
                                $totalAll_jumlah = 0;
                                $totalAll_danaGereja = 0;
                                $totalAll_danaSwadaya = 0;
                                $totalAll_sumberDana = 0;

                                $groupedStatus = [];
                                foreach ($komisiList as $program => $programList) { ?>
                                    <tr style="font-weight: bold;">
                                        <td></td>
                                        <td colspan="5" style="background-color:#f2f3f4; color: #1a2e62" width="">Program : <?= ($program == NULL ? 'Insidental' : $program) ?>
                                        </td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                    </tr>
                                    <?php
                                    foreach ($programList as $status => $items) {
                                        $total = 0;
                                        $total_danaGereja = 0;
                                        $total_danaSwadaya = 0;
                                        $subTotal = 0;
                                        foreach ($items as $data) {
                                            $total += $data["jumlah_penerimaan"];
                                            $total_danaGereja += $data["dana_gereja"];
                                            $total_danaSwadaya += $data["dana_swadaya"];
                                            $subTotal += $data["jumlah_penerimaan"];
                                            $cnourut++;
                                            $number++;
                                    ?>
                                            <tr>
                                                <td class="text-right"><?= $number; ?></td>
                                                <td class="text-center"><?= date('d-m-Y', strtotime($data["tanggal_penerimaan"])); ?></td>
                                                <td><?= $data["jenis_penerimaan"]; ?></td>
                                                <td><?= $data["volume"]; ?></td>
                                                <td class="text-start" style="">Rp. <?= number_format($data["harga_satuan"], 0, ',', '.'); ?>/<?= $data["satuan"] ?></td>
                                                <td width="8%" class="text-end"><?= number_format($data["jumlah_penerimaan"], 0, ',', '.'); ?></td>
                                                <td width="8%" class="text-end"><?= number_format($data["dana_gereja"], 0, ',', '.'); ?></td>
                                                <td width="8%" class="text-end"><?= number_format($data["dana_swadaya"], 0, ',', '.'); ?></td>
                                                <td width="8%" class="text-end"><?= number_format($data["dana_gereja"] + $data["dana_swadaya"], 0, ',', '.'); ?></td>
                                                <td></td>
                                                <td class="text-center" width='5%'>
                                                    <?php
                                                    $datadetail = array(
                                                        array("Bidang", ":", $data["nama_bidang"], 1, ""),
                                                        array("Komisi", ":", $data["nama_komisi"], 1, ""),
                                                        array("Tanggal Penerimaan", ":", date('d-m-Y', strtotime($data["tanggal_penerimaan"])), 1, ""),
                                                        array("Program", ":", $data["nama_program"], 1, ""),
                                                        array("Jenis Penerimaan/Kegiatan", ":", $data["jenis_penerimaan"], 1, ""),
                                                        array("Volume", ":", $data["volume"], 1, ""),
                                                        array("Satuan", ":", "Rp. " . number_format($data["harga_satuan"], 0, ',', '.') . "/" . $data["satuan"], 1, ""),
                                                        array("Jumlah", ":", "Rp. " . number_format($data["jumlah_penerimaan"], 0, ',', '.'), 1, ""),
                                                        array("Sumber Dana - Dana Gereja", ":", "Rp. " . number_format($data["dana_gereja"], 0, ',', '.'), 1, ""),
                                                        array("Sumber Dana - Dana Swadaya", ":", "Rp. " . number_format($data["dana_swadaya"], 0, ',', '.'), 1, ""),
                                                        array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 2, ""),
                                                        array("Tanggal Pencatatan", ":", date('d-m-Y', strtotime($data["tanggal_pencatatan"])), 1, ""),
                                                        array("Divalidasi oleh", ":", $data["nama_validator"] . " - " . $data["jbtn_validator"], 2, ""),
                                                    );
                                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Realisasi Penerimaan Komisi #Data Penerimaan $cnourut : " . $data["jenis_penerimaan"], "", $datadetail, "", "29", "");
                                                    ?>
                                                </td>
                                                <td class="text-center" width='5%'>
                                                    <?php
                                                    $disabled = ($status_aktif_fiskal == 1 && $data['status'] != 'Tervalidasi') ? false : true;
                                                    $dataupdate = array(
                                                        array("ID Penerimaaan", "id_penerimaan", $data["id_penerimaan"], 2, ""),
                                                        array("Bidang", "id_bidang", $data["bidang"], 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                                                        array("Komisi", "id_komisi", $data["komisi"], 51, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                        array("Program", "id_program", $data["id_program"], 51, "SELECT program.id_program field1, 
                                                        CONCAT(komisi.nama_komisi, ' - ', program.nama_program) field2 
                                                            FROM program 
                                                            JOIN komisi ON program.id_komisi = komisi.id_komisi 
                                                            WHERE program.id_fiskal = $id_fiskal"),
                                                        array("Tanggal Penerimaan", "tanggal_penerimaan", $data["tanggal_penerimaan"], 14, ""),
                                                        array("Jenis Penerimaan/Kegiatan", "jenis_penerimaan", $data["jenis_penerimaan"], 1, ""),
                                                        array("Volume", "volume", $data["volume"], 112, 'oninput="hitungOtomatis(this)"'),
                                                        array("Satuan", "satuan", $data["satuan"], 1),
                                                        array("Harga Satuan", "harga_satuan", $data["harga_satuan"], 112, 'oninput="hitungOtomatis(this)"'),
                                                        array("Jumlah Penerimaan", "jumlah", $data["jumlah_penerimaan"], 112, 'onkeydown="return false"'),
                                                        array("Dana Gereja", "dana_gereja", $data["dana_gereja"], 1, ""),
                                                        array("Dana Swadaya", "dana_swadaya", $data["dana_swadaya"], 1, ""),
                                                    );
                                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "lg", "", 'Edit Realisasi Penerimaan Komisi #Data Penerimaan ' . $cnourut, "", $dataupdate, "", "29", $disabled);
                                                    ?>
                                                </td>
                                                <td class="text-center" width='5%'>
                                                    <?php
                                                    $disabled = ($status_aktif_fiskal == 1 && $data['status'] != 'Tervalidasi') ? false : true;
                                                    $datadelete = array(
                                                        array("id_penerimaan", $data["id_penerimaan"], "realisasi_penerimaan_komisi")
                                                    );
                                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 500, "Hapus Realisasi Penerimaan Komisi #Data Penerimaan " . $cnourut, "", $datadelete, "29", $disabled);
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        $totalAll_jumlah += $total;
                                        $totalAll_danaGereja += $total_danaGereja;
                                        $totalAll_danaSwadaya += $total_danaSwadaya;
                                        $totalAll_sumberDana += ($total_danaGereja + $total_danaSwadaya);
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style="color:#5B90CD; font-weight:bolder">Total</td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format((float) ($total ?? 0), 0, ',', '.') ?></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format((float) ($total_danaGereja ?? 0), 0, ',', '.') ?></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format((float) ($total_danaSwadaya ?? 0), 0, ',', '.') ?></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format((float) ($subTotal ?? 0), 0, ',', '.') ?></td>
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
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
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
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    <?php
                                        $countprogram = 0;
                                        $firstBidangRow = false;
                                    }
                                    ?>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <td colspan="3" style="color:#2b3e66; font-weight:bolder">Total Penerimaan <?= $komisi ?></td>
                                    <td></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format((float) $totalAll_jumlah, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format((float) $totalAll_danaGereja, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format((float) $totalAll_danaSwadaya, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format((float) $totalAll_sumberDana, 0, ',', '.') ?></td>
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
<script>
    $("#bidang").change(function() {
        var id_bidang = $("#bidang").val();
        console.log(id_bidang);

        $.ajax({
            type: "POST",
            dataType: "html",
            url: "../_function_i/ambilData.php",
            data: "bidang=" + id_bidang,
            success: function(data) {
                $("#komisi").html(data);
            },
        });
    });
</script>