<?php
include_once("../_function_i/inc_f_object.php");
if (isset($_POST['save'])) {

  $_SESSION['is_edit_mode'] = false;

  $inputData = [
    'bidang' => $_POST["bidang"],
    'komisi' => $_POST["komisi"] ?? NULL,
    'akun' => $_POST['akun'],
    'anggaran' => $_POST['anggaran'],
    'program' => $_POST['program'],
    'penanggung_jawab' => $_POST['penanggung_jawab'],
    'tanggal_pengajuan' => $_POST['tanggal_pengajuan'],
    'jumlah' => $_POST['jumlah'],
    'deskripsi' => $_POST['deskripsi'] ?? NULL
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data6'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data6'])) {
      $_SESSION['temp_data6'] = [];
    }
    $_SESSION['temp_data6'][] = $inputData;
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data6']);
  unset($_SESSION['bidang']);
  unset($_SESSION['komisi']);
  unset($_SESSION['program']);
}

if (isset($_POST['delete'])) {
  $indexToDelete = $_POST['delete_index'];

  if (isset($_SESSION['temp_data6'][$indexToDelete])) {
    unset($_SESSION['temp_data6'][$indexToDelete]);

    $_SESSION['temp_data6'] = array_values($_SESSION['temp_data6']);
  }
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];

  if (isset($_SESSION['temp_data6'][$indexToEdit])) {
    $editData = $_SESSION['temp_data6'][$indexToEdit];

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

  if (isset($_SESSION['temp_data6']) && count($_SESSION['temp_data6']) > 0) {
    foreach ($_SESSION['temp_data6'] as $data) {

      $datafield = array("id_akun", "id_bidang", "id_komisi", "id_program", "id_anggaran", "id_bank", "jumlah_pengajuan", "tanggal_pengajuan", "tanggal_proses", "tanggal_transfer", "penanggungJawab_pengajuan", "deskripsi_pengajuan", "status", "id_user", "id_fiskal");

      $datavalue = array($data['akun'], $data['bidang'], $data['komisi'], $data['program'], $data['anggaran'], NULL, $data['jumlah'], $data['tanggal_pengajuan'],  '0000-00-00',  '0000-00-00', $data['penanggung_jawab'], $data['deskripsi'], 'Menunggu', $id_user, $id_fiskal);

      $insert = new cInsert();
      $insert->fInsertData($datafield, "pengajuan", $datavalue, 24);
    }
    unset($_SESSION['temp_data6']);
    unset($_SESSION["bidang"]);
    unset($_SESSION["komisi"]);
    unset($_SESSION["program"]);
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
      _myHeader("newspaper", "Pengajuan", "Entri Data");
      ?>
    </div>
  </div>

  <div class="section" style="height: 110px">
    <form action="" method="post">
      <div class="horizontal">
        <div class="form-group" style="width:100%; margin-left: 30px;">
          <label for="bidang" class="required">Bidang</label>
          <select style="width:90%" id="bidang" name="bidang" required>
            <option value="">-- Pilih Bidang --</option>
            <?php
            $sql = "SELECT id_bidang, nama_bidang FROM bidang";
            $result = $conn->query($sql);

            // Cek apakah ada data
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
    <div class="horizontal">


      <div class="form-group" style="width:100%; margin-left:30px">
        <label for="program" class="required">Nama Program</label>
        <select style="width:90%" id="program" name="program" required>
          <option value="">-- Pilih Program --</option>
          <?php
          if (isset($_SESSION['komisi'])) {
            $query = "SELECT program.id_program, program.nama_program, program.id_fiskal, program.id_bidang, program.id_komisi, fiskal.tahun
                        FROM program  
                        JOIN komisi ON program.id_komisi = komisi.id_komisi JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                        WHERE tahun = $tahun_aktif AND program.id_komisi = " . $_SESSION['komisi'] . " ORDER BY id_komisi ASC";
          } else if (isset($_SESSION['bidang'])) {
            $query = "SELECT program.id_program, program.nama_program, program.id_fiskal, program.id_bidang, fiskal.tahun
                        FROM program  
                        JOIN bidang ON program.id_bidang = bidang.id_bidang JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                        WHERE tahun = $tahun_aktif AND program.id_bidang = " . $_SESSION['bidang'] . " ORDER BY id_bidang ASC";
          } else {
            $query = "SELECT program.id_program, program.nama_program, program.id_fiskal, program.id_bidang, program.id_komisi, komisi.nama_komisi,  fiskal.tahun
                          FROM program  
                          JOIN komisi ON program.id_komisi = komisi.id_komisi JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                          WHERE tahun = $tahun_aktif ORDER BY id_program ASC";
          }
          $result = $conn->query($query);

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = '';

              if (isset($_POST['program']) && $_POST['program'] == $row['id_program']) {
                $selected = 'selected';
              } elseif (isset($_SESSION['program']) && $_SESSION['program'] == $row['id_program']) {
                $selected = 'selected';
              } elseif (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['program'] == $row['id_program']) {
                $selected = 'selected';
              }

              //$label = !empty($row['nama_komisi']) ? $row['nama_komisi'] : $row['nama_bidang'];
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
      <div class="form-group" style="width:100%; ">
        <label for="tanggal_pengajuan" class="required">Tanggal Pengajuan</label>
        <input style="width:90%" type="date" id="tanggal_pengajuan" name="tanggal_pengajuan" placeholder="" required  max="<?= date('Y-m-d'); ?>"  value="<?= $_POST['tanggal_pengajuan'] ?? $_SESSION['edit_data']['tanggal_pengajuan'] ?? ''; ?>" />
        <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
      </div>
    </div>
    <div class="horizontal">
      <div class="form-group" style="width:100%;   margin-left: 30px;">
        <label for="akun" class="required">Akun</label>
        <select style="width:95%" id="akun" name="akun" required>
          <option value="">-- Pilih Akun --</option>
          <?php
          $sql = "SELECT id_akun, nama_akun FROM akun WHERE jenis_debitKredit = 'Debet' AND status_input = 1 ORDER BY kode_akun";
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
      <div class="form-group" style="width:100%; margin-left: 30px;">
        <label for="anggaran" class="required">Jenis Kegiatan</label>
        <select style="width:90%" id="anggaran" name="anggaran" required>
          <option value="">-- Pilih Jenis Kegiatan --</option>
          <?php
          if (isset($_SESSION['program'])) {

            if (isset($_SESSION['komisi'])) {

              $sql = "SELECT a.id_anggaran, k.nama_komisi, a.item FROM rencana_pengeluaran_komisi a ";
              $sql .= "JOIN komisi k ON a.id_komisi = k.id_komisi ";
              $sql .= "WHERE id_fiskal = $id_fiskal AND a.id_komisi = "  . $_SESSION['komisi'] . " AND a.id_program = " . $_SESSION['program'];
            } elseif (isset($_SESSION['bidang'])) {

              $sql = "SELECT a.id_anggaran, k.nama_bidang, a.item FROM rencana_pengeluaran_komisi a ";
              $sql .= "JOIN bidang k ON a.id_bidang = k.id_bidang ";
              $sql .= "WHERE id_fiskal = $id_fiskal AND a.id_bidang = "  . $_SESSION['bidang'];
            }
          } else {
            $sql = "SELECT a.id_anggaran, k.nama_komisi, a.item FROM rencana_pengeluaran_komisi a ";
            $sql .= "JOIN komisi k ON a.id_komisi = k.id_komisi ";
            $sql .= "WHERE id_fiskal = $id_fiskal";
          }
          $result = $conn->query($sql);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = isset($_SESSION['edit_data']) && $_SESSION['edit_data']['anggaran'] == $row['id_anggaran'] ? 'selected' : '';
              echo "<option value='" . $row['id_anggaran'] . "' $selected>" . $row['item'] . "</option>";
            }
          } else {
            echo "<option value=''>Data tidak tersedia</option>";
          }
          ?>
        </select>
      </div>
      <div class="form-group" style="width:100%;">
        <label for="jumlah" class="required">Jumlah Pengajuan</label>
        <input style="width:90%;" type="number" id="jumlah" name="jumlah" placeholder="Jumlah Pengajuan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" required>
      </div>
    </div>
    <div class="horizontal">
      <div class="form-group" style="width:100%; margin-left: 30px;">
        <label for="penanggung_jawab" class="required">Penanggung Jawab</label>
        <input style="width:90%" type="text" id="penanggung_jawab" name="penanggung_jawab" placeholder="Masukkan Penanggung Jawab" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['penanggung_jawab'] : ''; ?>" required />
      </div>
      <div class="form-group" style="width:100%; ">
        <label for="deskripsi">Keterangan</label>
        <input style="width:90%" type="text" id="deskripsi" name="deskripsi" placeholder="Masukkan Keterangan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['deskripsi'] : ''; ?>" />
      </div>
    </div>
  </div>
  <br>
  <div class="section">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 40px;">
      <div style="display: flex; align-items: center; gap: 20px; color: #002e63;">
        <p style="margin: 0;"><b>Pengajuan Insidental:</b></p>
        <a href="341"
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
          Insidental
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
            <td width='3%' class="text-right">No</td>
            <td class="text-center">Bidang</td>
            <td class="text-center">Komisi</td>
            <td class="text-center" width=''>Tanggal Pengajuan</td>
            <td class="text-center" width=''>Akun</td>
            <td class="text-center" width=''>Program</td>
            <td class="text-center" width=''>Jenis Kegiatan</td>
            <td class="text-center" width=''>Jumlah</td>
            <td class="text-center" width=''>Penanggung Jawab</td>
            <td class="text-center" width=''>Keterangan</td>
            <td class="text-center" width='5%'></td>
            <td class="text-center" width='5%'></td>
          </tr>
        </thead>
        <tbody>

          <?php
          $cnourut = 0;
          $total = 0;
          if (isset($_SESSION['temp_data6']) && count($_SESSION['temp_data6']) > 0) {
            foreach ($_SESSION['temp_data6'] as $index => $data) {
              $cnourut = $cnourut + 1;
              $akunNama = getNameFromId('akun', 'id_akun', 'nama_akun', $data['akun']);
              $programNama = getNameFromId('program', 'id_program', 'nama_program', $data['program']);
              $anggaranNama = getNameFromId('rencana_pengeluaran_komisi', 'id_anggaran', 'item', $data['anggaran']);
              $bidangNama = getNameFromId('bidang', 'id_bidang', 'nama_bidang', $data['bidang']);
              $komisiNama = !empty($data['komisi']) ? getNameFromId('komisi', 'id_komisi', 'nama_komisi', $data['komisi']) : '-';
          ?>
              <tr class=''>
                <td class="text-right"><?= $cnourut; ?></td>
                <td><?= $bidangNama ?></td>
                <td><?= $komisiNama ?></td>
                <td><?= date('d-M-Y', strtotime($data["tanggal_pengajuan"])); ?></td>
                <td><?= $akunNama; ?></td>
                <td><?= $programNama ?></td>
                <td><?= $anggaranNama; ?></td>
                <td class="text-end"><?= number_format((float)$data["jumlah"], 0, ',', '.') ?></td>
                <td><?= $data["penanggung_jawab"]; ?></td>
                <td><?= !empty($data["deskripsi"]) ? $data["deskripsi"] : '-'; ?></td>
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
              $total += (float)$data['jumlah'];
            }
          } else {
            echo "<tr><td colspan='12' class='text-center'>Belum ada data</td></tr>";
          }
          ?>
        </tbody>
        <tr>
          <td colspan="4"></td>
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
        bidangProgram: id_bidang,
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
        komisiProgram: id_komisi,
        tahun: "<?php echo $_SESSION['tahun_aktif']; ?>"
      },
      success: function(data) {
        $("#program").html(data);
      },
    });
  });

  $("#program").change(function() {
    var id_program = $("#program").val();
    console.log(id_program);

    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: "program=" + id_program,
      success: function(data) {
        $("#anggaran").html(data);
      },
    });
  });

  let batasAnggaran = 0;
  let sudahAlert = false;
  $("#anggaran").change(function() {
    var id_anggaran = $("#anggaran").val();
    console.log(id_anggaran);

    // request jumlah anggaran
    $.ajax({
      type: "POST",
      dataType: "html",
      url: "../_function_i/ambilData.php",
      data: "anggaran=" + id_anggaran,
      success: function(data) {
        batasAnggaran = parseInt(data);
        console.log("Jumlah Anggaran:", data);
        $("#jumlah").val(data);
        sudahAlert = false;
      },
    });

    // // request akun pengajuan
    // $.ajax({
    //   type: "POST",
    //   dataType: "html",
    //   url: "../_function_i/ambilData.php",
    //   data: "anggaran=" + id_anggaran,
    //   success: function(data) {
    //     $("#akun").html(data);
    //   },
    // });
  });

  $("#jumlah").on("input", function() {
    var jumlah = parseInt($(this).val());
    var self = $(this);


    if (jumlah > batasAnggaran && !sudahAlert) {
      sudahAlert = true;
      Swal.fire({
        title: 'Peringatan',
        text: 'Jumlah pengajuan melebihi rencana anggaran. Lanjutkan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
        reverseButtons: false,
        width: '25em',
        position: 'center'
      }).then((result) => {
        if (result.isConfirmed) {
          // User klik "Ya" maka izinkan nilai tetap
          console.log("User memilih Ya");
          $("#deskripsi").val("Pengajuan melebihi anggaran yang direncanakan");
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          // User klik "Tidak" maka batasi nilainya
          self.val(batasAnggaran);
          console.log("User memilih Tidak");
          $("#deskripsi").val("");
        }
      });
    } 
  });
</script>
</body>

</html>