<?php

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
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Penerimaan Komisi", "Data Penerimaan");
            ?>
        </div>
    </div>

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
                            <td width='11%' class="text-center">Satuan</td>
                            <td width='7%' class="text-end">Jumlah</td>
                            <td width='7%' class="text-end">Dana Gereja</td>
                            <td width='7%' class="text-end">Dana Swadaya</td>
                            <td width='7%' class="text-end">SubTotal</td>
                            <td width='10%' class="text-center">Status</td>
                            <td width='5%' class="text-center">DETAIL</td>
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
                            $firstBidangRow = true;
                        ?>
                            <?php
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
                                    </tr>
                                    <?php
                                    foreach ($programList as $status => $items) {
                                        $total = 0;
                                        $total_danaGereja = 0;
                                        $total_danaSwadaya = 0;
                                        $subTotal = 0;
                                        $id_penerimaan = [];

                                        foreach ($items as $data) {
                                            $id_penerimaan[] = $data["id_penerimaan"];
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
                                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Realisasi Penerimaan Komisi #Data Penerimaan $cnourut : " . $data["jenis_penerimaan"], "", $datadetail, "", "29", "");
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
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total_danaGereja, 0, ',', '.') ?></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total_danaSwadaya, 0, ',', '.') ?></td>
                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($subTotal, 0, ',', '.') ?></td>
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
                                            <td class="text-center" style="font-weight:650; color: <?= $color; ?>;">
                                                <?= $data["status"]; ?>
                                            </td>
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
                                            <td colspan="" class="text-center">
                                                <?php
                                                $all_ids_string = implode(',', $id_penerimaan);
                                                $disabled = ($status_aktif_fiskal == 1 && $data['status'] != 'Tervalidasi') ? false : true;
                                                $caption = "Apakah Transaksi Penerimaan ini Valid ?";
                                                $datavalid = array(
                                                    array("id_penerimaan", $all_ids_string, "realisasi_penerimaan_komisi"),
                                                );
                                                _CreateWindowModalValid($cnourut, "val", "val-form", "val-button", "md", 200, "Validasi Transaksi Penerimaan# " . $data['nama_akun'] . " : Rp. " . number_format($data["jumlah_penerimaan"], 0, ',', '.'), $datavalid, "29", $disabled, "Validasi", $caption);
                                                ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                <?php
                                        $countprogram = 0;
                                        $firstBidangRow = false;
                                    }
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <td colspan="3" style="color:#2b3e66; font-weight:bolder">Total Penerimaan <?= $komisi ?></td>
                                    <td></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format($totalAll_jumlah, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format($totalAll_danaGereja, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format($totalAll_danaSwadaya, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder;" class="text-end"><?= number_format($totalAll_sumberDana, 0, ',', '.') ?></td>
                                    <td colspan="" class="text-center"></td>
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