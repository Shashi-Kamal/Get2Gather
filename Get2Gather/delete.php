<?php

	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new User();

	$user_id = $_SESSION['user_session'];

	$stmnt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmnt->execute(array(":user_id"=>$user_id));

	$userRow=$stmnt->fetch(PDO::FETCH_ASSOC);
  	$uid = $userRow['user_id'];
  	echo $uid;
	if(!$_SESSION['user_session']){

		header("location: denied.php");
	}
?>

<?php
include_once 'connection/dbconfig.php';

//Get the value of status_id passed from the url and fetch the data content from the db based on the status_id

if(isset($_POST['del_btn']))
{
	if(isset($_GET['status_id']))
	{
		$sid = $_GET['status_id'];
		$stmt=$db_con->prepare("DELETE FROM status WHERE status_id=:sid");
		$stmt->execute(array(':sid'=>$sid));
	}
	header("location: profile.php?uid=$uid");
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Delete Warning</title>
	<link rel="stylesheet" type="text/css" href="styles/delete.style.css">
</head>
<body>
	<!--<div class="delform">
		<h1>Confirmation !</h1>
		<div class="msg">
			<p>Do you really want to delete the current status ?</p>
		</div>
		<div class="btn">
			<a href="profile.php"><button>Cancel</button></a>
			<form method="post">
				<input type="submit" name="confirm" value="Confirm">
			</form>
		</div>
	</div>-->

	<div class="message">
		  <!-- Message content -->
		 <div class="message-content">
		  	<h2><font color="red">Do you really want to Delete</font></h2>
		    <!--<span class="close">&times;</span>-->
		    <div class="form">
			    <form action="#" method="post">
			    	<a href="profile.php?uid=<?php echo $uid ?>"><button>Cancel</button></a>
			    	
			    	<input type="submit" name="del_btn" value="Delete">
			    </form>
		    </div>
		 </div>
	</div>
</body>
</html>