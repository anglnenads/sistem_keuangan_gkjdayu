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
            _myHeader("document", "Rekapitulasi Rencana dan Realisasi Pengeluaran Gereja", "Laporan ");
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
                <a href="http://localhost:80/gkj_dayu/cetak/xlxCetakPengeluaranAkun.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
            </div> &nbsp;&nbsp;
            <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
                <a href="http://localhost:80/gkj_dayu/cetak/pdfCetakPengeluaranAkun.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
            </div>
        </div>
    </div>

    <div class="sub-title" style=" justify-content: space-between; align-items: center; ">
        <p>RENCANA DAN REALISASI PENGELUARAN GEREJA

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
                    f.tahun,
                    a.nama_akun,
                    COALESCE(SUM(r.jumlah), 0) AS jumlah_rencana,
                    COALESCE(SUM(r.dana_gereja), 0) AS dana_gereja_rencana,
                    COALESCE(SUM(r.dana_swadaya), 0) AS dana_swadaya_rencana,
                    (SELECT COALESCE(SUM(pg.jumlah), 0) FROM realisasi_pengeluaran_gereja pg 
                    WHERE pg.id_akun = a.id_akun AND pg.id_fiskal = f.id_fiskal) AS realisasi,
                    (SELECT COALESCE(SUM(pg.jumlah), 0) FROM realisasi_pengeluaran_gereja pg
                    WHERE pg.id_akun = a.id_akun AND pg.id_fiskal = f.id_fiskal AND pg.status = 'Tervalidasi') AS dana_gereja_realisasi,
                    0 AS dana_swadaya_realisasi
                    FROM akun a
                    CROSS JOIN fiskal f
                    LEFT JOIN (
                    -- Dari rencana komisi (jumlah = total)
                    SELECT id_akun, id_fiskal, jumlah, dana_gereja, dana_swadaya FROM rencana_pengeluaran_komisi
                    UNION ALL
    
                    -- Dari rencana gereja (jumlah dianggap dana_gereja, lainnya 0)
                    SELECT id_akun, id_fiskal, jumlah AS jumlah, jumlah AS dana_gereja, 0 AS dana_swadaya FROM rencana_pengeluaran_gereja) r ON a.id_akun = r.id_akun AND f.id_fiskal = r.id_fiskal ";


            if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                if ($_POST["tb_bulan"] == 0) {
                    $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = $tahun_aktif
                    GROUP BY f.tahun, a.kode_akun ";
                } else {
                    $nomorBulan = intval($bulan);
                    $sql = " SELECT a.nama_akun AS nama_akun, 0 AS jumlah_rencana, 0 AS dana_gereja_rencana, 0 AS dana_swadaya_rencana,
                                COALESCE(SUM(p.jumlah), 0) AS realisasi,
                                COALESCE(SUM(p.jumlah), 0) AS dana_gereja_realisasi,
                                0 AS dana_swadaya_realisasi
                            FROM akun a
                            LEFT JOIN realisasi_pengeluaran_gereja p ON a.id_akun = p.id_akun AND MONTH(p.tanggal_pengeluaran) = $nomorBulan AND p.id_fiskal = $id_fiskal
                            WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1
                            GROUP BY a.kode_akun ORDER BY a.kode_akun";
                }
            } else if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                if ($_POST["tb_tahun"] == 0) {
                    $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = $tahun_aktif
                    GROUP BY f.tahun, a.kode_akun ";
                } else {
                    $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = " . $_POST["tb_tahun"] . " GROUP BY f.tahun, a.kode_akun";
                }
            } else {
                $sql .= "WHERE a.jenis_debitKredit = 'Debet' AND a.statusAktif = 1 AND f.tahun = $tahun_aktif
                    GROUP BY f.tahun, a.kode_akun ";
            }

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id="" class="table table-bordered table-condensed w-100">
                    <thead>
                        <tr class='small' style="color: #FFFCFC; font-weight: 500; font-size: 16px; border:#FFFCFC;">
                            <td rowspan='2' class="text-center" width='5%'>No</td>
                            <td rowspan='2' class="text-center">Akun</td>
                            <td class='text-center' colspan='3' width=''>Rencana Pengeluaran</td>
                            <td class='text-center' colspan='' width='17%'>Realisasi Pengeluaran</td>
                        </tr>
                        <tr class='small'>
                            <td class='text-center' width='9%'>Dana Gereja</td>
                            <td class='text-center' width='9%'>Dana Swadaya</td>
                            <td class='text-center' width='9%'>Total Anggaran</td>
                            <td class='text-center' width='9%'>Dana Gereja</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $jumlah_rencana = 0;
                        $dana_gereja_rencana = 0;
                        $dana_swadaya_rencana = 0;
                        $subtotal_rencana = 0;
                        $jumlah_realisasi = 0;
                        $dana_gereja_realisasi = 0;
                        $dana_swadaya_realisasi = 0;
                        $subtotal_realisasi = 0;
                        $saldo = 0;
                        foreach ($array as $data) {
                            $cnourut = $cnourut + 1;
                        ?>
                            <tr class=''>
                                <td class="text-right"><?= $cnourut; ?></td>
                                <td><?= $data["nama_akun"]; ?></td>
                                <td class="text-end"><?= number_format($data["dana_gereja_rencana"], 0, ',', '.'); ?></td>
                                <td class="text-end"><?= number_format($data["dana_swadaya_rencana"], 0, ',', '.'); ?></td>
                                <td class="text-end"><?= number_format($data["dana_gereja_rencana"] + $data["dana_swadaya_rencana"], 0, ',', '.'); ?></td>
                                <td class="text-end"><?= number_format($data["dana_gereja_realisasi"], 0, ',', '.'); ?></td>
                            </tr>
                        <?php
                            $jumlah_rencana += $data["jumlah_rencana"];
                            $dana_gereja_rencana += $data["dana_gereja_rencana"];
                            $dana_swadaya_rencana += $data["dana_swadaya_rencana"];
                            $subtotal_rencana += $data["dana_gereja_rencana"] + $data["dana_swadaya_rencana"];
                            $jumlah_realisasi += $data["realisasi"];
                            $dana_gereja_realisasi += $data["dana_gereja_realisasi"];
                            $dana_swadaya_realisasi += $data["dana_swadaya_realisasi"];
                            $subtotal_realisasi += $data["dana_gereja_realisasi"] + $data["dana_swadaya_realisasi"];
                        }
                        ?>
                    </tbody>
                    <tr>
                        <td></td>
                        <td style="color:#5B90CD; font-weight:bolder">T O T A L</td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($dana_gereja_rencana, 0, ',', '.') ?></td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($dana_swadaya_rencana, 0, ',', '.') ?></td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($subtotal_rencana, 0, ',', '.') ?></td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($dana_gereja_realisasi, 0, ',', '.') ?></td>
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