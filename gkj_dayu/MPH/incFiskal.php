<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
            _myHeader("Folder", "Fiskal", "Data Fiskal");
            ?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <div class="col-md-12">
            <?php
            $sql = "SELECT * FROM fiskal ORDER BY tahun ASC";
            $view = new cView();
            $array = $view->vViewData($sql);
            ?>
            <div id="" class='table-responsive'>
                <table id='example' class='table table-condensed w-100'>
                    <thead>
                        <tr class='small'>
                            <td width='5%' class="text-right">No</td>
                            <td width=''>Tahun</td>
                            <td width=''>Tanggal Mulai</td>
                            <td width=''>Tanggal Selesai</td>
                            <td width=''>Status Aktif</td>
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
                                <td><?= $data["tahun"]; ?></td>
                                <td><?= date('d-m-Y', strtotime($data["tanggal_mulai"])) ?></td>
                                <td><?= date('d-m-Y', strtotime($data["tanggal_selesai"])) ?></td>
                                <td style=" font-weight: 650; color: <?= $data['status_aktif'] == 1 ? '#009e60' : '#808080'; ?>;">
                                    <?= $data["status_aktif"] == 1 ? "Aktif" : "Tidak Aktif"; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $datadetail = array(
                                        array("Tahun", ":", $data["tahun"], 1, ""),
                                        array("Tanggal Mulai", ":", date('d-m-Y', strtotime($data["tanggal_mulai"])), 1, ""),
                                        array("Tanggal Selesai", ":", date('d-m-Y', strtotime($data["tanggal_selesai"])), 1, ""),
                                        array("Status Aktif", ":", $data["status_aktif"] == 1 ? "Aktif" : "Tidak Aktif", 1, ""),
                                    );
                                    _CreateWindowModalDetil($cnourut, "view", "viewsasaran-form", "viewsasaran-button", "", 600, "Detail Data Fiskal#Data Fiskal $cnourut" . " : Tahun " . $data["tahun"], "", $datadetail, "", "11", "");
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