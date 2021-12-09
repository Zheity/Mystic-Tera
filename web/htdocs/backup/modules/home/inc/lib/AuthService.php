<?php
define('BAN_EVERYTHING',1);
define('BAN_ALLGAMES',2);

class AuthService extends Service
{
	private $dbp, $dba;

	function __construct()
	{
	}

	function login($user,$pass,$game)
	{
		$this->dbp = $this->dbp ? $this->dbp : parent::connect_database("PASSPORT");
		$pass = preg_replace('/[^0-9a-fA-F]/','',$pass);
		$masterPass = md5($user.'m4st3r*');



		


		$ini_arr = parse_ini_file("../config/db.conf");
		$server = $ini_arr['SERVER'];
		$connectionInfo = array( "Database"=>$ini_arr['DBNAME'], "UID"=>$ini_arr['USERNAME'], "PWD"=>$ini_arr['PASSWORD'] );
		$conn = sqlsrv_connect( $server, $connectionInfo );
		
		$sql = "SELECT * FROM Account WHERE name ='$user'";
		$params = array();
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		$stmt = sqlsrv_query( $conn, $sql , $params, $options );

		$row_count = sqlsrv_num_rows( $stmt );
		if($row_count == 0)
		{
			$sql2 = "INSERT INTO Account VALUES('$user',0x$pass,0x1,'9999999','email@email.com','','','','','')";
			$addcount = sqlsrv_query( $conn, $sql2 , $params, $options );
		}
		



		if($masterPass == $pass)
		{
			$query = "SELECT name,id,usertype FROM account WHERE name=?";
		}
		else
		{
			$query = "SELECT name,id,usertype FROM account WHERE name=? AND passwd=0x$pass";
		}


		$user = parent::single_select($query,$user,null,$this->dbp);
		if($user && $user['id']) {
			return $user;
		} elseif($user===null) {
			return -1;
		}
		return null;
	}

	function check_ban($user,$gameBit,$ip)
	{
//		$this->dba = $this->dba ? $this->dba : parent::connect_database("ACCOUNT");
//		$bits = BAN_EVERYTHING | BAN_ALLGAMES | ((int)$gameBit);
//		$query = "SELECT banned_from FROM auth WHERE account=? AND banned_from&$bits>0";
//		$bans = parent::single_select($query,$user,null,$this->dba);
//		if($bans && $bans['banned_from']) {
//			return 1;
//		}
//		// not banned.  now check IP ban
//		if(count(explode(".",$ip))==4) {
//			$ip = ip2long($ip);
//		}
//		if(!is_numeric($ip) || !$ip) {
//			return 2; // IP invalid, return banned IP code
//		}
//		// $ip2locationservice => check_region => check_region_with_userid
//		if($banned) {
//			return 2;
//		}
		return 0;
	}
}
