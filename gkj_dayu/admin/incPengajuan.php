<?php
// update
if (!empty($_POST["editbtn"])) {
    $linkurl = "";

    if ($_POST["id_program"] == 0) {
        $datafield = array("id_akun", "id_bidang", "id_komisi", "id_program", "id_anggaran", "jenis_kegiatan", "jumlah_pengajuan", "tanggal_pengajuan", "tanggal_proses", "penanggungJawab_pengajuan", "deskripsi_pengajuan");
        $datavalue = array($_POST["id_akun"], $_POST["id_bidang"], $_POST["id_komisi"], $_POST["id_program"], 0, $_POST["jenis_kegiatan"], $_POST["jumlah_pengajuan"],  $_POST["tanggal_pengajuan"], $_POST["tanggal_proses"],  $_POST["penanggungJawab_pengajuan"], $_POST["deskripsi_pengajuan"]);
    } else {
        $datafield = array("id_akun", "id_bidang", "id_komisi", "id_program", "id_anggaran", "jenis_kegiatan", "jumlah_pengajuan", "tanggal_pengajuan", "tanggal_proses", "penanggungJawab_pengajuan", "deskripsi_pengajuan");
        $datavalue = array($_POST["id_akun"], $_POST["id_bidang"], $_POST["id_komisi"], $_POST["id_program"], $_POST["id_anggaran"], NULL, $_POST["jumlah_pengajuan"],  $_POST["tanggal_pengajuan"], $_POST["tanggal_proses"],  $_POST["penanggungJawab_pengajuan"], $_POST["deskripsi_pengajuan"]);
    }

    $datakey = ' id_pengajuan =' . $_POST["id_pengajuan"];

    $update = new cUpdate();
    $update->fUpdateData($datafield, "pengajuan", $datavalue, $datakey, $linkurl);
}

// delete
if (!empty($_POST["btnhapus"])) {
    $delete = new cDelete();
    $delete->_dDeleteData($_POST["hiddendeletevalue0"], $_POST["hiddendeletevalue1"], $_POST["hiddendeletevalue2"]);
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Pengajuan", "Data Pengajuan");
            ?>
        </div>
    </div>

    <div class="row" style="width:12%">
        <?php if ($status_aktif_fiskal == 1): ?>
            <a href="34">
                <button type="button" class="button-add">
                    <ion-icon name="add-circle"></ion-icon> &nbsp; &nbsp; Tambah Data</button>
            </a>
        <?php else: ?>
            <a style="pointer-events: none;">
                <button type="button" class="button-add" style="background-color: #c4c3d0 " disabled>
                    <ion-icon name="add-circle"></ion-icon> &nbsp;&nbsp; Tambah Data
                </button>
            </a>
        <?php endif; ?>
    </div>

    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.id_program as program, a.*, b.id_akun, b.nama_akun,c.id_bidang, c.nama_bidang, 
                    d.id_komisi, d.nama_komisi, e.id_anggaran, e.item, g.id_program, g.nama_program, u1.*,
                    u2.nama AS nama_pemroses, u2.jbtn AS jbtn_pemroses,
                    u3.nama AS nama_validator, u3.jbtn AS jbtn_validator FROM pengajuan a 
                    LEFT JOIN akun b ON a.id_akun = b.id_akun 
                    LEFT JOIN bidang c ON a.id_bidang = c.id_bidang 
                    LEFT JOIN komisi d ON a.id_komisi = d.id_komisi 
                    LEFT JOIN rencana_pengeluaran_komisi e ON a.id_anggaran = e.id_anggaran  
                    LEFT JOIN program g ON e.id_program = g.id_program 
                    LEFT JOIN user u1 ON a.id_user = u1.id_user 
                    LEFT JOIN user u2 ON a.id_pemroses = u2.id_user 
                    LEFT JOIN user u3 ON a.id_penyetuju = u3.id_user ";
            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " ORDER BY tanggal_pengajuan DESC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width=''></td>
                            <td width=''>Jenis Kegiatan</td>
                            <td width='20%'>Nama Akun</td>
                            <td width='10%' class="text-end">Jumlah Pengajuan</td>
                            <td width="22%" class="text-center">Status</td>
                            <td width='1%' class="text-center"></td>
                            <td width="1%"></td>
                            <td width='5%' class="text-center">DETAIL</td>
                            <td width='5%' class="text-center">EDIT</td>
                            <td width='5%' class="text-center">HAPUS</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $groupedData = [];

                        foreach ($array as $data) {
                            $tanggal = $data["tanggal_pengajuan"];
                            $bidang = $data["nama_bidang"];
                            $komisi = $data["nama_komisi"];
                            $program = $data["nama_program"];

                            if ($program == 0) {
                                $program = 'Insidental';
                            }
                            $groupedData[$tanggal][$bidang][$komisi][$program][] = $data;
                        }

                        $number = 0;
                        foreach ($groupedData as $tanggal => $tanggalList) {
                            $firstTanggalRow = true;
                            foreach ($tanggalList as $bidang => $bidangList) {
                                foreach ($bidangList as $komisi => $komisiList) { ?>
                                    <tr style="font-weight: bold;">
                                        <td class="text-center" style="background-color: rgb(223, 240, 248);" width='8%'><?= $firstTanggalRow ? date('d-m-Y', strtotime($tanggal)) : "" ?></td>
                                        <td style="background-color: rgb(223, 240, 248);" width="20%"><?= $bidang  ?></td>
                                        <td style="background-color: rgb(223, 240, 248);"><?= $komisi ?></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                        <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                        <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                    </tr>
                                    <?php

                                    foreach ($komisiList as $program => $programList) {
                                        $number = $number + 1; ?>
                                        <tr>
                                            <td width='8%' class="text-center"><?= $number; ?></td>
                                            <td colspan="" style="background-color:#f2f3f4; font-weight: bold;" width="20%">Program : <?= $program ?></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;" width='5%'></td>
                                            <td style="background-color: #f2f3f4;" width='5%'></td>
                                            <td style="background-color: #f2f3f4;" width='5%'></td>
                                        </tr>
                                        <?php
                                        $total = 0;
                                        $id_pengajuan = [];
                                        foreach ($programList as $data) {
                                            $cnourut = $cnourut + 1;

                                            $id_pengajuan[] = $data["id_pengajuan"];
                                        ?>
                                            <tr class=''>
                                                <td></td>
                                                <td>
                                                    <?php
                                                    if ($data["nama_program"] == 0) {
                                                        echo $data["jenis_kegiatan"];
                                                    } else {
                                                        echo $data["item"];
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $data["nama_akun"]; ?></td>
                                                <td class="text-end"><?= number_format($data["jumlah_pengajuan"], 0, ',', '.'); ?></td>
                                                <td class="text-center"><?= $data["deskripsi_pengajuan"]; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">
                                                    <?php
                                                    $datadetail = array(
                                                        array("Tanggal Pengajuan", ":", date('d-m-Y', strtotime($data["tanggal_pengajuan"])), 1, ""),
                                                        array("Bidang", ":", $data["nama_bidang"], 1, ""),
                                                        array("Komisi", ":", $data["nama_komisi"], 1, ""),
                                                        array(
                                                            "Program",
                                                            ":",
                                                            ($data["nama_program"] == 0 ? "Insidental" : $data["nama_program"]),
                                                            1,
                                                            "",
                                                        ),
                                                        array("Akun", ":", $data["nama_akun"], 1, ""),
                                                        array(
                                                            "Jenis Kegiatan",
                                                            ":",
                                                            ($data["nama_program"] == 0 ? $data["jenis_kegiatan"] : $data["item"]),
                                                            1,
                                                            "",
                                                        ),
                                                        array("Jumlah Pengajuan", ":", 'Rp. ' . number_format($data["jumlah_pengajuan"], 0, ',', '.'), 1, ""),
                                                        array("Penanggung Jawab", ":", $data["penanggungJawab_pengajuan"], 1, ""),
                                                        array("Keterangan", ":", $data["deskripsi_pengajuan"], 1, ""),
                                                        array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                                        array("Status", ":", $data["status"], 1, ""),
                                                        array(
                                                            "Tanggal Proses",
                                                            ":",
                                                            $data["tanggal_proses"] == "0000-00-00" ? "-" : date('d-m-Y', strtotime($data["tanggal_proses"])),
                                                            1,
                                                            ":"
                                                        ),
                                                        array("Diproses oleh", ":", $data["nama_pemroses"] . " - " . $data["jbtn_pemroses"], 1),
                                                        array(
                                                            "Tanggal Pencairan",
                                                            ":",
                                                            $data["tanggal_transfer"] == "0000-00-00" ? "-" : date('d-m-Y', strtotime($data["tanggal_transfer"])),
                                                            1,
                                                            ""
                                                        ),
                                                        array("Dicairkan oleh", ":", $data["nama_validator"] . " - " . $data["jbtn_validator"], 1),
                                                    );
                                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Pengajuan#Data Pengajuan $cnourut", "", $datadetail, "", "23", "");
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $disabled = ($status_aktif_fiskal == 1 && $data["status"] == 'Menunggu') ? false : true;

                                                    if ($data["nama_program"] == 0) {
                                                        $array1 =
                                                            array("Program", "id_program", 0, 2, "");
                                                        $array2 =
                                                            array("Jenis Kegiatan", "jenis_kegiatan", $data["jenis_kegiatan"], 1, "");
                                                    } else {
                                                        $array1 =
                                                            array(
                                                                "Program",
                                                                "id_program",
                                                                $data["id_program"],
                                                                5,
                                                                "SELECT a.id_program AS field1, a.nama_program AS field2 FROM program a
                                                                WHERE a.id_fiskal = $id_fiskal AND a.id_bidang = " . $data["id_bidang"]
                                                            );
                                                        $array2 =
                                                            array(
                                                                "Jenis Kegiatan",
                                                                "id_anggaran",
                                                                $data["id_anggaran"],
                                                                5,
                                                                "SELECT a.id_anggaran AS field1, CONCAT(p.nama_program, ' - ', a.item) AS field2 FROM rencana_pengeluaran_komisi a
                                                            LEFT JOIN program p ON a.id_program = p.id_program WHERE a.id_fiskal = $id_fiskal"
                                                            );
                                                    }
                                                    $dataupdate = array(
                                                        array("ID", "id_pengajuan", $data["id_pengajuan"], 2, ""),
                                                        array("Tanggal Pengajuan", "tanggal_pengajuan", $data["tanggal_pengajuan"], 14, ""),
                                                        array("Bidang", "id_bidang", $data["id_bidang"], 5, "select id_bidang field1, nama_bidang field2 from bidang"),
                                                        array("Komisi", "id_komisi", $data["id_komisi"], 51, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                        array("Akun", "id_akun", $data["id_akun"], 5, "select id_akun field1, nama_akun field2 from akun WHERE status_input = 1 AND jenis_debitKredit = 'Debet' ORDER BY kode_akun ASC"),
                                                        $array1,
                                                        $array2,
                                                        array("Jumlah Pengajuan", "jumlah_pengajuan", $data["jumlah_pengajuan"], 1, ""),
                                                        array("Tanggal Proses", "tanggal_proses", $data["tanggal_proses"], 11, ""),
                                                        array("Tanggal Pencairan", "tanggal_transfer", $data["tanggal_transfer"] == "0000-00-00" ? "-" : date('d-m-Y', strtotime($data["tanggal_transfer"])), 11, ""),
                                                        array("Penanggung Jawab", "penanggungJawab_pengajuan", $data["penanggungJawab_pengajuan"], 1, ""),
                                                        array("Keterangan", "deskripsi_pengajuan", $data["deskripsi_pengajuan"], 17, ""),
                                                    );
                                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                                    _CreateWindowModalUpdate("edit" . $cnourut, "edit", "edit-form", "edit-button", "lg", "", 'Edit Data Pengajuan#Data Pengajuan ' . $cnourut, "", $dataupdate, "", "24", $disabled);
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php
                                                    $disabled = ($status_aktif_fiskal == 1 && $data["status"] == 'Menunggu') ? false : true;
                                                    //$disabled = false;
                                                    $datadelete = array(
                                                        array("id_pengajuan", $data["id_pengajuan"], "pengajuan")
                                                    );
                                                    _CreateWindowModalDelete($cnourut, "del", "del-form", "del-button", "md", 200, "Hapus Data Pengajuan#Data Pengajuan $cnourut", "", $datadelete, "24", $disabled);
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                            $total += $data["jumlah_pengajuan"];
                                        }
                                        $all_ids_string = implode(',', $id_pengajuan); ?>
                                        <tr>
                                            <td></td>
                                            <td style="color:#5B90CD; font-weight:bolder">Total</td>
                                            <td></td>

                                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
                                            <?php
                                            //  $query = "SELECT pengajuan.status, 
                                            // ( SELECT SUM(pencairan.jumlah_pencairan) 
                                            //         FROM pencairan
                                            //         WHERE pencairan.id_program = pengajuan.id_program
                                            //         AND pencairan.id_fiskal = pengajuan.id_fiskal) AS jumlah_pencairan
                                            //         FROM pengajuan
                                            //         WHERE pengajuan.id_program = " . intval($data["program"]) . "
                                            //         AND pengajuan.id_fiskal = $id_fiskal
                                            //         LIMIT 1";

                                            $query = "SELECT pengajuan.status, 
                                            (SELECT SUM(pencairan.jumlah_pencairan) FROM pencairan
                                            WHERE pencairan.id_program = pengajuan.id_program
                                            AND pencairan.id_fiskal = pengajuan.id_fiskal
                                            AND (pencairan.id_komisi = pengajuan.id_komisi OR 
                                            (pengajuan.id_komisi IS NULL AND pencairan.id_bidang = pengajuan.id_bidang))
                                            ) AS jumlah_pencairan
                                            FROM pengajuan
                                            WHERE pengajuan.id_program = " . intval($data["program"]) . "
                                            AND pengajuan.id_fiskal = $id_fiskal";

                                            if (!empty($data["komisi"])) {
                                                $query .= " AND pengajuan.id_komisi = " . intval($data["komisi"]);
                                            } elseif (!empty($data["bidang"])) {
                                                $query .= " AND pengajuan.id_bidang = " . intval($data["bidang"]);
                                            }

                                            $query .= " LIMIT 1";
                                            $view = new cView();
                                            $array = $view->vViewData($query);
                                            if (!empty($array)) {
                                                $jumlah_pencairan = $array[0]['jumlah_pencairan'];
                                                $status_pengajuan = $data['status'];
                                            }

                                            $color = "black";
                                            switch ($data["status"]) {
                                                case "Disetujui":
                                                    $color = "#2e8b57";
                                                    break;
                                                case "Diproses":
                                                    $color = "#007bb8";
                                                    break;
                                                case "Menunggu":
                                                    $color = "#91a3b0";
                                                    break;
                                                case "Ditolak":
                                                    $color = "#b22222";
                                                    break;
                                                case "Disetujui dan Dana telah Cair":
                                                    $color = "#2453a3";
                                                    break;
                                            }
                                            ?>
                                            <td class="text-center" style="font-weight:650; color: <?= $color; ?>;">
                                                <?= $data["status"]; ?> <?= ($status_pengajuan == 'Disetujui dan Dana telah Cair') ? 'Rp.' . number_format($jumlah_pencairan, 0, ',', '.') : "" ?>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                        // $query = "SELECT pengajuan.status, SUM(pencairan.jumlah_pencairan) AS jumlah_pencairan
                                        //             FROM pengajuan
                                        //             LEFT JOIN pencairan ON pencairan.id_program = pengajuan.id_program
                                        //             AND pencairan.id_fiskal = pengajuan.id_fiskal
                                        //             WHERE pengajuan.id_program = " . intval($data["program"]) . "
                                        //             AND pengajuan.id_fiskal = $id_fiskal
                                        //             GROUP BY pengajuan.status";

                                        // $view = new cView();
                                        // $array = $view->vViewData($query);
                                        // if (!empty($array)) {
                                        //     $jumlah_pencairan = $array[0]['jumlah_pencairan'];
                                        //     $status_pengajuan = $data['status'];
                                        // }
                                        ?>
                                        <!-- <tr>
                                            <td></td>
                                            <td style="color:#322E7D; font-weight:bolder">Jumlah Dana yang Sudah Dicairkan </td>
                                            <td></td>
                                            <td style="color:#322E7D; font-weight:bolder" class="text-end"><?= ($status_pengajuan == 'Disetujui dan Dana telah Cair') ? number_format($jumlah_pencairan, 0, ',', '.') : 0 ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="color:#322E7D; font-weight:bolder">Jumlah Dana yang Belum Dicairkan </td>
                                            <td></td>
                                            <td style="color:#322E7D; font-weight:bolder" class="text-end"><?= ($status_pengajuan == 'Disetujui dan Dana telah Cair') ? number_format($total - $jumlah_pencairan, 0, ',', '.') : 0 ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr> -->
                        <?php }
                                }
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>