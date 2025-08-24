<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uploadPath = "/uploads/bukti_penerimaan/";

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
            _myHeader("newspaper", "Penerimaan Gereja", "Data Penerimaan");
            ?>
        </div>
    </div>


    <p></p>

    <div class="second">
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
            <?php
            ?>
        </div>
    </div>
    <p></p>


    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, b.*, e.*, f1.*, f2.nama AS nama_validator, f2.jbtn AS jbtn_validator ";
            $sql .= "FROM realisasi_penerimaan_gereja a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun LEFT JOIN bank e ON a.id_bank = e.id_bank LEFT JOIN user f1 ON a.id_user = f1.id_user  LEFT JOIN user f2 ON a.id_validator = f2.id_user ";
            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " ";
            if ($_POST["tb_bulan"] == 0) {
                $sql .= " ";
            } else {
                $sql .= " AND month(a.tanggal_penerimaan) = " . intval($bulan) . "  ";
            }
            $sql .= " ORDER BY a.tanggal_penerimaan ASC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='3%' class="text-right">No</td>
                            <td width='12%'>Tanggal Penerimaan</td>
                            <td width=''>Jenis Penerimaan</td>
                            <td width=''>Nama Akun</td>
                            <td width='' class="text-end">Jumlah Penerimaan</td>
                            <td width='' class="text-center">Status</td>
                            <td width='5%' class="text-center">DETAIL</td>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;

                        $groupedData = [];
                        $total_penerimaan = 0;

                        foreach ($array as $data) {
                            $tanggal = $data["tanggal_penerimaan"];
                            $jenis = $data["jenis_penerimaan"];

                            $groupedData[$tanggal][$jenis][] = $data;
                        }

                        foreach ($groupedData as $tanggal => $jenisList) {
                            $firstDateRow = true;

                            $editId = 0;
                            foreach ($jenisList as $jenis => $dataList) {

                                $total = 0;
                                $firstJenisRow = true;

                                $datadetail = [];
                                $last_bukti_penerimaan = null;
                                $firstEntry = true;
                                 foreach ($dataList as $data) {
                                    if ($firstEntry) {
                                        $datadetail[] = array("Tanggal Penerimaan", ":", date('d-m-Y', strtotime($data["tanggal_penerimaan"])), 1, "");
                                        $datadetail[] = array("Jenis Penerimaan", ":", $data["jenis_penerimaan"], 1, "");
                                        $datadetail[] = array("Bank Penerimaan", ":", $data["nama_bank"] . " - " . $data["nama_rekening"], 1, "");
                                        if (!empty($data["bukti_penerimaan"])) {
                                            $buktiFile = htmlspecialchars($data["bukti_penerimaan"] ?? '', ENT_QUOTES, 'UTF-8');
                                            $last_bukti_penerimaan = $data["bukti_penerimaan"];
                                            $datadetail[] = array(
                                                "Bukti Penerimaan",
                                                ":",
                                            "<a href='{$protocol}://{$host}{$uploadPath}{$buktiFile}' target='_blank'>{$buktiFile}</a>",
                                                1
                                            );
                                        }
                                        $datadetail[] = array("Tanggal Penginputan", ":", date('d-m-Y', strtotime($data["tanggal_pencatatan"])), 1, "");
                                        $datadetail[] = array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1, "");
                                        $datadetail[] = array("Status Validasi", ":", $data["status"], 1, "");
                                        $datadetail[] = array("Divalidasi oleh", ":", $data["nama_validator"] . " - " . $data["jbtn_validator"], 1, "");
                                        $datadetail[] = array(" ", "", "", 1, "");
                                        $datadetail[] = array("Detail Akun", "", "", 1, "");
                                        $firstEntry = false;
                                    }
                                    $datadetail[] = array("Akun", ":", $data['nama_akun'], 1, "");
                                    $datadetail[] = array("Jumlah Penerimaan", ":", 'Rp. ' . number_format($data["jumlah_penerimaan"], 0, ',', '.'), 1, "");
                                    if (!empty($data["bukti_penerimaan"]) && $data["bukti_penerimaan"] !== $last_bukti_penerimaan) {
                                        $buktiFile = htmlspecialchars($data["bukti_penerimaan"] ?? '', ENT_QUOTES, 'UTF-8');
                                        $datadetail[] = array(
                                            "Bukti Penerimaan",
                                            ":",
                                            "<a href='{$protocol}://{$host}{$uploadPath}{$buktiFile}' target='_blank'>{$buktiFile}</a>",
                                            1
                                        );
                                        $last_bukti_penerimaan = $data["bukti_penerimaan"];
                                    }

                                    $datadetail[] = array("", "", "", 1, "");
                                }

                                $number = 0;
                                $id_penerimaan = [];
                                foreach ($dataList as $index => $data) {
                                    $cnourut++;
                                    $number++;
                                    $id_penerimaan[] = $data["id_penerimaan"];
                        ?>
                                    <tr>

                                        <td><?= $number ?></td>
                                        <td><?= $firstDateRow ? date('d-m-Y', strtotime($tanggal)) : ""; ?></td> <!-- Tanggal hanya di baris pertama -->
                                        <td style="font-weight: bold;"><?= $firstJenisRow ? $jenis : ""; ?></td> <!-- Jenis Penerimaan hanya di baris pertama -->
                                        <td><?= $data["nama_akun"]; ?></td>
                                        <td class="text-end"><?= number_format($data["jumlah_penerimaan"], 0, ',', '.'); ?></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php
                                    $total = $total + $data['jumlah_penerimaan'];
                                    $firstDateRow = false;
                                    $firstJenisRow = false;
                                }
                                $total_penerimaan = $total_penerimaan + $total;
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="font-weight: bold;">Total</td>

                                    <td class="text-end" style="font-weight: bold; "><?= number_format($total, 0, ',', '.'); ?></td>
                                    <?php
                                    $color = "black"; // Warna default

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

                                    <?php if ($index === count($dataList) - 1) { ?>
                                        <td class="text-center">
                                            <?php
                                            _CreateWindowModalDetil(
                                                $cnourut,
                                                "view",
                                                "viewsasaran-form",
                                                "viewsasaran-button",
                                                "lg",
                                                600,
                                                'Detail Realisasi Penerimaan Gereja#Data Penerimaan ',
                                                "",
                                                $datadetail,
                                                "",
                                                "27",
                                                ""
                                            );
                                            ?>
                                        </td>

                                    <?php } ?>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="font-weight: bold;"></td>

                                    <td class="text-end" style="font-weight: bold; "></td>

                                    <td class="text-center">

                                    </td>
                                    <?php if ($index === count($dataList) - 1) { ?>
                                        <td>
                                        </td>

                                    <?php } ?>

                                </tr>
                            <?php
                            }
                            ?>



                        <?php
                        }
                        ?>

                    </tbody>
                    <?php
                    $query = "SELECT SUM(jumlah_penerimaan) AS jumlah FROM realisasi_penerimaan_gereja WHERE status = 'Tervalidasi' AND id_fiskal = $id_fiskal";
                    $view = new cView();
                    $array = $view->vViewData($query);

                    if (!empty($array)) {
                        $saldo_tervalidasi = $array[0]['jumlah']; // Ambil data dari baris pertama
                    }
                    ?>
                    <tr>
                        <td colspan="8"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:#5B90CD; font-weight:bolder">Total Penerimaan Keseluruhan</td>
                        <td class="text-end" style="color:#483d8b; font-weight:bolder"><?= number_format((float) ($total_penerimaan ?? 0), 0, ',', '.') ?></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:#2e8b57; font-weight:bolder">Total Penerimaan Tervalidasi</td>
                        <td class="text-end" style="color:#2e8b57; font-weight:bolder"><?= number_format((float) ($saldo_tervalidasi ?? 0), 0, ',', '.') ?></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="color:#808080; font-weight:bolder">Total Penerimaan Belum Tervalidasi</td>
                        <td class="text-end" style="color:#808080; font-weight:bolder"><?= number_format((float) ($total_penerimaan - $saldo_tervalidasi ?? 0), 0, ',', '.') ?></td>
                        <td colspan="3"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p><br></p>
        </div>
    </div>