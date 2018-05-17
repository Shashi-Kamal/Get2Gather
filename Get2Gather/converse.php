<!-- ================================== This code authenticates the currently logged in User ===========================-->
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
  	$logd_uid_propic = $userRow['profile_pic'];
  	
	if(!$_SESSION['user_session'])
	{
		header("location: denied.php");
	}
?>

<?php

include_once 'connection/dbconfig.php';

if(isset($_GET['uid']))
{
	$from_uid = $_GET['uid']; // This holds the user-id of the user the logged-in user is searching for
	$stmt=$db_con->prepare("SELECT * FROM users WHERE user_id=:uid");
	$stmt->execute(array(':uid'=>$from_uid));
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
	$from_ufname = $row['f_name'];
	$from_ulname = $row['l_name'];
	$from_uid_propic = $row['profile_pic'];
}

?>

<!--==================== This code enables the logged in user to reply to the inbox messages =============================-->
<?php 

include_once 'connection/dbconfig.php';

if(isset($_POST['reply']))
{
	$msg = strip_tags($_POST['replybox']);
	$date = date("Y-m-d");
	$senderid = $_POST['senderid'];
	
	try
	{
		$stmt = $db_con->prepare("INSERT INTO messages(user_from, user_to, msg, date_received)VALUES(:uidf, :uidt, :m, :d)");

		$stmt->bindParam(":uidf", $logd_uid);
	  	$stmt->bindParam(":uidt", $senderid);
		$stmt->bindParam(":m", $msg);
		$stmt->bindParam(":d", $date);

		if($stmt->execute())
		{
			echo $senderid;
			echo $logd_uid;
			$valueYes = "yes"; 
			$valueNo = "no";
			$stmt=$db_con->prepare("UPDATE messages SET is_opened=:vy WHERE user_to=:uid and is_opened=:vn");

			$stmt->bindParam(":vy", $valueYes);
			$stmt->bindParam(":uid", $logd_uid);
			$stmt->bindParam(":vn", $valueNo);

			if($stmt->execute())
			{
				header("Location: converse.php?uid=$senderid");
				echo $from_uid;
				echo $logd_uid;
			}	
			else
			{
				echo "Query Problem";
			}
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

<!--========================= This code enables the logged in user to Delete the inbox msgs ===================-->
<?php 

include_once 'connection/dbconfig.php';

if(isset($_POST['del_btn']))
{
	$mid = $_POST['mid'];
	echo $mid;
	$stmt=$db_con->prepare("DELETE FROM messages WHERE msg_id=:mid");
	$stmt->execute(array(':mid'=>$mid));
}

?>

<!--====================================== Here goes the structure of the web page ===============================-->
<!DOCTYPE html>
<html>
<head>
	<title>Conversation</title>
	<link rel="stylesheet" type="text/css" href="styles/mymsgs.style.css">
</head>
<body>
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone"> 
			<li><a href="search.php">Search</a></li>
			<li><a href="#">Friends</a></li>
			<li><a href="home.php">Home</a></li>
			<li><a href="profile.php?uid=<?php echo $logd_uid ?>"> <?php echo $logd_ufname ?>'s profile</a></li>
			<li><a class="active" href="#">Inbox</a></li>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul> 
	</div>
	<div class="window">
		<div class="sendername">
			<table>
				<tr>
				  	<td width="50">
				  		<div class="small_pic"><img src="<?php echo $from_uid_propic?>"></div> 
				  	</td>
				  	<td colspan="3">
				  		<?php echo $from_ufname." ".$from_ulname ?>
				  	</td>
				 </tr>
			</table>
		</div>
		<div class="box">
			
	  	<?php 
	  		require_once 'connection/dbconfig.php';

	  		$stmt = $db_con->prepare("SELECT messages.msg_id, messages.msg,  messages.date_received, messages.user_from AS sender, messages.user_to AS receiver FROM messages LEFT JOIN users s ON messages.user_from = s.user_id LEFT JOIN users r ON messages.user_to = r.user_id WHERE (r.user_id = $from_uid and s.user_id = $logd_uid) OR (r.user_id = $logd_uid and s.user_id = $from_uid)");

	  		$stmt->execute();

			if($stmt->rowCount())
			{
				while($row=$stmt->fetch(PDO::FETCH_ASSOC))
				{
			  		$date = $row['date_received'];
			  		$messages = $row['msg'];

			  		if($row['receiver'] != $logd_uid)
			  		{
		?>
						<div class="bubble receiver">
						  <div class="right"><?php echo $messages ?>
						  <div class="time-right"><?php echo $date ?></div></div>
						</div>
						
		<?php
			  		}
			  		else
			  		{
		?>
						<div class="bubble sender">
						  <div class="left"><?php echo $messages ?>
						  <div class="time-right"><?php echo $date ?></div></div>
						</div>

		<?php
			  		}
			  	}
			}
	  	?>
		</div>
		<div class="reply">
			<table>
				<tr>
				  	<form method="post">
				  	<td>
				  		<div class="small_pic"><img src="<?php echo $logd_uid_propic ?>"></div> 
				  	</td>
				  	<td colspan="3">
				  		<textarea rows="2" cols="50" ="" name="replybox"></textarea>
				  	</td>
				  	<td>
				  		<input type="hidden" name="senderid" value="<?php echo $from_uid ?>">
				  		<input type="submit" name="reply" value="Reply">
				  	</td>
				  	</form>
				 </tr>
			</table>
		</div>
	</div>
</body>
</html>