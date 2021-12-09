<?php
#ini_set('display_errors','On');
#ini_set('error_reporting',E_ALL);

//print_r($_REQUEST,1);
//echo $HTTP_RAW_POST_DATA;

$xml = $HTTP_RAW_POST_DATA;
$xp = new XMLParser();
$data = $xp->parse($xml);

$type = strtolower($data[0]['name']);
$options = $data[0]['children'];
$params = array();
foreach($options as $child) {
	$params[strtolower($child['name'])] = $child['tagData'];
}

$params['account'] = strtolower($params['account']);

$status = 'failed'; // default
$reasonCode = -1; // no data supplied
$userType = 0;

$as = new AuthService();
$bs = new BillingService();
$log = '';

switch($type) {
	case 'login-request':
		$log .= "login: game=$params[game], user=$params[account], ip=$params[ip]";
		$loginResult = $as->login($params['account'],$params['password'],$params['game']);
		if($loginResult==null) {
			$reasonCode = 1;
			$message = 'Invalid Login';
			$log .= ", failed";
		} elseif($loginResult==-1) {
			$log .= ", no database connection";
			$message = 'Database Connect Error';
			$reasonCode = -1;
		} elseif(is_array($loginResult) && isset($loginResult['id'])) {
			$log .= ", valid";
			$userid = $loginResult['id'];
			if(!isset($params['ip']) || !$params['ip']) {
				$reasonCode = 3; // no IP
				$message = 'Client IP Required';
				$log .= ", no IP given";
			} else {
				$banned = $as->check_ban($loginResult['id'],$params['game'],$params['ip']);
				$account = $loginResult['name'];
				if($banned==1) {
					$reasonCode = 2;
					$message = 'Banned';
					$log .= ", banned account";
				} elseif($banned==2) {
					$reasonCode = 4;
					$message = 'Banned';
					$log .= ", banned region ($params[ip])";
				} else {
					$status = 'success';
					// usertype to hex, since it's a bitfield
					$userType = dechex($loginResult['usertype']);
					// pad to 8 chars and prepend with 0x
					if(strlen($userType)<=8) {
						$userType = '0x'.str_repeat('0',8-strlen($userType)).$userType;
					} else {
						$userType = '0x'.$userType;
					}
					$log .= ", userid=$userid, usertype: $userType";
				}
			}
		}
		break;
case 'currency-request':
		$log .= "checkBalance: game=$params[game], userid=$params[userid], server=$params[server]";
		$result = $bs->check_balance($params['userid'],$params['game'],$params['server']);
		if($result && isset($result['balance'])) {
			$status = 'success';
			$balance = $result['balance'];
			$log .= ", balance=$balance";
		} elseif($result===false) {
			$status = 'success';
			$balance = 0;
			$log .= ", balance not found, assuming 0";
		} else { // result===null
			$status = 'failed';
			$reasonCode = -1;
			$balance = 0;
			$log .= ", no database connection";
			$message = 'Internal database error';
		}
		$userid = $params['userid'];
		$server = $params['server'];
		break;

	case 'item-purchase-request':
		$log .= "purchaseItem: game=$params[game], userid=$params[userid], charid=$params[charid], server=$params[server], amount=$params[amount], itemid=$params[itemid], count=$params[count], unique=$params[uniqueid]";
		$result = $bs->purchase_item($params['userid'],$params['charid'],$params['game'],$params['server'],$params['amount'],$params['itemid'],$params['count'],$params['uniqueid']);
		$balance = 0;
		if($result) {
			$balance = $result['new_balance'];
			switch($result['status']) {
				case 'success':
					$status = 'success';
					$log .= ", ok, balance=$balance";
					break;
				case 'failed':
					$status = 'failed';
					switch($result['reason_code']) {
						case -1:
							$reasonCode = 1;
							$message = 'Not enough currency';
							$log .= ", not enough money, balance=$balance";
							break;
						case -2: case -3:
							$reasonCode = 2;
							$message = 'Internal database error';
							$log .= ", database error: $result[reason_code] $result[message], balance=$balance";
							break;
						default:
							$reasonCode = 3;
							$message = 'Internal database error';
							$log .= ", unknown error: $result[reason_code] $result[message], balance=$balance";
							break;
					}
					break;
				default:
					$reasonCode = 3;
					$message = 'Internal database error';
					$log .= ", unknown status: $result[status], error: $result[reason_code] $result[message], balance=$balance";
			}
		} else {
			$status = 'failed';
			$reasonCode = -1;
			$message = 'Internal database error';
			$log .= ", no database connection";
			// this is a REAL database error, so SP won't return us the user's balance.  Get it now using the regular balance query.
			$bal = $bs->check_balance($params['userid'],$params['game'],$params['server']);
			if($bal && isset($bal['balance'])) {
				$balance = $bal['balance'];
			}
		}
		$userid = $params['userid'];
		$server = $params['server'];
		break;
	default:
		$log = "invalid request. type=$type, data=".clean($xml);
		$origType = $type;
		$type = 'invalid';
		break;
}
writelog($type,$log);

function writelog($function,$text) {
	if($log = fopen(LOG_PATH."/$function.log",'a')) {
		fwrite($log,date('Y-m-d H:i:s')." hibryd-api [$_SERVER[REMOTE_ADDR]] $text\n");
		fclose($log);
	}
}

function clean($text) {
	for($i=0;$i<strlen($text);$i++) {
		if($text[$i]=="\n" || $text[$i]=="\t") {
			$text[$i] = ' ';
		} elseif(ord($text[$i])<32) {
			$text[$i] = '';
		}
	}
	return $text; // take out chars < ord(32), (newlines,tabs)=>spaces
}
