<?php
class cView
{
	function vViewData($sSql)
	{
		$data = array();
		$query = mysqli_query($GLOBALS["conn"], $sSql);
		while ($row = mysqli_fetch_assoc($query))
			$data[] = $row;
		return $data;
		mysqli_close($conn);
	}
}
