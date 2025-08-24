<?php
class cConnect
{
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbPort;
    private $dbName;

    public function __construct() {
        $this->dbHost = getenv('DB_HOST');
        $this->dbUser = getenv('DB_USERNAME');
        $this->dbPass = getenv('DB_PASSWORD');
        $this->dbName = getenv('DB_DATABASE');
        $this->dbPort = getenv('DB_PORT') ?: '3306'; 
    }
    
    //  public function __construct() {
    //     $this->dbHost = "localhost";
    //     $this->dbUser = "root"; 
    //     $this->dbPass = "";     
    //     $this->dbName = "gkj_dayu"; 
    // }

    function goConnect()
    {

        $conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName, (int)$this->dbPort);
        

        $GLOBALS["conn"] = $conn;


        return $conn;
    }
}