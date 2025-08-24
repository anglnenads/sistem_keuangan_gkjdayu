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
?>

<?php
if (empty($_POST["tb_tahun"])) {
    $_POST["tb_tahun"] = 0;
    unset($_SESSION['tahun']);
}
if (empty($_POST["tb_bulan"])) {
    $_POST["tb_bulan"] = 0;
    unset($_SESSION['bulan']);
} else {
    $bulan = $_POST["tb_bulan"];
}

if (empty($_POST["bidang"])) {
    $_POST["bidang"] = 0;
} else {
    $id_bidang = $_POST["bidang"];
    $sql = "SELECT id_bidang, nama_bidang FROM bidang WHERE id_bidang = $id_bidang";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $bidangKomisi = $row['nama_bidang'];
    }
}

if (empty($_POST["komisi"])) {
    $_POST["komisi"] = 0;
} else {
    $id_komisi = $_POST["komisi"];
    $sql = "SELECT id_komisi, nama_komisi FROM komisi WHERE id_komisi = $id_komisi";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $bidangKomisi = $row['nama_komisi']; 
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("document", "Laporan Realisasi Komisi", "Laporan");
            ?>
        </div>
    </div>

    <div class="section" style="width: 100%; height: 80px;">
        <form action="" method="post">
            <div class="horizontal">
                <div class="form-group1" style="width:50%; margin-left: 30px;">
                    <label for="bidang" class="required">Bidang</label>
                    <select style="width:80%; margin-left: 30px;" id="bidang" name="bidang">
                        <option value="">-- Pilih Bidang --</option>
                        <?php
                        $sql = "SELECT id_bidang, nama_bidang FROM bidang";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id_bidang'] . "' $selected>" . $row['nama_bidang'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>Data tidak tersedia</option>";
                        }
                        if (!empty($_POST['bidang'])) {
                            $_SESSION['bidang'] = $_POST['bidang'];
                        } else {
                            unset($_SESSION['bidang']);
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group1" style="width:50%">
                    <label for="komisi">Komisi</label>
                    <select style="width:80%; margin-left: 30px;" name="komisi" id="komisi">
                        <option value="">-- Pilih Komisi --</option>
                        <?php
                        $idbidang = $_SESSION['bidang'];
                        $query = "SELECT id_komisi, nama_komisi FROM komisi";
                        $result = mysqli_query($conn, $query);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['id_komisi'] . "' $selected>" . $row['nama_komisi'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>Data tidak tersedia</option>";
                        }
                        if (!empty($_POST['komisi'])) {
                            $_SESSION['komisi'] = $_POST['komisi'];
                        } else {
                            unset($_SESSION['komisi']);
                        }
                        ?>
                    </select>
                </div>
                <button name="filterKomisi" style="background-color: #49749C; color:white; border-radius:4px; border:none; width:8%; height: 30px;" type="submit">Pilih</button>
            </div>
        </form>
    </div>

    <br>

    <div style="border-radius:7px; width:100%; height: 7%; justify-content:center; border:1px solid #00008b; font-weight:bold; color:#003153">
        <form action="" method="post" style="display: flex; flex-direction: column; gap: 10px;">

            <input type="hidden" name="bidang" value="<?= $_SESSION['bidang'] ?? '' ?>">
            <input type="hidden" name="komisi" value="<?= $_SESSION['komisi'] ?? '' ?>">

            <div style="display: flex; gap: 50px; margin-top:12px; margin-left:50px;  ">
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
                if (empty($_POST['filter'])) {
                    unset($_SESSION['bulan']);
                    unset($_SESSION['tahun']);
                }
                ?>
            </div>
            </form>
        </div>

        <div style="display: flex; justify-content: end; width:30%;">
            <div class="" style="text-align:center; background-color:#2e8b57; width: 23%; color:white; border-radius:4px">
                <a href="cetak/xlxCetakRealisasiKomisi.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
            </div> &nbsp;&nbsp;
            <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
                <a href="cetak/pdfCetakRealisasiKomisi.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
            </div>
        </div>
    </div>
    <br>
    <div class="sub-title" style=" justify-content: space-between; align-items: center; ">
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

        if (isset($_SESSION['komisi']) || (isset($_SESSION['bidang']))) {
            if (!isset($_POST['filter'])) {
                if (!isset($_SESSION['komisi']) && (!isset($_SESSION['bidang']))){
                     echo "<p>LAPORAN REALISASI KOMISI TAHUN $tahun_aktif</p>";
                } else {
                       echo "<p>LAPORAN REALISASI " . strtoupper($bidangKomisi) . "  TAHUN $tahun_aktif</p>";
                }
            }
            if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                if ($_POST["tb_bulan"] == 0) {
                    echo "<p>LAPORAN REALISASI " . strtoupper($bidangKomisi) . "  TAHUN $tahun_aktif</p>";
                } else {
                    $nama_bulan = $nama_bulan[intval($bulan)];
                    echo "<p>LAPORAN REALISASI " . strtoupper($bidangKomisi) . "</p>";
                    echo "<p>BULAN $nama_bulan TAHUN $tahun_aktif</p>";
                }
            }
            if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                if ($_POST["tb_tahun"] == 0) {
                    echo "<p>LAPORAN REALISASI " . strtoupper($bidangKomisi) . "  TAHUN $tahun_aktif</p>";
                } else {
                    $waktu = $_POST['tb_tahun'];
                    echo "<p>LAPORAN REALISASI " . strtoupper($bidangKomisi) . "  TAHUN $waktu</p>";
                }
            }
        } else {
            if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                if ($_POST["tb_bulan"] == 0) {
                    echo "<p>LAPORAN REALISASI KOMISI TAHUN $tahun_aktif</p>";
                } else {
                    $nama_bulan = $nama_bulan[intval($bulan)];
                    echo "<p>LAPORAN REALISASI KOMISI BULAN $nama_bulan TAHUN $tahun_aktif</p>";
                }
            } else if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                if ($_POST["tb_tahun"] == 0) {
                    echo "<p>LAPORAN REALISASI KOMISI TAHUN $tahun_aktif</p>";
                } else {
                    $waktu = $_POST['tb_tahun'];
                    echo "<p>LAPORAN REALISASI KOMISI TAHUN $waktu</p>";
                }
            } else {
                echo "<p>LAPORAN REALISASI KOMISI TAHUN $tahun_aktif</p>";
            }
        }
        ?>
    </div>
    <br>

    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_SESSION['komisi']) || isset($_SESSION['bidang'])) {
                 if (isset($_SESSION['komisi'])) {
                $sql = "SELECT * from v_realisasikomisi WHERE id_fiskal = $id_fiskal AND id_komisi =" . $_POST["komisi"] . "";
                if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                    if ($_POST["tb_bulan"] == 0) {
                        $sql .= "";
                    } else {
                        $nomorBulan = intval($bulan); 
                        $sql .= " AND  month(tanggal) = " . $nomorBulan;
                    }
                }
                if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                    if ($_POST["tb_tahun"] == 0) {
                        $sql .= " ";
                    } else {
                        $sql = "SELECT * from v_realisasikomisi WHERE id_komisi =" . $_POST["komisi"];
                        $sql .= " AND tahun = " . $_POST["tb_tahun"];
                    }
                }

            } else if (isset($_SESSION['bidang'])) {
                $sql = "SELECT * from v_realisasikomisi WHERE id_fiskal = $id_fiskal AND id_bidang =" . $_POST["bidang"] . "";
                if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                    if ($_POST["tb_bulan"] == 0) {
                        $sql .= "";
                    } else {
                        $nomorBulan = intval($bulan); 
                        $sql .= " AND  month(tanggal) = " . $nomorBulan;
                    }
                }
                if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                    if ($_POST["tb_tahun"] == 0) {
                        $sql .= " ";
                    } else {
                        $sql = "SELECT * from v_realisasikomisi WHERE id_bidang =" . $_POST["bidang"];
                        $sql .= " AND tahun = " . $_POST["tb_tahun"];
                    }
                }
            }
                $sql .= " ORDER BY jenis ASC, tanggal ASC";

                $view = new cView();
                $array = $view->vViewData($sql);
            ?>

                <div id="" class='table-responsive'>
                    <table id='' class='table table-condensed w-100'>
                        <thead>
                            <tr class='small'>
                                <td width='2%' class="text-right">No</td>
                                <td width=''></td>
                                <td width=''>Jenis Kegiatan</td>
                                <td width='6%' class="text-center">Vol</td>
                                <td width='10%' class="text-end">Satuan</td>
                                <td width='' class="text-end">Jumlah</td>
                                <td width='' class="text-end">Dana Gereja</td>
                                <td width='' class="text-end">Dana Swadaya</td>
                                <td width='' class="text-end">Subtotal</td>
                                <td width=''></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cnourut = 0;
                            $groupedData = [];

                            foreach ($array as $data) {
                                $bidang = $data["nama_bidang"];
                                $komisi = $data["nama_komisi"];
                                $jenis = $data["jenis"]; 
                                $program = $data["nama_program"];

                                $groupedData[$bidang][$komisi][$jenis][$program][] = $data;
                            }

                            foreach ($groupedData as $bidang => $bidangList) { ?>
                                <tr style="font-weight: bold; background-color: #dcdcdc;">
                                </tr>

                                <?php foreach ($bidangList as $komisi => $komisiList) { ?>
                                    <tr style="font-weight: bold; background-color: #d3d3d3">
                                        <td colspan="10" style="font-weight: bold; background-color: #f5f5f5"><?= $bidang . " - " . $komisi ?></td>
                                    </tr>

                                    <?php foreach ($komisiList as $jenis => $jenisList) { ?>
                                        <tr>
                                            <td colspan="10" style="font-weight: bold; background-color: #bcd4e6;"><?= $jenis; ?></td>
                                        </tr>

                                        <?php
                                        $total1 = 0;
                                        $total2 = 0;
                                        $total3 = 0;
                                        $total4 = 0;

                                        foreach ($jenisList as $program => $programList) { ?>
                                            <tr>
                                                <td></td>
                                                <td colspan="9" style="font-weight: bold; background-color: #f2f3f4;">Program: <?= $program; ?></td>
                                            </tr>

                                            <?php
                                            $total = 0;
                                            $total_danaGereja = 0;
                                            $total_danaSwadaya = 0;
                                            $subtotal = 0;
                                            $cnourut = 0;

                                            foreach ($programList as $data) {
                                                $cnourut++;
                                                $total += $data["jumlah"];
                                                $total_danaGereja += $data["dana_gereja"];
                                                $total_danaSwadaya += $data["dana_swadaya"];
                                                $subtotal += ($data["dana_gereja"] + $data["dana_swadaya"]);
                                            ?>

                                                <tr>
                                                    <td><?= $cnourut; ?></td>
                                                    <td><?= date("d-m-Y", strtotime($data["tanggal"])); ?></td>
                                                    <td><?= $data["jenis_kegiatan"]; ?></td>
                                                    <td class="text-center"><?= $data["volume"]; ?></td>
                                                    <td class="text-end"><?= number_format($data["harga_satuan"], 0, ',', '.'); ?>/<?= $data["satuan"]?></td>
                                                    <td class="text-end"><?= number_format($data["jumlah"], 0, ',', '.'); ?></td>
                                                    <td class="text-end"><?= number_format($data["dana_gereja"], 0, ',', '.'); ?></td>
                                                    <td class="text-end"><?= number_format($data["dana_swadaya"], 0, ',', '.'); ?></td>
                                                    <td class="text-end"><?= number_format($data["dana_gereja"] + $data["dana_swadaya"], 0, ',', '.'); ?></td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr style="font-weight: bold; background-color: #d9edf7;">
                                                <td></td>
                                                <td></td>
                                                <td>Total</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end"><?= number_format($total, 0, ',', '.'); ?></td>
                                                <td class="text-end"><?= number_format($total_danaGereja, 0, ',', '.'); ?></td>
                                                <td class="text-end"><?= number_format($total_danaSwadaya, 0, ',', '.'); ?></td>
                                                <td class="text-end"><?= number_format($subtotal, 0, ',', '.'); ?></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                            $total1 += $total;
                                            $total2 += $total_danaGereja;
                                            $total3 += $total_danaSwadaya;
                                            $total4 += $subtotal;
                                        }
                                        ?>
                                        <tr style="font-weight: bold; background-color: #d9edf7;">
                                            <td></td>
                                            <td></td>
                                            <td style="color: #1C0F80">Total <?= $jenis ?></td>
                                            <td></td>
                                            <td></td>
                                            <td style="color: #1C0F80" class="text-end"><?= number_format($total1, 0, ',', '.'); ?></td>
                                            <td style="color: #1C0F80" class="text-end"><?= number_format($total2, 0, ',', '.'); ?></td>
                                            <td style="color: #1C0F80" class="text-end"><?= number_format($total3, 0, ',', '.'); ?></td>
                                            <td style="color: #1C0F80" class="text-end"><?= number_format($total4, 0, ',', '.'); ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="10"></td>
                                        </tr>
                            <?php
                                    } 
                                } 
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php
            } else {
                $sql = "SELECT
                        b.nama_bidang,
                        k.nama_komisi,
                        SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN v.dana_gereja ELSE 0 END) AS penerimaan_dana_gereja,
                        SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN v.dana_swadaya ELSE 0 END) AS penerimaan_dana_swadaya,
                        SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN v.jumlah ELSE 0 END) AS jumlah_penerimaan,
                        SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN v.dana_gereja ELSE 0 END) AS pengeluaran_dana_gereja,
                        SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN v.dana_swadaya ELSE 0 END) AS pengeluaran_dana_swadaya,
                        SUM(CASE WHEN v.jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN v.jumlah ELSE 0 END) AS jumlah_pengeluaran
                        FROM bidang b
                        LEFT JOIN komisi k ON k.id_bidang = b.id_bidang
                         LEFT JOIN v_realisasikomisi v 
                            ON v.id_bidang = b.id_bidang 
                             AND (
                            (v.id_komisi IS NULL AND (k.id_komisi IS NULL OR k.id_komisi = 0)) 
                            OR v.id_komisi = k.id_komisi)
                        AND v.id_fiskal = $id_fiskal";

                if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                    if ($_POST["tb_bulan"] == 0) {
                        $sql .= "";
                    } else {
                        $nomorBulan = intval($bulan); 
                        $sql .= " AND  month(v.tanggal) = " . $nomorBulan;
                    }
                }

                if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                    if ($_POST["tb_tahun"] == 0) {
                        $sql .= " ";
                    } else {
                        $sql = "SELECT
                        b.nama_bidang, k.nama_komisi,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN dana_gereja ELSE 0 END) AS penerimaan_dana_gereja,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN dana_swadaya ELSE 0 END) AS penerimaan_dana_swadaya,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Penerimaan' THEN jumlah ELSE 0 END) AS jumlah_penerimaan,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN dana_gereja ELSE 0 END) AS pengeluaran_dana_gereja,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN dana_swadaya ELSE 0 END) AS pengeluaran_dana_swadaya,
                       SUM(CASE WHEN jenis COLLATE utf8mb4_unicode_ci = 'Realisasi Pengeluaran' THEN jumlah ELSE 0 END) AS jumlah_pengeluaran
                   FROM bidang b
                        LEFT JOIN komisi k ON k.id_bidang = b.id_bidang
                        LEFT JOIN v_realisasikomisi v ON
                        v.id_bidang = b.id_bidang AND
                        ((v.id_komisi = k.id_komisi) OR (v.id_komisi IS NULL AND k.id_komisi IS NULL)) AND
                        v.tahun = " . $_POST["tb_tahun"];
                    }  
                }
                
                $sql .= "  GROUP BY b.id_bidang, k.id_komisi ORDER BY b.id_bidang, k.id_komisi";

                $view = new cView();
                $array = $view->vViewData($sql);
            ?>

                <div id="" class='table-responsive'>
                    <table id="" class="table table-bordered table-condensed w-100">
                        <thead>
                            <tr class='small'>
                                <td rowspan="2" width='2%' class="text-center">No</td>
                                <td rowspan="2" width='' class="text-center">Bidang</td>
                                <td rowspan='2' class="text-center">Komisi</td>
                                <td colspan="3" width='' class="text-center">Realisasi Penerimaan</td>
                                <td colspan="3" width='' class="text-center">Realisasi Pengeluaran</td>
                            </tr>
                            <tr class='small'>
                                <td class="text-end">Dana Gereja</td>
                                <td class="text-end">Dana Swadaya</td>
                                <td class="text-end">SubTotal</td>
                                <td class="text-end">Dana Gereja</td>
                                <td class="text-end">Dana Swadaya</td>
                                <td class="text-end">Subtotal</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cnourut = 0;
                            $penerimaan_danaGereja = 0;
                            $penerimaan_danaSwadaya = 0;
                            $penerimaan_subtotal = 0;
                            $pengeluaran_danaGereja = 0;
                            $pengeluaran_danaSwadaya = 0;
                            $pengeluaran_subtotal = 0;

                            foreach ($array as $data) { 
                                $cnourut++ 
                                ?>
                                <tr>
                                    <td><?= $cnourut ?></td>
                                    <td><?= $data['nama_bidang'] ?></td>
                                    <td><?= $data['nama_komisi'] ?></td>
                                    <td class="text-end"><?= number_format($data['penerimaan_dana_gereja'], 0, ',', '.') ?></td>
                                    <td class="text-end"> <?= number_format($data['penerimaan_dana_swadaya'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($data['jumlah_penerimaan'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($data['pengeluaran_dana_gereja'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($data['pengeluaran_dana_swadaya'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= number_format($data['jumlah_pengeluaran'], 0, ',', '.') ?></td>
                                </tr>
                            <?php
                                $penerimaan_danaGereja += $data['penerimaan_dana_gereja'];
                                $penerimaan_danaSwadaya += $data['penerimaan_dana_swadaya'];
                                $penerimaan_subtotal += $data['jumlah_penerimaan'];
                                $pengeluaran_danaGereja += $data['pengeluaran_dana_gereja'];
                                $pengeluaran_danaSwadaya += $data['pengeluaran_dana_swadaya'];
                                $pengeluaran_subtotal += $data['jumlah_pengeluaran'];
                            }
                            ?>
                        </tbody>
                        <tr class=''>
                            <td></td>
                            <td colspan="2">Total</td>
                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($penerimaan_danaGereja, 0, ',', '.') ?> </td>
                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($penerimaan_danaSwadaya, 0, ',', '.') ?> </td>
                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($penerimaan_subtotal, 0, ',', '.') ?> </td>
                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($pengeluaran_danaGereja, 0, ',', '.') ?> </td>
                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($pengeluaran_danaGereja, 0, ',', '.') ?> </td>
                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($pengeluaran_subtotal, 0, ',', '.') ?> </td>
                        </tr>
                    </table>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<script>
    $("#bidang").change(function() {
        var id_bidang = $("#bidang").val();
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
        handleChange("#bidang", "bidang");
        handleChange("#komisi", "komisi");
    });
</script>