 <?php
	session_start();
	include_once("class.user.php");

	$usr = new User();

	if($usr->is_loggedin()!="")
	{
		$usr->redirect('profile.php');
	}

	if(isset($_POST['login_btn']))
	{
		$fname = $_POST['txt_fname_email'];
		$email = $_POST['txt_fname_email'];
		$pass = $_POST['txt_pass'];

		if($usr->doLogin($fname, $email, $pass))
		{
			$usr->redirect('home.php');
		}
		else
		{
			$error = "<b><font color='red'>Wrong Details !</font></b>";
		}
	}
?><!--here's an error!! -->

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
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
				<h2>Log in</h2><br>
				<form action="#" method="post">
					<?php
				      if(isset($error))
				      {
				    ?>
				      <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
				    <?php
				      }
				    ?>
					<input type="text" name="txt_fname_email" placeholder="First Name / Email"><br>
					<input type="password" name="txt_pass" placeholder="Password"><br><br>
					<input type="submit" name="login_btn" value="Log In"><br><br>
				</form>
				<h2>New Member ?</h2><br>
				<a href="signup.php">
					<button>Create a New Account</button>
				</a>
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

