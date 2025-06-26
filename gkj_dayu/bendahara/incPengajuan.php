<?php
if (!empty($_POST["btnproses"])) {

    $tanggalProses = date('Y-m-d');

    $datafield = array("status", "id_pemroses", "tanggal_proses");
    $datavalue = array("'Diproses'", $id_user, "'" . $tanggalProses . "'");

    $update = new cUpdate();
    $update->_pengajuanStatus($_POST["hiddenupdatevalue0"], $_POST["hiddenupdatevalue1"], $_POST["hiddenupdatevalue2"], $datafield, $datavalue);
} else if (!empty($_POST["btnsetuju"])) {

    $tanggalSetuju = date('Y-m-d');

    $datafield = array("status");
    $datavalue = array("'Disetujui'");

    $update = new cUpdate();
    $update->_pengajuanStatus($_POST["hiddenupdatevalue0"], $_POST["hiddenupdatevalue1"], $_POST["hiddenupdatevalue2"], $datafield, $datavalue);
} else if (!empty($_POST["btntolak"])) {

    $tanggalTolak = date('Y-m-d');

    $datafield = array("status");
    $datavalue = array("'Ditolak'");

    $update = new cUpdate();
    $update->_pengajuanStatus($_POST["hiddenupdatevalue0"], $_POST["hiddenupdatevalue1"], $_POST["hiddenupdatevalue2"], $datafield, $datavalue);
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

    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_program as program, a.*, b.id_akun, b.nama_akun,c.id_bidang, c.nama_bidang, 
                    d.id_komisi, d.nama_komisi, e.id_anggaran, e.item, g.id_program, g.nama_program, u1.*,
                    u2.nama AS nama_pemroses, u2.jbtn AS jbtn_pemroses,
                    u3.nama AS nama_penyetuju, u3.jbtn AS jbtn_penyetuju FROM pengajuan a 
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
                            <td width='3%'></td>
                            <td width=''>Jenis Kegiatan</td>
                            <td width='18%'>Nama Akun</td>
                            <td width='10%' class="text-end">Jumlah Pengajuan</td>
                            <td width="17%" class="text-center">Status</td>
                            <td width='12%' class="text-center"></td>
                            <td width="1%"></td>
                            <td width='5%' class="text-center">DETAIL</td>
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
                                        <td class="text-center" style="background-color: rgb(223, 240, 248);" width='7%'><?= $firstTanggalRow ? date('d-m-Y', strtotime($tanggal)) : "" ?></td>
                                        <td style="background-color: rgb(223, 240, 248);" width="20%"><?= $bidang  ?></td>
                                        <td style="background-color: rgb(223, 240, 248);"><?= $komisi ?></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);"></td>
                                        <td style="background-color: rgb(223, 240, 248);" width='5%'></td>
                                    </tr>
                                    <?php

                                    foreach ($komisiList as $program => $programList) {
                                        $number = $number + 1; ?>
                                        <tr>
                                            <td width='5%' class="text-center"><?= $number; ?></td>
                                            <td colspan="" style="background-color:#f2f3f4; font-weight: bold;" width="20%">Program : <?= $program ?></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
                                            <td style="background-color: #f2f3f4;"></td>
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
                                                        array("Komisi", ":", $data["nama_komisi"] ?? '-', 1, ""),
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
                                                        array("Keterangan", ":", !empty($data["deskripsi_pengajuan"]) ? $data["deskripsi_pengajuan"] : '-', 1, ""),
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
                                                        array("Dicairkan oleh", ":", $data["nama_penyetuju"] . " - " . $data["jbtn_penyetuju"], 1),
                                                    );
                                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Pengajuan#Data Pengajuan $cnourut", "", $datadetail, "", "23", "");
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
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $datavalid = array(
                                                    array("id_pengajuan", $all_ids_string, "pengajuan"),
                                                );
                                                
                                                if ($data["status"] == "Menunggu") {
                                                    $caption = "Ingin proses pengajuan ini ?";
                                                    $disabled = ($status_aktif_fiskal == 1) ? false : true;
                                                    _CreateWindowModalProses($cnourut, "val", "val-form", "val-button", "md", 200, "Proses Pengajuan# Program : " . $data['nama_program'] . "#Total Pengajuan: Rp. " . number_format($total, 0, ',', '.'), $datavalid, "24", $disabled, "Proses", $caption);
                                                } else if ($data["status"] == "Diproses") {
                                                    $caption = "Ingin menyetujui pengajuan ini ?";
                                                    $disabled = ($status_aktif_fiskal == 1 && $data["status"] == "Diproses") ? false : true;
                                                    _CreateWindowModalValid($cnourut, "val", "val-form", "val-button", "md", 200, "Persetujuan Pengajuan# Program : " . $data['nama_program'] . "#Total Pengajuan: Rp. " . number_format($total, 0, ',', '.'), $datavalid, "24", $disabled, "Setujui", $caption);
                                                } else {
                                                    $caption = "Ingin menyetujui pengajuan ini ?";
                                                    _CreateWindowModalValid($cnourut, "val", "val-form", "val-button", "md", 200, "Persetujuan Pengajuan# Program : " . $data['nama_program'] . "#Total Pengajuan: Rp. " . number_format($total, 0, ',', '.'), $datavalid, "24",   $data["status"] != "Diproses", "Setujui", $caption);
                                                }
                                                ?>
                                            </td>
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
                                        </tr>
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