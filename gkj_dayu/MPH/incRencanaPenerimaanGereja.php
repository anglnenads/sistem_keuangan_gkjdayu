

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Rencana Penerimaan Gereja", "Data Rencana Penerimaan Gereja");
            ?>
        </div>
    </div>

    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, b.*, e.*, u.* ";
            $sql .= "FROM rencana_penerimaan_gereja a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun  LEFT JOIN fiskal e ON a.id_fiskal = e.id_fiskal LEFT JOIN user u ON a.id_user = u.id_user ";
            $sql .= "WHERE (e.tahun) = $tahun_aktif ORDER BY a.id_rencana";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='15%'>No</td>
                            <td width='40%'>Akun</td>
                            <td width='15%' class="text-end">Jumlah</td>
                            <td width='20%'></td>
                            <td></td>
                            <td width='5%' class="text-center">DETAIL</td>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $cnourut = 0;
                        $total = 0;

                        foreach ($array as $data) {
                            $cnourut++;
                        ?>
                            <tr>
                                <td class="text-right"><?= $cnourut; ?></td>
                                <td><?= $data["nama_akun"]; ?></td>
                                <td class="text-end"><?= number_format($data["jumlah_penerimaan"], 0, ',', '.'); ?></td>
                                <td></td>
                                <td></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Akun", ":", $data["nama_akun"], 1, ""),
                                        array("Jumlah Rencana Penerimaan", ":", number_format($data["jumlah_penerimaan"], 0, ',', '.'), 1, ""),
                                        array("Diinput oleh", ":", $data["nama"] . " - " . $data["jbtn"], 1),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Rencana Pencerimaan Gereja#Data Rencana $cnourut : " . $data["nama_akun"], "", $datadetail, "", "221", "");
                                    ?>
                                </td>
                            </tr>
                        <?php
                            $total = $total + $data["jumlah_penerimaan"];
                        }

                        ?>
                       
                        <?php
                        ?>
                    </tbody>
                     <tr>
                            <td></td>
                            <td style="color:#5B90CD; font-weight:bolder">T O T A L</td>
                            <td class="text-end" style="color:#483d8b; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                </table>
            </div>
        </div>
    </div>
</div>