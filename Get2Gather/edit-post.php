<?php

	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new User();

	$user_id = $_SESSION['user_session'];

	$stmnt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
	$stmnt->execute(array(":user_id"=>$user_id));

	$userRow=$stmnt->fetch(PDO::FETCH_ASSOC);
  	$uid = $userRow['user_id'];
  	$ufname = $userRow['f_name'];
  	echo $uid;
	if(!$_SESSION['user_session']){

		header("location: denied.php");
	}
?>

<?php
include_once 'connection/dbconfig.php';

//Get the value of status_id passed from the url and fetch the data content from the db based on the status_id

if(isset($_GET['status_id']))
{
	$sid = $_GET['status_id'];
	$stmt=$db_con->prepare("SELECT * FROM status WHERE status_id=:sid");
	$stmt->execute(array(':sid'=>$sid));
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<?php

require_once 'connection/dbconfig.php';

if(isset($_POST['txt_btn']))
{
	$status = $_POST['txt_status'];

	$stmt = $db_con->prepare("UPDATE status SET status_content=:stc WHERE status_id=:sid");

	$stmt->bindParam(":stc", $status);
	$stmt->bindParam(":sid", $sid);

	if($stmt->execute())
	{
		echo "<p>Status Successfully updated<p>";
		header("location: edit-post.php?status_id=$sid");
	}
	else{
		echo "Query Problem";
	}
}

?>

<?php

require_once 'connection/dbconfig.php';

if(isset($_POST['photo_btn']))
{
	if(isset($_FILES['img']['name']))
	{
		$stmt = $db_con->prepare("SELECT * FROM status ORDER BY status_id DESC LIMIT 1");
		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_ASSOC);

		$imgid = $row['status_id']; 
		$target_dir = "images/";
		$newimg = $imgid.".jpg";
		$target_path = $target_dir.$newimg;

		move_uploaded_file($_FILES["img"]["tmp_name"], $target_path);

		
		$photo = "images/".$newimg;

		$stmt = $db_con->prepare("UPDATE status SET status_photo=:stp WHERE status_id=:sid");

		$stmt->bindParam(":stp", $photo);
		$stmt->bindParam(":sid", $sid);

		if($stmt->execute())
		{
			echo "<p>Status Successfully updated<p>";
			//header("Location: edit-post.php?status_id=$sid");
			//header("location: profile.php?uid=$uid");
		}
		else
		{
			echo "Query Problem";
		}
	}
}
?>

<?php

require_once 'connection/dbconfig.php';

if(isset($_POST['view_btn']))
{
	$status = $_POST['view'];

	$stmt = $db_con->prepare("UPDATE status SET view_permit=:v WHERE status_id=:sid");

	$stmt->bindParam(":v", $status);
	$stmt->bindParam(":sid", $sid);

	if($stmt->execute())
	{
		echo "<p>Status Successfully updated<p>";
		header("location: edit-post.php?status_id=$sid");
	}
	else{
		echo "Query Problem";
	}
}

?>





<!DOCTYPE html>
<html>
<head>
	<title>Edit Profile</title>
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
			<li><a class="active" href="profile.php?uid=<?php echo $uid ?>"> <?php echo $ufname ?>'s profile</a></li>
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
	  				if ($userRow['cover_pic'] == "")
	  				{
	  			?>		
	  					<img height="100%" width="100%" src="images/defaultCoverPic.png">
	  			<?php
	  				}
	  				else
	  				{
	  			?>		
	  					<img height="100%" width="100%" src="<?php echo $userRow['cover_pic'] ?>">
	  			<?php
	  				}
	  			?>
		  		<!-- Div for profile pic -->
		  		<div class="pro_pic">
		  			<?php 
		  				if ($userRow['profile_pic'] == "") 
		  				{
		  					if ($userRow['sex'] == "Male") 
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
		  					<img height="100%" width="100%" src="<?php echo $userRow['profile_pic'] ?>">
		  				<?php
		  				}
		  			?>
		  		</div>
		  		<!-- Div for displaying username -->
		  		<div class="usrname">
		  			<h3><?php echo $userRow['f_name'] ."<br>". $userRow['l_name'] ?></h3>
		  		</div>
	  		</div>
	  		<!-- Div for the navbar below the cover page -->
	  		<div class="nav_area">
	  			<ul class="navone">
					<li><a href="profile.php?uid=<?php echo $uid ?>">Timeline</a></li>
					<li><a href="#">About</a></li>
					<li><a href="#">Friends</a></li>
					<li><a href="#">Photos</a></li>
					<li><a href="#">Edit Profile</a></li>
				</ul>
	  		</div>
	  	</div>

	  	
	  	<!-- This is the edit area -->
		<div class="box edit">

			<div class="edit_area">
				<form method="post">
					<table>
						
					    <tr>
					        <td>Change Text</td>
					        <form method="post">
					        	<td>
					        	<textarea rows="10" cols="20" name="txt_status"><?php echo $row['status_content'] ?></textarea>
					        </td>
					        <td><input type="submit" name="txt_btn" value="Save"></td>
					        </form>
				        </tr>
					    <tr>
					    	<?php 
					    		if($row['status_photo'] == "")
					    		{
					    	?>
					    			<td>Add a Photo</td>
					    			<form method="post" enctype="multipart/form-data">
								        <td>
								        	<input type="file" name="img">
								        </td>
					    				<td>
					    					<input type="submit" name="photo_btn" value="Save">
					    				</td>
					    			</form>
					    	<?php	
					    		}
					    		else
					    		{
					    	?>		
					    			<td>Change Photo</td>
					    			<form method="post" enctype="multipart/form-data">
								        <td>
								        	<img width="100%" height="100%" src="<?php echo $row['status_photo']?>">
								        	<input type="file" name="img">
								        </td>
					    				<td><input type="submit" name="photo_btn" value="Save"></td>
					    			</form>
					    	<?php	
					    		}
					    	?>
				        </tr>
				        
					    <tr>
					        <td>Change View Permissions (<?php echo $row['view_permit'] ?>)</td>
					        <form method="post">
					        <td>
					        	<input type="radio" name="view" value="Private">Private
					        	<input type="radio" name="view" value="Friends">Friends
					        	<input type="radio" name="view" value="Public">Public
					        </td>
					        <td><input type="submit" name="view_btn" value="Save"></td>
					        </form>
				        </tr>
					    <tr>
					        <td>&nbsp;</td>
					        <td>
					        	<input type=button onClick="location.href='home.php'" value='See your Post'>
					        </td>
				        </tr>
			      </table>
				</form>
		      
			</div>
		</div>

		

	<!-- The content ends here -->
	</div>
</body>
</html>