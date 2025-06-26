<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {
  $_SESSION['is_edit_mode'] = false;

  $inputData = [
    'akun' => isset($_POST['akun']) && $_POST['akun'] !== '' ? $_POST['akun'] : NULL,
    'item' => $_POST['item'],
    'jumlah' => $_POST['jumlah'],
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data3'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data3'])) {
      $_SESSION['temp_data3'] = [];
    }
    $_SESSION['temp_data3'][] = $inputData;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data3']);
  unset($_SESSION['bidang']);
  unset($_SESSION['komisi']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  if (isset($_SESSION['temp_data3'][$indexToDelete])) {
    unset($_SESSION['temp_data3'][$indexToDelete]);
    $_SESSION['temp_data3'] = array_values($_SESSION['temp_data3']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  if (isset($_SESSION['temp_data3'][$indexToEdit])) {
    $editData = $_SESSION['temp_data3'][$indexToEdit];
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

  if (isset($_SESSION['temp_data3']) && count($_SESSION['temp_data3']) > 0) {
    foreach ($_SESSION['temp_data3'] as $data) {

      $datafield = array("id_user",  "id_fiskal", "id_akun",  "nama_anggaran", "jumlah");
      $datavalue = array($id_user,  $id_fiskal, $data['akun'], $data['item'], $data['jumlah']);

      $insert = new cInsert();
      $insert->fInsertData($datafield, "rencana_pengeluaran_gereja", $datavalue, 22);
    }
    unset($_SESSION['temp_data3']);
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
      _myHeader("newspaper", "Rencana Pengeluaran Gereja", "Entri Data");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post">
    <div class="horizontal">
      <div class="form-group" style="width:100%;  margin-left: 30px;">
        <label for="akun" class="required">Akun</label>
        <select style="width:95%" id="akun" name="akun" required>
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
          ?>
        </select>
      </div>
    </div>
  </div>
 
  <br>
  <div class="section">
    <div class="horizontal" style="height:110px">
      <div class="form-group" style="width: 100%; margin-left: 30px;">
        <label for="item" class="required">Jenis Pengeluaran/Kegiatan</label>
        <input style="width:90%" type="text" id="item" name="item" placeholder="Masukkan Jenis Pengeluaran/Kegiatan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['item'] : ''; ?>" required />
      </div>
      <div class="form-group" style="width:100%">
        <label for="jumlah" class="required">Jumlah</label>
        <input style="width:90%" type="number" id="jumlah" name="jumlah" placeholder="Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" min="1" required />
      </div>
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
</div>
</form>
<br>

<div class="secondsection">
  <form action="" method="post">
    <table id='data-table' class='table table-condensed table-bordered'>
      <thead>
        <tr class='small'>
          <td width='2%' class="text-left">No</td>
          <td width='25%'>Akun</td>
          <td width='25%'>Jenis Kegiatan</td>       
          <td width='20%' class="text-center">Jumlah</td>
          <td width='5%'></td>
          <td width='5%'></td>
        </tr>
      </thead>
      <tbody>
        <?php
        $cnourut = 0;
        $total = 0;
        if (isset($_SESSION['temp_data3']) && count($_SESSION['temp_data3']) > 0) {
          foreach ($_SESSION['temp_data3'] as $index => $data) {
            $cnourut = $cnourut + 1;
            $akunNama = !empty($data['akun']) ?  getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']) : NULL;
        ?>
            <tr class=''>
              <td class="text-right"><?= $cnourut; ?></td>
              <td><?= $akunNama; ?></td>
              <td><?= $data["item"]; ?></td>
              <td class="text-end"><?= number_format($data["jumlah"], 0, ',', '.'); ?></td>
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
          echo "<tr><td colspan='6' class='text-center'>Belum ada data</td></tr>";
        }
        ?>
        <tr>
          <td colspan="2"></td>
          <td colspan="" style="color:#5B90CD; font-weight:bolder">Total</td>
          <td class="text-end" style="color:#483d8b; font-weight:bolder"><?= number_format($total, 0, ',', '.'); ?></td>
          <td width='5%'></td>
          <td width='5%'></td>
        </tr>
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
</script>
</body>
</html>