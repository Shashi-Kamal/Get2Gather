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
  	$logd_ulname = $userRow['l_name'];
  	$logd_uemail = $userRow['email'];
  	$logd_ugender = $userRow['sex'];

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
		header("location: profile.php?uid=$logd_uid");
	}
}
?>

<?php
	require_once 'connection/dbconfig.php';

	if(isset($_POST['cover_btn']))
	{
		if(isset($_FILES['coverimg']['name']))
		{
			$target_dir = "images/";
			$cov_img = "cover".$logd_uid.".jpg";
			$target_path = $target_dir.$cov_img;
			move_uploaded_file($_FILES["coverimg"]["tmp_name"], $target_path);

			$covimg = "images/".$cov_img;

			try
			{
				$stmt = $db_con->prepare("UPDATE users SET cover_pic=:covimg WHERE user_id=:uid");
				$stmt->bindParam(":uid", $logd_uid);
				$stmt->bindParam(":covimg", $covimg);

				if($stmt->execute())
				{
					header("location: profile.php?uid=$logd_uid");
				}
				else
				{
					echo "Query Problem";
				}

				/*if($stmt->execute())
				{
					//header("location: profile.php?uid=$logd_uid");
					if($logd_ugender = "Female")
					{
						$his = "her";
					}
					else
					{
						$his = "his";
					}
					$status = $logd_ufname." has updated ".$his." cover photo";
					$view = "Friends";

					$stmt = $db_con->prepare("INSERT INTO status(user_id, status_content, status_photo, view_permit)
					VALUES(:uid, :stc, :stp, :v)");
					$stmt->bindParam(":uid", $logd_uid);
				  	$stmt->bindParam(":stc", $status);
					$stmt->bindParam(":stp", $covimg);
					$stmt->bindParam(":v", $view);

					if($stmt->execute())
					{
						header("location: profile.php?uid=$logd_uid");
					}
					else
					{
						echo "Query Problem";
					}
				}
				else
				{
					echo "Query Problem";
				}*/
			}
			catch(PDOException $e)
			{
				echo $e->getMessage();
			}
		}
	}
?>


<!--============================================= This code enables user to change his profile picture ==================================-->
<?php
	require_once 'connection/dbconfig.php';

	if(isset($_POST['profile_btn']))
	{
		if(isset($_FILES['profileimg']['name']))
		{
			$target_dir = "images/";
			$pro_img = "profile".$logd_uid.".jpg";
			$target_path = $target_dir.$pro_img;
			move_uploaded_file($_FILES["profileimg"]["tmp_name"], $target_path);

			$proimg = "images/".$pro_img;

			try
			{
				$stmt = $db_con->prepare("UPDATE users SET profile_pic=:proimg WHERE user_id=:uid");
				$stmt->bindParam(":uid", $logd_uid);
				$stmt->bindParam(":proimg", $proimg);

				if($stmt->execute())
				{
					header("location: profile.php?uid=$logd_uid");
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
					//header("location: home.php");
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
				$stmt = $db_con->prepare("UPDATE status SET likes=:count WHERE status_id=:s");
				$stmt->bindParam(":count", $count);
				$stmt->bindParam(":s", $statusid);

				if($stmt->execute())
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

require_once 'connection/dbconfig.php';

	if(isset($_POST['comnt_btn']))
	{

		$com = $_POST['txt_comnt'];
		$comto = $_POST['sts_id'];
		$comby = $_POST['com_by'];

		try
		{
			$stmt = $db_con->prepare("INSERT INTO comments(com, com_by, com_to)VALUES(:c, :cb, :ct)");
			$stmt->bindParam(":c", $com);
		  	$stmt->bindParam(":ct", $comto);
			$stmt->bindParam(":cb", $comby);


			if($stmt->execute())
			{
				$stmt = $db_con->prepare("SELECT * FROM comments JOIN users ON comments.com_by = users.user_id where comments.com_to = $comto");

				$stmt->execute();
				if($count = $stmt->rowCount()) 
				{
					$stmt = $db_con->prepare("UPDATE status SET comments=:count WHERE status_id=:comto");
					$stmt->bindParam(":count", $count);
					$stmt->bindParam(":comto", $comto);

					if($stmt->execute())
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

<!--============================ This codes checks if the logged in user has some unreplied messages ====================-->
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
<!--=================================== This code enables user to send message to any other users from his profile page ==============================-->
<?php  //Problem in error message displaying
	require_once 'connection/dbconfig.php';

	if(isset($_POST['msg_btn']))
	{
		$error[] = " ";
		$usr_from = $_POST['logd_uid'];
		$usr_to = $_POST['srchd_uid'];
		$msg = strip_tags($_POST['msg_body']);
		$date = date("Y-m-d");
		$opened = "no";

		if($msg=="Write something here...")
		{
			$error[] = "<font color='red'>Message is Empty !</font>";
			//exit();
		}
		else
		{
			try
			{
				$stmt = $db_con->prepare("INSERT INTO messages(user_from, user_to, msg, date_received, is_opened)VALUES(:uidf, :uidt, :m, :d, :sts)");

				$stmt->bindParam(":uidf", $usr_from);
			  	$stmt->bindParam(":uidt", $usr_to);
				$stmt->bindParam(":m", $msg);
				$stmt->bindParam(":d", $date);
				$stmt->bindParam(":sts", $opened);

				if($stmt->execute())
				{
					header("location: profile.php?uid=$usr_to");
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

<!--================================================ This code checks if the two users are friend or not ======================================-->
<?php
require_once("session.php");
require_once("class.user.php");

function isFriend($user_id, $friend_id)
{
	$auth_user = new User();
	try
	{
		$stmt = $auth_user->runQuery("SELECT * FROM users_friends WHERE user_id=:uid and friend_id=:fid and is_accepted=:v");
		$stmt->execute(array(':uid'=>$user_id, ':fid'=>$friend_id, ':v'=>"true"));
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

<!--============== Checks the url if a user-id is passed from the search.php page and fetch the respective user info from db =================-->
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

<!--=========================================== This code Sends Friend Request to other users ================================================-->
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
			header("location: profile.php?uid=$srchd_uid");
		}
		else
		{
			echo "Query Problem";
		}
	}
?>

<!--======================================================= This code Cancels any Sent Friend Request =======================================-->
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
			header("location: profile.php?uid=$srchd_uid");
		}
		else
		{
			echo "Query Problem";
		}
	}
?>

<!--========================================================= This code Removes Friend =====================================================-->
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
			$stmt = $db_con->prepare("DELETE FROM users_friends WHERE user_id = :sid and friend_id = :uid and is_accepted = :v");

			if($stmt->execute(array(":uid" => $logd_uid, ":sid" => $srchd_uid, ":v" => $value)))
			{
				header("location: profile.php?uid=$srchd_uid");
			}
		}
		else
		{
			echo "Query Problem";
		}
	}
?>


<!--=============================== This code inserts the status data content into database once the user posted his status ===================-->
<?php
	require_once 'connection/dbconfig.php';

	/*if(isset($_POST['post_with_photo_btn']))
	{
		//if(!isset($_FILES['img']['name']))
		if(!isset($_POST['img']))
		{

			$status = $_POST['txt_status'];
			$photo = "";
			$view = $_POST['view'];

			try
			{
				$stmt = $db_con->prepare("INSERT INTO status(user_id, status_content, status_photo, view_permit)
				VALUES(:uid, :stc, :stp, :v)");
				$stmt->bindParam(":uid", $logd_uid);
			  	$stmt->bindParam(":stc", $status);
				$stmt->bindParam(":stp", $photo);
				$stmt->bindParam(":v", $view);

				if($stmt->execute())
				{
					header("location: profile.php?uid=$logd_uid");
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
				$stmt->bindParam(":uid", $logd_uid);
			  	$stmt->bindParam(":stc", $status);
				$stmt->bindParam(":stp", $photo);
				$stmt->bindParam(":v", $view);

				if($stmt->execute())
				{
					header("location: profile.php?uid=$logd_uid");
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
	}*/

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
				$stmt->bindParam(":uid", $logd_uid);
			  	$stmt->bindParam(":stc", $status);
				$stmt->bindParam(":stp", $photo);
				$stmt->bindParam(":v", $view);

				if($stmt->execute())
				{
					if($logd_uid != $srchd_uid)
					{
						header("location: profile.php?uid=$srchd_uid");
					}
					else
					{
						header("location: profile.php?uid=$logd_uid");
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
	}
?>

<?php
	require_once 'connection/dbconfig.php';

	if(isset($_POST['post_btn']))
	{

		$status = $_POST['txt_status'];
		$view = $_POST['view'];

		try
		{
			$stmt = $db_con->prepare("INSERT INTO status(user_id, status_content, view_permit)
			VALUES(:uid, :stc, :v)");
			$stmt->bindParam(":uid", $logd_uid);
		  	$stmt->bindParam(":stc", $status);
			//$stmt->bindParam(":stp", $photo);
			$stmt->bindParam(":v", $view);

			if($stmt->execute())
			{
				if($logd_uid != $srchd_uid)
				{
					header("location: profile.php?uid=$srchd_uid");
				}
				else
				{
					header("location: profile.php?uid=$logd_uid");
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

<!--========================================================= Here goes the structure of the web page =======================================-->
<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/profile.style.css">
	<script src="myScript.js"></script> 
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
			<div id="frndball">
			<?php
				frndReq_exist($logd_uid)
			?>
			</div>

			<li><a href="messages.php?val=yes">Inbox</a></li>
			<div id="msgball">
			<?php
				msg_exist($logd_uid)
			?>
			
			</div>
			<li><a href="logout.php?logout=true">Log Out</a></li>
		</ul> 
	</div>

	<!-- The whole content in grid layout -->
	<div class="container">
		<!-- This is the cover page area done in grid layout of 2 rows-->
	  	<div class="box cover_pic">
	  		<!-- Div for cover pic -->
	  		<div class="cov_area">

	  			<!--=============================== Div to display cover picture ===========================-->
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

	  			<!--================================ Cover picture change button ===========================-->
	  			<?php
	  				if($srchd_uid != $logd_uid)
	  				{
	  					echo "";
	  				}
	  				else
	  				{
	  			?>
	  					<button id="cov_pic_change_btn">Change Cover Picture</button>
	  			<?php
	  				}
	  			?>

	  			<!--=================== A popup modal box for uploading a new cover picture ================-->
	  			<div id="covpic" class="covWindow">
				  <!-- profile pic change window -->
				  <div class="covWindow-content">
				    <span class="closeUploadC">&times;</span>
				    <h2>Upload a Cover Picture</h2>
				    <form method="post" enctype="multipart/form-data">
				    	<input type="file" name="coverimg">
				    	<input type="submit" name="cover_btn" value="Upload">
				    </form>
				  </div>
				</div>

				<!--============== JS to enable a popup modal box to upload a new cover picture =============-->
				<script type="text/javascript">
					var div = document.getElementById('covpic');

					// Get the button that opens the modal
					var change_cov = document.getElementById("cov_pic_change_btn");

					// Get the <span> element that closes the modal
					var close = document.getElementsByClassName("closeUploadC")[0];

					// When the user clicks the button, open the modal
					change_cov.onclick = function()
					{
					    div.style.display = "block";
					}

					// When the user clicks on <span> (x), close the modal
					close.onclick = function()
					{
					    div.style.display = "none";
					}

					// When the user clicks anywhere outside of the modal, close it
					window.onclick = function(event)
					{
					    if (event.target == div)
					    {
					        div.style.display = "none";
					    }
					}
				</script>

		  		<!--================================== Div to display profile pic =============================-->
		  		<div class="pro_pic">
		  			
		  		</div>
	  		</div>
	  		<!-- Div for the navbar below the cover page -->
	  		<?php
	  		if($srchd_uid != $logd_uid)  // If they are different user; diff nav bar display
	  		{
	  			echo
	  			'<div class="nav_area">
		  			<ul class="navtwo">
						<li><a class="active" href="#">Timeline</a></li>
						<li><a href="about.php?uid='.$srchd_uid.'">About</a></li>
						<li><a href="friends.php?uid='.$srchd_uid.'">Friends</a></li>
						<li><a href="photos.php?uid='.$srchd_uid.'">Photos</a></li>
						<li><a href="#"id="msgLink">Send Message</a></li>
					</ul>
	  			</div>';
	  		}
	  		else // If the user is the logged-in user itself; then a diff nav bar display
	  		{
	  			echo
	  			'<div class="nav_area">
		  			<ul class="navtwo">
						<li><a class="active" href="#">Timeline</a></li>
						<li><a href="about.php?uid='.$logd_uid.'">About</a></li>
						<li><a href="friends.php?uid='.$logd_uid.'">Friends</a></li>
						<li><a href="photos.php?uid='.$logd_uid.'">Photos</a></li>
						<li><a href="edit-profile.php?uid='.$logd_uid.'">Edit Profile</a></li>
					</ul>
	  			</div>';
	  		}
	  		?>

	  	</div>

	  	<!--===================================== The Message Box =================================-->
		<div id="myMsg" class="message">
		  <!-- Message content -->
		  <div class="message-content">
		  	<p>Write a message to <?php echo $srchd_fname ?></p>
		    <span class="close">&times;</span>
		    <form action="#" method="post">
		    	<input type="hidden" name="srchd_uid" value="<?php echo $srchd_uid ?>">
				<input type="hidden" name="logd_uid" value="<?php echo $logd_uid ?>">
		    	<textarea name="msg_body">Write something here...</textarea>
		    	<input type="submit" name="msg_btn" value="Send Message">
		    	<?php
					if(isset($error))
					{
					 	foreach($error as $error)
					 	{
				?>
		                     <div class="msg">
		                        <?php echo $error ?>
		                     </div>
		        <?php
						}
					}
				?>
		    </form>
		  </div>
		</div>

	  	<!--================================= This is the about area ==============================-->
		<div class="box about">
			<div class= "shape">
			</div>
				<div class="circle">
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
		  			<!--=============================== Profile picture change button =========================-->
		  			<?php
		  				if($srchd_uid != $logd_uid)
		  				{
		  					echo "";
		  				}
		  				else
		  				{
		  			?>
		  					<button id="pro_pic_change_btn">Change Profile Picture</button>
		  			<?php
		  				}
		  			?>
		  			<div id="propic" class="modalWindow">
					  <!-- profile pic change window -->
					  <div class="modalWindow-content">
					    <span class="closeUploadP">&times;</span>
					    <h2>Upload a Profile Picture</h2>
					    <form method="post" enctype="multipart/form-data">
					    	<input type="file" name="profileimg">
					    	<input type="submit" name="profile_btn" value="Upload">
					    </form>
					  </div>
					</div>
					<!--============ JS to enable a popup modal box to upload a new cover picture ============-->
					<script type="text/javascript">
						var divid = document.getElementById('propic');

						// Get the button that opens the modal
						var change_btn = document.getElementById("pro_pic_change_btn");

						// Get the <span> element that closes the modal
						var spn = document.getElementsByClassName("closeUploadP")[0];

						// When the user clicks the button, open the modal
						change_btn.onclick = function()
						{
						    divid.style.display = "block";
						}

						// When the user clicks on <span> (x), close the modal
						spn.onclick = function()
						{
						    divid.style.display = "none";
						}

						// When the user clicks anywhere outside of the modal, close it
						window.onclick = function(event)
						{
						    if (event.target == divid)
						    {
						        divid.style.display = "none";
						    }
						}
					</script>
				</div>
			
			
				  		<!-- Div for displaying username -->
	  		<div class="usrname">
	  			<h2><?php echo $srchd_fname ." ". $srchd_lname ?></h2>

	  			<!--======================= This code decides which button is to be displayed =============-->
	  			<div class="friendButton">
		  		<?php
		  			if($logd_uid != $srchd_uid)
		  			{

		  				//echo $srchd_uid;
		  				$stmt=$db_con->prepare("SELECT * FROM users_friends WHERE user_id = :uid");
						$stmt->execute(array(":uid" => $logd_uid));
						$row=$stmt->fetch(PDO::FETCH_ASSOC);

						if($stmt->rowCount()) //friend request available
						{
							$stmt=$db_con->prepare("SELECT users_friends.friend_id FROM users_friends where users_friends.friend_id = $srchd_uid AND users_friends.is_accepted = 'false' AND users_friends.friend_id IN (SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $logd_uid)");

							$stmt1=$db_con->prepare("SELECT users_friends.friend_id FROM users_friends where users_friends.friend_id = $srchd_uid AND users_friends.is_accepted = 'true' AND users_friends.friend_id IN (SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $logd_uid)");

							$stmt->execute();
							$stmt1->execute();
							
							if($stmt->rowCount()==1) // if friend request yet to be accepted
							{
				?>					
									<p><?php echo "Friend Request Sent" ?></p>
									<form action="profile.php" method="post">
										<input type="hidden" name="srchd_uid" value="<?php echo $srchd_uid ?>">
										<input type="hidden" name="logd_uid" value="<?php echo $logd_uid ?>">
						  				<input type="submit" name="cancel_btn" value="Cancel Friend Request">
						  			</form>
				<?php
							}
							else if($stmt1->rowCount()==1)
							{
				?>
								<form action="profile.php" method="post">
									<input type="hidden" name="srchd_uid" value="<?php echo $srchd_uid ?>">
									<input type="hidden" name="logd_uid" value="<?php echo $logd_uid ?>">
					  				<input type="submit" name="unfriend_btn" value="Remove Friend">
					  			</form>
				<?php
							}
							else
							{
				?>				
								<form action="profile.php" method="post">
								<input type="hidden" name="srchd_uid" value="<?php echo $srchd_uid ?>">
								<input type="hidden" name="logd_uid" value="<?php echo $logd_uid ?>">
				  				<input type="submit" name="friend_btn" value="Add Friend">
				  				</form>
				<?php
							}
						}
						else
						{
				?>
							<form action="profile.php" method="post">
								<input type="hidden" name="srchd_uid" value="<?php echo $srchd_uid ?>">
								<input type="hidden" name="logd_uid" value="<?php echo $logd_uid ?>">
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
		  		</div>
	  			<!--================= A popup modal box for uploading a new profile picture ===============-->
		  			
	  		<div class="gallery"></div>
	  		<div class="friends"></div>
			</div>

			<div class="space"></div>

			<div class="first">
				<div class="intro">
					<?php
						require_once 'connection/dbconfig.php';
						
						if($srchd_uid != $logd_uid)
						{
							$stmt = $db_con->prepare("SELECT * FROM users WHERE user_id = $srchd_uid");
							$stmt->execute(); 

							if($stmt->rowCount())
							{
								while($row=$stmt->fetch(PDO::FETCH_ASSOC))
								{
					?>
									<p style="text-align: center; font-weight: bold; color: #00227b;">About me</p>
									<p style="text-align: center; color: #00227b"><?php echo $row['intro'] ?></p>
									<hr>
									<table id="introtable">
										<tr>
											<td><strong>Works at :</strong></td>
											<td><?php echo $row['job'] ?></td>
										</tr>
										<tr>
											<td><strong>Education :</strong></td>
											<td><?php echo $row['education'] ?></td>
										</tr>
										<tr>
											<td><strong>From :</strong></td>
											<td><?php echo $row['city'].",".$row['state'] ?></td>
										</tr>
										<tr>
											<td><strong>Email :</strong></td>
											<td><?php echo $row['email'] ?></td>
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
									<p style="text-align: center; font-weight: bold; color: #00227b;">About me</p>
									<p style="text-align: center; color: #00227b"><?php echo $row['intro'] ?></p>
									<hr>
									<table id="introtable">
										<tr>
											<td><strong>Works at :</strong></td>
											<td><?php echo $row['job'] ?></td>
										</tr>
										<tr>
											<td><strong>Education :</strong></td>
											<td><?php echo $row['education'] ?></td>
										</tr>
										<tr>
											<td><strong>From :</strong></td>
											<td><?php echo $row['city'].",".$row['state'] ?></td>
										</tr>
										<tr>
											<td><strong>Email :</strong></td>
											<td><?php echo $row['email'] ?></td>
										</tr>
									</table>
					<?php
								}
							}
						}
					?>
	  			</div>
			</div>

			<div class="space"></div>
			<div class="second">
				<p><font color="white">Photos</font></p>
			<?php
				require_once 'connection/dbconfig.php';
				
				if($srchd_uid != $logd_uid)
				{
					$stmt = $db_con->prepare("SELECT status_photo FROM users INNER JOIN status on users.user_id = status.user_id WHERE users.user_id = $srchd_uid AND status.status_photo != '' ORDER BY status.status_date DESC LIMIT 9");
						$stmt->execute(); 

					if($stmt->rowCount())
					{
						while($row=$stmt->fetch(PDO::FETCH_ASSOC))
						{
		?>
							<div class="photos">
							  <a target="_blank" href="<?php echo $row['status_photo'] ?>">
							    <img height="110px" width="110px" src="<?php echo $row['status_photo'] ?>">
							  </a>
							</div>
		<?php 
						}
					}
				}
				else
				{
					$stmt = $db_con->prepare("SELECT status_photo FROM users INNER JOIN status on users.user_id = status.user_id WHERE users.user_id = $logd_uid AND status.status_photo != '' ORDER BY status.status_date DESC LIMIT 9");
						$stmt->execute(); 

					if($stmt->rowCount())
					{
						while($row=$stmt->fetch(PDO::FETCH_ASSOC))
						{
		?>
							<div class="photos">
							  <a target="_blank" href="<?php echo $row['status_photo'] ?>">
							    <img height="110px" width="110px" src="<?php echo $row['status_photo'] ?>">
							  </a>
							</div>
		<?php 
						}
					}
				}
			?>
				
			</div>

			<div class="space"></div>
			<div class="third">
				<?php
					require_once 'connection/dbconfig.php';
					
					if($srchd_uid != $logd_uid)
					{
						//show mutual friends
				?>
						<p><font color="white">Friends : </font></p>
				<?php
						$stmt = $db_con->prepare("SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $logd_uid and users_friends.is_accepted = 'true' AND users_friends.friend_id IN (SELECT users_friends.friend_id FROM users_friends where users_friends.user_id = $srchd_uid and users_friends.is_accepted = 'true') LIMIT 9");

						$stmt->execute();

						//if there's any friend
						if($count = $stmt->rowCount())
						{
				?>			
							<?php echo $count." "."mutual friends" ?>
				<?php
							while($frow=$stmt->fetch(PDO::FETCH_ASSOC))
							{
								$friendid = $frow['friend_id'];
								
					            $stmt1 = $db_con->prepare("SELECT user_id, f_name, l_name, sex, profile_pic FROM users where user_id = $friendid");
					            $stmt1->execute();
								if($stmt1->rowCount())
								{
									while($row=$stmt1->fetch(PDO::FETCH_ASSOC))
									{
				?>
										<div class="photos">
											<a href="profile.php?uid=<?php echo $row['user_id'] ?>">
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
    													<img src="<?php echo $row["profile_pic"] ?>" width="110" height="110">
    											<?php
									  				}
									  			?>
									  			<div class="desc"><?php echo $row['f_name']." ".$row['l_name'] ?></div>
  											</a>
										</div>
				<?php
									}
								}
							}
						}
					}
					else
					{
						//show all friends
				?>
						<p><font color="white">Friends</font></p>
				<?php
						//collect all the friend ids
						$stmt = $db_con->prepare("SELECT friend_id FROM users_friends where user_id = $logd_uid and is_accepted = 'true' LIMIT 9");
						
						$stmt->execute();

						//if there's any friend
						if($count = $stmt->rowCount())
						{
							while($frow=$stmt->fetch(PDO::FETCH_ASSOC))
							{
								$friendid = $frow['friend_id'];
								
					            $stmt1 = $db_con->prepare("SELECT user_id, f_name, l_name, sex, profile_pic FROM users where user_id = $friendid");
					            $stmt1->execute();
								if($stmt1->rowCount())
								{
									while($row=$stmt1->fetch(PDO::FETCH_ASSOC))
									{
				?>
										<div class="photos">
											<a href="profile.php?uid=<?php echo $row['user_id'] ?>">
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
    													<img src="<?php echo $row["profile_pic"] ?>" width="110" height="110">
    											<?php
									  				}
									  			?>
									  			<div class="desc"><?php echo $row['f_name']." ".$row['l_name'] ?></div>
  											</a>
  											
										</div>
				<?php
									}
								}
							}
						}
					}
				?>
			</div>
			<div class="space"></div>
	  	</div>

	  		
		<!--============================================ This is the user input area ======================================-->
		<div class="box input_area">
			<div class="tab">
				<button class="tablink" onclick="openTab(event, 'Text')" id="defaultOpen">Write Something..</button>
				<button class="tablink" onclick="openTab(event, 'Photo')">Add Photo</button>
			</div>

			<div id="Text" class="tabcontent">
				<?php
					if($logd_uid != $srchd_uid)
					{
						if(isFriend($logd_uid, $srchd_uid)) // If the logged-in user and the searched user is friend
						{
				?>
							<form method="post" name="txt_frm_srchd_uid">
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
								  	<!--<input type="radio" name="view" value="Private">Private-->
								  </td>
								  <td>
								  	<input type="radio" name="view" value="Friends" checked>Friends
								  </td>
								  <td>
								  	<!--<input type="radio" name="view" value="Public" checked>Public-->
								  </td>
								</tr>
								<tr>
								  <td colspan=6>
								  	<textarea rows="5" cols="10" name="txt_status" wrap="hard" onkeypress="display_txt_post_btnS()"></textarea>
								  </td>
								</tr>
								<tr>
								  <td></td>
								  <td colspan=5>
								  </td>
								  <td>
								  	<input type="submit" name="post_btn" id="txt_post_srchd_btn" value="Post" style="display: none;">
								  </td>
								</tr>
								</table>
							</form>
						<?php
						}
						else // when logd_uid is not in friendship with the srchd_uid; do not show the input box
						{
							echo "";
						}
					}
					else // when the logged-in user and the searched user is the same user; show the input box, with full controls
					{
				?>
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
								  	<input type="submit" name="post_btn" id="txt_post_btn" value="Post" style="display: none">
								  </td>
								</tr>
							</table>
						</form>
				<?php
					}
				?>
			</div>
			<div id="Photo" class="tabcontent">
				<?php
					if($logd_uid != $srchd_uid)
					{
						if(isFriend($logd_uid, $srchd_uid)) // If the logged-in user and the searched user is friend
						{
				?>
							<form method="post" enctype="multipart/form-data" name="photo_frm_srchd_uid">
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
								  	<input type="radio" name="view" value="Friends">Friends
								  </td>
								  <td>
								  	<!--<input type="radio" name="view" value="Public" checked>Public-->
								  </td>
								</tr>
								<tr>
								  <td colspan=6>
								  	<textarea rows="5" cols="10" name="txt_status" wrap="hard" onkeypress="display_photo_post_btnS()"></textarea>
								  </td>
								</tr>
								<tr>
								  <td></td>
								  <td colspan=5>
								  	<input type="file" name="img">
								  </td>
								  <td>
								  	<input type="submit" name="post_with_photo_btn" id="photo_post_srchd_btn" value="Post" style="display: none">
								  </td>
								</table>
							</form>
						<?php
						}
						else // when logd_uid is not in friendship with the srchd_uid; do not show the input box
						{
							echo "";
						}
					}
					else // when the logged-in user and the searched user is the same user; show the input box, with full controls
					{
				?>
						<form method="post" name="photo_frm_logd_uid" enctype="multipart/form-data">
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
								  	<input type="submit" name="post_with_photo_btn" id="photo_post_btn" value="Post" style="display: none">
								  </td>
							</table>
						</form>
				<?php
					}
				?>
			</div>
		</div>

		<!--=========================================== This is user's posts area =======================================-->
		<div class="box post_area">

				<?php
					require_once 'connection/dbconfig.php';
					if($logd_uid != $srchd_uid)
					{
						if(isFriend($logd_uid, $srchd_uid))
						{
							$stmt = $db_con->prepare("SELECT * FROM users INNER JOIN status on users.user_id = status.user_id WHERE status.user_id = $srchd_uid or status.user_id = $logd_uid and view_permit != 'private'  ORDER BY status_date DESC");
							$stmt->execute();
							if($stmt->rowCount())
							{
								while($row=$stmt->fetch(PDO::FETCH_ASSOC))
								{
				?>
									<div class="post_box">
										<table style="width:100%">
										  <tr>
										    <td rowspan="2" width="50px">
										    	<!-- place for profile pic icon -->
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
										    	<strong><?php echo $row["f_name"] ." ". $row["l_name"] ?></strong>
										    </td>

										    <td width="50px"></td>

										    <td width="50px"></td>
										  </tr>
										  <tr style="border-bottom: 1px solid #98503c">
										  	<?php 
										  		$timestamp = strtotime($row['status_date']);	// Grabbing the 'timestamp' colm from mysql
										  		$date = date('j M', $timestamp);  				// j: day of mnth 1-31; M: short for mnth names
												$time = date('g:i a', $timestamp);				// G: 12hr withou leading 0's; i: mins with leading 0's; a: am or pm
										  	?>
										  	
										    <td colspan="5" style="font-size: 12px"><?php echo $date." at ".$time ?></td>
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
										    <?php
									    		if ($row['status_photo'] == "")
								  					{
								  						echo "";
								  			?>
								  			<?php
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
										    		if(isLiked($logd_uid, $row['status_id'])) // if already liked
										    		{
										    	?>
										    			<form method="post">
										    				<input type="hidden" name="user" value="<?php echo $logd_uid ?>">
										    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
												    		<input type="submit" name="liked_btn" value="Liked">
												    	</form>
												<?php
										    		}
										    		else // if not yet liked
										    		{
										    	?>
										    			<form method="post">
										    				<input type="hidden" name="user" value="<?php echo $logd_uid ?>">
										    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
												    		<input type="submit" name="like_btn" value="Like">
												    	</form>
												<?php
										    		}
										    	?>
										    </td>
										    <td><?php echo $row['likes']." "."Likes"?></td>
										    <!--<td></td>-->
										    <td><a href="comments.php?statusid=<?php echo $row['status_id'] ?>"><button>Comment</button></a>&nbsp;<?php echo $row['comments']." "."Comments" ?></td>

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
										  		<form method="post" name="">
										  			<textarea rows="2" cols="50" name="txt_comnt" ></textarea>
										  			<input type="hidden" name="sts_id" value="<?php echo $row['status_id'] ?>">
										  			<input type="hidden" name="com_by" value="<?php echo $logd_uid ?>">
										  	</td>
										  	<td><input type="submit" name="comnt_btn" value="Post"></td>
										  		</form>
										  </tr>
										</table>
									</div>
									<div class="space"></div>
				<?php			}
								echo "<h1>No more posts</h1>";
							}
							else
							{
								echo "<h1>No Posts Yet!</h1>";
							}
						}
						else
						{
							$stmt = $db_con->prepare("SELECT * FROM users INNER JOIN status on users.user_id = status.user_id WHERE status.user_id = $srchd_uid and view_permit = 'Public'  ORDER BY status_date DESC");
							$stmt->execute();
							if($stmt->rowCount())
							{
								while($row=$stmt->fetch(PDO::FETCH_ASSOC))
								{
				?>
									<div class="post_box">
										<table style="width:100%">
										  <tr>
										    <td rowspan="2" width="50px">
										    	<!-- place for profile pic icon -->
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
										    	<strong><?php echo $row["f_name"] ." ". $row["l_name"] ?></strong>
										    </td>

										    <td width="50px"></td>

										    <td width="50px"></td>
										  </tr>
										  <tr style="border-bottom: 1px solid #98503c;">
										  	<?php 
										  		$timestamp = strtotime($row['status_date']);	// Grabbing the 'timestamp' colm from mysql
										  		$date = date('j M', $timestamp);  				// j: day of mnth 1-31; M: short for mnth names
												$time = date('g:i a', $timestamp);				// G: 12hr withou leading 0's; i: mins with leading 0's; a: am or pm
										  	?>
										    <td colspan="5" style="font-size: 12px"><?php echo $date." at ".$time ?></td>
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
								  			?>
								  			<?php
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
								echo "<h1>No more posts</h1>";
							}
							else
							{
								echo "<h1>No Posts Yet!</h1>";
							}
						}
					}
					else // If the user is the logged-in user itself
					{
						$stmt = $db_con->prepare("SELECT * FROM users INNER JOIN status on users.user_id = status.user_id WHERE status.user_id = $logd_uid ORDER BY status_date DESC");
						$stmt->execute();
						if($stmt->rowCount())
						{
							while($row=$stmt->fetch(PDO::FETCH_ASSOC))
							{
				?>
								<div class="post_box">
									<table style="width:100%">
									  <tr>
									    <td rowspan="2" width="50px">
									    	<!-- place for profile pic icon -->
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

									    <td width="50px"><a href="edit-post.php?status_id=<?php echo $row['status_id'] ?>"><button>&#128393;</button></a></td>

									    <td width="50px">
									    	<form method="post">
									    		<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
									    		<input type="submit" name="del_btn" value="&times;">
									    	</form>
									    </td>
									  </tr>
									  <tr style="border-bottom: 1px solid #98503c">
									  	<?php 
									  		$timestamp = strtotime($row['status_date']);	// Grabbing the 'timestamp' colm from mysql
									  		$date = date('j M', $timestamp);  				// j: day of mnth 1-31; M: short for mnth names
											$time = date('g:i a', $timestamp);				// G: 12hr withou leading 0's; i: mins with leading 0's; a: am or pm
										?>
									    <td colspan="5" style="font-size: 12px"><?php echo $date." at ".$time ?></td>
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
							  			?>
							  			<?php
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
									    		if(isLiked($logd_uid, $row['status_id'])) // if already liked
									    		{
									    	?>
									    			<form method="post">
									    				<input type="hidden" name="user" value="<?php echo $logd_uid ?>">
									    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
											    		<input type="submit" name="liked_btn" value="Liked">
											    	</form>
											<?php
									    		}
									    		else // if not yet liked
									    		{
									    	?>
									    			<form method="post">
									    				<input type="hidden" name="user" value="<?php echo $logd_uid ?>">
									    				<input type="hidden" name="statusid" value="<?php echo $row['status_id'] ?>">
											    		<input type="submit" name="like_btn" value="Like">
											    	</form>
											<?php
									    		}
									    	?>
									    </td>
									    <td><?php echo $row['likes']." "."Likes"?></td>
									    <!--<td></td>-->
									    <td><a href="comments.php?statusid=<?php echo $row['status_id'] ?>"><button>Comment</button></a>&nbsp;<?php echo $row['comments']." "."Comments" ?></td>

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
									  		<form method="post" name="cmnt_frm">
									  			<textarea rows="2" cols="50" name="txt_comnt" onkeypress="display_cmnt_btn()"></textarea>
									  			<input type="hidden" name="sts_id" value="<?php echo $row['status_id'] ?>">
									  			<input type="hidden" name="com_by" value="<?php echo $logd_uid ?>">
									  	</td>
									  	<td><input type="submit" name="comnt_btn" class="cmnt_btn" value="Post" ></td>
									  		</form>
									  </tr>
									</table>
								</div>
								<div class="space"></div>

				<?php
							}
							echo "<h1>No more posts</h1>";
						}
						else
						{
							echo "<h1>No Posts Yet!</h1>";
						}

					}
				?>
		</div>

	<!-- The content ends here -->
	</div>

	<!--======================= Here goes the javascript area [Not needed] =========================-->
	<script type="text/javascript">

		document.getElementById("defaultOpen").click();

		// Get the modal
		var modal = document.getElementById('myMsg');

		// Get the button that opens the modal
		var btn = document.getElementById("msgLink");

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("close")[0];

		// When the user clicks the button, open the modal
		btn.onclick = function() {
		    modal.style.display = "block";
		}

		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
		    modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
		    if (event.target == modal) {
		        modal.style.display = "none";
		    }
		}

		// Profile pic change modal window
		// Get the modal
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
</body>
</html>
