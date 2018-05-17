<?php
	require_once('session.php');
	require_once('class.user.php');
	$user_logout = new User();

	if(isset($_GET['logout']) && $_GET['logout']=="true")
	{
		echo "come on";
		$user_logout->doLogout();
		$user_logout->redirect('index.php');
	}

?>
