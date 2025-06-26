<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {
  $_SESSION['is_edit_mode'] = false;

  $inputData = [
    'bidang' => $_POST["bidang"],
    'komisi' => $_POST["komisi"] ?? NULL,
    'akun' => $_POST['akun'],
    'jenis_kegiatan' => $_POST['jenis_kegiatan'],
    'penanggung_jawab' => $_POST['penanggung_jawab'],
    'tanggal_pengajuan' => $_POST['tanggal_pengajuan'],
    'jumlah' => $_POST['jumlah'],
    'deskripsi' => $_POST['deskripsi']
  ];
  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data8'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data8'])) {
      $_SESSION['temp_data8'] = [];
    }
    $_SESSION['temp_data8'][] = $inputData;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data8']);
  unset($_SESSION['bidang']);
  unset($_SESSION['komisi']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  if (isset($_SESSION['temp_data8'][$indexToDelete])) {
    unset($_SESSION['temp_data8'][$indexToDelete]);
    $_SESSION['temp_data8'] = array_values($_SESSION['temp_data8']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  if (isset($_SESSION['temp_data8'][$indexToEdit])) {
    $editData = $_SESSION['temp_data8'][$indexToEdit];

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


// insert ke database
if (isset($_POST["submit_all"])) {

  if (isset($_SESSION['temp_data8']) && count($_SESSION['temp_data8']) > 0) {
    foreach ($_SESSION['temp_data8'] as $data) {

      $datafield = array("id_akun", "id_bidang", "id_komisi", "id_program", "id_anggaran", "jenis_kegiatan", "id_bank", "jumlah_pengajuan", "tanggal_pengajuan", "tanggal_proses", "tanggal_transfer", "penanggungJawab_pengajuan", "deskripsi_pengajuan", "status", "id_user", "id_fiskal");
      $datavalue = array($data['akun'], $data['bidang'], $data['komisi'], 0, 0, $data['jenis_kegiatan'], NULL, $data['jumlah'], $data['tanggal_pengajuan'],  '0000-00-00',  '0000-00-00', $data['penanggung_jawab'], $data['deskripsi'], 'Menunggu', $id_user, $id_fiskal);

      $insert = new cInsert();
      $insert->fInsertData($datafield, "pengajuan", $datavalue, 24);
    }

    unset($_SESSION['temp_data8']);
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
      _myHeader("newspaper", "Pengajuan Insidental", "Entri Data");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post">
      <div class="horizontal">
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
                } elseif (isset($_SESSION['bidang']) && $_SESSION['bidang'] == $row['id_bidang']) {
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
                }  elseif (isset($_SESSION['komisi']) && $_SESSION['komisi'] == $row['id_komisi']) {
                  $selected = 'selected';
                } elseif (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['komisi'] == $row['id_komisi']) {
                  $selected = 'selected';
                }
                echo "<option value='" . $row['id_komisi'] . "' $selected>" . $row['nama_komisi'] . "</option>";
              }
            } else {
              echo "<option value=''>Data tidak tersedia</option>";
            }
            if (isset($_POST['komisi'])) {
              $_SESSION['komisi'] = $_POST['komisi'];
            }
            ?>
          </select>
        </div>

      </div>
  </div>
  <br>
  <div class="section">
    <div class="horizontal">
      <div class="form-group" style="width:100%;  margin-left:30px">
        <label for="tanggal_pengajuan" class="required">Tanggal Pengajuan</label>
        <input style="width:90%" type="date" id="tanggal_pengajuan" name="tanggal_pengajuan" placeholder="" required value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal_pengajuan'] : ''; ?>" />
        <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
      </div>
      <div class="form-group" style="width:100%;">
        <label for="akun" class="required">Akun</label>
        <select style="width:90%" id="akun" name="akun" required>
          <option value="">-- Pilih Akun --</option>
          <?php
          $sql = "SELECT id_akun, nama_akun FROM akun where jenis_debitKredit = 'Debet' AND status_input = 1 ORDER BY kode_akun";
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = isset($_SESSION['edit_data']) && $_SESSION['edit_data']['akun'] == $row['id_akun'] ? 'selected' : '';
              echo "<option value='" . $row['id_akun'] . "' $selected>" . $row['nama_akun'] . "</option>";
            }
          } else {
            echo "<option value=''>Data tidak tersedia</option>";
          }
          ?>
        </select>
      </div>
    </div>
    <br>
    <div class="horizontal">
      <div class="form-group" style="width:100%; margin-left:30px">
        <label for="jenis_kegiatan" class="required">Jenis Kegiatan</label>
        <input style="width:90%" type="text" id="jenis_kegiatan" name="jenis_kegiatan" placeholder="Masukkan Jenis Kegiatan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jenis_kegiatan'] : ''; ?>" required />
      </div>
      <div class="form-group" style="width:100%; ">
        <label for="jumlah" class="required">Jumlah Pengajuan</label>
        <input style="width:90%;" type="number" id="jumlah" name="jumlah" placeholder="Jumlah Pengajuan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" required>
      </div>
    </div>
    <div class="horizontal">
      <div class="form-group" style=" width:100%;  margin-left:30px">
        <label for="deskripsi">Keterangan</label>
        <input style="width:90%" type="text" id="deskripsi" name="deskripsi" placeholder="Masukkan Keterangan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['deskripsi'] : ''; ?>" />
      </div>
      <div class="form-group" style="width:100%;">
        <label for="penanggung_jawab" class="required">Penanggung Jawab</label>
        <input style="width:90%" type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="Masukkan Penanggung Jawab" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['penanggung_jawab'] : ''; ?>" required />
      </div>
    </div>
  </div>
  <br>
  <div class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 40px;">
      <div style="display: flex; align-items: center; gap: 20px; color: #002e63;">
        <p style="margin: 0;"><b>Pengajuan Biasa:</b></p>
        <a href="34"
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
          Pengajuan Biasa
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

    </form>
  </div>
  <br>

  <div class="secondsection">
    <form action="" method="post">
      <table id='data-table' class='table table-condensed table-bordered'>
        <thead>
          <tr class='small'>
            <td width='5%' class="text-right">No</td>
            <td>Bidang</td>
            <td>Komisi</td>
            <td width=''>Tanggal Pengajuan</td>
            <td width=''>Akun</td>
            <td width=''>Jenis Kegiatan</td>
            <td width=''>Jumlah Pegajuan</td>
            <td width=''>Penanggung Jawab</td>
            <td width=''>Keterangan</td>
            <td width='5%'></td>
            <td width='5%'></td>
          </tr>
        </thead>
        <tbody>

          <?php
          $cnourut = 0;
          $total = 0;
          if (isset($_SESSION['temp_data8']) && count($_SESSION['temp_data8']) > 0) {
            foreach ($_SESSION['temp_data8'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunNama = getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']);

              $bidangNama = getNameFromId('bidang', 'id_bidang', 'nama_bidang', $data['bidang']);
              $komisiNama = getNameFromId('komisi', 'id_komisi', 'nama_komisi', $data['komisi']);
          ?>
              <tr class=''>
                <td class="text-right"><?= $cnourut; ?></td>
                <td><?= $bidangNama ?></td>
                <td><?= $komisiNama ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal_pengajuan"])); ?></td>
                <td><?= $akunNama; ?></td>
                <td><?= $data["jenis_kegiatan"]; ?></td>
                <td class="text-end"><?= number_format($data["jumlah"], 0, ',', '.') ?></td>
                <td><?= $data["penanggung_jawab"]; ?></td>
                <td><?= $data["deskripsi"]; ?></td>
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
            }
            $total += $data['jumlah'];
          } else {
            echo "<tr><td colspan='12' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
        </tbody>
        <tr>
          <td colspan="3"></td>
          <td colspan="3" style="color:#5B90CD; font-weight:bolder">Total</td>
          <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total, 0, ',', '.') ?></td>
          <td colspan="5"></td>
        </tr>
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

    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: "bidang=" + id_bidang,
      success: function(data) {
        $("#komisi").html(data);
      },
    });
  });
</script>
</body>

</html>