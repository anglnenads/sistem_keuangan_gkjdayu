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
            _myHeader("document", "Rekapitulasi Rencana dan Realisasi Penerimaan Gereja", "Laporan ");
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
                <a href="http://localhost:80/gkj_dayu/Cetak/xlxCetakPenerimaanAkun.php" target="" style="color:white; text-decoration: none;">Cetak Excel</a>
            </div> &nbsp;&nbsp;
            <div class="" style="text-align:center; background-color:#dc143c; width: 23%; color:white; border-radius:4px">
                <a href="http://localhost:80/gkj_dayu/Cetak/pdfCetakPenerimaanAkun.php" target="_new" style="color:white; text-decoration: none;">Cetak PDF</a>
            </div>
        </div>
    </div>
    <br>
    <div class="sub-title" style=" justify-content: space-between; align-items: center; ">
        <p>RENCANA DAN REALISASI PENERIMAAN GEREJA

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
            fiskal.tahun AS tahun,
            akun.jenis_akun AS jenis_akun,
            akun.kode_akun AS kode_akun,
            akun.nama_akun AS nama_akun,
            COALESCE(rencana_rencana.jumlah_penerimaan, 0) AS jumlah_rencana,
            COALESCE(SUM(realisasi_penerimaan_gereja.jumlah_penerimaan), 0) AS jumlah_realisasi
          FROM (( fiskal JOIN akun ON (1 = 1))
            LEFT JOIN (
                SELECT r.id_fiskal AS id_fiskal, r.id_akun AS id_akun, SUM(r.jumlah_penerimaan) AS jumlah_penerimaan
                FROM rencana_penerimaan_gereja r
                GROUP BY r.id_fiskal, r.id_akun
              ) AS rencana_rencana 
              ON (akun.id_akun = rencana_rencana.id_akun AND fiskal.id_fiskal = rencana_rencana.id_fiskal))
            LEFT JOIN realisasi_penerimaan_gereja 
            ON ( akun.id_akun = realisasi_penerimaan_gereja.id_akun AND fiskal.id_fiskal = realisasi_penerimaan_gereja.id_fiskal AND realisasi_penerimaan_gereja.status = 'Tervalidasi')";

            if (isset($_POST['filter']) && $_POST['filter'] == 'bulan') {
                if ($_POST["tb_bulan"] == 0) {
                    $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND fiskal.tahun = $tahun_aktif
                    GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";
                } else {
                    $nomorBulan = intval($bulan);
                    $sql = "SELECT a.nama_akun AS nama_akun, 0 AS jumlah_rencana, COALESCE(SUM(p.jumlah_penerimaan), 0) AS jumlah_realisasi ";
                    $sql .= "FROM akun a ";
                    $sql .= "LEFT JOIN realisasi_penerimaan_gereja p ON a.id_akun = p.id_akun AND MONTH(p.tanggal_penerimaan) = $nomorBulan AND p.id_fiskal = $id_fiskal ";
                    $sql .= " WHERE a.jenis_debitKredit = 'Kredit' AND a.statusAktif = 1 GROUP BY a.id_akun";
                }
            } else if (isset($_POST['filter']) && $_POST['filter'] == 'tahun') {
                if ($_POST["tb_tahun"] == 0) {
                    $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND fiskal.tahun = $tahun_aktif
                    GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";
                } else {
                    $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND tahun = " . $_POST["tb_tahun"] . " 
                            GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";
                }
            } else {
                $sql .= " WHERE akun.jenis_debitKredit = 'Kredit' AND akun.statusAktif = 1 AND fiskal.tahun = $tahun_aktif
                GROUP BY fiskal.tahun, akun.id_akun ORDER BY fiskal.tahun, akun.id_akun";
            }
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id="" class="table table-bordered table-condensed w-100">
                    <thead>
                        <tr class='small'>
                            <td class='text-center' width=''>No</td>
                            <td class='text-center' colspan="3" width=''>Akun</td>
                            <td class='text-center' width=''>Jumlah Rencana Penerimaan</td>
                            <td class='text-center' width=''>Jumlah Realisasi Penerimaan</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $jumlah_rencana = 0;
                        $jumlah_realisasi = 0;

                        foreach ($array as $data) {
                            $cnourut = $cnourut + 1;
                        ?>
                            <tr class=''>
                                <td class="text-center"><?= $cnourut; ?></td>
                                <td colspan="3"><?= $data["nama_akun"]; ?></td>
                                <td class="text-end"><?= number_format($data["jumlah_rencana"], 0, ',', '.'); ?></td>
                                <td class="text-end"><?= number_format($data["jumlah_realisasi"], 0, ',', '.'); ?></td>
                            </tr>
                        <?php
                            $jumlah_rencana += $data["jumlah_rencana"];
                            $jumlah_realisasi += $data["jumlah_realisasi"];
                        }
                        ?>
                    </tbody>
                    <tr>
                        <td></td>
                        <td style="color:#5B90CD; font-weight:bolder" colspan="3">T O T A L</td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($jumlah_rencana, 0, ',', '.') ?></td>
                        <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($jumlah_realisasi, 0, ',', '.') ?></td>
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