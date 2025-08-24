<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uploadPath = "/uploads/bukti_transfer/";

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php _myHeader("folder", "Pencairan Dana", "Data Pencairan"); ?>
        </div>
    </div>
   
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, 
                     b.id_fiskal, b.tahun,
                    c.id_bidang, c.nama_bidang,
                    d.id_komisi, d.nama_komisi, 
                       e.id_akun, e.nama_akun, 
                    f.id_bank, f.nama_bank, f.nama_rekening,
                    g.id_program, g.nama_program,
                    u.id_user, u.nama, u.jbtn
                    FROM pencairan a 
                    LEFT JOIN fiskal b ON a.id_fiskal = b.id_fiskal
                    LEFT JOIN bidang c ON a.id_bidang = c.id_bidang 
                    LEFT JOIN komisi d ON a.id_komisi = d.id_komisi 
                     LEFT JOIN akun e ON a.id_akun = e.id_akun 
                    LEFT JOIN bank f ON a.id_bank = f.id_bank
                    LEFT JOIN program g ON a.id_program = g.id_program  
                    LEFT JOIN user u ON a.id_user = u.id_user ";

            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . "  ORDER BY c.id_bidang, d.id_komisi, tanggal_pencairan ASC, a.id_program ASC";

            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Tanggal Pencairan</td>
                            <td width='15%'>Nama Program</td>
                            <td width='15%' class="text-end">Jumlah Pencairan</td>
                            <td width='6%'></td>
                            <td width='' class="text-center">Bank Penerima</td>
                            <td width='5%' class="text-center">DETAIL</td>
                       
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;

                        foreach ($array as $data) {
                            $bidang = $data["nama_bidang"];
                            $komisi = $data["nama_komisi"];

                            $groupedData[$bidang][$komisi][] = $data;
                        }

                        $total_bidang = 0;
                        if (empty($groupedData)) { ?>
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td></td>
                                <td class="text-end">Tidak ada data</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td width='5%'></td>
                            </tr>
                            <?php
                        } else {

                            foreach ($groupedData as $bidang => $bidangList) {
                                $firstBidangRow = true;
                            ?>
                                <?php

                                foreach ($bidangList as $komisi => $komisiList) {  ?>
                                    <tr style="font-weight: bold;">
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;" width='17%'><?= $firstBidangRow ? $bidang : ""; ?></td>
                                        <td style="background-color: #f2f3f4;" width="20%"><?= $komisi ?></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;"></td>
                                        <td style="background-color: #f2f3f4;" width='5%'></td>
                                    

                                    </tr>
                                    <?php
                                    $countprogram = 0; 

                                    $total = 0;
                                    foreach ($komisiList as $data) {
                                        $cnourut = $cnourut + 1;

                                    ?>
                                        <tr class=''>
                                            <td class="text-right"><?= $cnourut; ?></td>
                                            <td><?= date('d-m-Y', strtotime($data["tanggal_pencairan"])); ?></td>
                                            <td><?= !empty($data["nama_program"]) ? $data["nama_program"] : "Insidental"; ?></td>

                                            <td class="text-end"><?= number_format($data["jumlah_pencairan"], 0, ',', '.'); ?></td>
                                            <td></td>
                                            <td class="text-center"><?= !empty($data["nama_bank"]) && !empty($data["nama_rekening"]) ? $data["nama_bank"] . " - " . $data["nama_rekening"] : "Cash"; ?></td>


                                            <td class="text-center">
                                                <?php
                                                $buktiTransferFile = htmlspecialchars($data["bukti_transfer"] ?? '', ENT_QUOTES, 'UTF-8');
                                                $datadetail = array(
                                                    array("Tanggal Pencairan", ":", date('d-m-Y', strtotime($data["tanggal_pencairan"])), 1, ""), 
                                                    array("Bidang", ":", $data["nama_bidang"], 1, ""),
                                                    array("Komisi", ":", $data["nama_komisi"], 1, ""),
                                                    array("Program", ":", $data["nama_program"], 1, ""),
                                                    array("Akun", ":", $data["nama_akun"], 1, ""),
                                                    array("Keterangan Pencairan", ":", $data["keterangan"], 1, ""),
                                                    array("Jumlah Pencairan", ":", 'Rp. ' . number_format($data["jumlah_pencairan"], 0, ',', '.'), 1, ""),
                                                    array("Tanggal Pencairan", ":", date('d-m-Y', strtotime($data["tanggal_pencairan"])), 1, ""),
                                                array("Bukti Transfer/Pencairan", ":", "<a href='{$protocol}://{$host}{$uploadPath}{$buktiTransferFile}' target='_blank'>{$buktiTransferFile}</a>", 1),
                                                    array("Dicairkan oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                                );
                                                _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Pencairan#Data Pencairan $cnourut", "", $datadetail, "", "23", "");
                                                ?>
                                            </td>
                                        </tr>
                                    <?php $total += $data['jumlah_pencairan'];
                                    }
                                    ?>
                                    <tr>
                                        <td width='5%' class="text-right"></td>
                                        <td width=''></td>
                                        <td width='' style="font-weight: bold;">Total Pencairan (Per Komisi)</td>
                                        <td width='6%' class="text-end" style="font-weight: bold;"><?= number_format($total, 0, ',', '.') ?></td>
                                        <td width='' class="text-end"></td>
                                        <td width=''></td>
                                        <td width='5%' class="text-center"></td>
                                    </tr>
                        <?php

                                    $total_bidang += $total;
                                }
                            }
                        } ?>

                    </tbody>
                    <tr>
                        <td width='5%' class="text-right"></td>
                        <td width=''></td>
                        <td width='' style="font-weight: bold; color:#5B90CD">Total Pencairan Keseluruhan</td>
                        <td width='6%' class="text-end" style="font-weight: bold; color:#5B90CD"><?= number_format($total_bidang, 0, ',', '.') ?></td>
                        <td width='' class="text-end"></td>
                        <td width=''></td>
                        <td width='5%' class="text-center"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</div>