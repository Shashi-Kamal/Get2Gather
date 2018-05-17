<!-- =================== This code authenticates the currently logged in User =================-->
<?php

	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new User();

	$user_id = $_SESSION['user_session'];

	$stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));

	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
  	$logd_uid = $userRow['user_id'];
  	$logd_ufname = $userRow['f_name'];
  	/*$logd_ulname = $userRow['l_name'];
  	$logd_uemail = $userRow['email'];
  	$logd_mobile = $userRow['mob'];*/
  	
	if(!$_SESSION['user_session'])
	{
		header("location: denied.php");
	}
?>

<!--============================ This code Deletes any Friend Requests ==========================-->
<?php 
	require_once 'connection/dbconfig.php';

	if(isset($_POST['del_btn']))
	{
		//$logd_uid = $_POST['logd_uid'];
		$senderid = $_POST['userid'];
		//$value = "false";

		$stmt = $db_con->prepare("DELETE FROM users_friends WHERE user_id = :fid and friend_id = :uid");

		if($stmt->execute(array(":uid" => $logd_uid, ":fid" => $senderid)))
		{
			//
			header("location: friendreqs.php");
		}
		else
		{
			echo "Query Problem";
		}	
	}	
?>

<!--============================= This code Accepts any Friend Requests ============================-->
<?php 

include_once 'connection/dbconfig.php';

if(isset($_POST['accept_btn']))
{
	$userid = $_POST['userid'];
	$value = "true";

	try
	{
		$stmt=$db_con->prepare("UPDATE users_friends SET is_accepted=:v WHERE friend_id=:fid and user_id =:uid ");

		$stmt->bindParam(":v", $value);
		$stmt->bindParam(":uid", $userid);
		$stmt->bindParam(":fid", $logd_uid);

		if($stmt->execute())
		{
			//header("Location: friendreqs.php"); 
			//echo $logd_uid;
			$stmt=$db_con->prepare("INSERT INTO users_friends(user_id, friend_id, is_accepted)VALUES(:fid, :uid, :v)");

			$stmt->bindParam(":fid", $logd_uid);
			$stmt->bindParam(":uid", $userid);
			$stmt->bindParam(":v", $value);

			$stmt->execute();
		}
		else
		{
			echo "Query Problem";
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}

?>

<!--============================================= Structure of the Web Page =====================================-->
<!DOCTYPE html>
<html>
<head>
	<title>Friend Requests</title>
	<link rel="stylesheet" type="text/css" href="styles/mymsgs.style.css">
</head>
<body>
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone"> 
			<li><a href="search.php">Search</a></li>
			<li><a href="home.php">Home</a></li>
			<li><a href="profile.php?uid=<?php echo $logd_uid ?>"> <?php echo $logd_ufname ?>'s profile</a></li>
			<li><a class="active" href="friendreqs.php">Friend Requests</a></li>
			<li><a href="messages.php">Inbox</a></li>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul> 
	</div>
	<div class="display">
		<table style="width:100%">
	  	<?php 
	  		require_once 'connection/dbconfig.php';

	  		$stmt = $db_con->prepare("SELECT * FROM users INNER JOIN users_friends ON users.user_id = users_friends.user_id WHERE users_friends.friend_id = $logd_uid and users_friends.is_accepted = 'false'");
	  		$stmt->execute();

			if($stmt->rowCount())
			{
				while($row=$stmt->fetch(PDO::FETCH_ASSOC))
				{
					$req_id = $row['id'];
					$user_id = $row['user_id']; // id of the user who sent friend request
			  		$from_name = $row['f_name']." ".$row['l_name'];
			  		$profile_pic = $row['profile_pic'];
			  		
	  	?>
					  <tr>
					  	
					    
					    <td>
					    	<div class="small_pic"><img src="<?php echo $profile_pic ?>"></div> 
					    </td>
					    <td><a href="profile.php?uid=<?php echo $user_id ?>"><?php echo $from_name ?> has sent you Friend request</td></a>
					    
					    <td>
					    	<form method="post">
					    		<input type="hidden" name="userid" value="<?php echo $user_id ?>">
					    		<input type="submit" name="accept_btn" value="Accept">
					    	</form>
					    </td>
					    <td>
					    	<form method="post">
					    		<input type="hidden" name="userid" value="<?php echo $user_id ?>">
					    		<input type="submit" name="del_btn" value="Delete">
					    	</form>
						</td>
					  </tr>
		<?php 
				}
			}
		?>
		</table>
	</div>
</body>
</html>