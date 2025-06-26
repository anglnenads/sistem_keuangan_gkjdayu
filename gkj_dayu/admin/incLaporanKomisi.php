<?php
if (empty($_POST["tb_tahun"])) {
    $_POST["tb_tahun"] = 0;
    unset($_SESSION['tahun']);
}

if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];

    $sql = "SELECT id_fiskal FROM fiskal WHERE tahun = $tahun_aktif";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_fiskal = $row['id_fiskal'];
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("document", "Rekapitulasi Rencana dan Realisasi Komisi", "Laporan");
            ?>
        </div>
    </div>

    <form action="" method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <div class="second" style="display: flex; justify-content: space-between; align-items: center; width: 100%; color:#003153; font-weight:500">
            <div class="" style="width:60%">
                <div>
                    <label for="tahun">Pilih Tahun : </label> &nbsp;
                    <select name="tb_tahun" id="tb_tahun" style="border-radius: 4px; border: 1px solid #676892;">
                        <option value="">-- Pilih --</option>
                        <?php
                        $sql = "SELECT tahun FROM fiskal ORDER BY tahun ASC";
                        $result = $GLOBALS["conn"]->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['tahun'] . "' $selected>" . $row['tahun'] . "</option>";
                        }
                        ?>
                    </select>
                    &nbsp;
                    <button style="background-color: #49749C; color:white; border-radius:4px; border:none; width:8%" name="filter-bttn" type="submit">Filter</button>
                </div>
            </div>
    </form>

    <div style="display: flex; justify-content: end; width:30%;">
        <div class="" style="text-align:center; background-color:#2e8b57; width: 23%; color:white; border-radius:4px">
            <a href="http://localhost:80/gkj_dayu/cetak/xlxCetakRencanaRealisasiKomisi.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
        </div> &nbsp;&nbsp;
        <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
            <a href="http://localhost:80/gkj_dayu/cetak/pdfCetakRencanaRealisasiKomisi.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
        </div>
    </div>
</div>
<br>
<br>
<div class="sub-title" style=" justify-content: space-between; align-items: center; ">
    <p>LAPORAN RENCANA DAN REALISASI KOMISI

        <?php
        if (!isset($_POST['tb_tahun'])) {
            echo "<span>TAHUN $tahun_aktif</span>";
        } elseif (isset($_POST['tb_tahun'])) {
            if ($_POST["tb_tahun"] == 0) {
                echo "<span>TAHUN $tahun_aktif</span>";
            } else {
                $waktu = $_POST['tb_tahun'];
                echo "<span>TAHUN $waktu</span>";
            }
        }
        ?>
    </p>
</div>

<br>

<div class="row">
    <div class="col-md-12">
        <?php
        $sql = "SELECT fiskal.id_fiskal, fiskal.tahun, bidang.nama_bidang, komisi.nama_komisi,

    -- Rencana pengeluaran dan penerimaan
    (SELECT SUM(rnk.jumlah) 
     FROM v_rencanakomisi rnk 
     WHERE ((komisi.id_komisi IS NULL AND rnk.id_komisi IS NULL) OR rnk.id_komisi = komisi.id_komisi)
       AND rnk.id_bidang = bidang.id_bidang 
       AND rnk.id_fiskal = fiskal.id_fiskal
       AND rnk.jenis COLLATE utf8mb4_unicode_ci = 'Rencana Penerimaan') AS jumlah_rencana_penerimaan,

    (SELECT SUM(rnk.jumlah) 
     FROM v_rencanakomisi rnk 
     WHERE ((komisi.id_komisi IS NULL AND rnk.id_komisi IS NULL) OR rnk.id_komisi = komisi.id_komisi)
        AND rnk.id_bidang = bidang.id_bidang 
       AND rnk.id_fiskal = fiskal.id_fiskal
       AND rnk.jenis COLLATE utf8mb4_unicode_ci = 'Rencana Pengeluaran') AS jumlah_rencana_pengeluaran,

    -- Realisasi pengeluaran dan penerimaan
    (SELECT SUM(rlk.jumlah) 
     FROM v_realisasikomisi rlk 
     WHERE ((komisi.id_komisi IS NULL AND rlk.id_komisi IS NULL) OR rlk.id_komisi = komisi.id_komisi)
       AND rlk.id_bidang = bidang.id_bidang 
       AND rlk.id_fiskal = fiskal.id_fiskal
       AND rlk.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan') AS jumlah_realisasi_penerimaan,

    (SELECT SUM(rlk.jumlah) 
        FROM v_realisasikomisi rlk
       WHERE ((komisi.id_komisi IS NULL AND rlk.id_komisi IS NULL) OR rlk.id_komisi = komisi.id_komisi)
       AND rlk.id_bidang = bidang.id_bidang 
       AND rlk.id_fiskal = fiskal.id_fiskal
       AND rlk.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran') AS jumlah_realisasi_pengeluaran

    FROM fiskal
    CROSS JOIN bidang
    LEFT JOIN komisi ON bidang.id_bidang = komisi.id_bidang";


        if (isset($_POST['tb_tahun'])) {
            if ($_POST["tb_tahun"] == 0) {
                $sql .= " WHERE fiskal.id_fiskal = $id_fiskal ORDER BY fiskal.tahun, bidang.id_bidang, komisi.id_komisi ";
            } else {
                $sql .= " WHERE fiskal.tahun =" . $_POST["tb_tahun"] . " ORDER BY fiskal.tahun, bidang.id_bidang, komisi.id_komisi; ";
            }
        } else {
            $sql .= " WHERE fiskal.id_fiskal = $id_fiskal ORDER BY fiskal.tahun, bidang.id_bidang, komisi.id_komisi ";
        }

        $view = new cView();
        $array = $view->vViewData($sql);

        ?>

        <div id="" class='table-responsive'>
            <table id="" class="table table-bordered table-condensed w-100">
                <thead>
                    <tr class='small'>
                        <td rowspan="2" width='3%' class="text-center">No</td>
                        <td rowspan="2" width='' class="text-center">Bidang</td>
                        <td rowspan='2' class="text-center">Komisi</td>
                        <td colspan="2" width='' class="text-center">Penerimaan</td>
                        <td colspan="2" width='' class="text-center">Pengeluaran</td>
                    </tr>

                    <tr class='small'>
                        <td class="text-center">Rencana</td>
                        <td class="text-center">Realisasi</td>
                        <td class="text-center">Rencana</td>
                        <td class="text-center">Realisasi</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    $total_rencanaPenerimaan = 0;
                    $total_realisasiPenerimaan = 0;
                    $total_rencanaPengeluaran = 0;
                    $total_realisasiPengeluaran = 0;

                    foreach ($array as $data) {
                        $cnourut++;
                        $color1 = "black";
                        $color2 = "black";
                        if ($data['jumlah_realisasi_penerimaan'] > $data['jumlah_rencana_penerimaan']) {
                            $color1 = "#008000";
                        } elseif ($data['jumlah_realisasi_penerimaan'] < $data['jumlah_rencana_penerimaan']) {
                            $color1 = "#a52a2a";
                        }

                        if ($data['jumlah_realisasi_pengeluaran'] < $data['jumlah_rencana_pengeluaran']) {
                            $color2 = "#008000";
                        } elseif ($data['jumlah_realisasi_pengeluaran'] > $data['jumlah_rencana_pengeluaran']) {
                            $color2 = "#a52a2a";
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?= $cnourut ?></td>
                            <td><?= $data['nama_bidang'] ?></td>
                            <td><?= $data['nama_komisi'] ?></td>
                            <td class="text-end" style="font-weight: bold;"><?= number_format($data['jumlah_rencana_penerimaan'], 0, ',', '.') ?></td>
                            <td class="text-end" style="font-weight: bold; color: <?= $color1 ?>"> <?= number_format($data['jumlah_realisasi_penerimaan'], 0, ',', '.') ?></td>
                            <td class="text-end" style="font-weight: bold;"><?= number_format($data['jumlah_rencana_pengeluaran'], 0, ',', '.') ?></td>
                            <td class="text-end" style="font-weight: bold; color: <?= $color2 ?>"><?= number_format($data['jumlah_realisasi_pengeluaran'], 0, ',', '.') ?></td>
                        </tr>
                    <?php
                        $total_rencanaPenerimaan += $data['jumlah_rencana_penerimaan'];
                        $total_realisasiPenerimaan += $data['jumlah_realisasi_penerimaan'];
                        $total_rencanaPengeluaran += $data['jumlah_rencana_pengeluaran'];
                        $total_realisasiPengeluaran += $data['jumlah_realisasi_pengeluaran'];
                    }
                    $color1 = "black";
                    $color2 = "black";
                    if ($total_realisasiPenerimaan > $total_rencanaPenerimaan) {
                        $color1 = "#008000";
                    } else {
                        $color1 = "#a52a2a";
                    }

                    if ($total_realisasiPengeluaran < $total_rencanaPengeluaran) {
                        $color2 = "#008000";
                    } else {
                        $color2 = "#a52a2a";
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td colspan="2" style="font-weight:bolder">Total</td>
                        <td class="text-end" style="font-weight:bolder"><?= number_format($total_rencanaPenerimaan, 0, ',', '.') ?></td>
                        <td class="text-end" style="font-weight:bolder; color: <?= $color1 ?>"> <?= number_format($total_realisasiPenerimaan, 0, ',', '.') ?></td>
                        <td class="text-end" style="font-weight:bolder"><?= number_format($total_rencanaPengeluaran, 0, ',', '.') ?></td>
                        <td class="text-end" style="font-weight:bolder; color: <?= $color2 ?>"><?= number_format($total_realisasiPengeluaran, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function handleChange(selector, type) {
            $(selector).change(function() {
                var value = $(this).val();
                console.log(type + " yang dikirim: " + value);

                $.ajax({
                    url: "../_function_i/ambilData.php",
                    type: "POST",
                    data: {
                        [type]: value
                    },
                    success: function(response) {
                        console.log("Response dari server untuk " + type + ": " + response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error untuk " + type + ": " + error);
                    },
                });
            });
        }

        handleChange("#tb_tahun", "tahun");
    });
</script>