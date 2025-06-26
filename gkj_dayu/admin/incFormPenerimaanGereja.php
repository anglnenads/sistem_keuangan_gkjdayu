<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {
  $_SESSION['is_edit_mode'] = false;

  if (!empty($_FILES['bukti_penerimaan']['name'])) {
    $uploadDir = realpath('../uploads/bukti_penerimaan/') . DIRECTORY_SEPARATOR;
    $fileName = basename($_FILES['bukti_penerimaan']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'];

    if (in_array($fileExt, $allowedExts)) {
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Buat folder jika belum ada

      if (move_uploaded_file($_FILES['bukti_penerimaan']['tmp_name'], $uploadDir . $fileName)) {
        $bukti_penerimaan = $fileName; // Simpan nama file jika berhasil diunggah
        //echo "File berhasil diunggah!";
      } else {
        //echo "Gagal menyimpan file!";
      }
    }
  }

  $inputData = [
    'tanggal_catat' => date("Y-m-d"),
    'tanggal_terima' => $_POST['tanggal_terima'],
    'jenis_terima' => $_POST['jenis_terima'],
    'akun' => $_POST['akun'],
    'bank' => isset($_POST['bank']) && $_POST['bank'] !== '' ? $_POST['bank'] : NULL,
    'jumlah' => $_POST['jumlah'],
    'deskripsi' => $_POST['deskripsi'],
    'bukti_penerimaan' => $bukti_penerimaan ?? NULL,
    'penanggung_jawab' => $_POST['penanggung_jawab'] ?? NULL
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data9'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data9'])) {
      $_SESSION['temp_data9'] = [];
    }
    $_SESSION['temp_data9'][] = $inputData;
  }
  unset($_SESSION['is_edit_mode']);
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];
  if (isset($_SESSION['temp_data9'][$indexToEdit])) {
    $editData = $_SESSION['temp_data9'][$indexToEdit];
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

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];
  if (isset($_SESSION['temp_data9'][$indexToDelete])) {
    unset($_SESSION['temp_data9'][$indexToDelete]);
    $_SESSION['temp_data9'] = array_values($_SESSION['temp_data9']);
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data9']);
  unset($_SESSION['edit_index']);
  unset($_SESSION['edit_data']);
  unset($_SESSION["tanggal_terima"]);
  unset($_SESSION["jenis_terima"]);
}

if (isset($_POST["submit_all"])) {

  if (isset($_SESSION['temp_data9']) && count($_SESSION['temp_data9']) > 0) {
    foreach ($_SESSION['temp_data9'] as $data) {

      $datafield = array("id_fiskal", "id_akun", "id_user", "id_bank",  "jumlah_penerimaan", "tanggal_penerimaan", "tanggal_pencatatan", "jenis_penerimaan",  "deskripsi_penerimaan", "penanggungJawab_penerimaan", "bukti_penerimaan", "status",);
      $datavalue = array($id_fiskal, $data['akun'], $id_user, $data['bank'], $data['jumlah'],  $data['tanggal_terima'],  $data['tanggal_catat'],  $data['jenis_terima'],  $data['deskripsi'], $data['penanggung_jawab'], $data['bukti_penerimaan'], "Belum Tervalidasi");

      $insert = new cInsert();
      $insert->fInsertData($datafield, "realisasi_penerimaan_gereja", $datavalue, 27);
    }
    unset($_SESSION['temp_data9']);
    unset($_SESSION["tanggal_terima"]);
    unset($_SESSION["jenis_terima"]);
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
      _myHeader("newspaper", "Penerimaan Gereja", "Entri Data");
      ?>
    </div>
  </div>
  <br>
  <div class="section">
    <form action="" method="post" enctype="multipart/form-data">
      <div class="horizontal">
        <div class="form-group" style="width:100%; margin-left: 30px;">
          <label for="tanggalPenerimaan" class="required">Tanggal Penerimaan</label>
          <input style="width:90%" type="date" class="form-control" name="tanggal_terima" id="" max="<?= date('Y-m-d'); ?>" value="<?= isset($_POST['tanggal_terima']) ? $_POST['tanggal_terima'] : (isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal_terima'] : ''); ?>"
            required />
          <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
          <?php
          if (isset($_POST['tanggal_terima'])) {
            $_SESSION['tanggal_terima'] = $_POST['tanggal_terima'];
          }
          ?>
        </div>
        <div class="form-group" style="width:100%; ">
          <label for="jenis" class="required">Jenis Penerimaan</label>
          <input style="width:90%;" type="text" id="jenis_terima" name="jenis_terima" placeholder="Masukkan Jenis Penerimaan"
            value="<?= isset($_POST['jenis_terima']) ? $_POST['jenis_terima'] : (isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jenis_terima'] : ''); ?>"
            required />
          <?php
          if (isset($_POST['jenis_terima'])) {
            $_SESSION['jenis_terima'] = $_POST['jenis_terima'];
          }
          ?>
        </div>
      </div>
  </div>
  <br>
  <div class="section">

    <div class="horizontal">
      <div class="form-group" style="width:45%; margin-left: 30px;">
        <label for="akun" class="required">Akun</label>
        <select style="width:95%" id="akun" name="akun" required>
          <option value="">-- Pilih Akun --</option>
          <?php
          $sql = "SELECT id_akun, nama_akun FROM akun WHERE jenis_debitKredit = 'Kredit' AND status_input = 1 ORDER BY kode_akun ASC";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = '';
              if (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['akun'] == $row['id_akun']) {
                $selected = 'selected';
              }
              echo "<option value='" . $row['id_akun'] . "' $selected>" . $row['nama_akun'] . "</option>";
            }
          } else {
            echo "<option value=''>Data tidak tersedia</option>";
          }
          // if (isset($_POST['akun'])) {
          //   $_SESSION['akun'] = $_POST['akun'];
          // }
          ?>
        </select>
      </div>
      <div class="form-group" style="width:40%; margin-left: 30px;">
        <label for="jumlah" class="required">Jumlah Penerimaan</label>
        <input style="width:100%" type="number" id="jumlah" name="jumlah" placeholder="Masukkan Jumlah Penerimaan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" min="1" required />
      </div>
    </div>
  </div>
  <br>
  <div class="section">
    <div class="horizontal">

      <div class="form-group" style="width:25%; margin-left: 30px;">
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
      <div class="form-group" style="width:25%; margin-left: -30px;">
        <label for="bukti_penerimaan">Bukti Penerimaan</label>
        <input style="width:95%" type="file" class="form-control" id="bukti_penerimaan" name="bukti_penerimaan" accept=".pdf, .docx, .xlsx">
      </div>

      <div class="form-group" style=" width:25%; margin-left: -30px;">
        <label for="jenis">Keterangan</label>
        <input style="width:87%" type="text" id="deskripsi" name="deskripsi" placeholder="Masukkan Keterangan Penerimaan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['deskripsi'] : ''; ?>" />
      </div>
      <div class="form-group" style=" width:25%; margin-left: -50px; margin-right: 50px;">
        <label for="penanggung_jawab" class="required">Penanggung Jawab</label>
        <input style="width:87%" type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="Masukkan Penanggung Jawab" value="<?= $_POST['penanggung_jawab'] ?? ($_SESSION['edit_data']['penanggung_jawab'] ?? '') ?>" required>
      </div>

    </div>
  </div>
  <br>

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
  </div>
  </form>

  <br>

  <div class="secondsection">
    <form action="" method="post">
      <table id='data-table' class='table table-condensed table-bordered'>
        <thead>
          <tr class='small'>
            <td width='2%' class="text-center">No</td>
            <td width='10%' class="text-center">Tanggal Penerimaan</td>
            <td width='15%' class="text-center">Jenis Penerimaan</td>
            <td width='18%' class="text-center">Akun</td>
            <td width='10%' class="text-center">Jumlah Penerimaan</td>
            <td width='16%' class="text-center">Bank Penerima</td>
            <td width='' class="text-center">Keterangan</td>
            <td width='' class="text-center">Bukti Penerimaan</td>
            <td width='' class="text-center">Penanggung Jawab</td>
            <td width='5%'></td>
            <td width='5%'></td>
          </tr>
        </thead>
        <tbody>

          <?php
          $cnourut = 0;
          $total = 0;
          if (isset($_SESSION['temp_data9']) && count($_SESSION['temp_data9']) > 0) {
            foreach ($_SESSION['temp_data9'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunNama = getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']);
              $bankNama = !empty($data['bank']) ?  getNameFromId('bank', 'id_bank', 'nama_bank', $data['bank']) : NULL;
              $rekeningNama = !empty($data['bank']) ? getNameFromId('bank', 'id_bank', 'nama_rekening', $data['bank']) : NULL;
          ?>
              <tr class=''>
                <td class="text-right"><?= $cnourut; ?></td>
                <td><?= date("d-M-Y", strtotime($data["tanggal_terima"])); ?></td>
                <td><?= $data["jenis_terima"]; ?></td>
                <td><?= $akunNama; ?></td>
                <td class="text-end"><?= number_format((float)$data["jumlah"], 0, ',', '.') ?> </td>
                <td><?= $bankNama . " - " .  $rekeningNama ?></td>
                <td><?= !empty($data["deskripsi"]) ? $data["deskripsi"] : '-'; ?></td>
                <td><?= !empty($data["bukti_penerimaan"]) ? $data["bukti_penerimaan"] : '-'; ?></td>
                    <td><?= $data["penanggung_jawab"]; ?></td>
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
              $total += $data["jumlah"];
            }
          } else {
            echo "<tr><td colspan='12' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
        </tbody>
        <tr class=''>
          <td></td>
          <td></td>
          <td colspan="2">Total</td>
          <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?> </td>
          <td></td>
          <td></td>
          <td>
          </td>
          <td>
          </td>
        </tr>
      </table>
      <div style="display: flex; justify-content: center; align-items: center; ">
        <button class="button" type="submit" name="submit_all"> Simpan</button>&nbsp; &nbsp; &nbsp;
        <button class="button" type="submit" name="reset" style="width:12%">Hapus Semua Data</button>
      </div>
    </form>
  </div>
</div>

<script>
  function resetTable() {
    var tableBody = document.getElementById("data-table").getElementsByTagName('tbody')[0];
    tableBody.innerHTML = '';
  }
</script>
</body>