<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("Folder", "Bidang", "Data Bidang");
            ?>
        </div>
    </div>
  
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.* FROM bidang a ";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Nama Bidang</td>
                            <td width=''>Koordinator</td>
                            <td width=''>Deskripsi</td>
                            <td></td>
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
                                <td><?= $data["nama_bidang"]; ?></td>
                                <td><?= $data["nama_ketuaBidang"]; ?></td>
                                <td><?= $data["deskripsi_bidang"]; ?></td>
                                <td></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Nama Bidang", ":", $data["nama_bidang"], 1, ""),
                                        array("Koordinator", ":", $data["nama_ketuaBidang"], 1, ""),
                                        array("Deskripsi", ":", $data["deskripsi_bidang"], 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Bidang #Data Bidang $cnourut : ". $data["nama_bidang"], "", $datadetail, "", "15", "");
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