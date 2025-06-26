<?php
class cDelete
{
	function _dDeleteData($field, $value, $table)
	{
		$sqldel = "DELETE FROM " . $table . " WHERE " . $field . " = " . $value;
		//echo "<br>".$sqldel."<br>";
		$query = mysqli_query($GLOBALS["conn"], $sqldel);

		if ($query) {
			echo "<script>
					Swal.fire({
					  position:'center',
					  width:'20em',
					  icon:'success',
					  text: 'Data berhasil dihapus',
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
					  text: 'Data tidak berhasil dihapus',
					  type: 'error',
					}).then(function (result) {
					  if (true) {
					    window.location = '';
					  }
			}) </script>";
		}
	}

	function _dDeleteDataTrial($field, $value, $table)
	{
		$sqldel = "DELETE FROM " . $table . " WHERE " . $field . " = " . $value;
		echo "<p>" . $sqldel . "</p>";
	}
}