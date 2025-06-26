<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("Folder", "Komisi", "Data Komisi");
            ?>
        </div>
    </div>
    <p></p>

    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT a.*, b.* FROM komisi a LEFT OUTER JOIN bidang b ON a.id_bidang = b.id_bidang ORDER BY b.id_bidang, a.id_komisi ASC";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Nama Bidang</td>
                            <td width=''>Nama Komisi</td>
                            <td width=''>Nama Ketua </td>
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
                                <td><?= $data["nama_bidang"]; ?></td>
                                <td><?= $data["nama_komisi"]; ?></td>
                                <td><?= $data["nama_ketuaKomisi"]; ?></td>
                                <td><?= $data["deskripsi_komisi"]; ?></td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Nama Bidang", ":", $data["nama_bidang"], 1, ""),
                                        array("Nama Komisi", ":", $data["nama_komisi"], 1, ""),
                                        array("Nama Ketua", ":", $data["nama_ketuaKomisi"], 1, ""),
                                        array("Deskripsi", ":", $data["deskripsi_komisi"], 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Komisi#Data $cnourut : " . $data["nama_komisi"], "", $datadetail, "", "16", "");
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