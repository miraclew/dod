<?php
/**
 * 用户及其关注的动态
 *
 * @property int $id
 * @property int $accountid
 * @property int $friendid
 * @property string $text
 * @property datetime $updated
 * 
 */
class HomeStatus extends Model {
	public static $useTable = 'home_statuses';
	public static $useDbConfig = 'message';
	
	public static function create($accountid, $friendid, $text) {
		$hs = new self();
		$hs->accountid = $accountid;
		$hs->friendid = $friendid;
		$hs->text = $text;
		$hs->save();
		return $hs;
	}
	
	public static function get($accountid, $friendid) {
		$hs = self::first(array("conditions"=>"accountid=? and friendid=?"), array($accountid, $friendid));
		if(!$hs) {
			$hs = new self();
			$hs->accountid = $accountid;
			$hs->friendid = $friendid;
// 			$hs->save();
		}
		return $hs;
	}
	
	public static function update_text($accountid, $friendid, $text) {
		$hs = self::get($accountid, $friendid);
		$hs->text = $text;
		$hs->save();
		return $hs;	
	}

	protected function afterSave() {
		if ($this->is_new_record()) {
			$ms = MessageStatus::get($this->accountid);
			$ms->last_home_status_id = $this->id;
		}
	}
}