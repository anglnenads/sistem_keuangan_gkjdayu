<?php
class cConnect
{
	private $dbHost  = "localhost";
	private $dbUser  = "root";
	private $dbPass  = "";
	private $dbPort = "3306";
	private $dbName  = "gkj_dayu";


	function goConnect()
	{
		$conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass,  $this->dbName, $this->dbPort );
		$GLOBALS["conn"] = $conn;
		//return $conn;
		// 
	}
}
