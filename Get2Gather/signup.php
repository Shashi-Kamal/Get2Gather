
<?php
session_start();
require_once ("class.user.php");

$user = new User();

/*if($user->is_Loggedin()!= "")
{
	$usr->redirect('');
}*/

if(isset($_POST['signup_btn']))
{
	$fname = strip_tags($_POST['fname']);
	$lname = strip_tags($_POST['lname']);
	$email = strip_tags($_POST['email']);
	$mobile = strip_tags($_POST['mobile']);
	$pass = strip_tags($_POST['pass']);
	//$day = strip_tags($_POST['day']);
	//$month = strip_tags($_POST['month']);
	//$year = strip_tags($_POST['year']);
	$sex = strip_tags($_POST['sex']);
	
	if($fname=="")	
	{
		$error[] = "<b><font color='red'>Provide your First Name !</font></b>";
	}
	else if($email=="")	{
		$error[] = "<b><font color='red'>Provide email id !";
	}
	else if(!filter_var($email, FILTER_VALIDATE_EMAIL))	{  // name@example.com
	    $error[] = "<b><font color='red'>Please enter a valid email address !</font></b>";
	}
	else if($mobile=="")	{
		$error[] = "<b><font color='red'>Provide mobile number !";
	}
	else if($pass=="")	{
		$error[] = "<b><font color='red'>Provide password !</font></b>";
	}
	else if(strlen($pass) < 6){
		$error[] = "<b><font color='red'>Password must be of atleast 6 characters</font></b>";
	}
	else
	{
		try
		{
			$stmnt = $user->runQuery("SELECT f_name, email FROM users WHERE f_name=:fname OR email=:email");
			$stmnt->execute(array(':fname'=>$fname, ':email'=>$email));
			$row = $stmnt->fetch(PDO::FETCH_ASSOC);
			
			if($row['f_name']==$fname)
			{
				$error[] = "<b><font color='red'>sorry username already taken !</font></b>";
			}
			if($row['email']==$email) 
			{
				$error[] = "<b><font color='red'>sorry email id already taken !</font></b>";
			}
			else
			{
				if($user->register($fname,$lname,$email,$mobile,$pass,$sex))
				{
					$user->redirect('signup.php?joined');
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sign Up</title>
	<link rel="stylesheet" type="text/css" href="styles/signup.login.style.css">
</head>
<body>
	<div class="container">
		<div class="box main">
			<div class="box logo">
				<p>Hi..</p>
				<p>Welcome<br>to</p>
				<!--<img src="images/logo.png">-->
			</div>
			<div class="box form">
				<h2>Create a New Account</h2><br>
				<form action="#" method="post">
					<?php
						if(isset($error))
						{
						 	foreach($error as $error)
						 	{
								 ?>
			                     <div class="alert alert-danger">
			                        <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
			                     </div>
			                     <?php
							}
						}
						else if(isset($_GET['joined']))
						{
							 ?>
			                 <div class="alert alert-info">
			                      <i class="glyphicon glyphicon-log-in"></i> &nbsp; Successfully registered <a href='index.php'>login</a> here
			                 </div>
			                 <?php
						}
					?>
					<input type="text" name="fname" placeholder="First name"><br>
					<input type="text" name="lname" placeholder="Last name"><br>
					<input type="text" name="email" placeholder="Email address"><br>
					<input type="text" name="mobile" placeholder="Mobile number"><br>
					<input type="password" name="pass" placeholder="Create a Password"><br><br>
	
					<input type="radio" name="sex" value="Male">&nbsp;Male&nbsp;&nbsp;
					<input type="radio" name="sex" value="Female">&nbsp;Female<br><br>
					<input type="submit" name="signup_btn" value="Create Account"><br>
				</form>
			</div>
		</div>
		<div class="box footer">
			<div class="left">
				<p style="font-size: 50px;">Get2Gather</p>
				<hr>
				<p style="font-size: 30px; letter-spacing: 5px;">A Social Network</p>
			</div>
			<div class="right">
				<p>Developed by</p>
				<hr>
				<p style="font-size: 20px;">Shashi Kamal Chakraborty</p>
				<p style="font-size: 20px;">Sukanya Sarkar</p> <hr>
				<p>MCA Final Year</p>
				<p>Siliguri Institute of Technology</p>
			</div>
		</div>
	</div>
</body>
</html>

