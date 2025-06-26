<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {
  $akun_sumber = $_POST['akun_sumber'];

  $view = new cView();

  $sql = "SELECT akunjenis.jenis_akun FROM akunjenis INNER JOIN akun ON akunjenis.id_jenisAkun = akun.jenis_akun WHERE id_akun = $akun_sumber";  
  $array = $view->vViewData($sql);
  $jenis_akun = $array[0]['jenis_akun'];

  $query = "SELECT saldo_akhir FROM v_saldo_akun WHERE jenis_akun = '$jenis_akun' AND tahun = $tahun_aktif";  
  $array = $view->vViewData($query);
  $saldo = $array[0]['saldo_akhir'];

  if  ($_POST['jumlah'] > $saldo) {
    echo "<script>
               Swal.fire({
               position:'center',
               width:'25em',
               icon: 'error',	
               text: 'Saldo $jenis_akun tidak mencukupi!',
               type: 'error',
               }).then(function (result) {
               if (true) {
               window.location = '';
               }
           }) </script>";
  } else {

  $_SESSION['is_edit_mode'] = false;

  $inputData = [
    'tanggal_catat' => date("Y-m-d"),
    'akun_sumber' => $_POST['akun_sumber'],
    'tanggal' => $_POST['tanggal'],
    'jumlah' => $_POST['jumlah'],
    'akun_tujuan' => $_POST['akun_tujuan'],
    'keterangan' => $_POST['keterangan'],
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data24'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data24'])) {
      $_SESSION['temp_data24'] = [];
    }
    $_SESSION['temp_data24'][] = $inputData;
  }
}
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data24']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  if (isset($_SESSION['temp_data24'][$indexToDelete])) {
    unset($_SESSION['temp_data24'][$indexToDelete]);
    $_SESSION['temp_data24'] = array_values($_SESSION['temp_data24']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  if (isset($_SESSION['temp_data24'][$indexToEdit])) {
    $editData = $_SESSION['temp_data24'][$indexToEdit];
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

  if (isset($_SESSION['temp_data24']) && count($_SESSION['temp_data24']) > 0) {
    foreach ($_SESSION['temp_data24'] as $data) {

      $datafield1 = array("id_fiskal", "id_akun", "id_user", "id_bank",  "jumlah_penerimaan", "tanggal_penerimaan", "tanggal_pencatatan", "jenis_penerimaan",  "deskripsi_penerimaan", "penanggungJawab_penerimaan", "bukti_penerimaan", "status",);
      $datavalue1 = array($id_fiskal, $data['akun_tujuan'], $id_user, NULL, $data['jumlah'],  $data['tanggal'],  $data['tanggal_catat'],  $data['keterangan'],  NULL, 'Bendahara Gereja', NULL, "Tervalidasi");

      $datafield2 = array("id_fiskal", "id_akun", "tanggal_pengeluaran", "bukti_pengeluaran",  "jenis_pengeluaran",  "jumlah", "tanggal_catat", "status", "id_user");
      $datavalue2 = array($id_fiskal,  $data['akun_sumber'], $data['tanggal'], NULL, $data['keterangan'],  $data['jumlah'], $data['tanggal_catat'], "Tervalidasi", $id_user);


      $insert = new cInsert();
      $insert->fInsertData($datafield1, "realisasi_penerimaan_gereja", $datavalue1, '');
      $insert->fInsertData($datafield2, "realisasi_pengeluaran_gereja", $datavalue2, 271);
    }
    unset($_SESSION['temp_data24']);
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
      _myHeader("newspaper", "Pemindahan Dana", "Entri Data Pemindahan Dana");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post" enctype="multipart/form-data">
      <div class="horizontal">
        <div class="form-group" style="width:100%; margin-left: 30px; ">
          <label for="tanggal" class="required">Tanggal Pemindahan</label>
          <input style="width:90%" type="date" id="tanggal" name="tanggal" placeholder="" max="<?= date('Y-m-d'); ?>" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal'] : ''; ?>" required />
          <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
          <?php
          if (isset($_POST['tanggal'])) {
            $_SESSION['tanggal'] = $_POST['tanggal'];
          }
          ?>
        </div>
        <div class="form-group" style="width: 100%">
          <label for="keterangan" class="required">Keterangan Pengeluaran</label>
          <input style="width:77%" type="text" id="keterangan" name="keterangan" placeholder="Masukkan Keterangan Pengeluaran" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['keterangan'] : ''; ?>" required />
        </div>
      </div>
      <br>
      <div class="horizontal" style="height:80px">
        <div class="form-group" style="width:40%; margin-left: 30px ">
          <label for="akun_sumber" class="required">Sumber Akun</label>
          <select style="width:90%" id="akun_sumber" name="akun_sumber" required>
            <option value="">-- Pilih Akun --</option>
            <?php
            $sql = "SELECT id_akun, nama_akun FROM akun where jenis_debitKredit = 'Debet' ORDER BY kode_akun";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $selected = '';
                if (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['akun_sumber'] == $row['id_akun']) {
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

        <div class="form-group" style="width:20%; margin-left: -70px ">
          <label for="jumlah" class="required">Jumlah Pemindahan(Rp)</label>
          <input style="width:90%;" type="number" id="jumlah" name="jumlah" placeholder="Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" min="1" required />
        </div>

        <div class="horizontal" style="height:80px">
          <div class="form-group" style="width: 120%; margin-left: -40px">
            <label for="akun_tujuan" class="required">Akun Tujuan</label>
            <select style="width:90%" id="akun_tujuan" name="akun_tujuan" required>
              <option value="">-- Pilih Akun --</option>
              <?php
              $sql = "SELECT id_akun, nama_akun FROM akun where jenis_debitKredit = 'Kredit' ORDER BY kode_akun";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $selected = '';
                  if (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['akun_tujuan'] == $row['id_akun']) {
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
            <td width='5%' class="text-center">No</td>
            <td width='14%' class="text-center">Tanggal Pemindahan</td>
            <td width='14%' class="text-center">Keterangan</td>
            <td width='17%' class="text-center">Akun Sumber</td>
            <td width='17%' class="text-center">Jumlah Pemindahan</td>
            <td width='10%' class="text-center">Akun Tujuan</td>
            <td width='5%'></td>
            <td width='5%'></td>
          </tr>
        </thead>
        <tbody>
          <?php
          $cnourut = 0;
          $total = 0;

          if (isset($_SESSION['temp_data24']) && count($_SESSION['temp_data24']) > 0) {
            foreach ($_SESSION['temp_data24'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunSumber = !empty($data['akun_sumber']) ?  getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun_sumber']) : '-';
              $akunTujuan = !empty($data['akun_tujuan']) ?  getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun_tujuan']) : '-';
          ?>
              <tr class=''>
                <td class="text-right"><?= $cnourut; ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal"])); ?></td>
                <td><?= $data["keterangan"]; ?></td>
                <td><?= $akunSumber; ?></td>
                <td class="text-end"><?= number_format((float)$data["jumlah"], 0, ',', '.'); ?></td>
                <td><?= $akunTujuan; ?></td>
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
            echo "<tr><td colspan='5' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="color:#483d8b; font-weight:bolder">Total</td>
            <td style="color:#483d8b; font-weight:bolder" class="text-end"><?= number_format($total, 0, ',', '.'); ?></td>
            <td></td>
          </tr>
        </tbody>
      </table>

      <div style="display: flex; justify-content: center; text-align: center; ">
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