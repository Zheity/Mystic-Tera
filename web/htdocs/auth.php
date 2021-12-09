<?php
require 'core/init.php';
$general->logged_in_protect();

if (empty($_POST) === false) 
{
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (empty($username) === true) 
	{
		echo ('Login_UsernameDataEmpty');
		exit();
	}
	if (empty($password) === true) 
	{
		echo ('Login_PasswordDataEmpty');		
		exit();
	}
	if (strlen($password) >32 || strlen($password) <4)
	{
		echo ('Login_PasswordLengthWrong');
		exit();
	}
	if ($accounts->username_exists($username) === false) 
	{
		echo ('Login_UsernameNotExists');
		exit();
	}
	if ($accounts->account_activated($username) === false) 
	{
		echo ('Login_AccountNotActivated');
		exit();
	}
	//try login
	else 
	{
		$login = $accounts->login($username, $password);
		if ($login) //login success
		{
			session_regenerate_id(true);// destroying the old session id and creating a new one
			$_SESSION['Id'] =  $login;
			
			echo ('Login_OK');
			exit();
		}
		if ($login === false) //login failed
		{
			echo ('Login_FAIL');
			exit();
		}
	}
} 
?>

		
		