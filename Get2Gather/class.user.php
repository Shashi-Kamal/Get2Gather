<?php
//this file is required for successful execution of the following code (for db connection)
require_once 'connection/dbconfig.php';

//creating a class "User"
class User
{
	private $conn;

	//class "User" constructor. It initialises a newly created object with a db connection
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}

	//this function is used to run an sql query
	public function runQuery($sql)
	{
		$stmnt = $this->conn->prepare($sql);
		return $stmnt;
	}

	//this function is used to register a new user
	public function register($fname, $lname, $email, $mob, $pass, $sex)
	{
		try
		{
			//the received "password" is encrypted using hash encryption
			//$new_password = password_hash($pass, PASSWORD_DEFAULT);

			//an sql query statement is prepared
			$stmnt = $this->conn->prepare("INSERT INTO users(f_name, l_name, email, mobile, pass, dob, sex)
											VALUES(:ufname, :ulname, :uemail, :umob, :upass, :udob, :usex)");

			//all the default selectors are bounded to the respective server instance variables
			$stmnt->bindparam(":ufname", $fname);
			$stmnt->bindparam(":ulname", $lname);
			$stmnt->bindparam(":uemail", $email);
			$stmnt->bindparam(":umob", $mob);
			//$stmnt->bindparam(":upass", $new_password);
			$stmnt->bindparam(":upass", $pass);
			$stmnt->bindparam(":udob", $dob);
			$stmnt->bindparam(":usex", $sex);

			//the sql query statement is executed
			$stmnt->execute();

			return $stmnt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

	//this function is used to login an already existing user
	public function doLogin($fname, $email, $pass)
	{
		try
		{
			//an sql query statement is prepared
			$stmnt = $this->conn->prepare("SELECT user_id, f_name, email, pass FROM users WHERE f_name=:ufname OR email=:uemail ");

			//the sql query statement is then executed
			$stmnt->execute(array(':ufname'=>$fname, ':uemail'=>$email));

			//based on the above query statement a no. of rows are fetched from db
			$userRow = $stmnt->fetch(PDO::FETCH_ASSOC);

			//checks the existence of an unique user in db with the provided username or email
			if($stmnt->rowCount() == 1)
			{
				//checks if the provided password matches with the password in db
				
				//if(password_verify($upass, $userRow['pass']))
				if($pass == $userRow['pass'])
				{
					//then the $_SESSION variable is set to user_id
					echo $userRow['f_name'];
					echo $userRow['pass'];
					$_SESSION['user_session'] = $userRow['user_id'];
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}

	public function redirect($url)
	{
		header("Location: $url");
	}

	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		//header("Location: login.php");
		return true;

	}
}
?>