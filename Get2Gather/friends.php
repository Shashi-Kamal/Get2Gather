<?php

	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new User();

	$user_id = $_SESSION['user_session'];

	$stmnt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmnt->execute(array(":user_id"=>$user_id));

	$userRow=$stmnt->fetch(PDO::FETCH_ASSOC);
  	$logd_uid = $userRow['user_id'];
  	$logd_ufname = $userRow['f_name'];
  	$logd_ulname = $userRow['l_name'];
  	//echo $uid;
	if(!$_SESSION['user_session']){

		header("location: denied.php");
	}
?>

<?php

include_once 'connection/dbconfig.php';

if(isset($_GET['uid']))
{
	$srchd_uid = $_GET['uid']; // This holds the user-id of the user the logged-in user is searching for
	$stmt=$db_con->prepare("SELECT * FROM users WHERE user_id=:uid");
	$stmt->execute(array(':uid'=>$srchd_uid));
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
	$srchd_fname = $row['f_name'];
	$srchd_lname = $row['l_name'];
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Friends</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/edit-profile.style.css">
</head>

<body>
	<!-- This is the top navbar -->
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone">
			<li><a href="search.php">Search</a></li>
			<li><a href="home.php">Home</a></li>
			<li><a class="active" href="profile.php?uid=<?php echo $logd_uid ?>"> <?php echo $logd_ufname ?>'s profile</a></li>
			<li><a href="friendreqs.php">Friend Requests</a></li>
			<li><a href="messages.php">Messages</a></li>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul>
	</div>


	<!-- The whole content in grid layout -->
	<div class="container">
		<!-- This is the cover page area done in grid layout of 2 rows-->
	  	<div class="box cover_pic">
	  		<!-- Div for cover pic -->
	  		<div class="cov_area">
	  			<?php
	  				if ($row['cover_pic'] == "")
	  				{
	  			?>
	  					<img height="100%" width="100%" src="images/defaultCoverPic.png">
	  			<?php
	  				}
	  				else
	  				{
	  			?>
	  					<img height="100%" width="100%" src="<?php echo $row['cover_pic'] ?>">
	  			<?php
	  				}
	  			?>
		  		<!-- Div for profile pic -->
		  		<div class="pro_pic">
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
		  					<img height="100%" width="100%" src="<?php echo $row['profile_pic'] ?>">
		  				<?php
		  				}
		  			?>
		  		</div>
		  		<!-- Div for displaying username -->
		  		<div class="usrname">
		  			<h3><?php echo $srchd_fname ." ". $srchd_lname ?></h3>
		  		</div>
	  		</div>
	  		<!-- Div for the navbar below the cover page -->
	  		<div class="nav_area">
	  			<ul class="navone">

	  				<!--========================================= Timeline link =======================================-->
	  				<?php
						if($srchd_uid != $logd_uid) 
						{
					?>
							<li><a href="profile.php?uid=<?php echo $srchd_uid ?>">Timeline</a></li>
					<?php		
						}
						else
						{
					?>
							<li><a href="profile.php?uid=<?php echo $logd_uid ?>">Timeline</a></li>
					<?php
						}
					?>
					
					<!--=========================================== About link =========================================-->
					<?php
						if($srchd_uid != $logd_uid) 
						{
					?>
							<li><a href="about.php?uid=<?php echo $srchd_uid ?>">About</a></li>
					<?php		
						}
						else
						{
					?>
							<li><a href="about.php?uid=<?php echo $logd_uid ?>">About</a></li>
					<?php
						}
					?>

					<!--=========================================== Friends link =========================================-->		
					<li><a class="active" href="#">Friends</a></li>

					<!--=========================================== Photos link =========================================-->

					<?php
						if($srchd_uid != $logd_uid) 
						{
					?>
							<li><a href="Photos.php?uid=<?php echo $srchd_uid ?>">Photos</a></li>
					<?php		
						}
						else
						{
					?>
							<li><a href="Photos.php?uid=<?php echo $logd_uid ?>">Photos</a></li>
					<?php
						}
					?>
					
					<!--========================================= Send msg link =======================================-->
					<?php
						if($srchd_uid != $logd_uid) 
						{
					?>		
							<li><a href="#"id="msgLink">Send Message</a></li>
					<?php
						}
						else
						{
					?>
							<li><a href="edit-profile.php?uid=<?php echo $logd_uid ?>">Edit Profile</a></li>
					<?php
						}
					?>
				</ul>
	  		</div>
	  	</div>


	  	<!-- This is the edit area -->
		<div class="box edit">
			
				<?php
					require_once 'connection/dbconfig.php';
					
					if($srchd_uid != $logd_uid)
					{
						//show mutual friends
				?>
						<h1><font color="white">Mutual Friends </font></h1>
				<?php
						//collect all the mutual friend ids
						$stmt = $db_con->prepare("SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $logd_uid and users_friends.is_accepted = 'true' AND users_friends.friend_id IN (SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $srchd_uid and users_friends.is_accepted = 'true')");

						$stmt->execute();

						//if there's any friend
						if($count = $stmt->rowCount())
						{
				?>
							<p id="mutual"><?php echo $count." "."mutual friends" ?></p>
				<?php
							while($frow=$stmt->fetch(PDO::FETCH_ASSOC))
							{
								$friendid = $frow['friend_id'];
								
					            $stmt1 = $db_con->prepare("SELECT user_id, f_name, l_name, profile_pic FROM users where user_id = $friendid");
					            $stmt1->execute();
								if($stmt1->rowCount())
								{
									while($row=$stmt1->fetch(PDO::FETCH_ASSOC))
									{
				?>
										<div class="friendList">
											<table>
												<tr>
													<td>
														<div class="small_pic">
														<?php 
											  				if ($row['profile_pic'] == "") 
											  				{
											  					if ($row['sex'] == "Male") 
											  					{
											  			?>			
											  						<a href="profile.php?uid=<?php echo $row['user_id'] ?>"><img src="images/user-male.jpg"></a>
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
											  					<a href="profile.php?uid=<?php echo $row['user_id'] ?>"><img height="100%" width="100%" src="<?php echo $row["profile_pic"] ?>"></a>
											  			<?php
											  				}
											  			?>
														</div>
													</td>
													<td><strong><a href="profile.php?uid=<?php echo $row['user_id'] ?>"><?php echo $row['f_name']." ".$row['l_name'] ?></strong></a></td>
												</tr>
											</table>
										</div>
				<?php
									}
								}
							}
						}
						else 
						{
							echo "<h1>No Mutual Friends Yet!</h1>";
						}
					}
					else 
					{
						//show all friends
				?>
						<h1><font color="white">All Friends</font></h1>
				<?php
						//collect all the friend ids
						$stmt = $db_con->prepare("SELECT friend_id FROM users_friends where user_id = $logd_uid and is_accepted = 'true'");
						
						$stmt->execute();

						//if there's any friend
						if($count = $stmt->rowCount())
						{
							while($frow=$stmt->fetch(PDO::FETCH_ASSOC))
							{
								$friendid = $frow['friend_id'];
								
					            $stmt1 = $db_con->prepare("SELECT user_id, f_name, l_name, profile_pic FROM users where user_id = $friendid");
					            $stmt1->execute();
								if($stmt1->rowCount())
								{
									while($row=$stmt1->fetch(PDO::FETCH_ASSOC))
									{
				?>
										<div class="friendList">
											<table>
												<tr>
													<td>
														<div class="small_pic">
														<?php 
											  				if ($row['profile_pic'] == "") 
											  				{
											  					if ($row['sex'] == "Male") 
											  					{
											  			?>			
											  						<a href="profile.php?uid=<?php echo $row['user_id'] ?>"><img src="images/user-male.jpg"></a>
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
											  					<a href="profile.php?uid=<?php echo $row['user_id'] ?>"><img height="100%" width="100%" src="<?php echo $row["profile_pic"] ?>"></a>
											  			<?php
											  				}
											  			?>
														</div>
													</td>
													<td><strong><a href="profile.php?uid=<?php echo $row['user_id'] ?>"><?php echo $row['f_name']." ".$row['l_name'] ?></strong></a></td>
												</tr>
											</table>
										</div>
				<?php
									}
								}
							}
						}
						else 
						{
							echo "<h1>No Friends Yet!</h1>";
						}
					}
				?>		
      	</div>
    </div>
</body>   
</html> 
