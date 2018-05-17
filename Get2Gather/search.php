<!-- =================== This code authenticates the currently logged in User =================-->
<?php

	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new User();

	$user_id = $_SESSION['user_session'];

	$stmnt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmnt->execute(array(":user_id"=>$user_id));

	$userRow=$stmnt->fetch(PDO::FETCH_ASSOC);
  	$uid = $userRow['user_id'];
  	
	if(!$_SESSION['user_session'])
	{
		header("location: denied.php");
	}
?>

<?php 
	require_once 'connection/dbconfig.php';

	$stmt = $db_con->prepare("SELECT * FROM users_friends WHERE user_id = $uid");
	$stmt->execute();
	$frow=$stmt->fetch(PDO::FETCH_ASSOC);

	$fid = $frow['friend_id'];
	$is_accepted = $frow['is_accepted'];
?>

<?php
	require_once 'connection/dbconfig.php';

	if(isset($_POST['friend_btn']))
	{
		$logd_uid = $_POST['logd_uid'];
		$srchd_uid = $_POST['srchd_uid'];
		$value = "false";

		$stmt = $db_con->prepare("INSERT INTO users_friends(user_id, friend_id, is_accepted)VALUES(:uid, :sid, :v)");

		$stmt->bindParam(":uid", $logd_uid);
	  	$stmt->bindParam(":sid", $srchd_uid);
		$stmt->bindParam(":v", $value);

		if($stmt->execute())
		{
			//echo "Your Product Successfully Added";
			header("location: search.php");
		}
		else
		{
			echo "Query Problem";
		}	
	}	
?>

<?php 
	require_once 'connection/dbconfig.php';

	if(isset($_POST['cancel_btn']))
	{
		$logd_uid = $_POST['logd_uid'];
		$srchd_uid = $_POST['srchd_uid'];
		//$value = "false";

		$stmt = $db_con->prepare("DELETE FROM users_friends WHERE user_id = :uid and friend_id = :sid");

		if($stmt->execute(array(":uid" => $logd_uid, ":sid" => $srchd_uid)))
		{
			//
			header("location: search.php");
		}
		else
		{
			echo "Query Problem";
		}	
	}	
?>

<?php 
	require_once 'connection/dbconfig.php';

	if(isset($_POST['unfriend_btn']))
	{
		$logd_uid = $_POST['logd_uid'];
		$srchd_uid = $_POST['srchd_uid'];
		$value = "true";

		$stmt = $db_con->prepare("DELETE FROM users_friends WHERE user_id = :uid and friend_id = :sid and is_accepted = :v");

		if($stmt->execute(array(":uid" => $logd_uid, ":sid" => $srchd_uid, ":v" => $value)))
		{
			//echo "Your Product Successfully Added";
			header("location: search.php");
		}
		else
		{
			echo "Query Problem";
		}	
	}	
?>

<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
	<link rel="stylesheet" type="text/css" href="styles/search.style.css">
</head>
<body>
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone">
			<li><a class="active" href="#">Search</a></li>
			
			<li><a href="home.php">Home</a></li>
			<li><a href="profile.php?uid=<?php echo $uid ?>"> <?php echo $userRow["f_name"]."'s profile" ?> </a></li>
			<li><a href="friendreqs.php">Friend Requests</a></li>
			<li><a href="messages.php?val=yes">Inbox</a></li>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul>
	</div>
	<div class="container">
		<div class="box search_area">
			<div class="searchbox">
				<form method="post">
					<input type="text" name="search_field">
					<input type="submit" name="search_btn" value="Search">
				</form>
			</div>
		</div>
		<div class="box result_area">
			<?php 

				require_once 'connection/dbconfig.php';

				$output = '';
				if(isset($_POST['search_btn']))
				{
					$searchq = $_POST['search_field'];
					$searchq = preg_replace("#[^0-9a-z]#i", "", $searchq);

					$stmt = $db_con->prepare("SELECT * FROM users WHERE f_name LIKE '%$searchq%' OR l_name LIKE '%$searchq%'");
					$stmt->execute();
					if($stmt->rowCount())
					{
						while($row=$stmt->fetch(PDO::FETCH_ASSOC))
						{
							$fname = $row['f_name'];
							$lname = $row['l_name'];
							$suid = $row['user_id'];

							$output.= '<div>'.$fname.' '.$lname.'</div>'; //<div>$fname &nbsp $lname</div>
			?>
							<div class="resultbox">
								<table style="width:100%">
								<tr>
    								<td rowspan=2>
    									<div class="small_pic">
									<?php 
						  				if ($row['profile_pic'] == "") 
						  				{
						  					if ($row['sex'] == "Male") 
						  					{
						  			?>			
						  						<img src="images/user-male.jpg">
						  			<?php 
						  					}
						  					else
						  					{
						  			?>			
						  						<img src="images/user-female.png">
						  			<?php
						  					}
						  				}
						  				else 
						  				{
						  			?>
						  					<img height="100%" width="100%" src="<?php echo $row["profile_pic"] ?>">
						  			<?php
						  				}
						  			?>
									</div>
    								</td>
								  	<td rowspan="2"><strong><a href="profile.php?uid=<?php echo $suid ?>"><?php echo $output ?></a></strong></td>
								  	
								    <td>
								    	<?php 
								    	if($uid != $suid)
								    	{
								    		$stmt0=$db_con->prepare("SELECT * FROM users_friends WHERE user_id = :uid");
											$stmt0->execute(array(":uid" => $uid));
											$row=$stmt0->fetch(PDO::FETCH_ASSOC);

											if($stmt0->rowCount()) //friend request available
											{
												$stmt1=$db_con->prepare("SELECT users_friends.friend_id FROM users_friends where users_friends.friend_id = $suid AND users_friends.is_accepted = 'false' AND users_friends.friend_id IN (SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $uid)");

												$stmt2=$db_con->prepare("SELECT users_friends.friend_id FROM users_friends where users_friends.friend_id = $suid AND users_friends.is_accepted = 'true' AND users_friends.friend_id IN (SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $uid)");

												$stmt1->execute();
												$stmt2->execute();
												
												if($stmt1->rowCount()==1) // if friend request yet to be accepted
												{
									?>					
														<p><?php echo "Friend Request Sent" ?></p>
														<form action="search.php" method="post">
															<input type="hidden" name="srchd_uid" value="<?php echo $suid ?>">
															<input type="hidden" name="logd_uid" value="<?php echo $uid ?>">
											  				<input type="submit" name="cancel_btn" value="Cancel Friend Request">
											  			</form>
									<?php
												}
												else if($stmt2->rowCount()==1)
												{
									?>
													<form action="search.php" method="post">
														<input type="hidden" name="srchd_uid" value="<?php echo $suid ?>">
														<input type="hidden" name="logd_uid" value="<?php echo $uid ?>">
										  				<input type="submit" name="unfriend_btn" value="Remove Friend">
										  			</form>
									<?php
												}
												else
												{
									?>				
													<form action="search.php" method="post">
													<input type="hidden" name="srchd_uid" value="<?php echo $suid ?>">
													<input type="hidden" name="logd_uid" value="<?php echo $uid ?>">
									  				<input type="submit" name="friend_btn" value="Add Friend">
									  				</form>
									<?php
												}
											}
											else
											{
									?>
												<form action="search.php" method="post">
													<input type="hidden" name="srchd_uid" value="<?php echo $suid ?>">
													<input type="hidden" name="logd_uid" value="<?php echo $uid ?>">
									  				<input type="submit" name="friend_btn" value="Add Friend">
									  			</form>
									<?php
											}
							  			}
							  			else
							  			{
							  				echo "";
							  			}
						  			?>
								    </td>
								</tr>
								</table>
							</div>
			<?php		
						}
					}
					else
					{
						echo 'No such results found !';
					}
				}
			?>
		</div>
	</div>
</body>
</html>