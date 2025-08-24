<?php
class cInsert
{
	function vInsertData($afields, $atable, $avalues)
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
			$fieldname = $fieldname . $afields[$i] . $separator;
			$datavalue = "" . $datavalue . $avalues[$i] . $separator;
		}
		$allstatement = "insert into " . $atable . "(" . $fieldname . ") values(" . $datavalue . ")";
		$query = $allstatement;
		$result = mysqli_query($GLOBALS["conn"], $query);

		echo "<br>";
		if ($result) {
			echo "<script>
						Swal.fire({
						  position:'center',
						  width:'16em',
						  icon: 'success',	
						  text: 'Data berhasil disimpan',
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
						  text: 'Data tidak berhasil disimpan',
						  type: 'error',
						}).then(function (result) {
						  if (true) {
						    window.location = '';
						  }
				}) </script>";
		}
	}

	function fInsertData($afields, $atable, $avalues, $link) {
		$countarray_field = count($afields) - 1;
		$countarray_value = count($avalues) - 1;
		$fieldname = "";
		$datavalue = "";
		
		for ($i = 0; $i <= $countarray_field; $i++) {
			$separator = ($i == $countarray_field) ? "" : ",";

			if ($avalues[$i] === NULL || $avalues[$i] === '') {
				$datavalue .= 'NULL' . $separator; 
			} elseif (is_numeric($avalues[$i])) {
				$datavalue .= $avalues[$i] . $separator;
			} elseif (is_string($avalues[$i])) {
				$escaped_value = mysqli_real_escape_string($GLOBALS["conn"], $avalues[$i]);
				$datavalue .= "'" . $escaped_value . "'" . $separator;
			} else {
				$datavalue .= $avalues[$i] . $separator;
			}

			$fieldname .= $afields[$i] . $separator;
		}
		
		$allstatement = "INSERT INTO " . $atable . " (" . $fieldname . ") VALUES (" . $datavalue . ")";
		$query = $allstatement;
		
		$result = mysqli_query($GLOBALS["conn"], $query);
	
		echo "<br>";
		if ($result) {
			echo "<script>
					Swal.fire({
						position: 'center',
						width: '20em',
						icon: 'success',	
						text: 'Data berhasil disimpan',
						type: 'success',
					}).then(function (result) {
						if (result.value) {
							// Pengalihan ke halaman yang diteruskan melalui parameter $link
							window.location.href = '$link';
						}
					});
				  </script>";
		} else {
			echo "<script>
					Swal.fire({
						position: 'center',
						width: '20em',
						icon: 'error',	
						text: 'Data tidak berhasil disimpan',
						type: 'error',
					}).then(function (result) {
						if (result.value) {
							// Pengalihan ke halaman lain jika gagal (misalnya, tetap di halaman yang sama)
							window.location.href = '$link'; 
						}
					});
				  </script>";
		}
	}
	

	function vInsertDataTrial($afields, $atable, $avalues, $url)
	{
		$countarray_field = count($afields) - 1;
		$fieldname = "";
		$datavalue = "";
		for ($i = 0; $i <= $countarray_field; $i++) {
			if ($i == $countarray_field) {
				$separator = "";
			} else {
				$separator = ",";
			}
			$fieldname = $fieldname . $afields[$i] . $separator;
			$datavalue = "" . $datavalue . $avalues[$i] . $separator;
		}
		$allstatement = "insert into " . $atable . "(" . $fieldname . ") values(" . $datavalue . ")";
		$query = $allstatement;
		echo "<p>" . $query . "</p>";
	}

	public function InsertDataArray($afields, $atable, $avalues, $url) {
        global $conn; // Pastikan koneksi database tersedia

        $fieldname = implode(", ", $afields);
        $placeholders = "'" . implode("', '", $avalues) . "'";

        $sql = "INSERT INTO $atable ($afields) VALUES ($avalues)";
        $result = mysqli_query($GLOBALS["conn"], $sql);
		echo "<p>" . $sql . "</p>";

        if ($result) {
            return mysqli_insert_id($GLOBALS["conn"]); // Kembalikan ID anggaran terbaru
        } else {
            die("Error: " . mysqli_error($GLOBALS["conn"]));
        }
    }

	function vInsertDataArray($afields, $atable, $avalues)
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
			$fieldname = $fieldname . $afields[$i] . $separator;
			$datavalue = "" . $datavalue . $avalues[$i] . $separator;
		}
		$allstatement = "insert into " . $atable . "(" . $fieldname . ") values(" . $datavalue . ")";
		$query = $allstatement;
		$result = mysqli_query($GLOBALS["conn"], $query);

		if ($result) {
            return mysqli_insert_id($GLOBALS["conn"]); // Kembalikan ID anggaran terbaru
        } else {
            die("Error: " . mysqli_error($GLOBALS["conn"]));
        };
	}
}



