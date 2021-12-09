<?php
class BillingService extends Service
{
	public function __construct()
	{
		parent::__construct("GAME_BANK");
	}

	/**
	 * Check user's balance in the game bank for this server
	 *
	 * @param int $userid
	 * @param int $gameid
	 * @param int $serverid
	 * @return array with balance, or false for no-balance
	 */
	public function check_balance($userid,$gameid,$serverid)
	{
		if (!is_numeric($userid)) {
			$userid = parent::single_select("EXEC SP_Get_Passport_Id ?", array($userid));
			$userid = $userid['id'];
		}

		$query = "EXEC SP_Bank_Balance_Check_Balance ?,?,?";
		$balance = parent::query_write($query,array($userid,$gameid,$serverid));
		return $balance;
	}

	/**
	 * Purchase an item and deduct its price
	 *
	 * @param int $userid
	 * @param int $charid
	 * @param int $gameid
	 * @param int $serverid
	 * @param int $amount	total cost - pass price-per-item * $count
	 * @param int $itemid
	 * @param int $count
	 * @param string $uniqueid
	 * @return array
	 */
	public function purchase_item($userid,$charid,$gameid,$serverid,$amount,$itemid,$count,$uniqueid)
	{
		if (!is_numeric($userid)) {
			$userid = parent::single_select("EXEC SP_Get_Passport_Id ?", array($userid));
			$userid = $userid['id'];
		}

		/*if (!is_numeric($charid)) {
			$charid = parent::single_select("EXEC SP_Bank_Get_Character_Rowid ?, ?, ?", array($charid, $gameid, $serverid));
			$charid = $charid['rowid'];
if($log = fopen(LOG_PATH."/DBError.log",'a')) {
fwrite($log,date('Y-m-d H:i:s')." hibryd-api [$_SERVER[REMOTE_ADDR]]====    $charid\n");
fclose($log);
}

		}*/

		// SP will check balance for us: reason_code=-1 for not enough
		// SP will purchase item: reason_code=-2 for failed insert
		// SP will deduct currency: reason_code=-3 for failed deduct
		// SP will return new balance: reason_code=-4 for 
		$query = "EXEC SP_Item_Purchase ?,?,?,?,?,?,?,?";
		$status = parent::query_write($query,array($userid,$charid,$gameid,$serverid,$amount,$itemid,$count,$uniqueid));
		return $status;
	}
}
