<?php
/**
 * 关注表
 * @property int $id
 * @property int $accountid
 * @property int $targetid
 * @property datetime $created
 */
class Follow extends Model {
    public static $useTable = 'follows';
    public static $useDbConfig = 'relation';
    
    /**
     * 获取关注列表
     * @param int $accountid
     * @return array 关注用户数组
     */
    public static function list_following($accountid, $keywords, $page, $count) {
    	$limit = ($page-1)*$count;
    	if(!empty($keywords)) {
    		return self::queryBySql("select u.accountid,u.nickname,u.avatar,u.level,l.value as value
    			from follows f
    			left join qyh_user.user_profiles u on f.targetid=u.accountid
    			left join interaction_stat l on (f.targetid=l.targetid and l.accountid=? and l.type=?)
    			where f.accountid=? and u.nickname LIKE '%{$keywords}%' order by l.value desc limit ?,?
    		", array($accountid, InteractionStat::STAT_TYPE_LISTEN, $accountid, $limit, $count)
    		);
    	}
    	else {
    		return self::queryBySql("select u.accountid,u.nickname,u.avatar,u.level,l.value as value
    			from follows f
    			left join qyh_user.user_profiles u on f.targetid=u.accountid
    			left join interaction_stat l on (f.targetid=l.targetid and l.accountid=? and l.type=?)
    			where f.accountid=? order by l.value desc limit ?,?
    		", array($accountid, InteractionStat::STAT_TYPE_LISTEN, $accountid, $limit, $count)
    		);
    	}
    }
    
    /**
     * 获取粉丝列表
     * @param int $accountid
     * @return array 用户数组
     */
    public static function list_followers($accountid, $keywords, $page, $count) {
    	$limit = ($page-1)*$count;
    	
    	if(!empty($keywords)) {
    		return self::queryBySql("select u.accountid,u.nickname,u.avatar,u.level,l.value as value
    			from follows f
    			left join qyh_user.user_profiles u on f.accountid=u.accountid
    			left join interaction_stat l on (f.accountid=l.accountid and l.targetid=? and l.type=?)
    			where f.targetid=? and u.nickname LIKE '%{$keywords}%' order by l.value desc,f.id desc limit ?,?
    		", array($accountid, InteractionStat::STAT_TYPE_LISTEN, $accountid, $limit, $count)
    		);    		
    	}
    	else {
    		return self::queryBySql("select u.accountid,u.nickname,u.avatar,u.level,l.value as value
    			from follows f
    			left join qyh_user.user_profiles u on f.accountid=u.accountid
    			left join interaction_stat l on (f.accountid=l.accountid and l.targetid=? and l.type=?)
    			where f.targetid=? order by l.value desc,f.id desc limit ?,?
    		", array($accountid, InteractionStat::STAT_TYPE_LISTEN, $accountid, $limit, $count)
    		);
    	}
    }
    
    /**
     * 获取关注的人ID列表
     * @param int $accountid
     * @return array 关注用户ID数组
     */
    public static function get_following_ids($accountid) {
    	$data = self::query(array('fields'=>array('targetid'), 'conditions'=>"accountid=?"),array($accountid));
    	$following = array();
    	foreach ($data as $value) {
    		$following[] = $value['targetid'];
    	}
    	return $following;
    }
    
    /**
     * 获取粉丝ID列表
     * @param int $accountid
     * @return array 用户ID数组
     */
    public static function get_follower_ids($accountid) {
    	$data = self::query(array('fields'=>array('accountid'), 'conditions'=>"targetid=?"),array($accountid));
    	$followers = array();
    	foreach ($data as $value) {
    		$followers[] = $value['accountid'];
    	}
    	return $followers;
    }
    
    public static function is_following($accountid, $targetid) {
    	return self::exsit("accountid=? and targetid=?", array($accountid, $targetid));
    }
    
    protected function afterSave() {
    	if($this->is_new_record()) {
    		// user info update
    		UserInfo::get($this->accountid)->following = Follow::count("accountid=?",array($this->accountid));
    		UserInfo::get($this->targetid)->followers = Follow::count("targetid=?",array($this->targetid));
    		
    		// task
    		if(UserInfo::get($this->targetid)->followers == 10) {
    			$task = Task::findByPk(Task::ID4_10_FOLLOWS); /* @var $task Task */
    			$task->accomplish($this->targetid);
    		}    		
    		$task = Task::findByPk(Task::ID3_FIRST_FOLLOW); /* @var $task Task */
    		$task->accomplish($this->accountid);
    		
    		// system message
//     		$sm = new SystemMessage();
//     		$sm->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
//     		$sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_FOLLOWER;
//     		$sm->toid = $this->targetid;
//     		$sm->fromid = $this->accountid;
//     		$sm->objectid = $this->id;
//     		$sm->save();
    		
    		// status  
    		//$this->create_follow_statuses();
    		MessageStatus::get($this->targetid)->new_follow($this);    		  
    	}
    }
    
    private function create_follow_statuses() {
    	$p2 = UserProfile::findByPk($this->targetid); /* @var $p2 UserProfile */
    	// p1 user status, home status
    	// p1 fans status
    	$follwers = Follow::get_follower_ids();
    	foreach ($follwers as $follwer_id) {
    		$text = "我关注了{$p2->nickname} ({$p2->title})";
    		HomeStatus::create($follwer_id, $this->accountid, $text);
    	}
    	
    	// p2 user status, home status
    	$us = new UserStatus();
    	$us->accountid = $this->targetid;
    	$us->text = "hi！{$p2->nickname}，我已经是你的新粉丝了！";
    	$us->save();
    }
}
