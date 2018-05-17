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

<?php 

include_once 'connection/dbconfig.php';

//Get the value of searched id passed from the url and fetch the data content from the db based on the status_id

if(isset($_GET['statusid']))
{
	$status_id = $_GET['statusid'];
	$stmt=$db_con->prepare("SELECT * FROM status WHERE status_id=:sid");
	$stmt->execute(array(':sid'=>$status_id));
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
	//$statusid = $row['status_id'];
	//$srchd_lname = $row['l_name'];


}
	
?>

<?php  // Comments get inserted into db here....
	require_once 'connection/dbconfig.php';

	if(isset($_POST['post_btn']))
	{

		$comment = $_POST['txt_comnt'];
		//$count = $_POST['rowcount'];
		
		try 
		{
			$stmt = $db_con->prepare("INSERT INTO comments(com, com_by, com_to)VALUES(:com, :comby, :comto)");
			$stmt->bindParam(":com", $comment);
		  	$stmt->bindParam(":comby", $logd_uid);
			$stmt->bindParam(":comto", $status_id);
			//$stmt->bindParam(":comdt", $view);

			if($stmt->execute())
			{
				$stmt = $db_con->prepare("SELECT * FROM comments JOIN users ON comments.com_by = users.user_id where comments.com_to = $status_id");

				$stmt->execute();
				if($count = $stmt->rowCount())
				{
					//echo $count;
					$stmt = $db_con->prepare("UPDATE status SET comments=:count WHERE status_id=:comto");
					$stmt->bindParam(":count", $count);
					$stmt->bindParam(":comto", $status_id);	

					if($stmt->execute())
					{
						//header("location: comments.php?statusid=$status_id");
						//echo $count;
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

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="styles/home.style.css">
</head>
<body>
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone">
			<li><a href="search.php">Search</a></li>
			<li><a href="#">Friends</a></li>
			<li><a  class="active" href="posts.php">Home</a></li>
			<li><a href="profile.php?uid=<?php echo $uid ?>"> <?php echo $userRow["f_name"]."'s profile" ?> </a></li>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul> 
	</div>


	<div class="container">
		<!--<div class="box input_area">
			<div class="input_box">
				<form method="post" enctype="multipart/form-data">
				<table style="width:100%; color: white" >
				<tr>
				  <td rowspan="2" width="50px">
				  	<div class="small_pic">
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
		  					<img height="100%" width="100%" src="<?php echo $userRow["profile_pic"] ?>">
		  			<?php
		  				}
		  			?>
					</div>
				  </td>
				  <td colspan=3 width="250px">Write Something...</td>
				  
				  <td>
				  	<!--<input type="radio" name="view" value="Private">Private-->
				  </td>
				  <td>
				  	<!--<input type="radio" name="view" value="Friends">Friends
				  </td>
				  <td>
				  	<input type="radio" name="view" value="Public" checked>Public
				  </td>
				</tr>
				<tr>
				  <td colspan=6>
				  	<textarea rows="5" cols="10" name="txt_comnt" wrap="hard"></textarea>
				  </td>
				</tr>
				<tr>
				  <td></td>
				  <td colspan=6> 
				  	<input type="file" id="real_file" name="img_photo">
				  </td>
				</tr>
				<tr>
				  <td colspan=6><span id="custom_txt"></span></td>
				  <td>
				  	
				  	<input type="submit" name="post_btn" value="Post">
				  </td>
				</tr>
				</table>
			</form>
			</div>
		</div> -->

		<div class="box feed_area">
			<?php 
				/*If there's any posts by the user
					print all the posts in descending order 
				else 
					print "No posts yet"*/
				//echo $status_id;
        
				require_once 'connection/dbconfig.php';

				$stmt = $db_con->prepare("SELECT * FROM comments JOIN users ON comments.com_by = users.user_id where comments.com_to = $status_id order by com_date desc");


				$stmt->execute();
				if($count = $stmt->rowCount())
				{
					while($row=$stmt->fetch(PDO::FETCH_ASSOC))
					{
				?>
			<div class="feed_box">
				<table style="width:100%;">
				
			  <tr>
			    <td rowspan="2" width="50px">
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
			    <td colspan="4">
			    	
						<?php echo $row["f_name"] ." ". $row["l_name"] ?>
					
			    </td>
			    <!-- Edit button; passing the status_id to the edit-post page-->
			    <td width="50px">
			    	<?php 
			    		if($row['user_id'] == $logd_uid)
			    		{
			    	?>		
			    			<a href="edit-post.php?status_id=<?php echo $row['status_id']; ?>"><button>&#128393;</button></a>
			    	<?php
			    		}
			    		else
			    		{
			    			echo "";
			    		}
			    	?>
			    </td>
			    <!-- Delete button; passing the status_id to the del-post page-->
			    <td width="50px">
			    	<?php 
			    		if($row['user_id'] == $logd_uid)
			    		{
			    	?>		
			    			<form method="post">
					    		<input type="hidden" name="comid" value="<?php echo $row['status_id'] ?>">
					    		<input type="submit" name="del_btn" value="&times;">
					    	</form>
			    	<?php
			    		}
			    		else
			    		{
			    			echo "";
			    		}
			    	?>
			    </td>
			  </tr>
			  <tr style="border-bottom: 1px solid #98503c">
			    <td colspan="5" style="font-size: 12px"><?php echo $row["com_date"] ?></td>
			    <td></td>
			  </tr>
			  <tr>
			    <td colspan="7" width="100px">
			    	<?php echo $row['com'] ?>
			    </td>
			  </tr>
			  <tr>
			    <td colspan="7">
			    	
			    </td>
			  </tr>
			  <tr>
			  	<!-- Place for Like, Comment buttons -->
			    <td></td>
			    <td></td> 
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			    <td></td>
			  </tr>
			</table>
			
			</div>
			<div class="space"></div>
			<?php
					}
					echo "<h1>No more comments</h1>";
				}

				else 
				{
					echo "<h1>No Comments Yet!</h1>";
				}
				?>
		</div>
		</div>
	</div>
</body>
</html>