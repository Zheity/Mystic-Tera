<?php 
require 'core/init.php';
$general->logged_in_protect();

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/style.css" >
	<title>ProjectS1-Development Server </title>
</head>
<body>	
<div id="header">
</div>
	<div id="container">
		<?php include 'includes/menu.php'; ?>
		<h1>Tera - Project S1 Development Server</h1>
		<h2_hl>Development Status:</h2_hl><br>
		<h2_hl>Website:</h2_hl><br>
		<h2_std>-Website Functions Completed!</h2_std><br>
		<h2_std>-Registration, Login, PasswordRecovery, EmailActivation Done!</h2_std><br>
		<h2_std>-AccountList & PlayerList Done!</h2_std><br>		
		<h2_std>-WebsiteStyle: In Progress!</h2_std><br>
		
		<h2_hl>Game Login:</h2_hl><br>
		<h2_std>-Authorization wit Web or LoginServer Done! </h2_std><br>
		
		<h2_hl>GameServer:</h2_hl><br>
		<h2_std>-Client Revision: 30.09 EU</h2_std><br>
		<h2_std>-Server Revision: 3009.150228.1 </h2_std><br>
		<h2_std>-Lobby: Packets Updated to 30.09</h2_std><br>
		<h2_std>-Lobby: Character Creation, Delete Done! </h2_std><br>
		<h2_std>-Lobby: Character List: In Progress! </h2_std><br>

	</div>
</body>
</html>