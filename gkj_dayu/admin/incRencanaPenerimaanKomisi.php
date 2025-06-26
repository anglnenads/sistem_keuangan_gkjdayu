<?php
// update
if (!empty($_POST["editbtn"])) {

    $id_program = ($_POST["id_program"] ?? '-') !== '-' && $_POST["id_program"] !== '' ? $_POST["id_program"] : 0;
    $id_komisi = ($_POST["id_komisi"] ?? '-') !== '-' && $_POST["id_komisi"] !== '' ? $_POST["id_komisi"] : 'NULL';

    $linkurl = "";
    $datafield = array("id_bidang", "id_komisi", "id_program", "jenis_penerimaan", "volume", "satuan",  "harga_satuan", "jumlah", "dana_gereja", "dana_swadaya", "id_user");
    $datavalue = array($_POST["id_bidang"], $id_komisi, $id_program,  "'" . $_POST["jenis_penerimaan"] . "'", $_POST["volume"], "'" . $_POST["satuan"] . "'", $_POST["harga_satuan"], $_POST["jumlah"], $_POST["dana_gereja"], $_POST["dana_swadaya"], $id_user);

    $datakey = ' id_rencana_penerimaan =' . $_POST["id_rencana_penerimaan"];

    $update = new cUpdate();
    $update->vUpdateData($datafield, "rencana_penerimaan_komisi", $datavalue, $datakey, $linkurl);
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
            _myHeader("newspaper", "Rencana Penerimaan Komisi", "Data Rencana Penerimaan Komisi");
            ?>
        </div>
    </div>

    <div class="row" style="width:12%">
        <?php if ($status_aktif_fiskal == 1): ?>
            <a href="38">
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
            <?php
            ?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.id_fiskal as fiskal, a.*, b.*,c.*, d.*, e.*, f.*, u.* ";
            $sql .= "FROM rencana_penerimaan_komisi a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun LEFT JOIN bidang c ON a.id_bidang = c.id_bidang LEFT JOIN komisi d ON a.id_komisi = d.id_komisi LEFT JOIN fiskal e ON a.id_fiskal = e.id_fiskal LEFT JOIN program f ON a.id_program = f.id_program LEFT JOIN user u ON a.id_user = u.id_user ";
            $sql .= "WHERE (e.tahun) = $tahun_aktif ORDER BY a.id_bidang, a.id_rencana_penerimaan";

            if (!empty($_POST['bidang']) || !empty($_POST['komisi'])) {
                $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.id_fiskal as fiskal, a.*, b.*,c.*, d.*, e.*, f.*, u.* ";
                $sql .= "FROM rencana_penerimaan_komisi a ";
                $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun LEFT JOIN bidang c ON a.id_bidang = c.id_bidang LEFT JOIN komisi d ON a.id_komisi = d.id_komisi LEFT JOIN fiskal e ON a.id_fiskal = e.id_fiskal LEFT JOIN program f ON a.id_program = f.id_program LEFT JOIN user u ON a.id_user = u.id_user ";

                if (!empty($_POST['komisi'])) {
                    $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " AND a.id_komisi = " . $_POST['komisi'];
                } else {
                    $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " AND a.id_bidang = " . $_POST['bidang'];
                }
            }

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width=''>No</td>
                            <td width='25%'>Jenis Penerimaan/Kegiatan</td>
                            <td width='4%'>Vol</td>
                            <td width='15%' class="text-center">Satuan</td>
                            <td width='' class="text-end">Jumlah</td>
                            <td width='' class="text-end">Dana Gereja</td>
                            <td width='' class="text-end">Dana Swadaya</td>
                            <td width='' class="text-end">Subtotal</td>
                            <td></td>
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

                            $groupedData[$bidang][$komisi][$program][] = $data;
                        }

                        $cnourut = 0;
                        foreach ($groupedData as $bidang => $bidangList) {
                            $firstBidangRow = true;
                        ?>
                            <?php
                            foreach ($bidangList as $komisi => $komisiList) {  ?>
                                <tr style="font-weight: bold;">
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);" width="22%"><?= $bidang . " - " . $komisi ?></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);" width='2%'></td>
                                    <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                    <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                    <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                </tr>
                                <?php
                                $number = 0;
                                $total_komisi = 0;
                                $total_danaGerejaKomisi = 0;
                                $total_danaSwadayaKomisi = 0;
                                $subTotal_komisi = 0;

                                foreach ($komisiList as $program => $programList) { ?>
                                    <tr style="font-weight: bold;">
                                        <td></td>
                                        <td style="background-color:#f2f3f4; color: #1a2e62" width="20%"><?= !empty($program) ? "Program : " . $program : '' ?></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                    </tr>
                                    <?php
                                    $total = 0;
                                    $total_danaGereja = 0;
                                    $total_danaSwadaya = 0;
                                    $subTotal = 0;

                                    foreach ($programList as $data) {
                                        $cnourut++;
                                        $number++;
                                    ?>
                                        <tr>
                                            <td class="text-right"><?= $number; ?></td>
                                            <td><?= $data["jenis_penerimaan"]; ?></td>
                                            <td><?= $data["volume"]; ?></td>
                                            <td class="text-start" style="">Rp. <?= number_format($data["harga_satuan"], 0, ',', '.'); ?>/<?= $data["satuan"] ?></td>
                                            <td width="9%" class="text-end"><?= number_format($data["jumlah"], 0, ',', '.'); ?></td>
                                            <td width="9%" class="text-end"><?= number_format($data["dana_gereja"], 0, ',', '.'); ?></td>
                                            <td width="9%" class="text-end"><?= number_format($data["dana_swadaya"], 0, ',', '.'); ?></td>
                                            <td width="9%" class="text-end"><?= number_format($data["dana_gereja"] + $data["dana_swadaya"], 0, ',', '.'); ?></td>
                                            <td></td>
                                            <td class="text-center">
                                                <?php
                                                $datadetail = array(
                                                    array("Bidang", ":", $data["nama_bidang"], 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                                                    array("Komisi", ":", $data["nama_komisi"], 5, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                    array("Program", ":", $data["nama_program"] ?: "-", 1, ""),
                                                    array("Jenis Penerimaan/Kegiatan", ":", $data["jenis_penerimaan"], 1),
                                                    array("Volume", ":", $data["volume"], 1),
                                                    array("Satuan", ":", "Rp. " . number_format($data["harga_satuan"], 0, ',', '.') . "/" . $data["satuan"], 1, ""),
                                                    array("Jumlah", ":", "Rp. " . number_format($data["jumlah"], 0, ',', '.'), 1),
                                                    array("Sumber Dana", "", "", "", 1),
                                                    array("Dana Gereja", ":", "Rp. " . number_format($data["dana_gereja"], 0, ',', '.'), 1),
                                                    array("Dana Swadaya", ":", "Rp. " . number_format($data["dana_swadaya"], 0, ',', '.'), 1),
                                                    array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                                );
                                                _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Rencana Penerimaan Komisi#Data Rencana $cnourut : " . $data["jenis_penerimaan"], "", $datadetail, "", "28", "");
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                                $dataupdate = array(
                                                    array("ID", "id_rencana_penerimaan", $data["id_rencana_penerimaan"], 2, ""),
                                                    array("Bidang", "id_bidang", $data["bidang"], 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                                                    array("Komisi", "id_komisi", $data["komisi"], 51, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                    array("Program", "id_program", $data["program"], 51, "SELECT program.id_program AS field1, 
                                                    CONCAT( IF(komisi.nama_komisi IS NOT NULL AND komisi.nama_komisi != '', komisi.nama_komisi, bidang.nama_bidang), ' - ', program.nama_program) AS field2
                                                    FROM program
                                                    LEFT JOIN komisi ON program.id_komisi = komisi.id_komisi
                                                    LEFT JOIN bidang ON program.id_bidang = bidang.id_bidang
                                                    WHERE program.id_fiskal = $id_fiskal
                                                    ORDER BY program.id_bidang, program.id_komisi"),
                                                    array("Jenis Penerimaan/Kegiatan", "jenis_penerimaan", $data["jenis_penerimaan"], 1),
                                                    array("Volume", "volume", $data["volume"], 112, 'oninput="hitungOtomatis(this)"'),
                                                    array("Satuan", "satuan", $data["satuan"], 1),
                                                    array("Harga Satuan", "harga_satuan", $data["harga_satuan"], 112, 'oninput="hitungOtomatis(this)"'),
                                                    array("Jumlah", "jumlah", $data["jumlah"], 112, 'onkeydown="return false"'),
                                                    array("Dana Gereja", "dana_gereja", $data["dana_gereja"], 111),
                                                    array("Dana Swadaya", "dana_swadaya", $data["dana_swadaya"], 111),
                                                );
                                                // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                                _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "md", "", "Edit Data Rencana Penerimaan Komisi#Data Rencana $cnourut : " . $data["jenis_penerimaan"], "", $dataupdate, "", "28", $disabled);
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                                $datadelete = array(
                                                    array("id_rencana_penerimaan", $data["id_rencana_penerimaan"], "rencana_penerimaan_komisi")
                                                );
                                                _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Rencana Penerimaan Komisi#Data Rencana $cnourut : " . $data["jenis_penerimaan"], "", $datadelete, "28", $disabled);
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                        $total = $total + $data["jumlah"];
                                        $total_danaGereja = $total_danaGereja + $data["dana_gereja"];
                                        $total_danaSwadaya = $total_danaSwadaya + $data["dana_swadaya"];
                                        $subTotal = $subTotal + ($data["dana_swadaya"] + $data["dana_gereja"]);
                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td style="font-weight:bolder">Total Per Program</td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end" style="font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
                                        <td class="text-end" style="font-weight:bolder"><?= number_format($total_danaGereja, 0, ',', '.') ?></td>
                                        <td class="text-end" style="font-weight:bolder"><?= number_format($total_danaSwadaya, 0, ',', '.') ?></td>
                                        <td class="text-end" style="font-weight:bolder"><?= number_format($subTotal, 0, ',', '.') ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php
                                    $total_komisi += $total;
                                    $total_danaGerejaKomisi += $total_danaGereja;
                                    $total_danaSwadayaKomisi += $total_danaSwadaya;
                                    $subTotal_komisi +=  $subTotal;
                                }
                                $countprogram = 0;
                                $firstBidangRow = false;
                                ?>
                                <tr>
                                    <td></td>
                                    <td style="color:#5B90CD; font-weight:bolder">Total <?= $komisi ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-end" style="color:#0047ab; font-weight:bolder"><?= number_format($total_komisi, 0, ',', '.') ?></td>
                                    <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total_danaGerejaKomisi, 0, ',', '.') ?></td>
                                    <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total_danaSwadayaKomisi, 0, ',', '.') ?></td>
                                    <td class="text-end" style="color:#0047ab; font-weight:bolder"><?= number_format($subTotal_komisi, 0, ',', '.') ?></td>
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