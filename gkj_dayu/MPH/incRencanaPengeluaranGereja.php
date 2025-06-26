
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("newspaper", "Rencana Pengeluaran Gereja", "Data Rencana Pengeluaran Gereja");
            ?>
        </div>
    </div>

    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.id_fiskal as fiskal, a.*, b.*, e.*, u.* ";
            $sql .= "FROM rencana_pengeluaran_gereja a ";
            $sql .= "LEFT JOIN akun b ON a.id_akun = b.id_akun  LEFT JOIN fiskal e ON a.id_fiskal = e.id_fiskal LEFT JOIN user u ON a.id_user = u.id_user ";
            $sql .= "WHERE (e.tahun) = $tahun_aktif  ORDER BY a.id_anggaran";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width=''>No</td>
                            <td width=''>Jenis Pengeluaran</td>
                            <td width=''>Akun</td>
                            <td class='text-end' width=''>Jumlah</td>
                            <td width='14%'></td>
                            <td class='text-center' width='5%'>DETAIL</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        $total = 0;

                        foreach ($array as $data) {
                            $cnourut++

                        ?>
                            <tr>
                                <td class="text-right"><?= $cnourut; ?></td>

                                <td><?= $data["nama_anggaran"]; ?></td>
                                <td><?= $data["nama_akun"]; ?></td>
                                <td class='text-end'><?= number_format($data["jumlah"], 0, ',', '.'); ?></td>

                                <td></td>
                        
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Tahun Fiskal", ":", $data["tahun"], 1, ""),
                                       
                                    
                                        array("Akun", ":", $data["nama_akun"], 1, ""),
                                        array("Jenis Kegiatan", ":", $data["nama_anggaran"], 1, ""),
                                        array("Jumlah", ":", number_format($data["jumlah"], 0, ',', '.'), 1),
                                        array("Diinput oleh ", ":", $data["nama"] ." - ". $data["jbtn"], 1),
                                    );
                                    // $number, $type, $name, $button, $width, $height, $title, $acaption, $afield, $value, $linkurl
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "lg", 600, "Detail Data Rencana Pengeluaran Gereja#Data Rencana $cnourut : " . $data["nama_anggaran"], "", $datadetail, "", "22", "");
                                    ?>
                                </td>
                            
                            </tr>
                        <?php
                            $total =  $total + ($data['jumlah']);
                        }
                    
                        ?>
                        
                        <?php


                        ?>


                        <?php


                        ?>
                    </tbody>
                    <tr>
                            <td></td>
                            <td style="color:#5B90CD; font-weight:bolder">Total</td>
                            <td></td>

                            <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
                            <td></td>
                         
                      

                            <td></td>

                        </tr>
                </table>

                <?php
                ?>
            </div>
        </div>
    </div>
</div>