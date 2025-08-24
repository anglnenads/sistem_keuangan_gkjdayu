<?php

if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif'];
} 

if (empty($_POST["tb_tahun"])) {
    $_POST["tb_tahun"] = 0;
}
if (empty($_POST["tb_bulan"])) {
    $_POST["tb_bulan"] = 0;
} else {
    $bulan = $_POST["tb_bulan"];
}

if (empty($_POST['filter'])) {
    unset($_SESSION['bulan']);
    unset($_SESSION['tahun']);
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("document", "Laporan Pengeluaran ", "Laporan ");
            ?>
        </div>
    </div>

    <div style="border-radius:7px; width:100%; height: 7%; justify-content:center; border:1px solid #00008b; font-weight:bold; color:#003153">
        <form action="" method="post" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; gap: 50px; margin-top:12px; margin-left:20px;  ">
                <label>
                    <input type="radio" name="filter" value="bulan"
                        <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'bulan') ? 'checked' : ''; ?>
                        onchange="this.form.submit()"> Bulan
                </label>
                <label>
                    <input type="radio" name="filter" value="tahun"
                        <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'tahun') ? 'checked' : ''; ?>
                        onchange="this.form.submit()"> Tahun
                </label>
            </div>
    </div>
    <br>

    <div class="second" style="display: flex; justify-content: space-between; align-items: center; width: 100%; color:#003153; font-weight:500">
        <div class="" style="width:60%">
            <div>
                <?php
                if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                    unset($_SESSION['tahun']);

                    if (empty($_POST['tb_bulan'])) {
                        unset($_SESSION['bulan']);
                    } else {
                        $_SESSION['bulan'] = $_POST['tb_bulan']; 
                    }
                ?>
                    <label for="tb_bulan">Pilih Bulan : </label> &nbsp;
                    <select style="border-radius: 4px; border: 1px solid #676892;" name="tb_bulan" id="tb_bulan">
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
                    <button style="background-color: #49749C; color:white; border-radius:4px; border:none; width:8%" type="submit">Filter</button>

                <?php
                }
                if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                    unset($_SESSION['bulan']);

                    if (empty($_POST['tb_tahun'])) {
                        unset($_SESSION['tahun']);
                    } else {
                        $_SESSION['tahun'] = $_POST['tb_tahun']; 
                    }
                ?>
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
                    <button style="background-color: #49749C; color:white; border-radius:4px; border:none; width:8%" type="submit">Filter</button>
                <?php
                }
                ?>
            </div>
            </form>
        </div>

        <div style="display: flex; justify-content: end; width:30%;">
            <div class="" style="text-align:center; background-color:#2e8b57; width: 23%; color:white; border-radius:4px">
                <a href="Cetak/xlxCetakPengeluaran.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
            </div> &nbsp;&nbsp;
            <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
                <a href="Cetak/pdfCetakPengeluaran.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
            </div>
        </div>
    </div>

    <br>
    <div class="sub-title" style=" justify-content: space-between; align-items: center; ">
        <p>LAPORAN PENGELUARAN GEREJA

        <?php
        $nama_bulan = [
            1 => 'JANUARI',
            2 => 'FEBRUARI',
            3 => 'MARET',
            4 => 'APRIL',
            5 => 'MEI',
            6 => 'JUNI',
            7 => 'JULI',
            8 => 'AGUSTUS',
            9 => 'SEPTEMBER',
            10 => 'OKTOBER',
            11 => 'NOVEMBER',
            12 => 'DESEMBER'
        ];

        if (!isset($_POST['filter'])) {
            echo "<span>TAHUN $tahun_aktif</span>";
        }
        if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
            if ($_POST["tb_bulan"] == 0) {
                echo "<span>TAHUN $tahun_aktif</span>";
            } else {
                $nama_bulan = $nama_bulan[intval($bulan)];
                echo "<span>BULAN $nama_bulan TAHUN $tahun_aktif</span>";
            }
        }
        if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
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

            $sql = "SELECT 
            p.tanggal_pengeluaran,
            p.jenis_pengeluaran,
            p.jumlah AS jumlah_pengeluaran,
            p.id_akun AS id_akun,
            a.nama_akun AS nama_akun,
            f.tahun AS tahun
          FROM realisasi_pengeluaran_gereja p
            LEFT JOIN akun a ON p.id_akun = a.id_akun
            LEFT JOIN fiskal f ON p.id_fiskal = f.id_fiskal ";

            if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                if ($_POST["tb_bulan"] == 0) {
                     $sql .= " WHERE tahun = " . $tahun_aktif . " AND p.status = 'Tervalidasi'";
                } else {
                    $nomorBulan = intval($bulan); 
                    $sql .= " WHERE tahun = $tahun_aktif AND month(tanggal_pengeluaran) = $nomorBulan  AND p.status = 'Tervalidasi'";
                }
            } else if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                if ($_POST["tb_tahun"] == 0) {
                     $sql .= "WHERE tahun =  $tahun_aktif  AND p.status = 'Tervalidasi'";
                } else {
                    $sql .= "WHERE tahun = " . $_POST["tb_tahun"] . " AND p.status = 'Tervalidasi'";
                }
            } else {
                 $sql .= "WHERE tahun = " . $tahun_aktif . " AND p.status = 'Tervalidasi'";
            }

            $sql .= " ORDER BY tanggal_pengeluaran ASC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width="10%"></td>
                            <td width='35%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='25%'></td>
                            <td width='15%'></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $all_total = 0;
                        $groupedData = [];

                        foreach ($array as $data) {
                            $tanggal = $data["tanggal_pengeluaran"];

                            $groupedData[$tanggal][] = $data;
                        }
                        foreach ($groupedData as $tanggal => $tanggalData) {
                            $firstRow = true;
                            $total_pengeluaran = 0;
                            foreach ($tanggalData as $data) {
                                $total_pengeluaran = $total_pengeluaran + $data["jumlah_pengeluaran"];
                        ?>
                                <tr>
                                    <?php if ($firstRow) { ?>
                                        <td style="white-space: pre;"><?= date('d-m-Y', strtotime($tanggal)); ?></td>
                                    <?php } else { ?>
                                        <td></td> 
                                    <?php } ?>
                                    <td><?= $data["jenis_pengeluaran"]; ?></td>
                                    <td class="text-end">
                                        <?= ($data["jumlah_pengeluaran"] == 0) ? '-' : number_format($data["jumlah_pengeluaran"], 0, ',', '.'); ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                            <?php
                                $firstRow = false;
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td style=" font-weight:bolder">Total</td> 
                                <td></td>
                                <td class="text-start" style=" font-weight:bolder"><?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php
                            $all_total = $all_total + $total_pengeluaran;
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td style="color:#5B90CD; font-weight:bolder">Total Pengeluaran</td>
                            <td></td>
                            <td class="text-start" style="color:#5B90CD; font-weight:bolder"><?= number_format($all_total, 0, ',', '.') ?></td>
                            <td></td>
                            <td></td>
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
        handleChange("#tb_bulan", "bulan");
        handleChange("#tb_tahun", "tahun");
    });
</script>