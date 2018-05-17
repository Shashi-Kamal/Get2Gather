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
  	$logd_uemail = $userRow['email'];
  	$logd_ugender = $userRow['sex'];
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
	<title>About</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/edit-profile.style.css">
</head>

<body>
	<!-- This is the top navbar -->
	<div class="nav">
		<!-- Need to edit later -->
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

					<li><a class="active" href="#">About</a></li>

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

					<!--========================================== Photos link =========================================-->
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
					
					<!--========================================== Send msg link ========================================-->
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

	  	<!-- This is the about area -->
		<div class="box edit">
			<div class="edit_area">
				
				<?php
					if($srchd_uid != $logd_uid)
					{
						$stmt = $db_con->prepare("SELECT * FROM users WHERE user_id = $srchd_uid");
						$stmt->execute(); 

						if($stmt->rowCount())
						{
							while($row=$stmt->fetch(PDO::FETCH_ASSOC))
							{
				?>
								<p>Basic Information :</p>
								<hr>
									<table width="363" height="190" border="1" cellpadding="6">
										<tr>
								        <td>First Name</td>
									        <td>
									        	<?php echo $row['f_name'] ?>
									        </td>
								        </tr>
								        <tr>
								        <td>Last Name</td>
									        <td>
									        	<?php echo $row['l_name'] ?>
									        </td>
								        </tr>
									    <tr>
									        <td width="138">Introduction</td>
									        <td width="195">
									        	<?php echo $row['intro'] ?>
									        </td>
								        </tr>
									    
								        <tr>
									        <td>Birthday</td>
									        <td>
									        	<?php echo $row['dob'] ?>
									        </td>
								        </tr>

									    <tr>
									        <td>Relation</td>
									        <td>
									        	<?php echo $row['relation'] ?>
									        </td>
								        </tr>
							      </table>
								

								<hr>
								<p>Word and Education</p>
								<hr>
								
									<table>
										<tr>
											<td>Job</td>
											<td>
												<?php echo $row['job'] ?>
											</td>
										</tr>
										<tr>
											<td>Education</td>
											<td>
												<?php echo $row['education'] ?>
											</td>
										</tr>
									</table>
								

								<hr>
								<p>Current Address</p>
								<hr>
								
									<table>
										<tr>
											<td>State</td>
											<td>
												<?php echo $row['state'] ?>
											</td>
										</tr>
										<tr>
											<td>City</td>
											<td>
												<?php echo $row['city'] ?>
											</td>
										</tr>
										
									</table>
								

								<hr>
								<p>Contact Information</p>
								<hr>
									<table>
										<tr>
									        <td>Email Address</td>
									        <td>
									        	<?php echo $row['email'] ?>
									        </td>
								        </tr>
								        <tr>
								        <td>Contact number</td>
									        <td>
									        	<?php echo $row['mobile'] ?>
									        </td>
								        </tr>
										
									</table>
				<?php				
							}
						}
					}
					else
					{
						$stmt = $db_con->prepare("SELECT * FROM users WHERE user_id = $logd_uid");
						$stmt->execute(); 

						if($stmt->rowCount())
						{
							while($row=$stmt->fetch(PDO::FETCH_ASSOC))
							{
				?>
								<p>Basic Information :</p>
								<hr>
									<table width="363" height="190" border="1" cellpadding="6">
										<tr>
								        <td>First Name</td>
									        <td>
									        	<?php echo $row['f_name'] ?>
									        </td>
								        </tr>
								        <tr>
								        <td>Last Name</td>
									        <td>
									        	<?php echo $row['l_name'] ?>
									        </td>
								        </tr>
									    <tr>
									        <td width="138">Introduction</td>
									        <td width="195">
									        	<?php echo $row['intro'] ?>
									        </td>
								        </tr>
									    
								        <tr>
									        <td>Birthday</td>
									        <td>
									        	<?php echo $row['dob'] ?>
									        </td>
								        </tr>

									    <tr>
									        <td>Relation</td>
									        <td>
									        	<?php echo $row['relation'] ?>
									        </td>
								        </tr>
							      </table>
								

								<hr>
								<p>Word and Education</p>
								<hr>
								
									<table>
										<tr>
											<td>Job</td>
											<td>
												<?php echo $row['job'] ?>
											</td>
										</tr>
										<tr>
											<td>Education</td>
											<td>
												<?php echo $row['education'] ?>
											</td>
										</tr>
									</table>
								

								<hr>
								<p>Current Address</p>
								<hr>
								
									<table>
										<tr>
											<td>State</td>
											<td>
												<?php echo $row['state'] ?>
											</td>
										</tr>
										<tr>
											<td>City</td>
											<td>
												<?php echo $row['city'] ?>
											</td>
										</tr>
										
									</table>
								

								<hr>
								<p>Contact Information</p>
								<hr>
									<table>
										<tr>
									        <td>Email Address</td>
									        <td>
									        	<?php echo $row['email'] ?>
									        </td>
								        </tr>
								        <tr>
								        <td>Contact number</td>
									        <td>
									        	<?php echo $row['mobile'] ?>
									        </td>
								        </tr>
										
									</table>
				<?php
							}
						}
					}
				?>
					
			</div>
		</div>
	</div>

		

	<!-- The content ends here -->
	</div>

	<!-- Here goes the javascript area -->
	
</body>
</html>