<?php
include_once("../_function_i/inc_f_object.php");
?>

<?php
if (isset($_POST['save'])) {
  $inputData = [
    'tanggal_catat' => date("Y-m-d"),
    'tanggal_terima' => $_POST['tanggal_terima'],
    'bidang' => $_POST['bidang'],
    'komisi' => $_POST['komisi'] ?? NULL,
    'program' => $_POST['program'],
    'jenis_terima' => $_POST['jenis_terima'],
    'volume' => $_POST['volume'],
    'harga' => $_POST['harga'],
    'jumlah' => $_POST['jumlah'],
    'satuan' => $_POST['satuan'],
    'dana_gereja' => (float) ($_POST['dana_gereja'] ?? 0),
    'dana_swadaya' => (float) ($_POST['dana_swadaya'] ?? 0),
    'sumber_dana' => $_POST['sumber_dana']
  ];

  if (isset($_SESSION['edit_index'])) {
    $_SESSION['temp_data11'][$_SESSION['edit_index']] = $inputData;
    unset($_SESSION['edit_index']);
    unset($_SESSION['edit_data']);
  } else {
    if (!isset($_SESSION['temp_data11'])) {
      $_SESSION['temp_data11'] = [];
    }
    $_SESSION['temp_data11'][] = $inputData;
  }
  unset($_SESSION['is_edit_mode']);
}

if (isset($_POST['edit'])) {
  $indexToEdit = $_POST['edit_index'];
  if (isset($_SESSION['temp_data11'][$indexToEdit])) {
    $editData = $_SESSION['temp_data11'][$indexToEdit];
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
  if (isset($_SESSION['temp_data11'][$indexToDelete])) {
    unset($_SESSION['temp_data11'][$indexToDelete]);
    $_SESSION['temp_data11'] = array_values($_SESSION['temp_data11']);
  }
}

if (isset($_POST['reset'])) {
  unset($_SESSION['temp_data11']);
  unset($_SESSION['edit_index']);
  unset($_SESSION['edit_data']);
  unset($_SESSION["bidang"]);
  unset($_SESSION["komisi"]);
  unset($_SESSION["program"]);
}


if (isset($_POST["submit_all"])) {

  if (isset($_SESSION['temp_data11']) && count($_SESSION['temp_data11']) > 0) {

    $total_jumlah = 0;
    $total_sumberDana = 0;

    foreach ($_SESSION['temp_data11'] as $data) {
      $total_jumlah += (float)$data['jumlah'];
      $total_sumberDana += (float)$data['dana_gereja'] + (float)$data['dana_swadaya'];
    }

    if ($total_jumlah == $total_sumberDana) {
      foreach ($_SESSION['temp_data11'] as $data) {

        $datafield = array("id_fiskal", "id_program", "id_user", "id_bidang", "id_komisi", "jumlah_penerimaan", "tanggal_penerimaan", "tanggal_pencatatan", "jenis_penerimaan", "status", "volume", "satuan", "harga_satuan", "dana_gereja", "dana_swadaya");
        $datavalue = array($id_fiskal, $data['program'], $id_user, $data['bidang'], $data['komisi'], $data['jumlah'], $data['tanggal_terima'], $data['tanggal_catat'], $data['jenis_terima'], "Belum Tervalidasi", $data['volume'], $data['satuan'], $data['harga'], $data['dana_gereja'], $data['dana_swadaya']);

        $insert = new cInsert();
        $insert->fInsertData($datafield, "realisasi_penerimaan_komisi", $datavalue, 29);
      }
      unset($_SESSION["bidang"]);
      unset($_SESSION["komisi"]);
      unset($_SESSION["program"]);
      unset($_SESSION['temp_data11']);
    } else {
      echo "<script>
        Swal.fire({
            position:'center',
            width:'25em',
            icon: 'error',    
            text: 'Data tidak bisa disimpan! Total jumlah tidak seimbang dengan total sumber dana',
            type: 'error',
        }).then(function (result) {
            if (true) {
                window.location = '';
            }
        }) 
    </script>";
    }
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
      _myHeader("newspaper", "Penerimaan Komisi", "Entri Data");
      ?>
    </div>
  </div>
  <br>
  <div class="section">
    <form action="" method="post">
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
      <div class="form-group" style="width:100%; margin-left: 30px;">
        <label for="program" class="required">Nama Program</label>
        <select style="width:95%" id="program" name="program" required>
          <option value="">-- Pilih Program --</option>
          <option value="0" <?php echo (isset($_POST['program']) && $_POST['program'] == '0') || (isset($_SESSION['edit_data']) && $_SESSION['edit_data']['program'] == '0') ? 'selected' : ''; ?>>Insidental</option>
          <?php
          if (isset($_SESSION['komisi'])) {
            $query = "SELECT program.id_program, program.nama_program, program.id_fiskal, program.id_bidang, program.id_komisi, fiskal.tahun
                        FROM program  
                        JOIN komisi ON program.id_komisi = komisi.id_komisi JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                        WHERE tahun = $tahun_aktif AND program.id_komisi = " . $_SESSION['komisi'] . " ORDER BY id_komisi ASC";
          } elseif (isset($_SESSION['bidang'])) {
            $query = "SELECT program.id_program, program.nama_program, program.id_fiskal, program.id_bidang, fiskal.tahun
                        FROM program  
                        JOIN bidang ON program.id_bidang = bidang.id_bidang JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                        WHERE tahun = $tahun_aktif AND program.id_bidang = " . $_SESSION['bidang'] . " ORDER BY id_bidang ASC";
          } else {
            $query = "SELECT program.id_program, program.nama_program, program.id_fiskal, program.id_bidang, program.id_komisi, komisi.nama_komisi,  fiskal.tahun
                          FROM program  
                          JOIN komisi ON program.id_komisi = komisi.id_komisi JOIN fiskal ON program.id_fiskal = fiskal.id_fiskal
                          WHERE tahun = $tahun_aktif ORDER BY id_komisi ASC";
          }
          $result = $conn->query($query);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $selected = '';
              if (isset($_POST['program']) && $_POST['program'] == $row['id_program']) {
                $selected = 'selected';
              } elseif (isset($_SESSION['program']) && $_SESSION['program'] == $row['id_program']) {
                $selected = 'selected';
              } elseif (
                isset($_SESSION['edit_data']) &&
                (
                  ($_SESSION['edit_data']['program'] === null && $row['id_program'] === null) ||
                  ($_SESSION['edit_data']['program'] == $row['id_program'])
                )
              ) {
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
   
      <div class="form-group" style="width:100%; margin-left: 30px;">
        <label for="tanggalPenerimaan" class="required">Tanggal Penerimaan</label>
        <input style="width:90%" type="date" class="form-control" name="tanggal_terima" id="" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['tanggal_terima'] : ''; ?>"
          required />
        <p style="margin-top: -15px; margin-left: 10px; color:#838996; font-weight: 500;">mm/dd/yyyy</p>
        <?php
        ?>
      </div>
      
    </div>
    <br>

  </div>
  <br>
  <div class="section">
    <div class="horizontal" style="height:90px">
      <div class="form-group" style="width: 30%; margin-left: 30px;">
        <label for="jenis" class="required">Jenis Penerimaan/Kegiatan</label>
        <input style="width:95%;" type="text" id="jenis_terima" name="jenis_terima" placeholder="Masukkan Jenis Penerimaan/Kegiatan"
          value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jenis_terima'] : ''; ?>"
          required />
        <?php
        ?>
      </div>
      <div class="form-group" style="width:13%; margin-left: -40px;">
        <label for="volume" class="required">Volume</label>
        <input style="width:79%" type="number" id="volume" name="volume" placeholder="Masukkan Vol" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['volume'] : ''; ?>" oninput="hitungTotal()" min="0" required />
      </div>

      <div class="form-group" style="width:14%; margin-left: -60px;">
        <label for="satuan" class="required">Satuan</label>
        <input style="width:79%" type="text" id="satuan" name="satuan" placeholder="Masukkan Satuan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['satuan'] : ''; ?>" required />
      </div>

      <div class="form-group" style="width:18%; margin-left: -60px;">
        <label for="harga" class="required">Harga Satuan (Rp)</label>
        <input style="width:79%" type="number" id="harga" name="harga" placeholder="Masukkan Harga Satuan" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['harga'] : ''; ?>" oninput="hitungTotal()" min="1" required />
      </div>

      <div class="form-group" style="width:19%; margin-left: -70px;">
        <label for="jumlah" class="required">Jumlah</label>
        <input style="width:90%;" type="number" id="jumlah" name="jumlah" placeholder="Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['jumlah'] : ''; ?>" required />
      </div>
    </div>
  </div>
  <br>
  <div class="section">
    <div class="horizontal">
      <div class="form-group" style="width:20%; margin-left: 30px;">
        <label for="sumber_dana">Dana Gereja</label>
        <input style="width:90%" type="number" id="dana_gereja" name="dana_gereja" placeholder="Masukkan Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['dana_gereja'] : ''; ?>" oninput="hitungTotalSumberDana()" min="0" />
      </div>

      <div class="form-group" style="width:20%;">
        <label for="sumber_dana">Dana Swadaya</label>
        <input style="width:90%" type="number" id="dana_swadaya" name="dana_swadaya" placeholder="Masukkan Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['dana_swadaya'] : ''; ?>" oninput="hitungTotalSumberDana()" min="0" />
      </div>
      <div class="form-group" style="width:48%; margin-left: 30px;">
        <label for="sumber_dana" class="required">Jumlah Sumber Dana (Rp)</label>
        <input style="width:90%; background-color: #f2f3f4;" type="number" id="sumber_dana" name="sumber_dana" placeholder="Jumlah" value="<?= isset($_SESSION['edit_data']) ? $_SESSION['edit_data']['sumber_dana'] : ''; ?>" onkeydown="return false" required />
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
</div>

<br>

<div class="secondsection">
  <form action="" method="post">
    <table id='data-table' class='table table-condensed table-bordered'>
      <thead>
        <tr class='small'>
          <td width='2%' class="text-center">No</td>
          <td width='' class="text-center">Tanggal Penerimaan</td>
          <td width='' class="text-center">Program</td>
          <td width='' class="text-center">Jenis Penerimaan</td>
          <td width='' class="text-center">Vol</td>
          <td width='8%' class="text-center">Satuan (Rp)</td>
          <td width='8%' class="text-center">Jumlah</td>
          <td width='8%' class="text-center">Dana Gereja</td>
          <td width='8%' class="text-center">Dana Swadaya</td>
          <td class="text-center">Subtotal</td>
          <td width='5%'></td>
          <td width='5%'></td>
        </tr>
      </thead>
      <tbody>
        <?php
        $cnourut = 0;
        $total = 0;
        $total_danaGereja = 0;
        $total_danaSwadaya = 0;
        $subTotal = 0;
        if (isset($_SESSION['temp_data11']) && count($_SESSION['temp_data11']) > 0) {
          foreach ($_SESSION['temp_data11'] as $index => $data) {
            $cnourut = $cnourut + 1;
            $rekeningNama = !empty($data['bank']) ? getNameFromId('bank', 'id_bank', 'nama_rekening', $data['bank']) : NULL;
            $bidangNama = !empty($data['bidang']) ? getNameFromId('bidang', 'id_bidang', 'nama_bidang', $data['bidang']) : NULL;
            $komisiNama = !empty($data['komisi']) ? getNameFromId('komisi', 'id_komisi', 'nama_komisi', $data['komisi']) : NULL;
            $programNama = !empty($data['program']) ? getNameFromId('program', 'id_program', 'nama_program', $data['program']) : 'Insidental';
        ?>
            <tr class=''>
              <td class="text-right"><?= $cnourut; ?></td>
              <td><?= date('d-M-Y', strtotime($data["tanggal_terima"])); ?></td>
              <td><?= $programNama; ?></td>
              <td><?= $data["jenis_terima"]; ?></td>
              <td><?= $data["volume"]; ?></td>
              <td class="text-end"><?= number_format(floatval($data["harga"]), 0, ',', '.') ?>/<?= $data["satuan"] ?></td>
              <td class="text-end"><?= number_format(floatval($data["jumlah"]), 0, ',', '.') ?></td>
              <td class="text-end"><?= number_format(!empty($data["dana_gereja"]) ? floatval($data["dana_gereja"]) : 0, 0, ',', '.'); ?></td>
              <td class="text-end"><?= number_format(!empty($data["dana_swadaya"]) ? floatval($data["dana_swadaya"]) : 0, 0, ',', '.'); ?></td>
              <td class="text-end"><?= number_format(floatval($data["dana_gereja"]) + floatval($data["dana_swadaya"]), 0, ',', '.'); ?></td>
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
            $total += floatval($data["jumlah"]);
            $total_danaGereja += floatval($data["dana_gereja"]);
            $total_danaSwadaya += floatval($data["dana_swadaya"]);
            $subTotal += floatval($data["dana_gereja"]) + floatval($data["dana_swadaya"]);
          }
        } else {
          echo "<tr><td colspan='12' class='text-center'>Belum ada data</td></tr>";
        }
        ?>
        <tr>
          <td colspan="2"></td>
          <td style="color:#483d8b; font-weight:bolder" colspan="4">Total</td>
          <td class="text-end" style="color:#2e8b57; font-weight:bolder"><?= number_format($total, 0, ',', '.'); ?></td>
          <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total_danaGereja, 0, ',', '.'); ?></td>
          <td class="text-end" style="color:#5B90CD; font-weight:bolder"><?= number_format($total_danaSwadaya, 0, ',', '.'); ?></td>
          <td class="text-end" style="color:#2e8b57; font-weight:bolder"><?= number_format($subTotal, 0, ',', '.'); ?></td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
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
        bidangProgramRealisasi: id_bidang,
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
        komisiProgramRealisasi: id_komisi,
        tahun: "<?php echo $_SESSION['tahun_aktif']; ?>"
      },
      success: function(data) {
        $("#program").html(data);
      },
    });
  });

  function hitungTotal() {
    let input1 = parseFloat(document.getElementById("volume").value) || 0;
    let input2 = parseFloat(document.getElementById("harga").value) || 0;

    let hasil = input1 * input2;

    document.getElementById("jumlah").value = hasil;
  }

  function hitungTotalSumberDana() {
    let input3 = parseFloat(document.getElementById("dana_gereja").value) || 0;
    let input4 = parseFloat(document.getElementById("dana_swadaya").value) || 0;

    let hasil = input3 + input4;

    document.getElementById("sumber_dana").value = hasil;
  }
</script>
</body>