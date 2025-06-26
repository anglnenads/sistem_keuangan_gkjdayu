<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
//insert ke table sementara
if (isset($_POST['save'])) {
  $_SESSION['is_edit_mode'] = false;

  if (!empty($_FILES['bukti_transfer']['name'])) {
    $uploadDir = realpath('../uploads/bukti_transfer/') . DIRECTORY_SEPARATOR;
    $fileName = basename($_FILES['bukti_transfer']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'];

    if (in_array($fileExt, $allowedExts)) {
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

      if (move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $uploadDir . $fileName)) {
        $bukti_transfer = $fileName; // Simpan nama file jika berhasil diunggah
        //echo "File berhasil diunggah!";
      } else {
        //echo "Gagal menyimpan file!";
      }
    }
  }


  $inputData = [
    'bidang' => $_POST["bidang"],
    'komisi' => $_POST["komisi"] ?? NULL,
    'bukti_transfer' => $bukti_transfer ?? NULL,
    'akun' => isset($_POST['akun']) && $_POST['akun'] !== '' ? $_POST['akun'] : NULL,
    'bank' => isset($_POST['bank']) && $_POST['bank'] !== '' ? $_POST['bank'] : NULL,
    'program' => $_POST['program'],
    'tanggal_pencairan' => $_POST['tanggal_pencairan'],
    'biaya_tf' => isset($_POST['biaya_tf']) && $_POST['biaya_tf'] !== '' ? $_POST['biaya_tf'] : 0,
    'jumlah' => $_POST['jumlah'],
    'keterangan' => $_POST['keterangan']
  ];
  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data15'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data15'])) {
      $_SESSION['temp_data15'] = [];
    }
    $_SESSION['temp_data15'][] = $inputData;
  }
  if (!empty($_POST["bukti_transfer"])) {
    $_SESSION["bukti_transfer"] = $bukti_transfer;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data15']);
  unset($_SESSION['bidang']);
  unset($_SESSION['komisi']);
  unset($_SESSION['bukti_transfer']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  if (isset($_SESSION['temp_data15'][$indexToDelete])) {
    unset($_SESSION['temp_data15'][$indexToDelete]);
    $_SESSION['temp_data15'] = array_values($_SESSION['temp_data15']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  if (isset($_SESSION['temp_data15'][$indexToEdit])) {
    $editData = $_SESSION['temp_data15'][$indexToEdit];
    $_SESSION['edit_index'] = $indexToEdit;
    $_SESSION['edit_data'] = $editData;
    $_SESSION['is_edit_mode'] = true;
  }
}

if (isset($_POST['cancel'])) {
  unset($_SESSION['is_edit_mode']);
  unset($_SESSION['edit_data']);
  unset($_SESSION['edit_index']);
}

if (isset($_POST["submit_all"])) {

  if (isset($_SESSION['temp_data15']) && count($_SESSION['temp_data15']) > 0) {

    $tanggal_catat =  date("Y-m-d");
    foreach ($_SESSION['temp_data15'] as $data) {

      $insert = new cInsert();

      $datafield = array("id_akun", "id_bidang", "id_komisi", "id_fiskal", "id_program",  "tanggal_pencairan", "jumlah_pencairan", "biaya_transfer", "id_bank", "bukti_transfer", "keterangan", "id_user");
      $datavalue = array($data['akun'], $data['bidang'], $data['komisi'], $id_fiskal, $data['program'], $data['tanggal_pencairan'],  $data['jumlah'], $data['biaya_tf'],  $data['bank'], $data['bukti_transfer'], $data['keterangan'], $id_user);

      $insert->fInsertData($datafield, "pencairan", $datavalue, 25);

      $datafield_penerimaanKomisi = array("id_fiskal", "id_program", "id_user", "id_bidang", "id_komisi", "jumlah_penerimaan", "tanggal_penerimaan", "tanggal_pencatatan", "jenis_penerimaan", "status", "volume", "satuan", "harga_satuan", "dana_gereja", "dana_swadaya");
      $datavalue_penerimaanKomisi = array($id_fiskal, $data['program'], $id_user, $data['bidang'], $data['komisi'], $data['jumlah'], $data['tanggal_pencairan'], $tanggal_catat, 'Dana dari Gereja', "Tervalidasi", 1, '-', $data['jumlah'], $data['jumlah'], 0);

      $insert->fInsertData($datafield_penerimaanKomisi, "realisasi_penerimaan_komisi", $datavalue_penerimaanKomisi, '');


      $datafield_pengeluaran = array("id_akun", "id_fiskal",  "tanggal_pengeluaran", "jenis_pengeluaran", "bukti_pengeluaran", "jumlah", "id_user", "tanggal_catat", "status");
      $datavalue_pengeluaran = array( $data['akun'], $id_fiskal,  $data['tanggal_pencairan'], $data['keterangan'],  $data['bukti_transfer'], $data['jumlah'] + $data['biaya_tf'],  $id_user, $tanggal_catat, 'Belum Tervalidasi');
      $insert = new cInsert();
      $insert->fInsertData($datafield_pengeluaran, "realisasi_pengeluaran_gereja", $datavalue_pengeluaran, 25);
    }
    unset($_SESSION['temp_data15']);
    unset($_SESSION["bidang"]);
    unset($_SESSION["komisi"]);
  } else {
    echo "<script>
          Swal.fire({
          position:'center',
          width:'25em',
          icon: 'error',	
          text: 'Tidak ada data yang disimpan karena masih kosong',
          type: 'error',
          }).then(function (result) {
          if (true) {
          window.location = '';
          }
          }) </script>";
  }
}
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <?php
      _myHeader("newspaper", "Pencairan", "Entri Data");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post" enctype="multipart/form-data">
      <div class="horizontal" style="height: 70px;">
        <div class="form-group" style="width:100%; margin-left: 30px;">
          <label for="bidang" class="required">Bidang</label>
          <select style="width:90%" id="bidang" name="bidang" required>
            <option value="">-- Pilih Bidang --</option>
            <?php
            $sql = "SELECT id_bidang, nama_bidang FROM bidang";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $selected = '';
                if (isset($_POST['bidang']) && $_POST['bidang'] == $row['id_bidang']) {
                  $selected = 'selected';
                } elseif (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['bidang'] == $row['id_bidang']) {
                  $selected = 'selected';
                }
                echo "<option value='" . $row['id_bidang'] . "' $selected>" . $row['nama_bidang'] . "</option>";
              }
            } else {
              echo "<option value=''>Data tidak tersedia</option>";
            }
            if (isset($_POST['bidang'])) {
              $_SESSION['bidang'] = $_POST['bidang'];
            }
            ?>
          </select>
        </div>
        <div class="form-group" style="width:100%">
          <label for="komisi">Komisi</label>
          <select style="width:90%" name="komisi" id="komisi">
            <option value="">-- Pilih Komisi --</option>
            <?php
            if (isset($_SESSION['bidang'])) {
              $query = "SELECT id_komisi, nama_komisi FROM komisi WHERE id_bidang =" . $_SESSION['bidang'];
            } else {
              $query = "SELECT id_komisi, nama_komisi FROM komisi";
            }
            $result = mysqli_query($conn, $query);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $selected = '';
                if (isset($_POST['komisi']) && $_POST['komisi'] == $row['id_komisi']) {
                  $selected = 'selected';
                } elseif (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['komisi'] == $row['id_komisi']) {
                  $selected = 'selected';
                }
                echo "<option value='" . $row['id_komisi'] . "' $selected>" . $row['nama_komisi'] . "</option>";
              }
            } else {
              echo "<option value=''>Data tidak tersedia</option>";
            }
           if (isset($_POST['komisi']) && !empty($_POST['komisi'])) {
              $_SESSION['komisi'] = $_POST['komisi'];
            }
            ?>
          </select>
        </div>

      </div>
  </div>
  <br>
  <div class="section">
    <div class="horizontal" style="height: 90px;">
      <div class="form-group" style="width:100%; margin-left:30px;">
        <label for="tanggal_pencairan" class="required">Tanggal Pencairan</label>
        <input style="width:95%" type="date" id="tanggal_pencairan" name="tanggal_pencairan" placeholder="" required max="<?= date('Y-m-d'); ?>" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal_pencairan'] : ''; ?>" />
        <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
      </div>
    </div>
    <br>
    <div class="horizontal">
      <div class="form-group" style="width:100%; margin-left: 30px;">
        <label for="program" class="required">Nama Program</label>
        <select style="width:90%" id="program" name="program" required>
          <option value="">-- Pilih Program --</option>
          <option value="0" <?php echo (isset($_POST['program']) && $_POST['program'] == '0') || (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['program'] == '0') ? 'selected' : ''; ?>>Insidental</option>
          <?php
          if (isset($_SESSION['komisi'])) {
            $query = "SELECT program.*, komisi.nama_komisi, bidang.nama_bidang, fiskal.tahun
                      FROM program
                      LEFT JOIN komisi ON program.id_komisi = komisi.id_komisi
                      JOIN bidang ON (komisi.id_bidang = bidang.id_bidang OR program.id_bidang = bidang.id_bidang)
                      JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                      JOIN pengajuan ON program.id_program = pengajuan.id_program
                      LEFT JOIN ( SELECT id_program, SUM(jumlah_pencairan) AS total_cair FROM pencairan
                      GROUP BY id_program) AS cair ON program.id_program = cair.id_program
                      LEFT JOIN (SELECT id_program, SUM(jumlah_pengajuan) AS total_pengajuan FROM pengajuan
                      WHERE status = 'Disetujui dan Dana telah Cair' GROUP BY id_program
                      ) AS total_ajuan ON program.id_program = total_ajuan.id_program
                      WHERE fiskal.tahun = $tahun_aktif AND program.id_komisi = " . $_SESSION['komisi'] . "
                      AND ( pengajuan.status = 'Disetujui' OR (
                      pengajuan.status = 'Disetujui dan Dana telah Cair'
                      AND (cair.total_cair IS NULL OR cair.total_cair < total_ajuan.total_pengajuan )))
                      GROUP BY program.id_program
                      ORDER BY bidang.id_bidang ASC, komisi.id_komisi ASC";
          } elseif (isset($_SESSION['bidang'])) {
            $query = "SELECT program.*, bidang.nama_bidang, fiskal.tahun
                      FROM program
                      JOIN bidang ON program.id_bidang = bidang.id_bidang
                      JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                      JOIN pengajuan ON program.id_program = pengajuan.id_program
                      LEFT JOIN ( SELECT id_program, SUM(jumlah_pencairan) AS total_cair FROM pencairan
                      GROUP BY id_program) AS cair ON program.id_program = cair.id_program
                      LEFT JOIN (SELECT id_program, SUM(jumlah_pengajuan) AS total_pengajuan FROM pengajuan
                      WHERE status = 'Disetujui dan Dana telah Cair' GROUP BY id_program
                      ) AS total_ajuan ON program.id_program = total_ajuan.id_program
                      WHERE fiskal.tahun = $tahun_aktif AND program.id_bidang = " . $_SESSION['bidang'] . "
                      AND ( pengajuan.status = 'Disetujui' OR (
                      pengajuan.status = 'Disetujui dan Dana telah Cair'
                      AND (cair.total_cair IS NULL OR cair.total_cair < total_ajuan.total_pengajuan )))
                      GROUP BY program.id_program
                      ORDER BY bidang.id_bidang ASC";
          } else {
            $query = "SELECT program.*, komisi.nama_komisi, bidang.nama_bidang, fiskal.tahun
                      FROM program
                      LEFT JOIN komisi ON program.id_komisi = komisi.id_komisi
                      JOIN bidang ON (komisi.id_bidang = bidang.id_bidang OR program.id_bidang = bidang.id_bidang)
                      JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                      JOIN pengajuan ON program.id_program = pengajuan.id_program
                      LEFT JOIN ( SELECT id_program, SUM(jumlah_pencairan) AS total_cair FROM pencairan
                      GROUP BY id_program) AS cair ON program.id_program = cair.id_program
                      LEFT JOIN (SELECT id_program, SUM(jumlah_pengajuan) AS total_pengajuan FROM pengajuan
                      WHERE status = 'Disetujui dan Dana telah Cair' GROUP BY id_program
                      ) AS total_ajuan ON program.id_program = total_ajuan.id_program
                      WHERE fiskal.tahun = $tahun_aktif
                      AND ( pengajuan.status = 'Disetujui' OR (
                    pengajuan.status = 'Disetujui dan Dana telah Cair'
                    AND (cair.total_cair IS NULL OR cair.total_cair < total_ajuan.total_pengajuan )))
                    GROUP BY program.id_program
                    ORDER BY bidang.id_bidang ASC, komisi.id_komisi ASC";
          }
          $result = $conn->query($query);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = '';

              if (isset($_POST['program']) && $_POST['program'] == $row['id_program']) {
                $selected = 'selected';
              } elseif (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['program'] == $row['id_program']) {
                $selected = 'selected';
              }

              echo "<option value='" . $row['id_program'] . "' $selected>" . $row['nama_program'] . "</option>";
            }
          } else {
            echo "<option value=''>Data tidak tersedia</option>";
          }
          if (isset($_POST['program'])) {
            $_SESSION['program'] = $_POST['program'];
          }
          ?>
        </select>
      </div>
      <div class="form-group" style="width:100%">
        <label for="akun" class="required">Akun</label>
        <select style="width:90%" id="akun" name="akun" required>
          <option value="">-- Pilih Akun --</option>
          <?php
          $sql = "SELECT id_akun, nama_akun FROM akun WHERE jenis_debitKredit = 'Debet' AND status_input = 1 ORDER BY kode_akun ASC";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = '';
              if (isset($_POST['akun']) && $_POST['akun'] == $row['id_akun']) {
                $selected = 'selected';
              } elseif (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['akun'] == $row['id_akun']) {
                $selected = 'selected';
              }
              echo "<option value='" . $row['id_akun'] . "' $selected>" . $row['nama_akun'] . "</option>";
            }
          } else {
            echo "<option value=''>Data tidak tersedia</option>";
          }
          if (isset($_POST['akun'])) {
            $_SESSION['akun'] = $_POST['akun'];
          }
          ?>
        </select>
      </div>
    </div>
    <div class="horizontal">
      <div class="form-group" style="width:45%; margin-left:30px">
        <label for="jumlah" class="required">Jumlah Pencairan</label>
        <input style="width:90%" type="number" id="jumlah" name="jumlah" placeholder="Masukkan Jumlah Pencairan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" min="1" required />
      </div>
      <div class="form-group" style="width:45%; margin-left:-40px">
        <label for="biaya_tf">Biaya Admin</label>
        <input style="width:89%" type="number" id="biaya_tf" name="biaya_tf" placeholder="Jumlah Biaya Admin Transfer" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['biaya_tf'] : ''; ?>" min="1" />
      </div>
      <div class="form-group" style="width:100%; margin-left:50px">
        <label for="bank">Bank Penerima</label>
        <select style="width:90%" id="bank" name="bank">
          <option value="">-- Pilih Bank --</option>
          <?php
          $sql = "SELECT id_bank, nama_bank, nama_rekening FROM bank";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = isset($_SESSION['edit_data']) && $_SESSION['edit_data']['bank'] == $row['id_bank'] ? 'selected' : '';
              echo "<option value='" . $row['id_bank'] . "' $selected>" . $row['nama_bank'] . " - " . $row['nama_rekening'] . "</option>";
            }
          } else {
            echo "<option value=''>Data tidak tersedia</option>";
          }
          ?>
        </select>
      </div>
    </div>
    <div class="horizontal">
      <div class="form-group" style=" width:100%; margin-left: 30px;">
        <label for="keterangan" class="required">Keterangan</label>
        <input style="width:90%" type="text" id="keterangan" name="keterangan" placeholder="Masukkan Keterangan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['keterangan'] : ''; ?>" required />
      </div>
      <div class="form-group" style="height:110px; width:100%;">
        <label for="bukti_transfer">Bukti Transfer/Pencairan</label>
        <input style="width:90%" type="file" class="form-control" id="bukti_transfer" name="bukti_transfer" accept=".pdf, .docx, .xlsx, .jpg, .jpeg, .png">
      </div>
    </div>
  </div>
  <br>
  <div class="section">
    <div style="display: flex; justify-content: flex-end;">
      <?php if (isset($_SESSION['is_edit_mode']) && $_SESSION['is_edit_mode']): ?>
        <button class="button" type="submit" name="save" style="background-color:#002e63; height: 35px; width: 200px; margin-right: 20px;">Tambah</button> &nbsp;
        <button class="button2" type="submit" name="cancel" class="btn btn-secondary" style="background-color:#b22222; height: 35px; width: 200px; margin-right: 70px;">Cancel</button> &nbsp;
      <?php else : ?>
        <button class="button" type="submit" name="save" style="background-color:#002e63; height: 35px; width: 200px; margin-right: 70px;">Tambah</button> &nbsp;
      <?php endif; ?>
    </div>
    </form>
  </div>
  <br>

  <div class="secondsection">
    <form action="" method="post">
      <table id='data-table' class='table table-condensed table-bordered'>
        <thead>
          <tr class='small'>
            <td width='5%' class="text-right">No</td>
            <td class="text-center">Bidang</td>
            <td class="text-center">Komisi</td>
            <td class="text-center" width=''>Tanggal Pencairan</td>
            <td class="text-center" width=''>Program</td>
            <td class="text-center" width=''>Akun</td>
            <td class="text-center" width=''>Jumlah Pencairan</td>
            <td class="text-center" width=''>Biaya Transfer</td>
            <td class="text-center" width=''>Bank Penerima</td>
            <td class="text-center" width=''>Keterangan</td>
            <td class="text-center" width=''>Bukti Transfer</td>
            <td class="text-center" width='5%'></td>
            <td class="text-center" width='5%'></td>
          </tr>
        </thead>
        <tbody>

          <?php
          $cnourut = 0;
          if (isset($_SESSION['temp_data15']) && count($_SESSION['temp_data15']) > 0) {

            $total = 0;

            foreach ($_SESSION['temp_data15'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunNama = !empty($data['akun']) ?  getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']) : NULL;
              $bidangNama = getNameFromId('bidang', 'id_bidang', 'nama_bidang', $data['bidang']);
              $komisiNama = !empty($data['komisi']) ? getNameFromId('komisi', 'id_komisi', 'nama_komisi', $data['komisi']) : '-';
              $programNama = !empty($data['program']) ? getNameFromId('program', 'id_program', 'nama_program', $data['program']) : 'Insidental';
              $bankNama = !empty($data['bank']) ? getNameFromId('bank', 'id_bank', 'nama_bank', $data['bank']) : 'Cash';

          ?>
              <tr class=''>
                <td class="text-right"><?= $cnourut; ?></td>
                <td><?= $bidangNama ?></td>
                <td><?= $komisiNama ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal_pencairan"])); ?></td>
                <td><?= $programNama; ?></td>
                <td><?= $akunNama ?></td>
                <td class="text-end"><?= number_format((float)$data["jumlah"], 0, ',', '.') ?></td>
                <td class="text-end"><?= number_format((float)$data["biaya_tf"], 0, ',', '.') ?></td>
                <td><?= $bankNama; ?></td>
                <td><?= !empty($data["keterangan"]) ? $data["keterangan"] : '-'; ?></td>
                <td><?= $data["bukti_transfer"]; ?></td>
                <td>
                  <form method='post' action=''>
                    <input type='hidden' name='edit_index' value="<?= $index; ?>">
                    <button class="button" type='submit' name='edit' style='width:100%; background-color:#ffa500'>Edit</button>
                  </form>
                </td>
                <td>
                  <form method='post' action=''>
                    <input type='hidden' name='delete_index' value="<?= $index; ?>">
                    <button class="button" type='submit' name='delete' style='width:100%; background-color:#ec5353'>Hapus</button>
                  </form>
                </td>
              </tr>
            <?php
              $total += (float)$data["jumlah"];
            } ?>
            <tr class=''>
              <td class="text-right"></td>
              <td>Total</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="text-end"><?= number_format($total, 0, ',', '.') ?></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>

              </td>
              <td>

              </td>
            </tr>
          <?php } else {
            echo "<tr><td colspan='12' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <div style="display: flex; justify-content: center; align-items: center; ">

        <button class="button" type="submit" name="submit_all"> Simpan</button>

        &nbsp; &nbsp; &nbsp;
        <button class="button" type="submit" name="reset" style="width:15%">Hapus Seluruh Data</button>
      </div>
    </form>
  </div>
</div>


<script>
  function resetTable() {
    var tableBody = document.getElementById("data-table").getElementsByTagName('tbody')[0];

    tableBody.innerHTML = '';
  }

  $("#bidang").change(function() {
    var id_bidang = $("#bidang").val();
    console.log(id_bidang);

    // Request untuk #komisi
    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: {
        bidang: id_bidang
      },
      success: function(data) {
        $("#komisi").html(data);
      },
    });

    // Request untuk #program
    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: {
        bidangPencairan: id_bidang,
        tahun: "<?php echo $_SESSION['tahun_aktif']; ?>"
      },
      success: function(data) {
        $("#program").html(data);
      },
    });
  });


  $("#komisi").change(function() {
    var id_komisi = $("#komisi").val();
    console.log(id_komisi);
    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: {
        komisiPencairan: id_komisi,
        tahun: "<?php echo $_SESSION['tahun_aktif']; ?>"
      },
      success: function(data) {
        $("#program").html(data);
      },
    });
  });


  let batasPencairan = 0;
  $("#program").change(function() {
    var id_program = $("#program").val();
    console.log("Program:" , id_program);
    var id_komisi = $("#komisi").val();
    var id_bidang = $("#bidang").val();

    var pengenal = id_komisi ? {
      komisiCair: id_komisi
    } : {
      bidangCair: id_bidang
    };

    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: Object.assign({
        programPencairan: id_program,
        tahun: "<?php echo $_SESSION['tahun_aktif']; ?>"
      }, pengenal),
      success: function(data) {
        batasPencairan = parseInt(data);
        console.log("Jumlah Pengajuan:", data);
        $("#jumlah").val(data);
      }
    });
  });

  $("#jumlah").on("input", function() {
    var jumlah = parseInt($(this).val());
    var self = $(this);


    if (jumlah > batasPencairan) {
      Swal.fire({
        position: 'center',
        icon: 'error',
        text: 'Jumlah Pencairan tidak boleh melebihi jumlah pengajuan',
        width: '25em'
      });
      $("#jumlah").val(batasPencairan);
    }
  });
</script>
</body>

</html>