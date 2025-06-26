<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php _myHeader("newspaper", "Program", "Data Program"); ?>
        </div>
    </div>

    <p></p>

    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_bidang as bidang, a.id_komisi as komisi, a.*, b.*, c.*, d.*, e.*
            FROM program a 
            LEFT JOIN bidang b ON a.id_bidang = b.id_bidang 
            LEFT JOIN komisi c ON a.id_komisi = c.id_komisi 
            LEFT JOIN fiskal d ON a.id_fiskal = d.id_fiskal 
            LEFT JOIN user e ON a.id_user = e.id_user ";
            $sql .= "WHERE a.id_fiskal = " . $id_fiskal . " ORDER BY b.id_bidang, c.id_komisi, a.id_program ASC";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>

            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td class="text-right">No</td>
                            <td></td>
                            <td width=''>Nama Program</td>
                            <td width=''>Tanggal Mulai</td>
                            <td width=''>Tanggal Selesai</td>
                            <td width=''>Penanggung Jawab</td>
                            <td width='5%' class="text-center">DETAIL</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $groupedData = [];

                        foreach ($array as $data) {
                            $bidang = $data["nama_bidang"];
                            $komisi = $data["nama_komisi"];

                            $groupedData[$bidang][$komisi][] = $data;
                        }

                        foreach ($groupedData as $bidang => $bidangList) {
                            $firstBidangRow = true;
                            foreach ($bidangList as $komisi => $komisiList) { ?>
                                <tr style="font-weight: bold;">
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;" width='10%' class="text-left"><?= $firstBidangRow ? $bidang : ""; ?></td>
                                    <td style="background-color: #f2f3f4;" width="28%"><?= $komisi ?></td>
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;"></td>
                                    <td style="background-color: #f2f3f4;" width='5%'></td>
                                </tr>
                                <?php
                                $countprogram = 0;
                                foreach ($komisiList as $data) {
                                    $cnourut = $cnourut + 1;
                                    $countprogram = $countprogram + 1;
                                ?>
                                    <tr class=''>
                                        <td class="text-right"><?= $countprogram; ?></td>
                                        <td></td>
                                        <td><?= $data["nama_program"]; ?></td>
                                        <td><?= date('d-m-Y', strtotime($data["tgl_mulai"])) ?></td>
                                        <td><?= date('d-m-Y', strtotime($data["tgl_selesai"])) ?></td>
                                        <td><?= $data["penanggung_jawab"]; ?></td>
                                       <td class="text-center">
                                            <?php
                                            $datadetail = array(
                                                array("Nama Program", ":", $data["nama_program"], "", 1), 
                                                array("Tanggal Mulai", ":", date('d-m-Y', strtotime($data["tgl_mulai"])), "", 1),
                                                array("Tanggal Selesai", ":", date('d-m-Y', strtotime($data["tgl_selesai"])), "", 1),
                                                array("Bidang", ":", $data["nama_bidang"], "", 5, "select id_unit field1, nama_bidang field2 from unit"),
                                                array("Komisi", ":", !empty($data["nama_komisi"]) ? $data["nama_komisi"] : "-", "", 5, "select id_komisi field1, nama_komisi field2 from komisi"),
                                                array("Keterangan", ":", !empty($data["keterangan"]) ? $data["keterangan"] : "-", "", 1),
                                                array("Penanggung Jawab", ":", $data["penanggung_jawab"], "", 1),
                                                array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], "", 1),
                                            );
                                            _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Program#Data Program $cnourut : " . $data["nama_program"], "", $datadetail, "", "21", "");
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $countprogram = 0;
                                $firstBidangRow = false;
                                ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>