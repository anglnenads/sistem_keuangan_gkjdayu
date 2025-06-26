<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Pengeluaran Komisi", "Data pengeluaran");
            ?>
        </div>
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
            ?>
            <div class='table-responsive'>
                <table id='' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='2%' class="text-right">No</td>
                            <td width='7%'></td>
                            <td width='20%' class="text-start">Jenis Pengeluaran/Kegiatan</td>
                            <td width='5%' class="text-center">Vol</td>
                            <td width='10%' class="text-center">Satuan</td>
                            <td width='8%' class="text-end">Jumlah</td>
                            <td width='8%' class="text-end">Dana Gereja</td>
                            <td width='8%' class="text-end">Dana Swadaya</td>
                            <td width='8%' class="text-end">Subtotal</td>
                            <td width='12%' class="text-center">Status</td>
                            <td width='5%' class="text-center">DETAIL</td>
                        </tr>
                    </thead>
                    <?php
                    $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.*, a.status AS status_pengeluaran,
                            b.*, c.*, d.*, e.*, f.*, u1.*, u2.nama AS nama_validator, u2.jbtn AS jbtn_validator
                            FROM realisasi_pengeluaran_komisi a
                            LEFT JOIN akun b ON a.id_akun = b.id_akun 
                            LEFT JOIN bidang c ON a.id_bidang = c.id_bidang 
                            LEFT JOIN komisi d ON a.id_komisi = d.id_komisi 
                            LEFT JOIN program e ON a.id_program = e.id_program 
                            LEFT JOIN fiskal f ON a.id_fiskal = f.id_fiskal 
                            LEFT JOIN user u1 ON a.id_user = u1.id_user 
                            LEFT JOIN user u2 ON a.id_validator = u2.id_user 
                            WHERE a.id_bidang = ( SELECT id_bidang FROM realisasi_pengeluaran_komisi 
                                                    WHERE id_fiskal = $id_fiskal
                                                    ORDER BY tanggal_pengeluaran DESC LIMIT 1
                                                ) AND a.id_fiskal = $id_fiskal
                            ORDER BY a.tanggal_pengeluaran ASC;";

                    if (!empty($_POST['bidang']) || !empty($_POST['komisi'])) {
                        $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.*, a.status AS status_pengeluaran,
                        b.*,
                        c.*,
                        d.*,
                        e.*,
                        f.*,
                        u1.*,
                        u2.nama AS nama_validator, u2.jbtn AS jbtn_validator
                        FROM realisasi_pengeluaran_komisi a 
                        LEFT JOIN akun b ON a.id_akun = b.id_akun 
                        LEFT JOIN bidang c ON a.id_bidang = c.id_bidang 
                        LEFT JOIN komisi d ON a.id_komisi = d.id_komisi 
                   
                      
                        LEFT JOIN program e ON a.id_program = e.id_program 
                        LEFT JOIN fiskal f ON a.id_fiskal = f.id_fiskal 
                        LEFT JOIN user u1 ON a.id_user = u1.id_user 
                        LEFT JOIN user u2 ON a.id_validator = u2.id_user ";

                        if (!empty($_POST['komisi'])) {
                            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " AND a.id_komisi = " . $_POST['komisi'];
                        } else {
                            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " AND a.id_bidang = " . $_POST['bidang'];
                        }
                    }
                    $view = new cView();
                    $array = $view->vViewData($sql);

                    ?>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $total_keseluruhan = 0;

                        $groupedData = [];

                        foreach ($array as $data) {
                            $bidang = $data["nama_bidang"];
                            $komisi = $data["nama_komisi"];
                            $program = $data["nama_program"];
                            $status = $data["status"];

                            $groupedData[$bidang][$komisi][$program][$status][] = $data;
                        }

                        foreach ($groupedData as $bidang => $bidangList) {
                            $total_komisi = 0;
                            foreach ($bidangList as $komisi => $komisiList) {  ?>
                                <tr style="font-weight: bold;">
                                    <td colspan="8" style="background-color: rgb(223, 240, 248); color: #1a2e62"><?= $bidang . " - " . $komisi; ?></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                    <td style="background-color: rgb(223, 240, 248);"></td>
                                </tr>

                                <?php

                                $total = 0;
                                $total_pakai = 0;

                                $totalAll_jumlah = 0;
                                $totalAll_danaGereja = 0;
                                $totalAll_danaSwadaya = 0;
                                $totalAll_sumberDana = 0;


                                foreach ($komisiList as $program => $programList) { ?>
                                    <tr style="font-weight: bold;">
                                        <td></td>
                                        <td colspan="2" style="background-color:#f2f3f4;" width="">Program : <?= ($program == NULL ? 'Insidental' : $program) ?>
                                        </td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                    </tr>
                                    <?php
                               
                                    $count = 0;

                                    $lastStatus = null;
                                      $count = 0;
                                    $lastStatus = null;
                                    $totall = 0;
                                    $totall_danaGereja = 0;
                                    $totall_danaSwadaya = 0;
                                    $subTotall = 0;


                                    foreach ($programList as $status => $items) {

                                          $id_pengeluaran = [];
                                        $number = 0;
                                        $total = 0;
                                        $total_danaGereja = 0;
                                        $total_danaSwadaya = 0;
                                        $subTotal = 0;

                                        foreach ($items as $data) {
                                            $cnourut = $cnourut + 1;
                                            $number = $number + 1;

                                            $total += $data["jumlah"];
                                            $total_danaGereja += $data["dana_gereja"];
                                            $total_danaSwadaya += $data["dana_swadaya"];
                                            $subTotal += $data["jumlah"];
                                            $id_pengeluaran[] = $data["id_pengeluaran"];
                                    ?>
                                            <tr class=''>
                                                <td class="text-right"><?= $number; ?></td>
                                                <td class="text-end"><?= date('d-m-Y', strtotime($data["tanggal_pengeluaran"])); ?></td>
                                                <td><?= $data["item"]; ?></td>
                                                <td class="text-center"><?= $data["volume"]; ?></td>
                                                <td class="text-start" style="">Rp. <?= number_format($data["harga_satuan"], 0, ',', '.'); ?>/<?= $data["satuan"] ?></td>
                                                <td class="text-end"><?= number_format($data["jumlah"], 0, ',', '.'); ?></td>
                                                <td class="text-end"><?= number_format($data["dana_gereja"], 0, ',', '.'); ?></td>
                                                <td class="text-end"><?= number_format($data["dana_swadaya"], 0, ',', '.'); ?></td>
                                                <td class="text-end"><?= number_format($data["dana_gereja"] + $data["dana_swadaya"], 0, ',', '.'); ?></td>
                                                <td></td>
                                                <td class="text-center">
                                                    <?php
                                                    $datadetail = array(
                                                        array("Tanggal Pengeluaran", ":", date('d-m-Y', strtotime($data["tanggal_pengeluaran"])), 1),
                                                        array("Bidang", ":", $data["nama_bidang"], 1, ""),
                                                        array("Komisi", ":", $data["nama_komisi"], 1, ""),
                                                        array("Akun", ":", $data["nama_akun"], 1, ""),
                                                        array("Program", ":", $data["nama_program"], 1, ""),
                                                        array("Jenis Pengeluaran/Kegiatan", ":", $data["item"], 1, ""),
                                                        array("Volume", ":", $data["volume"], 1, ""),
                                                        array("Satuan", ":", "Rp. " . number_format($data["harga_satuan"], 0, ',', '.') . "/" . $data["satuan"], 1, ""),
                                                        array("Jumlah", ":", "Rp. " . number_format($data["jumlah"], 0, ',', '.'), 1, ""),
                                                        array("Sumber Dana - Dana Gereja", ":", "Rp. " . number_format($data["dana_gereja"], 0, ',', '.'), 1, ""),
                                                        array("Sumber Dana - Dana Swadaya", ":", "Rp. " . number_format($data["dana_swadaya"], 0, ',', '.'), 1, ""),
                                                        array("Keterangan", ":", $data["keterangan"], 1),
                                                        array("LPJ", ":", "<a href='http://localhost:80/gkj_dayu/uploads/lpj/" . htmlspecialchars($data["lpj"]) . "' target='_blank'>" . htmlspecialchars($data["lpj"]) . "</a>", 1),
                                                        array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                                        array("Tanggal Pencatatan", ":", date('d-m-Y', strtotime($data["tanggal_catat"])), 1),
                                                        array("Divalidasi oleh", ":", $data["nama_validator"] . " - " . $data["jbtn_validator"], 1),
                                                    );
                                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Realisasi Pengeluaran Komisi#Data Pengeluaran  $cnourut", "", $datadetail, "", "25", "");
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php

                                        }
                                        $totalAll_jumlah += $total;
                                        $totalAll_danaGereja += $total_danaGereja;
                                        $totalAll_danaSwadaya += $total_danaSwadaya;
                                        $totalAll_sumberDana += ($total_danaGereja + $total_danaSwadaya);
                                        $total_pakai += $total_danaGereja;

                                        $total_keseluruhan += $total_danaGereja;

                                        ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td style=" font-weight:bolder">Total</td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end" style="font-weight:bolder"> <?= number_format($total, 0, ',', '.') ?></td>

                                            <td class="text-end" style=" font-weight:bolder"><?= number_format($total_danaGereja, 0, ',', '.') ?></td>
                                            <td class="text-end" style="font-weight:bolder"><?= number_format($total_danaSwadaya, 0, ',', '.') ?></td>
                                            <td class="text-end" style="font-weight:bolder"><?= number_format($subTotal, 0, ',', '.') ?></td>



                                            <?php
                                            switch ($data["status_pengeluaran"]) {
                                                case "Tervalidasi":
                                                    $color = "#3cb371";
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
                                                <?= $data["status_pengeluaran"]; ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php
                                      $totall += $total;
                                        $totall_danaGereja += $total_danaGereja;
                                        $totall_danaSwadaya += $total_danaSwadaya;
                                        $subTotall += $subTotal;
                                    }
                                    ?>
                                    <?php
                                    $query = "SELECT SUM(jumlah_pencairan) AS jumlah 
                                    FROM pencairan 
                                    WHERE id_program = " . intval($data["program"]) . "  
                                    AND id_fiskal = " . intval($id_fiskal) . "
                                    AND id_bidang = " . intval($data["bidang"]) . "
                                    AND id_komisi = " . intval($data["komisi"]);

                                    //echo $query;
                                    $view = new cView();
                                    $array = $view->vViewData($query);

                                    if (!empty($array)) {
                                        $jumlah_pencairan = $array[0]['jumlah']; 
                                    }

                                    ?>
                                    <tr>
                                        <td colspan="13"></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="7" style="color:#5B90CD; font-weight:bolder">Jumlah Pencairan Dana Gereja (Per Program)</td>
                                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($jumlah_pencairan, 0, ',', '.') ?></td>
                                        <td colspan=""></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="7" style="color:#5B90CD; font-weight:bolder">Jumlah Pemakaian Dana Pencairan (Per Program)</td>
                                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($totall_danaGereja, 0, ',', '.') ?></td>
                                        <td colspan=""></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="7" style="color:#5B90CD; font-weight:bolder">Sisa Dana Pencairan (Per Program)</td>
                                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($jumlah_pencairan - $totall_danaGereja, 0, ',', '.') ?></td>
                                        <td colspan="">

                                        </td>
                                    </tr>

                                <?php
                                }


                                ?>
                                <?php
                                $query = "SELECT SUM(jumlah_pencairan) AS jumlah 
                                    FROM pencairan 
                                    WHERE id_komisi = " . intval($data["komisi"]) . "  
                                    AND id_fiskal = " . intval($id_fiskal);

                                $view = new cView();
                                $array = $view->vViewData($query);

                                if (!empty($array)) {
                                    $jumlah_pencairan = $array[0]['jumlah']; 
                                }

                                ?>
                                <tr>
                                    <td colspan="11"></td>

                                </tr>
                                <tr>
                                    <td style="background-color: rgb(223, 240, 248)"></td>
                                    <td colspan="4" style="color:#322E7D; font-weight:bolder; background-color: rgb(223, 240, 248)">Total Keseluruhan Program</td>
                                    <td style="color:#322E7D; font-weight:bolder; background-color: rgb(223, 240, 248)" class="text-end"><?= number_format($totalAll_jumlah, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder; background-color: rgb(223, 240, 248)" class="text-end"><?= number_format($totalAll_danaGereja, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder; background-color: rgb(223, 240, 248)" class="text-end"><?= number_format($totalAll_danaSwadaya, 0, ',', '.') ?></td>
                                    <td style="color:#322E7D; font-weight:bolder; background-color: rgb(223, 240, 248)" class="text-end"><?= number_format($totalAll_sumberDana, 0, ',', '.') ?></td>
                                    <td colspan="2" style="background-color: rgb(223, 240, 248)"></td>
                                </tr>
                                <tr>
                                    <td></td>

                                    <td colspan="4" style="color:#2b3e66; font-weight:bolder">Jumlah Pencairan Dana <?= $komisi ?></td>

                                    <td> </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="color:#2b3e66; font-weight:bolder" class="text-end"><?= number_format($jumlah_pencairan, 0, ',', '.') ?></td>
                                    <td class="text-center"></td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td colspan="4" style="color:#2b3e66; font-weight:bolder">Jumlah Pemakaian Dana Pencairan <?= $komisi ?></td>

                                    <td> </td>
                                    <td> </td>
                                    <td></td>
                                    <td></td>
                                    <td style="color:#2b3e66; font-weight:bolder" class="text-end"><?= number_format($total_pakai, 0, ',', '.') ?></td>
                                    <td class="text-center"></td>

                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>

                    <?php
                    ?>

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