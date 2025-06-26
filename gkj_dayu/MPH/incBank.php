<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("Folder", "Rekening Bank", "Data Rekening Bank");
            ?>
        </div>
    </div>
   
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.* FROM bank a ";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Nama Rekening</td>
                            <td width=''>Nomor Rekening</td>
                            <td width=''>Nama Bank</td>
                            <td width=''>Jabatan</td>
                            <td width=''>Deskripsi</td>
                            <td width='5%' class="text-center">DETAIL</td>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cnourut = 0;
                        foreach ($array as $data) {
                            $cnourut = $cnourut + 1;
                        ?>
                            <tr class=''>
                                <td class="text-right"><?= $cnourut; ?></td>
                                <td><?= $data["nama_rekening"]; ?></td>
                                <td><?= $data["no_rekening"]; ?></td>
                                <td><?= $data["nama_bank"]; ?></td>
                                <td><?= $data["jabatan"]; ?></td>
                                <td><?= $data["keterangan"]; ?></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Nama Rekening", ":", $data["nama_rekening"], 1, ""),
                                        array("Nomor Rekening", ":", $data["no_rekening"], 1, ""),
                                        array("Nama Bank", ":", $data["nama_bank"], 1, ""),
                                        array("Jabatan", ":", $data["jabatan"], 1, ""),
                                        array("Deskripsi", ":", $data["keterangan"], 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Rekening Bank#Nama Rekening : ". $data["nama_rekening"], "", $datadetail, "", "13", "");
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>