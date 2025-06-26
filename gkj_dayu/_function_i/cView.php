<?php
class cView
{
	function vViewData($sSql)
	{
		//echo "<br>".$sSql."<br>";
		$data = array();
		$query = mysqli_query($GLOBALS["conn"], $sSql);
		while ($row = mysqli_fetch_assoc($query))
			$data[] = $row;
		return $data;
		mysqli_close($conn);
	}
}
