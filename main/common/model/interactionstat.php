<?php
/**
 * 用户交互统计
 * 
 * @property int $id
 * @property int $accountid
 * @property int $targetid
 * @property int $type
 * @property int $value
 * @property datetime $created
 */
class InteractionStat extends Model {
    public static $useTable = 'interaction_stat';
    public static $useDbConfig = 'relation';
    
	const STAT_TYPE_LISTEN  		= 1; // 收听时长
	const STAT_TYPE_POP_CONTRIBUTE  = 2; // 人气贡献
	const STAT_TYPE_NEW_PRIVATE_MSG = 3; // 新未读私信数
	
	public static function get($type, $accountid, $targetid) {
		$is = self::first(array('conditions'=>"type=? and accountid=? and targetid=?"), array($type, $accountid, $targetid));
		if(!$is) {
			$is = new InteractionStat(array('type'=>$type, 'accountid'=>$accountid, 'targetid'=>$targetid, 'value'=>0));
		}
		return $is;
	}
	
	public static function getListeners($accountid) {
		return self::query(array(
				'conditions'=>"interactionstat.targetid=? and interactionstat.type=".self::STAT_TYPE_LISTEN,
				'fields' => array('u.accountid','u.nickname','u.avatar','interactionstat.value'),
				'joins' => array(
						array('type' => 'inner','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => "interactionstat.accountid = u.accountid")
						),
				'order' => "value desc"
				), array($accountid));
	}
	
	public static function getPopContributors($accountid) {
		return self::query(array(
				'conditions'=>"interactionstat.targetid=? and interactionstat.type=".self::STAT_TYPE_POP_CONTRIBUTE,				
				'order' => "value desc"
		), array($accountid));		
	}
}