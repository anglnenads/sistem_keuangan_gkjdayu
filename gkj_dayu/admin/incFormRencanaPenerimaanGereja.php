<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {
  $_SESSION['is_edit_mode'] = false;

  $inputData = [
    'akun' => $_POST['akun'],
    'jumlah' => $_POST['jumlah'],
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data2'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data2'])) {
      $_SESSION['temp_data2'] = [];
    }
    $_SESSION['temp_data2'][] = $inputData;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data2']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  // Hapus elemen berdasarkan index
  if (isset($_SESSION['temp_data2'][$indexToDelete])) {
    unset($_SESSION['temp_data2'][$indexToDelete]);
    // Reindex array agar tidak ada gap di antara index
    $_SESSION['temp_data2'] = array_values($_SESSION['temp_data2']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  // Muat data yang akan diedit ke form
  if (isset($_SESSION['temp_data2'][$indexToEdit])) {
    $editData = $_SESSION['temp_data2'][$indexToEdit];

    // Simpan data sementara untuk pengeditan
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
  if (isset($_SESSION['temp_data2']) && count($_SESSION['temp_data2']) > 0) {
    foreach ($_SESSION['temp_data2'] as $data) {

      $datafield = array("id_user",  "id_fiskal", "id_akun", "jumlah_penerimaan");
      $datavalue = array($id_user, $id_fiskal,  $data['akun'], $data['jumlah']);

      $insert = new cInsert();
      $insert->fInsertData($datafield, "rencana_penerimaan_gereja", $datavalue, 221);
    }
    unset($_SESSION['temp_data2']);
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
      _myHeader("newspaper", "Rencana Penerimaan Gereja", "Entri Data");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post">
      <div class="horizontal" style="height: 80px;">
        <div class="form-group" style="width:100%; margin-left: 30px;">
          <label for="akun" class="required">Akun</label>
          <select style="width:90%" id="akun" name="akun" required>
            <option value="">-- Pilih Akun --</option>
            <?php
            $sql = "SELECT id_akun, nama_akun FROM akun where jenis_debitKredit = 'Kredit' AND status_input = 1";
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
        <div class="form-group" style="width:100%">
          <label for="jumlah" class="required">Jumlah Rencana Penerimaan (Rp)</label>
          <input style="width:90%;" type="number" id="jumlah" name="jumlah" placeholder="Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" min="1" required />
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
            <td class="text-center" width='40%'>Akun</td>
            <td class="text-center" width='25%'>Jumlah</td>
            <td width='5%'></td>
            <td width='5%'></td>
          </tr>
        </thead>
        <tbody>
          <?php
          $cnourut = 0;
          $total = 0;
          if (isset($_SESSION['temp_data2']) && count($_SESSION['temp_data2']) > 0) {
            foreach ($_SESSION['temp_data2'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunNama = getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']);
          ?>
              <tr class=''>
                <td class="text-center"><?= $cnourut; ?></td>
                <td><?= $akunNama; ?></td>
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
            echo "<tr><td colspan='12' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
          <tr>
            <td></td>
            <td style="color:#5B90CD; font-weight:bolder">Total</td>
            <td class="text-end" style="color:#483d8b; font-weight:bolder"><?= number_format($total, 0, ',', '.'); ?></td>
            <td></td>
            <td></td>
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
    // Get the table body element
    var tableBody = document.getElementById("data-table").getElementsByTagName('tbody')[0];

    // Clear all rows inside the table body
    tableBody.innerHTML = '';
  }
</script>
</body>
</html>