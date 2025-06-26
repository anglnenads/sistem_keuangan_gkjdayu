<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
//insert ke tabel sementara
if (isset($_POST['save'])) {
  $_SESSION['is_edit_mode'] = false;

  $inputData = [
    'bidang' => $_POST["bidang"],
    'komisi' => $_POST["komisi"] ?? NULL,
    'nama_program' => $_POST['nama_program'],
    'tanggal_mulai' => $_POST['tanggal_mulai'],
    'tanggal_selesai' => $_POST['tanggal_selesai'],
    'keterangan' => $_POST['keterangan'] ?? NULL,
    'penanggung_jawab' => $_POST['penanggung_jawab'],
  ];


  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data_program'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data_program'])) {
      $_SESSION['temp_data_program'] = [];
    }
    $_SESSION['temp_data_program'][] = $inputData;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data_program']);
  unset($_SESSION['bidang']);
  unset($_SESSION['komisi']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  // Hapus elemen berdasarkan index
  if (isset($_SESSION['temp_data_program'][$indexToDelete])) {
    unset($_SESSION['temp_data_program'][$indexToDelete]);
    // Reindex array agar tidak ada gap di antara index
    $_SESSION['temp_data_program'] = array_values($_SESSION['temp_data_program']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  // Muat data yang akan diedit ke form
  if (isset($_SESSION['temp_data_program'][$indexToEdit])) {
    $editData = $_SESSION['temp_data_program'][$indexToEdit];

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


// insert ke database
if (isset($_POST["submit_all"])) {
  if (isset($_SESSION['temp_data_program']) && count($_SESSION['temp_data_program']) > 0) {
    foreach ($_SESSION['temp_data_program'] as $data) {

      $datafield = array("nama_program", "id_user", "id_fiskal", "id_bidang", "id_komisi", "tgl_mulai", "tgl_selesai", "keterangan", "penanggung_jawab");
      $datavalue = array($data['nama_program'], $id_user, $id_fiskal, $data['bidang'], $data['komisi'], $data['tanggal_mulai'], $data['tanggal_selesai'], $data['keterangan'],  $data['penanggung_jawab']);

      $insert = new cInsert();
      $insert->fInsertData($datafield, "program", $datavalue, 21);
    }

    unset($_SESSION["bidang"]);
    unset($_SESSION["komisi"]);
    unset($_SESSION['temp_data_program']);
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
      _myHeader("newspaper", "Program", "Entri Data");
      ?>
    </div>
  </div>

  <div class="section">
    <form action="" method="post">
      <div class="horizontal" style="height:70px">
        <div class="form-group" style="width:100%;  margin-left: 30px">
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
        <div class="form-group" style="width:100%;">
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
                } elseif (isset($_SESSION['komisi']) && $_SESSION['komisi'] == $row['id_komisi']) {
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

      <div class="form-group" style="width:45%; margin-left: 30px; margin-bottom: 10px;">
        <label for="nama_program" class="required">Nama Program</label>
        <input style="width:95%" type="text" id="nama_program" name="nama_program" placeholder="Masukkan Nama Program" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['nama_program'] : ''; ?>" required />
      </div>
      <div class="form-group" style="width:23%; margin-left: 30px;">
        <label for="tanggal_mulai" class="required">Tanggal Mulai</label>
        <input style="width:80%;" type="date" id="tanggal_mulai" name="tanggal_mulai" placeholder="" required value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal_mulai'] : ''; ?>" />
        <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
      </div>

      <div class="form-group" style="width:23%; margin-left: -50px">
        <label for="tanggal_selesai" class="required">Tanggal Selesai</label>
        <input style="width:80%" type="date" id="tanggal_selesai" name="tanggal_selesai" placeholder="" required value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal_selesai'] : ''; ?>" />
        <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
      </div>
    </div>
    <br>
    <div class="horizontal">

      <div class="form-group" style="width:100%; margin-left: 30px;">
        <label for="keterangan">Keterangan</label>
        <input style="width:90%;" type="text" id="keterangan" name="keterangan" placeholder="Masukkan Keterangan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['keterangan'] : ''; ?>" />
      </div>

      <div class="form-group" style="width:100%">
        <label for="penanggung_jawab" class="required">Penanggung Jawab</label>
        <input style="width:90%" type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="Masukkan Penanggung Jawab" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['penanggung_jawab'] : ''; ?>" required />
      </div>
    </div>
  </div>
  <br>
  <div class="section">
    <div style="display: flex; justify-content: flex-end; ">
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
            <td class="text-center" width='5%' class="text-right">No</td>
            <td class="text-center" width=''>Bidang</td>
            <td class="text-center" width=''>Komisi</td>
            <td class="text-center" width=''>Nama Program</td>
            <td class="text-center" width=''>Tanggal Mulai</td>
            <td class="text-center" width=''>Tanggal Selesai</td>
            <td class="text-center" width=''>Keterangan</td>
            <td class="text-center" width=''>Penanggung Jawab</td>
            <td width='5%'></td>
            <td width='5%'></td>
          </tr>
        </thead>
        <tbody>

          <?php
          $cnourut = 0;
          if (isset($_SESSION['temp_data_program']) && count($_SESSION['temp_data_program']) > 0) {
            foreach ($_SESSION['temp_data_program'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $namaBidang = getNameFromId('bidang', 'id_bidang', 'nama_bidang', $data['bidang']);
              $namaKomisi = !empty($data['komisi']) ? getNameFromId('komisi', 'id_komisi', 'nama_komisi', $data['komisi']) : '-';

          ?>
              <tr class=''>
                <td class="text-center"><?= $cnourut; ?></td>
                <td><?= $namaBidang ?></td>
                <td><?= $namaKomisi ?></td>
                <td><?= $data["nama_program"]; ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal_mulai"])); ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal_selesai"])); ?></td>
                <td><?= !empty($data["keterangan"]) ? $data["keterangan"] : "-"; ?></td>
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
            }
          } else {
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