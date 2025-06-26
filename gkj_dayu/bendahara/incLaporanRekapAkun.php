<?php
if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];

    $sql = "SELECT id_fiskal FROM fiskal WHERE tahun = $tahun_aktif";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_fiskal = $row['id_fiskal'];
    }
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
            _myHeader("document", "Rekapitulasi Bulanan", "Laporan");
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
            <a href="http://localhost:80/gkj_dayu/cetak/xlxCetakRekapAkun.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
        </div> &nbsp;&nbsp;
        <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
            <a href="http://localhost:80/gkj_dayu/cetak/pdfCetakRekapAkun.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
        </div>
    </div>
</div>
<br>
<div class="sub-title" style=" justify-content: space-between; align-items: center; ">
    <p>LAPORAN REKAPITULASI BULANAN

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
        $sql = 'SELECT * FROM v_akun WHERE tahun = ' . $tahun_aktif;
        if (isset($_POST['tb_tahun'])) {
            if ($_POST["tb_tahun"] == 0) {
                $sql .= "";
            } else {
                $sql = 'SELECT * FROM v_akun WHERE tahun = ' . $_POST['tb_tahun'];
            }
        }

        $query = "SELECT jenis_akun, saldo_awal FROM v_saldo_akun WHERE tahun = $tahun_aktif GROUP BY jenis_akun";
        if (isset($_POST['tb_tahun'])) {
            if ($_POST["tb_tahun"] == 0) {
                $query .= "";
            } else {
                $query = "SELECT jenis_akun, saldo_awal FROM v_saldo_akun WHERE tahun = " . $_POST['tb_tahun'] . " GROUP BY jenis_akun";
            }
        }

        $view = new cView();
        $saldo = $view->vViewData($query);
        $array = $view->vViewData($sql);
        ?>

        <div id="" class='table-responsive'>
            <table id='' class='table table-condensed table-bordered'>
                <thead>
                    <tr class='small'>
                        <td width='20%' class="text-center">NAMA POS</td>
                        <td width='12%'></td>
                        <td width='18%' class="text-center">Saldo Awal</td>
                        <td width='20%' class="text-center">Januari</td>
                        <td width='12%' class="text-center">Februari</td>
                        <td width='12%' class="text-center">Maret</td>
                        <td width='12%' class="text-center">April</td>
                        <td width='12%' class="text-center">Mei</td>
                        <td width='12%' class="text-center">Juni</td>
                        <td width='12%' class="text-center">Juli</td>
                        <td width='12%' class="text-center">Agustus</td>
                        <td width='12%' class="text-center">September</td>
                        <td width='12%' class="text-center">Oktober</td>
                        <td width='12%' class="text-center">November</td>
                        <td width='12%' class="text-center">Desember</td>
                        <td width='12%' class="text-center">Total</td>
                        <td rowspan="2" width='12%' class="text-center">Saldo Akhir</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPerBulan = [
                        'penerimaan' => [],
                        'pengeluaran' => [],
                    ];

                    $dataGrouped = [];
                    foreach ($array as $row) {
                        $jenis = $row['jenis_akun'];

                        if (!isset($dataGrouped[$jenis])) {
                            $dataGrouped[$jenis] = [
                                'jenis_akun' => $jenis,
                                'bulan_penerimaan' => [],
                                'bulan_pengeluaran' => [],
                                'total_penerimaan' => 0,
                                'total_pengeluaran' => 0,
                            ];
                        }

                        $bulan = (int)$row['bulan'];
                        $penerimaan = (float)$row['total_penerimaan'];
                        $pengeluaran = (float)$row['total_pengeluaran'];

                        $dataGrouped[$jenis]['bulan_penerimaan'][$bulan] =
                            ($dataGrouped[$jenis]['bulan_penerimaan'][$bulan] ?? 0) + $penerimaan;

                        $dataGrouped[$jenis]['bulan_pengeluaran'][$bulan] =
                            ($dataGrouped[$jenis]['bulan_pengeluaran'][$bulan] ?? 0) + $pengeluaran;

                        // Total per jenis akun
                        $dataGrouped[$jenis]['total_penerimaan'] += $penerimaan;
                        $dataGrouped[$jenis]['total_pengeluaran'] += $pengeluaran;

                        // total per bulan dari semua jenis akun
                        $totalPerBulan['penerimaan'][$bulan] =
                            ($totalPerBulan['penerimaan'][$bulan] ?? 0) + $penerimaan;

                        $totalPerBulan['pengeluaran'][$bulan] =
                            ($totalPerBulan['pengeluaran'][$bulan] ?? 0) + $pengeluaran;
                    }

                    $saldoTahunLalu = [];
                    foreach ($saldo as $row) {
                        $jenis = $row['jenis_akun'];
                        $saldoTahunLalu[$jenis] = (float)$row['saldo_awal'];
                    }

                    $totalSaldoAwal = 0;
                    $totalPenerimaanSemua = 0;
                    $totalPengeluaranSemua = 0;
                    $totalSaldoAkhir = 0;

                    foreach ($dataGrouped as $jenisAkun => $data) :
                        $saldo_awal = $saldoTahunLalu[$jenisAkun] ?? 0;
                        $saldo_akhir = $saldo_awal + $data['total_penerimaan'] - $data['total_pengeluaran'];

                        // Total keseluruhan
                        $totalSaldoAwal += $saldo_awal;
                        $totalPenerimaanSemua += $data['total_penerimaan'];
                        $totalPengeluaranSemua += $data['total_pengeluaran'];
                        $totalSaldoAkhir += $saldo_akhir;
                    ?>
                        <tr>
                            <td class="text-center" rowspan="2" style="background-color: #5B90CD; color : #FFFCFC; font-weight: bold;"><?= htmlspecialchars($jenisAkun) ?></td>
                            <td class="text-center" style="background-color: #5f9ea0; color : #FFFCFC; font-weight: bold;">Penerimaan</td>
                            <td class="text-end align-middle" rowspan="2" style="color: #555555; font-weight: bold;"><?= number_format($saldo_awal, 0, ',', '.') ?></td>
                            <?php
                            for ($b = 1; $b <= 12; $b++) {
                                $val = $data['bulan_penerimaan'][$b] ?? 0;
                                echo '<td width="20%" class="text-end">' . ($val == 0 ? '-' : number_format($val, 0, ',', '.')) . '</td>';
                            }
                            ?>
                            <td class="text-end" style="color: #555555; font-weight: bold;"><?= number_format($data['total_penerimaan'], 0, ',', '.') ?></td>
                            <td class="text-end align-middle" rowspan="2" style="color : #324ab2; font-weight: bold;"><?= number_format($saldo_akhir, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-end" style="background-color: #7cb9e8; color : #FFFCFC; font-weight: bold;">Pengeluaran</td>
                            <?php
                            for ($b = 1; $b <= 12; $b++) {
                                $val = $data['bulan_pengeluaran'][$b] ?? 0;
                                echo '<td width="20%" class="text-end">' . ($val == 0 ? '-' : number_format($val, 0, ',', '.')) . '</td>';
                            }
                            ?>
                            <td class="text-end" style="color: #555555; font-weight: bold;"><?= number_format($data['total_pengeluaran'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td class="text-center" style="background-color: #5B90CD; color : #FFFCFC; font-weight: bold;" rowspan="2">TOTAL</td>
                        <td style="background-color: #5f9ea0; color : #FFFCFC; font-weight: bold;">Penerimaan</td>
                        <td class="text-end align-middle" rowspan="2" style="color: #555555; font-weight: bold;"><?= number_format($totalSaldoAwal, 0, ',', '.') ?></td>
                        <?php
                        for ($b = 1; $b <= 12; $b++) {
                            $totalPenerimaanPerBulan = $totalPerBulan['penerimaan'][$b] ?? 0;
                            echo '<td width="20%" class="text-end" style="color: #555555; font-weight: bold;">' . number_format($totalPenerimaanPerBulan, 0, ',', '.') . '</td>';
                        }
                        ?>
                        <td class="text-end" style="color: #367588; font-weight: bold;"><?= number_format($totalPenerimaanSemua, 0, ',', '.') ?></td>
                        <td rowspan="2" class="text-end align-middle" style="color : #324ab2; font-weight: bold;"><?= number_format($totalSaldoAkhir, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td style="background-color: #7cb9e8; color : #FFFCFC; font-weight: bold;">Pengeluaran</td>
                        <?php
                        for ($b = 1; $b <= 12; $b++) {
                            $totalPengeluaranPerBulan = $totalPerBulan['pengeluaran'][$b] ?? 0;
                            echo '<td width="20%" class="text-end" style="color: #555555; font-weight: bold;">' . number_format($totalPengeluaranPerBulan, 0, ',', '.') . '</td>';
                        }
                        ?>
                        <td class="text-end" style="color: #367588; font-weight: bold;"><?= number_format($totalPengeluaranSemua, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
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