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
	<title>Photos</title>
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
					
					<!--========================================== Friends link =========================================-->

					<?php
						if($srchd_uid != $logd_uid) 
						{
					?>
							<li><a href="friends.php?uid=<?php echo $srchd_uid ?>">Friends</a></li>
					<?php		
						}
						else
						{
					?>
							<li><a href="friends.php?uid=<?php echo $logd_uid ?>">Friends</a></li>
					<?php
						}
					?>	

					<!--=========================================== Photos link =========================================-->

					<li><a class="active" href="#">Photos</a></li>
					
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
						$stmt = $db_con->prepare("SELECT status_photo FROM users INNER JOIN status on users.user_id = status.user_id WHERE users.user_id = $srchd_uid AND status.status_photo != '' ORDER BY status.status_date DESC");
						$stmt->execute(); 

						if($stmt->rowCount())
						{
							while($row=$stmt->fetch(PDO::FETCH_ASSOC))
							{
				?>
								<div class="gallery">
								  <a target="_blank" href="<?php echo $row['status_photo'] ?>">
								    <img src="<?php echo $row['status_photo'] ?>">
								  </a>
								</div>
				<?php 
							}
						}
						else 
						{
							echo '<h1>No photos</h1>';		
						}
					}
					else
					{
						$stmt = $db_con->prepare("SELECT status_photo FROM users INNER JOIN status on users.user_id = status.user_id WHERE users.user_id = $logd_uid AND status.status_photo != '' ORDER BY status.status_date DESC");
						$stmt->execute(); 

						if($stmt->rowCount())
						{
							while($row=$stmt->fetch(PDO::FETCH_ASSOC))
							{
				?>
								<div class="gallery">
								  <a target="_blank" href="<?php echo $row['status_photo'] ?>">
								    <img src="<?php echo $row['status_photo'] ?>">
								  </a>
								</div>
				<?php 
							}
						}
						else 
						{
							echo '<h1>No photos</h1>';		
						}
					}	
				?>
			
      	</div>
    </div>
</body>   
</html> 
