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
  	$propic = $userRow['profile_pic'];
  	
	if(!$_SESSION['user_session'])
	{
		header("location: denied.php");
	}
?>

<?php
include_once 'connection/dbconfig.php';

//Get the value of status_id passed from the url and fetch the data content from the db based on the status_id

if(isset($_POST['del_btn']))
{
	$sid = $_POST['statusid'];

	$stmt=$db_con->prepare("DELETE FROM status WHERE status_id=:sid");
	$stmt->execute(array(':sid'=>$sid));

	if($stmt->execute())
	{
		header("location: home.php?uid=$uid");
	}
}
?>

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

<?php
require_once("session.php");
require_once("class.user.php");

function msg_exist($user_id)
{
	$auth_user = new User();
	try
	{
		$stmt = $auth_user->runQuery("SELECT * FROM messages WHERE user_to=:uid and is_opened=:v");
		$stmt->execute(array(':uid'=>$user_id, ':v'=>"no"));
		$stmt->fetch(PDO::FETCH_ASSOC);
		if($count = $stmt->rowCount())
		{
?>			
			<script type="text/javascript">
				msg_notify();
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
	function msg_notify()
	{
		var msgball = document.getElementById("msgball");
		var prev_handler = window.onload;
		window.onload = function()
		{
			if (prev_handler)
		    {
		        prev_handler();
		    }
			msgball.style.display = "block";
		}
	}
</script>


<?php 

require_once 'connection/dbconfig.php';

if(isset($_POST['like_btn']))
{
	$statusid = $_POST['statusid'];
	$user = $_POST['user'];
	$value = "true";

	try
	{
		$stmt = $db_con->prepare("INSERT INTO status_likes(status_liked, liked_by, is_liked)VALUES(:s, :u, :v)");

		$stmt->bindParam(":s", $statusid);
	  	$stmt->bindParam(":u", $user);
		$stmt->bindParam(":v", $value);

		if($stmt->execute())
		{
			$stmt = $db_con->prepare("SELECT * FROM status_likes JOIN users ON status_likes.liked_by = users.user_id where status_likes.status_liked = $statusid and status_likes.is_liked = 'true'");

			$stmt->execute();
			if($count = $stmt->rowCount())
			{
				$stmt = $db_con->prepare("UPDATE status SET likes=:count WHERE status_id=:s");
				$stmt->bindParam(":count", $count);
				$stmt->bindParam(":s", $statusid);	

				if($stmt->execute())
				{
					header("location: home.php");
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
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}

?>

<?php 

require_once 'connection/dbconfig.php';

if(isset($_POST['liked_btn']))
{
	$statusid = $_POST['statusid'];
	$user = $_POST['user'];
	$value = "false";

	try
	{
		$stmt = $db_con->prepare("UPDATE status_likes SET is_liked=:v WHERE status_liked=:s and liked_by=:u");

		$stmt->bindParam(":s", $statusid);
	  	$stmt->bindParam(":u", $user);
		$stmt->bindParam(":v", $value);

		if($stmt->execute())
		{
			$stmt = $db_con->prepare("SELECT * FROM status_likes JOIN users ON status_likes.liked_by = users.user_id where status_likes.status_liked = $statusid and status_likes.is_liked = 'true'");

			$stmt->execute();
			if($count = $stmt->rowCount()) //update is not working
			{
				$stmt1 = $db_con->prepare("UPDATE status SET likes=:count WHERE status_id=:s");
				$stmt1->bindParam(":count", $count);
				$stmt1->bindParam(":s", $statusid);	

				if($stmt1->execute())
				{
					//header("location: home.php");
					echo "updated";
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
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}

?>

<?php 
require_once("session.php");
require_once("class.user.php");

function isLiked($user_id, $status_id)
{
	$auth_user = new User();
	try
	{
		$stmt = $auth_user->runQuery("SELECT * FROM status_likes WHERE liked_by=:uid and status_liked=:fid and is_liked=:v");
		$stmt->execute(array(':uid'=>$user_id, ':fid'=>$status_id, ':v'=>"true"));
		$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
		if($stmt->rowCount() == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
}
?>

<!-- This code inserts the status data content into database once the user posted his status -->
<?php
	require_once 'connection/dbconfig.php';

	if(isset($_POST['post_with_photo_btn']))
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

			$status = $_POST['txt_status'];
			$photo = "images/".$newimg;
			$view = $_POST['view'];
			
			try 
			{
				$stmt = $db_con->prepare("INSERT INTO status(user_id, status_content, status_photo, view_permit)
				VALUES(:uid, :stc, :stp, :v)");
				$stmt->bindParam(":uid", $uid);
			  	$stmt->bindParam(":stc", $status);
				$stmt->bindParam(":stp", $photo);
				$stmt->bindParam(":v", $view);

				if($stmt->execute())
				{
					header("location: home.php");
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
	}
?>

<?php
	require_once 'connection/dbconfig.php';

	if(isset($_POST['post_btn']))
	{

		$status = $_POST['txt_status'];
		//$photo =  "images/".$newimg;
		$view = $_POST['view'];
		
		try 
		{
			$stmt = $db_con->prepare("INSERT INTO status(user_id, status_content, view_permit)
			VALUES(:uid, :stc, :v)");
			$stmt->bindParam(":uid", $uid);
		  	$stmt->bindParam(":stc", $status);
			//$stmt->bindParam(":stp", $photo);
			$stmt->bindParam(":v", $view);

			if($stmt->execute())
			{
				//echo "Your Product Successfully Added";
				header("location: home.php");
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

<?php 

require_once 'connection/dbconfig.php';

	if(isset($_POST['comnt_btn']))
	{

		$com = $_POST['txt_comnt'];
		$comto = $_POST['sts_id'];
		$comby = $_POST['com_by'];
		
		try 
		{
			$stmt1 = $db_con->prepare("INSERT INTO comments(com, com_by, com_to)VALUES(:c, :cb, :ct)");
			$stmt1->bindParam(":c", $com);
		  	$stmt1->bindParam(":ct", $comto);
			$stmt1->bindParam(":cb", $comby);
			

			if($stmt1->execute())
			{
				$stmt2 = $db_con->prepare("SELECT * FROM comments JOIN users ON comments.com_by = users.user_id where comments.com_to = $comto");

				$stmt2->execute();
				if($count = $stmt2->rowCount())
				{
					echo $count;

					$stmt3 = $db_con->prepare("UPDATE status SET comments=:count WHERE status_id=:comto");
					$stmt3->bindParam(":count", $count);
					$stmt3->bindParam(":comto", $comto);	

					if($stmt3->execute())
					{
						header("location: comments.php?statusid=$comto");
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
	<script src="myScript.js"></script> 
</head>
<body>
	<div class="nav">
		<!-- Need to edit on go -->
		<ul class="navone">
			<li><a href="search.php">Search</a></li>
			<li><a  class="active" href="posts.php">Home</a></li>
			<li><a href="profile.php?uid=<?php echo $uid ?>"> <?php echo $userRow["f_name"]."'s profile" ?> </a></li>
			<li><a href="friendreqs.php">Friend Requests</a></li>
			<div id="frndball"> 
			<?php 
				frndReq_exist($uid)
			?>
			</div>
			<li><a href="messages.php?val=yes">Inbox</a></li>
			<div id="msgball">
			<?php 
				msg_exist($uid)
			?>
			</div>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul>
	</div> 
	<div class="container">
		<div class="box input_area">
			<div class="tab">
				<button class="tablink" onclick="openTab(event, 'Text')" id="defaultOpen">Write Something..</button>
				<button class="tablink" onclick="openTab(event, 'Photo')">Add Photo</button>
			</div>

			<div id="Text" class="tabcontent">
			  
				  <form method="post" name="txt_frm_logd_uid">
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
					  <td colspan=3 width="250px"></td>
					  
					  <td>
					  	<input type="radio" name="view" value="Private">Private
					  </td>
					  <td>
					  	<input type="radio" name="view" value="Friends">Friends
					  </td>
					  <td>
					  	<input type="radio" name="view" value="Public" checked>Public
					  </td>
					</tr>
					<tr>
					  <td colspan=6>
					  	<textarea rows="5" cols="10" name="txt_status" wrap="hard" onkeypress="display_txt_post_btn()"></textarea>
					  </td>
					</tr>
					<tr>
					  <td></td>
					  <td colspan=5> 
					  </td>
					  <td>
					  	<input type="submit" name="post_btn" value="Post" id="txt_post_btn" style="display: none;">
					  </td>
					</tr>
					</table>
				</form>
			</div>

			<div id="Photo" class="tabcontent">
				  <form method="post" enctype="multipart/form-data" name="photo_frm_logd_uid">
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
					  	<input type="radio" name="view" value="Private">Private
					  </td>
					  <td>
					  	<input type="radio" name="view" value="Friends">Friends
					  </td>
					  <td>
					  	<input type="radio" name="view" value="Public" checked>Public
					  </td>
					</tr>
					<tr>
					  <td colspan=6>
					  	<textarea rows="5" cols="10" name="txt_status" wrap="hard" onkeypress="display_photo_post_btn()"></textarea>
					  </td>
					</tr>
					<tr>
					  <td></td>
					  <td colspan=5> 
					  	<input type="file" name="img">
					  </td>
					  <td>
					  	<input type="submit" name="post_with_photo_btn" value="Post" id="photo_post_btn" style="display: none;">
					  </td>
					</table>
				</form>
			</div>
		</div>


		<div class="box feed_area">
			<?php 
				/*If there's any posts by the user
					print all the posts in descending order 
				else 
					print "No posts yet"*/

				require_once 'connection/dbconfig.php';

				$stmt = $db_con->prepare("SELECT * FROM status JOIN users ON status.user_id = users.user_id where users.user_id = $uid and view_permit != 'Private' ORDER by status_date DESC ");
				$stmt->execute();
				if($stmt->rowCount())
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
									    <td colspan="4" style="color: #ffab00";>
									    	
												<strong><?php echo $row["f_name"] ." ". $row["l_name"] ?></strong>
											
									    </td>
									    <!-- Edit button; passing the status_id to the edit-post page-->
									    <td width="50px">
									    	<?php 
									    		if($row['user_id'] == $uid)
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
									    		if($row['user_id'] == $uid)
									    		{
									    	?>		
									    			<form method="post">
											    		<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
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
									  	<?php 
										  		$timestamp = strtotime($row['status_date']);	// Grabbing the 'timestamp' colm from mysql
										  		$date = date('j M', $timestamp);  				// j: day of mnth 1-31; M: short for mnth names
												$time = date('g:i a', $timestamp);				// G: 12hr withou leading 0's; i: mins with leading 0's; a: am or pm
										  	?>
									    <td colspan="5" status_date style="font-size: 12px"><?php echo $date." at ".$time ?></td>
									    <td style="font-size: 12px"><?php echo $row["view_permit"] ?></td>
									  </tr>
									  <tr>
									    <td colspan="7" width="100px">
									    	<?php 
									    		if($row['status_content'] == "") 
									    		{
									    			echo "";
									    		}
									    		else
									    		{
									    	?>
									    			<?php echo $row["status_content"] ?>
									    	<?php
									    		}
									    	?>
									    </td>
									  </tr>
									  <tr>
									    <td colspan="7">
									    	<?php //There's a problem
									    		if ($row['status_photo'] == "") 
								  					{
								  						echo "";
								  					}
								  					else
								  					{
								  			?>			
								  						<img height="100%" width="100%" src="<?php echo $row['status_photo'] ?>">
								  			<?php
								  					}
								  			?>
									    </td>
									  </tr>
									  <tr>
									  	<!-- Place for Like, Comment buttons -->
									    <td></td>
									    <td>
									    	<?php 
									    		if(isLiked($uid, $row['status_id'])) // if already liked
									    		{
									    	?>		
									    			<form method="post">
									    				<input type="hidden" name="user" value="<?php echo $uid ?>">
									    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
											    		<input type="submit" name="liked_btn" value="Liked">
											    	</form> 
											<?php
									    		}
									    		else // if not yet liked
									    		{
									    	?>	
									    			<form method="post">
									    				<input type="hidden" name="user" value="<?php echo $uid ?>">
									    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
											    		<input type="submit" name="like_btn" value="Like">
											    	</form> 
											<?php		
									    		}
									    		
									    	?>
									    </td> 
									    <td><?php echo $row['likes']." "."Likes"?></td>
									    <td></td>
									    <td><a href="comments.php?statusid=<?php echo $row['status_id'] ?>"><button>Comment</button></a>&nbsp;<?php echo $row['comments']." "."Comments" ?></td>
									    <td></td>
									    <td></td>
									  </tr>
									  <tr>
									  	<td>
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
									  	<td colspan="5">
									  		<form method="post">
									  			<textarea rows="2" cols="50" name="txt_comnt" ></textarea>
									  			<input type="hidden" name="sts_id" value="<?php echo $row['status_id'] ?>">
									  			<input type="hidden" name="com_by" value="<?php echo $uid ?>">
									  			
									  		
									  	</td>
									  	<td><input type="submit" name="comnt_btn" value="Post"></td>
								  			</form>
									  </tr>
									</table>
						</div>
						<div class="space"></div>
			<?php			
					}
				}
			?>
		<?php
				$stmt = $db_con->prepare("SELECT friend_id FROM users_friends where user_id = $uid and is_accepted = 'true'");
						
				$stmt->execute();
				if($count = $stmt->rowCount())
				{
					//echo $count;
					while($frow=$stmt->fetch(PDO::FETCH_ASSOC))
					{
						$friendid = $frow['friend_id'];
						//echo $friendid;
						
			            $stmt1 = $db_con->prepare("SELECT * FROM status JOIN users ON status.user_id = users.user_id where users.user_id = $friendid and view_permit != 'Private' ORDER by status_date DESC ");
			            $stmt1->execute();
						if($stmt1->rowCount())
						{
							while($row=$stmt1->fetch(PDO::FETCH_ASSOC))
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
										    <td colspan="4"  style="color: #ffab00";>
										    	
													<strong><?php echo $row["f_name"] ." ". $row["l_name"] ?></strong>
												
										    </td>
										    <!-- Edit button; passing the status_id to the edit-post page-->
										    <td width="50px">
										    	<?php 
										    		if($row['user_id'] == $uid)
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
										    		if($row['user_id'] == $uid)
										    		{
										    	?>		
										    			<form method="post">
												    		<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
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
									    	<td colspan="5" style="font-size: 12px"><?php echo $row["status_date"] ?></td>
									    	<td style="font-size: 12px"><?php echo $row["view_permit"] ?></td>
									  	</tr>
									  	<tr>
										    <td colspan="7" width="100px">
										    	<?php 
										    		if($row['status_content'] == "") 
										    		{
										    			echo "";
										    		}
										    		else
										    		{
										    	?>
										    			<?php echo $row["status_content"] ?>
										    	<?php
										    		}
										    	?>
										    </td>
										</tr>
										<tr>
										    <td colspan="7">
										    	<?php //There's a problem
										    		if ($row['status_photo'] == "") 
									  					{
									  						echo "";
									  					}
									  					else
									  					{
									  			?>			
									  						<img height="100%" width="100%" src="<?php echo $row['status_photo'] ?>">
									  			<?php
									  					}
									  			?>
										    </td>
										</tr>
										<tr>
									  	<!-- Place for Like, Comment buttons -->
										    <td></td>
										    <td>
										    	<?php 
										    		if(isLiked($uid, $row['status_id'])) // if already liked
										    		{
										    	?>		
										    			<form method="post">
										    				<input type="hidden" name="user" value="<?php echo $uid ?>">
										    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
												    		<input type="submit" name="liked_btn" value="Liked">
												    	</form> 
												<?php
										    		}
										    		else // if not yet liked
										    		{
										    	?>	
										    			<form method="post">
										    				<input type="hidden" name="user" value="<?php echo $uid ?>">
										    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
												    		<input type="submit" name="like_btn" value="Like">
												    	</form> 
												<?php		
										    		}
										    		
										    	?>
										    </td> 
										    <td><?php echo $row['likes']." "."Likes"?></td>
										    <td></td>
										    <td><a href="comments.php?statusid=<?php echo $row['status_id'] ?>"><button>Comment</button></a>&nbsp;<?php echo $row['comments']." "."Comments" ?></td>
										    <td></td>
										    <td></td>
									  	</tr>
									  	<tr>
										  	<td>
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
										  	<td colspan="5">
										  		<form method="post">
										  			<textarea rows="2" cols="50" name="txt_comnt" ></textarea>
										  			<input type="hidden" name="sts_id" value="<?php echo $row['status_id'] ?>">
										  			<input type="hidden" name="com_by" value="<?php echo $uid ?>">
										  			
										  		
										  	</td>
										  	<td><input type="submit" name="comnt_btn" value="Post"></td>
									  			</form>
										 </tr>
									</table>
								</div>
								<div class="space"></div>
			<?php				
							}
						}
					}
					echo "<h1>No more posts</h1>";
				}
				else 
				{
					echo "<h1>No Posts Yet!</h1>";
				}
				?>
		</div>
		</div>
	</div>
</body>
</html>

<script type="text/javascript">

document.getElementById("defaultOpen").click();
function openTab(evt, tabName) 
{
    var i, tabcontent, tablink;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) 
    {
        tabcontent[i].style.display = "none";
    }
    tablink = document.getElementsByClassName("tablink");
    for (i = 0; i < tablink.length; i++) 
    {
        tablink[i].className = tablink[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
// Get the element with id="defaultOpen" and click on it


</script>







