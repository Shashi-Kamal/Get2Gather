<?php

//creating a class "Database"
class Database
{
    private $host = "localhost";
    private $db_name = "socialnet";
    private $username = "root";
    private $password = "";
    public $db_con;

    public function dbConnection()
	{
	    $this->db_con = null; 
        try
		{
            $this->db_con = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password); 
			$this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			//echo "Database Connected";

        }
		catch(PDOException $e)
		{
            echo "Connection error: " . $e->getMessage();
        }

        return $this->db_con;
    }
}

$db_host = "localhost";
$db_name = "socialnet";
$db_user = "root";
$db_pass = "";

try{

  $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
  $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


}
catch(PDOException $e){
  echo $e->getMessage();
}
?>

?>