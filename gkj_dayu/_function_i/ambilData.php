<?php

include "cConnect.php";
$connection = new cConnect();
$connection->goConnect();

//untuk ambil komisi per bidang
if (isset($_POST['bidang'])) {
    $bidang = $_POST["bidang"];

    $sql = "select * from komisi where id_bidang=$bidang";
    $hasil = mysqli_query($GLOBALS["conn"], $sql);
    //$no = 0;
    echo '<option value="">-- Pilih Komisi --</option>';
    while ($data = mysqli_fetch_array($hasil)) {
?>
        <option value="<?php echo  $data['id_komisi']; ?>"><?php echo $data['nama_komisi']; ?></option>
    <?php
    }
    exit;
}


//untuk ambil program per komisi atau bidang
if (isset($_POST['komisiProgram']) && isset($_POST['tahun'])) {
    $komisi = $_POST["komisiProgram"];
    $tahun = $_POST["tahun"];

    $sql = "SELECT program.*, fiskal.tahun 
            FROM program 
            JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal 
            WHERE program.id_komisi = $komisi AND fiskal.tahun = $tahun";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);

    echo '<option value="">-- Pilih Program --</option>';
    while ($data = mysqli_fetch_array($hasil)) {
        echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
    }
} else if (isset($_POST['bidangProgram']) && isset($_POST['tahun'])) {
    $bidang = $_POST["bidangProgram"];
    $tahun = $_POST["tahun"];

    $sql = "SELECT program.*, fiskal.tahun 
            FROM program 
            JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal 
            WHERE program.id_bidang = $bidang  AND fiskal.tahun = $tahun";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);

    echo '<option value="">-- Pilih Program --</option>';
    while ($data = mysqli_fetch_array($hasil)) {
        echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
    }
    exit;
}



//untuk program realisasi bidang/komisi
if (isset($_POST['komisiProgramRealisasi']) && isset($_POST['tahun'])) {
    $komisi = $_POST["komisiProgramRealisasi"];
    $tahun = $_POST["tahun"];

    $sql = "SELECT program.*, fiskal.tahun 
            FROM program 
            JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal 
            WHERE program.id_komisi = $komisi AND fiskal.tahun = $tahun";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);

    echo '<option value="">-- Pilih Program --</option>';
    echo '<option value="0">Insidental</option>';
    while ($data = mysqli_fetch_array($hasil)) {
        echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
    }
} elseif (isset($_POST['bidangProgramRealisasi']) && isset($_POST['tahun'])) {
    $bidang = $_POST["bidangProgramRealisasi"];
    $tahun = $_POST["tahun"];

    $sql = "SELECT program.*, fiskal.tahun 
            FROM program 
            JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal 
            WHERE program.id_bidang = $bidang  AND fiskal.tahun = $tahun";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);

    echo '<option value="">-- Pilih Program --</option>';
    echo '<option value="0">Insidental</option>';
    while ($data = mysqli_fetch_array($hasil)) {
        echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
    }
    exit;
}


//untuk form pencairan pemilihan program
if (isset($_POST['komisiPencairan']) && isset($_POST['tahun'])) {
    $komisi = $_POST["komisiPencairan"];
    $tahun = $_POST["tahun"];

    $sql = "SELECT pengajuan.*, program.nama_program, fiskal.tahun, cair.total_cair, total_pengajuan.total_pengajuan
            FROM pengajuan
            JOIN fiskal ON pengajuan.id_fiskal = fiskal.id_fiskal 
            JOIN program ON pengajuan.id_program = program.id_program

            -- Total pencairan per program
            LEFT JOIN ( SELECT id_program, SUM(jumlah_pencairan) AS total_cair FROM pencairan
            GROUP BY id_program) AS cair ON cair.id_program = pengajuan.id_program

            -- Total pengajuan per program
            JOIN (SELECT id_program, SUM(jumlah_pengajuan) AS total_pengajuan FROM pengajuan
            GROUP BY id_program) AS total_pengajuan ON total_pengajuan.id_program = pengajuan.id_program

            WHERE pengajuan.id_komisi = $komisi AND fiskal.tahun = $tahun
            AND (pengajuan.status = 'Disetujui' OR (
                pengajuan.status = 'Disetujui dan Dana telah Cair' 
                AND (cair.total_cair IS NULL OR cair.total_cair < total_pengajuan.total_pengajuan)
            )
        )
        GROUP BY program.id_program ORDER BY program.nama_program ASC";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);

    echo '<option value="">-- Pilih Program --</option>';
    echo '<option value="0">Insidental</option>';
    while ($data = mysqli_fetch_array($hasil)) {
        echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
    }
    exit;
} else if (isset($_POST['bidangPencairan']) && isset($_POST['tahun'])) {
    $bidang = $_POST["bidangPencairan"];
    $tahun = $_POST["tahun"];

    $sql = "SELECT pengajuan.*, program.nama_program, fiskal.tahun, cair.total_cair, total_pengajuan.total_pengajuan
            FROM pengajuan
            JOIN fiskal ON pengajuan.id_fiskal = fiskal.id_fiskal 
            JOIN program ON pengajuan.id_program = program.id_program

            -- Total pencairan per program
            LEFT JOIN ( SELECT id_program, SUM(jumlah_pencairan) AS total_cair FROM pencairan
            GROUP BY id_program) AS cair ON cair.id_program = pengajuan.id_program

            -- Total pengajuan per program
            JOIN (SELECT id_program, SUM(jumlah_pengajuan) AS total_pengajuan FROM pengajuan
            GROUP BY id_program) AS total_pengajuan ON total_pengajuan.id_program = pengajuan.id_program

            WHERE pengajuan.id_bidang = $bidang AND fiskal.tahun = $tahun
            AND (pengajuan.status = 'Disetujui' OR (
                pengajuan.status = 'Disetujui dan Dana telah Cair' 
                AND (cair.total_cair IS NULL OR cair.total_cair < total_pengajuan.total_pengajuan)
            )
        )
        GROUP BY program.id_program ORDER BY program.nama_program ASC";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);

    echo '<option value="">-- Pilih Program --</option>';
    echo '<option value="0">Insidental</option>';
    while ($data = mysqli_fetch_array($hasil)) {
        echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
    }
    exit;
}

//untuk jumlah pengajuan per program di halaman pencairan
if (isset($_POST['programPencairan']) && isset($_POST['tahun'])) {
    $id_program = $_POST['programPencairan'];
    $tahun = $_POST["tahun"];

    if (isset($_POST['komisiCair'])) {
        $komisi = $_POST['komisiCair'];

        $sql = "SELECT SUM(jumlah_pengajuan) AS total_pengajuan 
                FROM pengajuan 
                LEFT JOIN fiskal ON pengajuan.id_fiskal = fiskal.id_fiskal
                WHERE id_program = $id_program 
                  AND id_komisi = $komisi AND fiskal.tahun = $tahun AND (status = 'Disetujui' OR status = 'Disetujui dan Dana telah Cair')";
    } elseif (isset($_POST['bidangCair'])) {
        $bidang = $_POST['bidangCair'];

        $sql = "SELECT SUM(jumlah_pengajuan) AS total_pengajuan 
                FROM pengajuan 
                LEFT JOIN fiskal ON pengajuan.id_fiskal = fiskal.id_fiskal
                WHERE id_program = $id_program 
                  AND id_bidang = $bidang 
                  AND fiskal.tahun = $tahun AND (status = 'Disetujui' OR status = 'Disetujui dan Dana telah Cair')";
    }

    // Eksekusi query jika query sudah terbentuk
    if (isset($sql)) {
        $hasil = mysqli_query($GLOBALS["conn"], $sql);
        $data = mysqli_fetch_array($hasil);
        echo $data['total_pengajuan'];
        exit;
    }
}



//untuk form pengajuan

//pemilihan jenis anggaran berdasarkan program untuk halaman pengajuan
if (isset($_POST['program'])) {
    $program = $_POST["program"];

    $sql = "select * from rencana_pengeluaran_komisi where id_program = $program";

    $hasil = mysqli_query($GLOBALS["conn"], $sql);
    $no = 0;
    echo '<option value="">-- Pilih Jenis Kegiatan --</option>'; // Opsi default
    while ($data = mysqli_fetch_array($hasil)) {
    ?>
        <option value="<?php echo  $data['id_anggaran']; ?>"><?php echo $data['item']; ?></option>
<?php
    }
    exit;
}

//pemilihan jumlah pengajuan berdasarkan rencana anggaran
if (isset($_POST['anggaran'])) {
    $id_anggaran = $_POST['anggaran'];

    $sql = "SELECT dana_gereja FROM rencana_pengeluaran_komisi WHERE id_anggaran = $id_anggaran";

    $hasil1 = mysqli_query($GLOBALS["conn"], $sql);
    $data = mysqli_fetch_array($hasil1);

    echo $data['dana_gereja'];

    // $query_selected = "SELECT a.id_akun, b.nama_akun FROM rencana_pengeluaran_komisi a INNER JOIN akun b ON a.id_akun =  b.id_akun WHERE a.id_anggaran = $id_anggaran";
    // $hasil2 = mysqli_query($GLOBALS["conn"], $query_selected);
    // if ($row = mysqli_fetch_array($hasil2)) {
    //     $selected_akun = $row['id_akun'];
    // }

    // $query = "SELECT id_akun, nama_akun FROM akun WHERE jenis_debitKredit = 'Debet' AND status_input = 1 ORDER BY kode_akun";

    // $hasil3 = mysqli_query($GLOBALS["conn"], $query);
    // echo '<option value="">-- Pilih Jenis Kegiatan --</option>'; // Opsi default
    // while ($data = mysqli_fetch_array($hasil3)) {
    //     $selected = ($data['id_akun'] == $selected_akun) ? 'selected' : '';
    //     echo '<option value="' . $data['id_akun'] . '" ' . $selected . '>' . $data['nama_akun'] . '</option>';
    // }

    exit;
}



//bagian laporan
if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
    session_start();
    $_SESSION['filter'] = $filter;
    echo "filter yang diterima: " . $filter;
} else {
    echo "filter tidak diterima";
}

if (isset($_POST['bulan'])) {
    $bulan = $_POST['bulan'];
    session_start();
    $_SESSION['bulan'] = $bulan;
    echo "Bulan yang diterima: " . $bulan;
} else {
    echo "Bulan tidak diterima";
}

if (isset($_POST['tahun'])) {
    $tahun = $_POST['tahun'];
    session_start();
    $_SESSION['tahun'] = $tahun;
    echo "tahun yang diterima: " . $tahun;
} else {
    echo "tahun tidak diterima";
}

if (isset($_POST['tahun_aktif'])) {
    $tahun_aktif = $_POST['tahun_aktif'];
    session_start();
    $_SESSION['tahun_aktif'] = $tahun_aktif;
    echo "tahun_aktif yang diterima: " . $tahun_aktif;
} else {
    echo "tahun_aktif tidak diterima";
}

//untuk session bidang dan komisi di laporan
if (isset($_POST['bidang'])) {
    $bidang = $_POST['bidang'];
    session_start();
    $_SESSION['bidang'] = $bidang;
    echo "bidang yang diterima: " . $bidang;
} else {
    echo "bidang tidak diterima";
}


?>