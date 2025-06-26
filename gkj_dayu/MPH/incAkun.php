<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php _myHeader("folder", "Akun", "Data Akun"); ?>
        </div>
    </div>

    <br>
    <div style="border-radius:7px; width:100%; height: 7%; justify-content:center; border:1px solid #00008b; font-weight:bold; color:#003153">
        <form action="" method="post" style="display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; gap: 50px; margin-top:12px; margin-left:20px;  ">
                <label>
                    <input type="radio" name="filter" value="penerimaan"
                        <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'penerimaan') ? 'checked' : ''; ?>
                        onchange="this.form.submit()"> Penerimaan
                </label>
                <label>
                    <input type="radio" name="filter" value="pengeluaran"
                        <?php echo (isset($_POST['filter']) && $_POST['filter'] == 'pengeluaran') ? 'checked' : ''; ?>
                        onchange="this.form.submit()"> Pengeluaran
                </label>
            </div>
        </form>
    </div>

    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_POST['filter']) && $_POST['filter'] == 'penerimaan') {
                $sql = "SELECT a.*, b.jenis_akun as jenis FROM akun a  JOIN akunjenis b on a.jenis_akun = b.id_jenisAkun WHERE jenis_debitKredit = 'Kredit' ORDER BY kode_akun ASC";
            } else if (isset($_POST['filter']) && $_POST['filter'] == 'pengeluaran') {
                $sql = "SELECT a.*, b.jenis_akun as jenis FROM akun a  JOIN akunjenis b on a.jenis_akun = b.id_jenisAkun WHERE jenis_debitKredit = 'Debet' ORDER BY kode_akun ASC";
            } else if (!isset($_POST['filter'])) {
                $sql = "SELECT a.*, b.jenis_akun as jenis FROM akun a  JOIN akunjenis b on a.jenis_akun = b.id_jenisAkun ORDER BY kode_akun ASC";
            }
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>

            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Kode Akun</td>
                            <td width=''>Nama Akun</td>
                            <td width=''>Debet /Kredit</td>
                            <td width=''>Deskripsi</td>
                            <td width=''>Status Aktif</td>
                            <td width=''>Status Input</td>
                            <td width='5%' class="text-center">DETAIL</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;

                        foreach ($array as $data) {
                            $jenis_debitKredit = $data["jenis_debitKredit"];
                            $jenis = $data["jenis"];

                            $groupedData[$jenis_debitKredit][$jenis][] = $data;
                        }

                        foreach ($groupedData as $jenis_debitKredit => $debetKredit) { ?>
                            <tr style="font-weight: bold;">
                                <td style="background-color: #dcdcdc;"></td>
                                <td style="background-color: #dcdcdc;" width="10%"><?= $jenis_debitKredit === "Kredit" ? "Penerimaan" : "Pengeluaran"; ?></td>
                                <td style="background-color: #dcdcdc;"></td>
                                <td style="background-color: #dcdcdc;"></td>
                                <td style="background-color: #dcdcdc;"></td>
                                <td style="background-color: #dcdcdc;"></td>
                                <td style="background-color: #dcdcdc;"></td>
                                <td style="background-color: #dcdcdc;"></td>
                            </tr>

                            <?php
                            foreach ($debetKredit as $jenis => $jenisAkun) { ?>

                                <tr style="font-weight: bold;">
                                    <td></td>
                                    <td style="background-color: #f4f0ec;" width="10%"><?= $jenis ?></td>
                                    <td style="background-color: #f4f0ec;"></td>
                                    <td style="background-color: #f4f0ec;"></td>
                                    <td style="background-color: #f4f0ec;"></td>
                                    <td style="background-color: #f4f0ec;"></td>
                                    <td style="background-color: #f4f0ec;"></td>
                                    <td style="background-color: #f4f0ec;"></td>
                                </tr>
                                <?php

                                foreach ($jenisAkun as $data) {
                                    $cnourut = $cnourut + 1;
                                ?>
                                    <tr class=''>
                                        <td class="text-right"><?= $cnourut; ?></td>
                                        <td><?= $data["kode_akun"]; ?></td>
                                        <td><?= $data["nama_akun"]; ?></td>
                                        <td><?= $data["jenis_debitKredit"]; ?></td>
                                        <td><?= $data["deskripsi"]; ?></td>
                                        <td><?= $data["statusAktif"] == 1 ? "Aktif" : "Tidak Aktif"; ?></td>
                                        <td><?= $data["status_input"] == 1 ? "Ya" : "Tidak"; ?></td>
                                        <td class="text-center">
                                            <?php
                                            $datadetail = array(
                                                array("Kode Akun", ":", $data["kode_akun"], 1, ""),
                                                array("Nama Akun", ":", $data["nama_akun"], 1, ""),
                                                array("Jenis Akun", ":", $data["jenis"], 1, ""),
                                                array("Debet/Kredit", ":", $data["jenis_debitKredit"], 1, ""),
                                                array("Deskripsi", ":", $data["deskripsi"], 3, ""),
                                                array("Status Aktif", ":", $data["statusAktif"] == 1 ? "Aktif" : "Tidak Aktif", 1, ""),
                                                array("Status Input", ":", $data["status_input"] == 1 ? "Ya" : "Tidak", 1, ""),
                                            );
                                            _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Akun#Data Akun $cnourut : " . $data["nama_akun"], "", $datadetail, "", "12", "");
                                            ?>
                                        </td>
                                    </tr>
                        <?php }
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>