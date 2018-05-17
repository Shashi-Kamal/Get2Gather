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

<!--============================ This codes checks if the logged-in user has any friend requests =======================-->

<?php
require_once("session.php");
require_once("class.user.php");

function frndReq_exist($user_id)
{
	$auth_user = new User();
	try
	{
		$stmt = $auth_user->runQuery("SELECT * FROM users_friends WHERE friend_id=:uid and is_accepted=:v");
		$stmt->execute(array(':uid'=>$user_id, ':v'=>"false"));
		$stmt->fetch(PDO::FETCH_ASSOC);
		if($count = $stmt->rowCount())
		{
			//echo $count;
?>
			<script type="text/javascript">
				frnd_notify();
			</script>
<?php		echo $count;
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}
?>
<script type="text/javascript">
	function frnd_notify()
	{
		var frndball = document.getElementById("frndball");
		window.onload = function () 
		{
		    frndball.style.display = "block";
		}
	}
</script>

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
			$valueYes = "yes"; 
			$valueNo = "no";
			$stmt=$db_con->prepare("UPDATE messages SET is_opened=:vy WHERE user_to=:uid and is_opened=:vn");

			$stmt->bindParam(":vy", $valueYes);
			$stmt->bindParam(":uid", $logd_uid);
			$stmt->bindParam(":vn", $valueNo);

			if($stmt->execute())
			{
				header("Location: messages.php");
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
	<title>Inbox</title>
	<link rel="stylesheet" type="text/css" href="styles/mymsgs.style.css">
</head>
<body>
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone"> 
			<li><a href="search.php">Search</a></li>
			<li><a href="home.php">Home</a></li>
			<li><a href="profile.php?uid=<?php echo $logd_uid ?>"> <?php echo $logd_ufname ?>'s profile</a></li>
			<li><a href="friendreqs.php">Friend Requests</a></li>
			<div id="frndball">
			<?php
				frndReq_exist($logd_uid)
			?>
			</div>

			<li><a class="active" href="#">Inbox</a></li>
			
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul> 
	</div>
	<div class="display">
		<table style="width:100%">
		  <tr>
		  	
		  	<th>Date</th>
		    <th colspan="2">From</th>
		    <th>Messages</th> 
		    <th colspan=2>Action</th>
		  </tr>
	  	<?php 
	  		require_once 'connection/dbconfig.php';

	  		$stmt = $db_con->prepare("SELECT * FROM users INNER JOIN messages ON users.user_id = messages.user_from WHERE messages.user_to = $logd_uid and messages.is_opened = 'no' order by messages.date_received desc");
	  		$stmt->execute();

			if($stmt->rowCount())
			{
				while($row=$stmt->fetch(PDO::FETCH_ASSOC))
				{
					$msgid = $row['msg_id'];
					$date = $row['date_received'];
					$from_id = $row['user_from']; //sender id
			  		$from = $row['f_name']." ".$row['l_name'];
			  		$profile_pic = $row['profile_pic'];
			  		$messages = $row['msg'];
	  	?>
					  <tr>
					  	
					    <td><?php echo $date ?></td>
					    <td width="50">
					    	<div class="small_pic"><img src="<?php echo $profile_pic ?>"></div> 
					    </td>
					    <td><a href="profile.php?uid=<?php echo $from_id ?>"><?php echo $from ?></td></a>
					    <td>
					    	<!--It will print only the first sub-part of the msg-->
					    	<?php 
					    		if(strlen($messages) > 25)
					    		{
					    			$messages = substr($messages, 0, 25)."...";
					    		}
					    		else
					    		{
					    			$messages = $messages;
					    		}
					    		echo $messages
					    	?>
					    </td>
					    <td>
					    	<form method="post">
					    		<input type="hidden" name="mid" value="<?php echo $msgid ?>">
					    		<input type="submit" name="del_btn" value="&times;">
					    	</form>
						</td>
						<td><a href="converse.php?uid=<?php echo $from_id ?>"><button>View</button></a></td>
					  </tr>
		<?php 
				}
			}
		?>
		</table>
	</div>
	
</body>
</html>