<?php
if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
} 

if (empty($_POST["tb_tahun"])) {
    $_POST["tb_tahun"] = 0;
    unset($_SESSION['tahun']);
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("document", "Rekapitulasi Komisi", "Laporan");
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
            <a href="cetak/xlxCetakRekapKomisi.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
        </div> &nbsp;&nbsp;
        <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
            <a href="cetak/pdfCetakRekapKomisi.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
        </div>
    </div>


</div>
<br>
<div class="sub-title" style=" justify-content: space-between; align-items: center; ">
    <p>REKAPITULASI KOMISI

        <?php
        if (!isset($_POST['tb_tahun'])) {
            echo "<span>TAHUN $tahun_aktif</span>";
        }
        if (isset($_POST['tb_tahun'])) {
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

        $sql = "SELECT * FROM _saldo_komisi where tahun = $tahun_aktif";

        if (isset($_POST['tb_tahun'])) {
            if ($_POST["tb_tahun"] == 0) {
                $sql .= " ";
            } else {
                $sql = "SELECT * FROM _saldo_komisi where tahun = " . $_POST['tb_tahun'] ;
            }
        }

        $view = new cView();
        $array = $view->vViewData($sql);
        ?>

        <div id="" class='table-responsive'>
            <table id='' class='table table-condensed table-bordered w-100'>
                <thead>
                    <tr class='small'>
                        <td width='3%' class="text-right">No</td>
                        <td width=''>Bidang</td>
                        <td width=''>Komisi</td>
                        <td class="text-end">Saldo Awal</td>
                        <td class="text-end" width=''>Jumlah Penerimaan</td>
                        <td class="text-end" width=''>Jumlah Pengeluaran</td>
                        <td class="text-end" width=''>Saldo Akhir</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnourut = 0;
                    $total_saldoAwal = 0;
                    $total_penerimaan = 0;
                    $total_pengeluaran = 0;
                    $total_saldoAkhir = 0;
                    
                    foreach ($array as $data) {
                        $cnourut = $cnourut + 1;
                        $total_saldoAwal += $data["saldo_awal"];
                        $total_penerimaan += $data["jumlah_penerimaan"];
                        $total_pengeluaran += $data["jumlah_pengeluaran"];
                        $total_saldoAkhir += $data["saldo_akhir"];
                        
                    ?>
                        <tr class=''>
                            <td class="text-right"><?= $cnourut; ?></td>
                            <td><?= $data["nama_bidang"]; ?></td>
                            <td><?= $data["nama_komisi"]; ?></td>
                            <td class="text-end"><?= number_format($data["saldo_awal"], 0, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($data["jumlah_penerimaan"], 0, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($data["jumlah_pengeluaran"], 0, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($data["saldo_akhir"], 0, ',', '.') ?></td>
                          
                        </tr>
                    <?php } ?>
                </tbody>
                <tr>
                    <td></td>
                    <td colspan="2" style="color:#5B90CD; font-weight:bolder">T O T A L</td>
                    <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($total_saldoAwal, 0, ',', '.') ?></td>
                    <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($total_penerimaan, 0, ',', '.') ?></td>
                    <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
                    <td class="text-end" style="color:#5B90CD; font-weight:bolder"> <?= number_format($total_saldoAkhir, 0, ',', '.') ?></td>
                </tr>

            </table>
        </div>
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
        // Tangani perubahan untuk tb_bulan dan tb_tahun
        handleChange("#tb_tahun", "tahun");
    });
</script>