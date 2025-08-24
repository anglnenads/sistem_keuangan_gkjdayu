<?php
class cUpdate
{
	// penerimaan
	function vUpdateData($afields, $atable, $avalues, $datakey, $linkurl)
	{
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}
			$fieldname = $fieldname . $afields[$i] . '=' . $avalues[$i] . $separator;
		}
		$allstatement = "UPDATE " . $atable . " SET " . $fieldname . " WHERE " . $datakey . "";
		$query = $allstatement;
		$result = mysqli_query($GLOBALS["conn"], $query);
		if ($result) {
			if (strpos($fieldname, 'password') !== false) {
				$pesan = 'Password berhasil di reset menjadi 123';
			} else {
				$pesan = 'Data berhasil diubah';
			}
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'success',	
						  text: '$pesan',
						  type: 'success',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		} else {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'error',	
						  text: 'Data tidak berhasil diubah',
						  type: 'error',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		}
	}

	function vUpdateDataTrial($afields, $atable, $avalues, $datakey, $linkurl)
	{
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}
			$fieldname = $fieldname . $afields[$i] . '=' . $avalues[$i] . $separator;
		}
		$allstatement = "UPDATE " . $atable . " SET " . $fieldname . " WHERE " . $datakey . "";
		$query = $allstatement;
		echo "<p>" . $query . "</p>";
	}


function fUpdateData($afields, $atable, $avalues, $datakey, $linkurl)
	{
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}

			if ($avalues[$i] === NULL) {
				$fieldname .= $afields[$i] . "=NULL" . $separator;
			} elseif (is_numeric($avalues[$i])) {
				$fieldname .= $afields[$i] . "=" . $avalues[$i] . $separator;
			} elseif (is_string($avalues[$i])) {
				$escaped_value = mysqli_real_escape_string($GLOBALS["conn"], $avalues[$i]);
				$fieldname .= $afields[$i] . "='" . $escaped_value . "'" . $separator;
			} else {
				$fieldname .= $afields[$i] . "=" . $avalues[$i] . $separator;
			}
		}
		$allstatement = "UPDATE " . $atable . " SET " . $fieldname . " WHERE " . $datakey . "";
		$query = $allstatement;
		$result = mysqli_query($GLOBALS["conn"], $query);
		if ($result) {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'success',	
						  text: 'Data berhasil diubah',
						  type: 'success',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		} else {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'error',	
						  text: 'Data tidak berhasil diubah',
						  type: 'error',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		}
	}


	function _prosesStatus($field, $value1, $table, $status)
	{
		$sqlvalid = "UPDATE " . $table . " SET status = '$status', tanggal_proses = CURDATE() WHERE " . $field . " = " . $value1;

		$query = mysqli_query($GLOBALS["conn"], $sqlvalid);

		if ($query) {
			echo "<script>
					Swal.fire({
					  position:'center',
					  width:'20em',
					  icon:'success',
					  text: 'Pengajuan Diproses',
					  type: 'error',
					}).then(function (result) {
					  if (true) {
					    window.location = '';
					  }
			}) </script>";
		} else {
			echo "<script>
					Swal.fire({
					  position:'center',
					  width:'20em',
					  icon: 'error',	
					  text: 'Pengajuan tidak diproses',
					  type: 'error',
					}).then(function (result) {
					  if (true) {
					    window.location = '';
					  }
			}) </script>";
		}
	}

	function _pengajuanStatus($id_field, $id_value, $table, $afields, $avalues)
	{
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}
			$fieldname = $fieldname . $afields[$i] . '=' . $avalues[$i] . $separator;
		}

		$query = "UPDATE " . $table . " SET " . $fieldname . " WHERE " . $id_field . " IN (" . $id_value . ")";
				
		$result = mysqli_query($GLOBALS["conn"], $query);
		if ($result) {
			if (in_array("'Ditolak'", $avalues)) {
				$pesan = 'Pengajuan Ditolak';
			} elseif (in_array("'Diproses'", $avalues)) {
				$pesan = 'Pengajuan Diproses';
			} elseif (in_array("'Disetujui'", $avalues)) {
				$pesan = 'Pengajuan Disetujui';
			}
			
    		echo "<script>
            		Swal.fire({
              		position: 'center',
              		width: '16em',
              		icon: 'success',
              		text: '$pesan',
              		type: 'success',
            		}).then(function (result) {
              		if (true) {
                	window.location = '';
              		}
            		});
          		</script>";
		} else {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'error',	
						  text: 'Status pengajuan tidak berhasil diubah',
						  type: 'error',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		}
	}

	function _functionStatus($id_field, $id_value, $table, $afields, $avalues)
	{
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}
			$fieldname = $fieldname . $afields[$i] . '=' . $avalues[$i] . $separator;
		}

		$query = "UPDATE " . $table . " SET " . $fieldname . " WHERE " . $id_field . " IN (" . $id_value . ")";
				
		$result = mysqli_query($GLOBALS["conn"], $query);
		if ($result) {
			if (strpos($table, 'pengeluaran') !== false) {
				$pesan = 'Pengeluaran berhasil divalidasi';
			} if (strpos($table, 'user') !== false) {
				$pesan = 'Password berhasil di reset';
			}else {
				$pesan = 'Penerimaan berhasil divalidasi';
			}
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'20em',
						  icon: 'success',	
						  text: '$pesan',
						  type: 'success',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		} else {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'error',	
						  text: 'Data tidak berhasil diubah',
						  type: 'error',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		}
	}

	function _functionSetor($afields, $atable, $avalues, $datakey, $linkurl)
	{
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}

			if ($avalues[$i] === NULL) {
				$fieldname .= $afields[$i] . "=NULL" . $separator;
			} elseif (is_numeric($avalues[$i])) {
				$fieldname .= $afields[$i] . "=" . $avalues[$i] . $separator;
			} elseif (is_string($avalues[$i])) {
				$escaped_value = mysqli_real_escape_string($GLOBALS["conn"], $avalues[$i]);
				$fieldname .= $afields[$i] . "='" . $escaped_value . "'" . $separator;
			} else {
				$fieldname .= $afields[$i] . "=" . $avalues[$i] . $separator;
			}
		}
		$allstatement = "UPDATE " . $atable . " SET " . $fieldname . " WHERE " . $datakey . "";
		$query = $allstatement;
		echo "<br>".$query."<br>";
		$result = mysqli_query($GLOBALS["conn"], $query);
		if ($result) {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'success',	
						  text: 'Data berhasil diubah',
						  type: 'success',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		} else {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'error',	
						  text: 'Data tidak berhasil diubah',
						  type: 'error',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		}
	}
}
