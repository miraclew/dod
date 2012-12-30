<?php
/**
 * 用户最近访客
 * 
 * @property int $id
 * @property int $accountid
 * @property int $visitorid
 * @property string $modified
 *  
 */
class Visitor extends Model {
	public static $useTable = 'visitors';
	public static $useDbConfig = 'user';
	
	const MAX_VISITORS_COUNT =  50;
	
	public static function create($accountid, $visitorid) {
		$visitor = self::first(array('conditions'=>"accountid=? and visitorid=?"),array($accountid,$visitorid));
		if (!$visitor) {
			$visitor = new Visitor(array('accountid'=>$accountid,'visitorid'=>$visitorid));
		}
		$visitor->modified = date('Y-m-d H:i:s');
		$visitor->save();
		
		// delete early visitors
		$max = self::MAX_VISITORS_COUNT;
		//self::queryBySql("delete from qyh_user.visitors where accountid=$accountid and id not in (select id from qyh_user.visitors order by modified limit $max)");
	}
	
	public static function find_all($accountid,$page,$count=20) {
		$visitors = self::find(array('conditions'=>"accountid=?",'order'=>"modified desc",'page'=>$page,'limit'=>$count), array($accountid));
		$data = array();
		foreach ($visitors as $value) { /* @var $value Visitor */
			$profile = UserProfile::findByPk($value->visitorid);
			$data[] = array('accountid'=>$profile->accountid,'nickname'=>$profile->nickname,'avatar'=>$profile->avatar,'created'=>$value->modified);
		}
		
		return $data;
	}
}