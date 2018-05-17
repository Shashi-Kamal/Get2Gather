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

<?php

require_once 'connection/dbconfig.php';


	if(isset($_POST['edit_btn1']))
	{
		if(isset($_POST['txt_fname']))
		{
			$fname = $_POST['txt_fname'];

			$stmt = $db_con->prepare("UPDATE users SET f_name=:fname WHERE user_id=:id");

 	  		$stmt->bindParam(":fname", $fname);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}

		if(isset($_POST['txt_lname']))
		{
			$lname = $_POST['txt_lname'];

			$stmt = $db_con->prepare("UPDATE users SET l_name=:lname WHERE user_id=:id");

 	  		$stmt->bindParam(":lname", $lname);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}
		if(isset($_POST['txt_intro']))
		{
			$uintro = $_POST['txt_intro'];

			$stmt = $db_con->prepare("UPDATE users SET intro=:ui WHERE user_id=:id");

 	  		$stmt->bindParam(":ui", $uintro);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}
		if(isset($_POST['bday']))
		{
			$ubday = $_POST['bday'];

			$stmt = $db_con->prepare("UPDATE users SET dob=:ub WHERE user_id=:id");

 	  		$stmt->bindParam(":ub", $ubday);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}
		if(isset($_POST['urel']))
		{
			$urel = $_POST['urel'];

			$stmt = $db_con->prepare("UPDATE users SET relation=:urel WHERE user_id=:id");

 	  		$stmt->bindParam(":urel", $urel);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
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


	if(isset($_POST['edit_btn2']))
	{
		if(isset($_POST['txt_job']))
		{
			$ujob = $_POST['txt_job'];

			$stmt = $db_con->prepare("UPDATE users SET job=:uj WHERE user_id=:id");

 	  		$stmt->bindParam(":uj", $ujob);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}

		if(isset($_POST['txt_edu']))
		{
			$uedu = $_POST['txt_edu'];

			$stmt = $db_con->prepare("UPDATE users SET education=:ued WHERE user_id=:id");

 	  		$stmt->bindParam(":ued", $uedu);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
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


	if(isset($_POST['edit_btn3']))
	{
		if(isset($_POST['txt_state']))
		{
			$ustate = $_POST['txt_state'];

			$stmt = $db_con->prepare("UPDATE users SET state=:ust WHERE user_id=:id");

 	  		$stmt->bindParam(":ust", $ustate);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}

		if(isset($_POST['txt_city']))
		{
			$ucity = $_POST['txt_city'];

			$stmt = $db_con->prepare("UPDATE users SET city=:uc WHERE user_id=:id");

 	  		$stmt->bindParam(":uc", $ucity);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
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


	if(isset($_POST['edit_btn4']))
	{
		if(isset($_POST['txt_email']))
		{
			$uemail = $_POST['txt_email'];

			$stmt = $db_con->prepare("UPDATE users SET email=:um WHERE user_id=:id");

 	  		$stmt->bindParam(":um", $uemail);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}

		if(isset($_POST['txt_mob']))
		{
			$umob = $_POST['txt_mob'];
			echo $umob;

			$stmt = $db_con->prepare("UPDATE users SET mobile=:umob WHERE user_id=:id");

 	  		$stmt->bindParam(":umob", $umob);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				echo $umob;
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
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


	if(isset($_POST['edit_btn5']))
	{
		if(isset($_POST['txt_pass']))
		{
			$upass = $_POST['txt_pass'];

			$stmt = $db_con->prepare("UPDATE users SET pass=:up WHERE user_id=:id");

 	  		$stmt->bindParam(":up", $upass);
 	  		$stmt->bindParam(":id", $logd_uid);

 	  		if($stmt->execute())
			{
				//echo "<p> Product Successfully updated <p>";
				header("location: about.php?uid=$logd_uid");
			}
			else
			{
				echo "Query Problem";
			}
		}
	}
?>
   		
<!--============================== Here goes the structure of the web page ===========================-->
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
		  			<h3><?php echo $userRow['f_name'] ." ". $userRow['l_name'] ?></h3>
		  		</div>
	  		</div>
	  		<!-- Div for the navbar below the cover page -->
	  		<div class="nav_area">
	  			<ul class="navone">
					<li><a href="profile.php?uid=<?php echo $logd_uid ?>">Timeline</a></li>
					<li><a href="about.php?uid=<?php echo $logd_uid ?>">About</a></li>
					<li><a href="friends.php?uid=<?php echo $logd_uid ?>">Friends</a></li>
					<li><a href="Photos.php?uid=<?php echo $logd_uid ?>">Photos</a></li>
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
							<li><a class="active" href="#">Edit Profile</a></li>
					<?php
						}
					?>
				</ul>
	  		</div>
	  	</div>


	  	<!-- This is the edit area -->
		<div class="box edit">
			<div class="edit_area">
				<p>Basic Information :</p>
				<hr>
				<form method="post">
					<table width="363" height="190" border="1" cellpadding="6">
						<tr>
				        <td>First Name</td>
					        <td>
					        	<input type="text" name="txt_fname" value= "<?php echo $userRow['f_name'] ?>">
					        </td>
				        </tr>
				        <tr>
				        <td>Last Name</td>
					        <td>
					        	<input type="text" name="txt_lname" value="<?php echo $userRow['l_name'] ?>">
					        </td>
				        </tr>
					    <tr>
					        <td width="138">Introduction</td>
					        <td width="195">
					        	<textarea name="txt_intro"><?php echo $userRow['intro'] ?></textarea>
					        </td>
				        </tr>
					    
				        <tr>
					        <td>Birthday</td>
					        <td><input type="date" name="bday" value="<?php echo $userRow['dob'] ?>"></td>
				        </tr>

					    <tr>
					        <td>Relation</td>
					        <td>
					        	<input type="text" name="urel" value="<?php echo $userRow['relation'] ?>">
					        </td>
				        </tr>
					    <tr>
					        <td></td>
					        <td>
					        	<button>Cancel</button>
					        	<input type="submit" name="edit_btn1" value="Save">
					        </td>
				        </tr>
			      </table>
				</form>

				<hr>
				<p>Word and Education</p>
				<hr>
				<form method="post">
					<table>
						<tr>
							<td>Job</td>
							<td>
								<input type="text" name="txt_job" value="<?php echo $userRow['job'] ?>">
							</td>
						</tr>
						<tr>
							<td>Education</td>
							<td>
								<input type="text" name="txt_edu" value="<?php echo $userRow['education'] ?>">
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<button>Cancel</button>
								<input type="submit" name="edit_btn2" value="Save">
							</td>
						</tr>
					</table>
				</form>

				<hr>
				<p>Current Address</p>
				<hr>
				<form method="post">
					<table>
						<tr>
							<td>State</td>
							<td>
								<input type="text" name="txt_state" value="<?php echo $userRow['state'] ?>">
							</td>
						</tr>
						<tr>
							<td>City</td>
							<td>
								<input type="text" name="txt_city" value="<?php echo $userRow['city'] ?>">
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<button>Cancel</button>
								<input type="submit" name="edit_btn3" value="Save">
							</td>
						</tr>
					</table>
				</form>

				<hr>
				<p>Contact Information</p>
				<hr>
				<form method="post">
					<table>
						<tr>
					        <td>Email Address</td>
					        <td>
					        	<input type="text" name="txt_email" value="<?php echo $userRow['email'] ?>">
					        </td>
				        </tr>
				        <tr>
				        <td>Contact number</td>
					        <td>
					        	<input type="text" name="txt_mob" value="<?php echo $userRow['mobile'] ?>">
					        </td>
				        </tr>
						<tr>
							<td></td>
							<td>
								<button>Cancel</button>
								<input type="submit" name="edit_btn4" value="Save">
							</td>
						</tr>
					</table>
				</form>

				<hr>
				<p>Change Password</p>
				<hr>
				<form method="post">
				<table>
					<tr>
				        <td>Password</td>
				        <td>
				        	<input type="text" name="txt_pass" value="<?php echo $userRow['pass'] ?>">
				        </td>
			        </tr>
			        <tr>
						<td></td>
						<td>
							<button>Cancel</button>
							<input type="submit" name="edit_btn5" value="Save">
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
