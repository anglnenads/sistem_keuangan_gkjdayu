<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {

  $_SESSION['is_edit_mode'] = false;

  if (!empty($_FILES['bukti_pengeluaran']['name'])) {
    $uploadDir = realpath('../uploads/bukti_transfer/') . DIRECTORY_SEPARATOR;
    $fileName = basename($_FILES['bukti_pengeluaran']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'];

    if (in_array($fileExt, $allowedExts)) {
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true); // Buat folder jika belum ada

      if (move_uploaded_file($_FILES['bukti_pengeluaran']['tmp_name'], $uploadDir . $fileName)) {
        $bukti_pengeluaran = $fileName; // Simpan nama file jika berhasil diunggah
        //echo "File berhasil diunggah!";
      } else {
        //echo "Gagal menyimpan file!";
      }
    }
  }

  $inputData = [
    'tanggal_catat' => date("Y-m-d"),
    'bukti_pengeluaran' => $bukti_pengeluaran ?? NULL,
    'akun' => $_POST['akun'],
    'tanggal' => $_POST['tanggal'],
    'jenis_pengeluaran' => $_POST['jenis_pengeluaran'],
    'total' => $_POST['total'],
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data16'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data16'])) {
      $_SESSION['temp_data16'] = [];
    }
    $_SESSION['temp_data16'][] = $inputData;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data16']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  if (isset($_SESSION['temp_data16'][$indexToDelete])) {
    unset($_SESSION['temp_data16'][$indexToDelete]);
    $_SESSION['temp_data16'] = array_values($_SESSION['temp_data16']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  if (isset($_SESSION['temp_data16'][$indexToEdit])) {
    $editData = $_SESSION['temp_data16'][$indexToEdit];
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
  unset($_SESSION["bidang"]);
  unset($_SESSION["komisi"]);
  unset($_SESSION["bukti_pengeluaran"]);

  if (isset($_SESSION['temp_data16']) && count($_SESSION['temp_data16']) > 0) {
    foreach ($_SESSION['temp_data16'] as $data) {

      $datafield = array("id_fiskal",  "id_akun", "tanggal_pengeluaran", "bukti_pengeluaran",  "jenis_pengeluaran",  "jumlah", "tanggal_catat", "status", "id_user");
      $datavalue = array($id_fiskal,  $data['akun'], $data['tanggal'], $data['bukti_pengeluaran'], $data['jenis_pengeluaran'],  $data['total'], $data['tanggal_catat'], "Tervalidasi", $id_user);

      $insert = new cInsert();
      $insert->fInsertData($datafield, "realisasi_pengeluaran_gereja", $datavalue, 271);
    }
    unset($_SESSION['temp_data16']);
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
      _myHeader("newspaper", "Pengeluaran Gereja", "Entri Data Pengeluaran");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post" enctype="multipart/form-data">
      <div class="horizontal">
        <div class="form-group" style="width:100%; margin-left: 30px; ">
          <label for="tanggal" class="required">Tanggal Pengeluaran</label>
          <input style="width:90%" type="date" id="tanggal" name="tanggal" placeholder="" max="<?= date('Y-m-d'); ?>" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal'] : ''; ?>" required />
          <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
          <?php
          if (isset($_POST['tanggal'])) {
            $_SESSION['tanggal'] = $_POST['tanggal'];
          }
          ?>
        </div>

        <div class="form-group" style="width:100%; ">
          <label for="akun" class="required">Akun</label>
          <select style="width:90%" id="akun" name="akun" required>
            <option value="">-- Pilih Akun --</option>
            <?php
            $sql = "SELECT id_akun, nama_akun FROM akun WHERE jenis_debitKredit = 'Debet' AND status_input = 1 ORDER BY kode_akun ASC";
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
      </div>
      <br>
      <div class="horizontal" style="height:80px">
        <div class="form-group" style="width: 100%; margin-left: 30px;">
          <label for="jenis_pengeluaran" class="required">Keterangan Pengeluaran</label>
          <input style="width:90%" type="text" id="jenis_pengeluaran" name="jenis_pengeluaran" placeholder="Masukkan Keterangan Pengeluaran" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jenis_pengeluaran'] : ''; ?>" required />
        </div>

        <div class="form-group" style="width:100%">
          <label for="total" class="required">Jumlah Pengeluaran(Rp)</label>
          <input style="width:90%;" type="number" id="total" name="total" placeholder="Total" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['total'] : ''; ?>" min="1" required />
        </div>
      </div>
      <br>
      <div class="horizontal" style="height:70px">
        <div class="form-group" style="width:50%; margin-left: 30px;">
          <label for="bukti_pengeluaran">Bukti Pengeluaran</label>
          <input style="width:85%" type="file" class="form-control" id="bukti_pengeluaran" name="bukti_pengeluaran">
        </div>
      </div>
  </div>
  <br>


  <div class="section">
   <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 40px;">
      <div style="display: flex; align-items: center; gap: 20px; color: #002e63;">
        <p style="margin: 0;"><b>Pemindahan Dana:</b></p>
        <a href="282"
          style="
             display: inline-flex;
             align-items: center;
             justify-content: center;
             padding: 6px 15px;
             background-color: transparent;
             border-radius: 4px;
             color: #a42711;
             text-decoration: underline;
             font-weight: 600;
           ">
          Pemindahan Dana
        </a>
      </div>
      <div style="display: flex; gap: 20px; align-items: center;">
        <?php if (isset($_SESSION['is_edit_mode']) && $_SESSION['is_edit_mode']): ?>
          <button class="button" type="submit" name="save" style="background-color: #002e63; height: 35px; width: 200px;">Tambah</button>
          <button class="button2" type="submit" name="cancel" style="background-color: #b22222; height: 35px; width: 200px;">Cancel</button>
        <?php else: ?>
          <button class="button" type="submit" name="save" style="background-color: #002e63; height: 35px; width: 200px;">Tambah</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
  </form>
  <br>
  <div class="secondsection">
    <form action="" method="post">
      <table id='data-table' class='table table-condensed table-bordered'>
        <thead>
          <tr class='small'>
            <td width='5%' class="text-center">No</td>
            <td width='14%' class="text-center">Tanggal Pengeluaran</td>
            <td width='17%' class="text-center">Akun</td>
            <td width='17%' class="text-center">Keterangan Pengeluaran</td>
            <td width='10%' class="text-center">Jumlah Pengeluaran</td>
            <td width='13%' class="text-center">Bukti Pengeluaran</td>
            <td width='5%'></td>
            <td width='5%'></td>
          </tr>
        </thead>
        <tbody>
          <?php
          $cnourut = 0;
          $total = 0;

          if (isset($_SESSION['temp_data16']) && count($_SESSION['temp_data16']) > 0) {
            foreach ($_SESSION['temp_data16'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunNama = !empty($data['akun']) ?  getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']) : '-';
          ?>
              <tr class=''>
                <td class="text-right"><?= $cnourut; ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal"])); ?></td>
                <td><?= $akunNama; ?></td>
                <td><?= $data["jenis_pengeluaran"]; ?></td>
                <td class="text-end"><?= number_format((float)$data["total"], 0, ',', '.'); ?></td>
                <td><?= !empty($data["bukti_pengeluaran"]) ? $data["bukti_pengeluaran"] : '-'; ?></td>
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
              $total += $data["total"];
            }
          } else {
            echo "<tr><td colspan='8' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="color:#483d8b; font-weight:bolder">Total</td>
            <td style="color:#483d8b; font-weight:bolder" class="text-end"><?= number_format($total, 0, ',', '.'); ?></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </tbody>
      </table>

      <div style="display: flex; justify-content: center; align-jenis_pengeluarans: center; ">
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
</script>
</body>

</html>