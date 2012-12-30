<?php
class RelationApiController extends ApiController {
    public $components = array('Resource');
    
    const PAGE_LIMIT = 20;
    
    public function www_following() {
        $accountid = $this->_getParam('accountid', '', true);
        $keywords = $this->_getParam('keywords','', true);
        $page = $this->_getParam('page',1, true);
        $count = $this->_getParam('count',self::PAGE_LIMIT, true);
        if ( true == empty($accountid) || !is_numeric($accountid) ) {
            $accountid = Auth::user('accountid');
        }

        $items = Follow::list_following($accountid, $keywords, $page, $count);
        foreach ( $items as & $_item ) {
            $_item['badge'] = $this->Resource->getBadge($_item['level']);
            unset($_item['level']);
        }
        $data = array('items'=>$items);
        $data['is_last_page'] = count($items) < $count ? 1:0;
        
        $this->success($data);
    }
    
    public function www_followers() {
        $accountid     = $this->_getParam('accountid', '', true);
        $keywords = $this->_getParam('keywords','', true);
        $page = $this->_getParam('page',1, true);
        $count = $this->_getParam('count',self::PAGE_LIMIT, true);
        
        if ( true == empty($accountid) || !is_numeric($accountid) ) {
            $accountid = Auth::user('accountid');
        }
        
		MessageStatus::get($accountid)->new_followers = 0;
        
        $items = Follow::list_followers($accountid, $keywords, $page, $count);
        foreach ( $items as & $item ) {
            //$item['badge'] = $this->Resource->getBadge($item['level']);
            $item['is_following'] = Follow::is_following($accountid, $item['accountid'])?1:0;
            unset($item['level']);
        }
        
        $data = array('items'=>$items);
        $data['is_last_page'] = count($items) < $count ? 1:0;
        $this->success($data);
    }
    
    public function www_follow() {
        $accountid = Auth::user('accountid');
        $accountid2 = $this->_getParam('accountid','', true);
        $ids = $this->_getParam('ids','', true);
        if (empty($accountid2) && empty($ids)) {
        	$this->failed(err::$INPUT_REQUIRED);
        }
        
        if (!empty($accountid2)) {
	        if($accountid == $accountid2) {
	        	$this->failed(Err::$RELATION_FOLLOW_FAILED);	        	
	        }
	        
	        if(Follow::exsit("accountid=? and targetid=?", array($accountid, $accountid2))) {
	        	$this->failed(Err::$RELATION_ALREADY_FOLLOWED);
	        }
	        
	        $follow = new Follow();
	        $follow->accountid = $accountid;
	        $follow->targetid = $accountid2;
	        if(!$follow->save()) {
	        	$this->failed(Err::$RELATION_FOLLOW_FAILED);
	        }
	        EventManager::instance()->dispatch(new Event(EventNames::RELATION_FOLLOW, $this, array('follow'=>$follow)));
        }
        else {
        	$ids = explode(',', $ids);
        	foreach ($ids as $targetid) {
        		if($accountid == $targetid || empty($targetid)) {        			
        			continue;
        		}
        		 
        		if(Follow::exsit("accountid=? and targetid=?", array($accountid, $targetid))) {
        			continue;
        		}
        		 
        		$follow = new Follow();
        		$follow->accountid = $accountid;
        		$follow->targetid = $targetid;
        		if(!$follow->save()) {
        			continue;
        		}
        		 
        		EventManager::instance()->dispatch(new Event(EventNames::RELATION_FOLLOW, $this, array('follow'=>$follow)));
        	}        	
        }
        
        $this->success();
    }
    
    public function www_unfollow() {
        $accountid = Auth::user('accountid');
        $targetid = $this->_getParam('accountid');
        
        
        $follow = Follow::first(array('conditions'=>'accountid=? and targetid=?'), array($accountid, $targetid));
        if($follow) {
        	$follow->destroy();
        	EventManager::instance()->dispatch(new Event(EventNames::RELATION_UNFOLLOW, $this, array('follow'=>$follow)));
        }
        
        $this->success();
    }
    
    /**
     * 介绍好友
     *
     * @return
     */
    public function www_introduce() {
        //分别获得 介绍人id 接受人id 被介绍人id列表
        $fromid = Auth::user('accountid');
        $targetid = $this->_getParam('accountid');
        $followers = explode(',', $this->_getParam('followers'));
        
        if (count($followers) <= 0) $this->failed(Err::$INPUT_INVALID);
        
        $profile = UserProfile::findByPk($fromid);
//         if($profile->vip <= 0)
//         	$this->failed(Err::$OPERATE_VIP_ONLY);
        
//         //分解插入消息
//         foreach ( $followers as $follower_id ) {
//         	$exist = false;
//         	// $exist = SystemMessage::exist("toid={$follower_id} and fromid={$fromid} and objectid={$objectid}");
//         	if ($exist || Follow::is_following($fromid, $follower_id)) continue;
        	
//         	$message = new SystemMessage();
//         	$message->toid = $follower_id;
//         	$message->fromid = $fromid;
//         	$message->objectid = $objectid;
//         	$message->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
//         	$message->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_INTRODUCE;
//         	$message->message = '';
//         	$message->has_read = 0;
//         	$message->annotations = '';
//         	$message->save();
//         }
 
		Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::INTRODUCTION,'accountid'=>$fromid,'data'=>array('targetid'=>$targetid)));
        
        $this->success();        
    }
    
    public function www_recommend_following() {
    	$accountid = Auth::user('accountid');
    	
    	$filter = array();    	
    	// row1 系统推荐
    	$row1 = RecommendFollowing::random();
    	
    	// row2 最热    	
    	$users = UserProfile::queryBySql("select accountid from qyh_user.user_profiles where accountid!=$accountid order by pop_value desc limit 4");
    	$row2 = Utility::collectField($users, 'accountid');
    	
    	// row3 新人
    	$users = UserProfile::queryBySql("select accountid from qyh_user.user_profiles where accountid!=$accountid order by accountid desc limit 4");
    	$row3 = Utility::collectField($users, 'accountid');
    	
    	$this->success(array(
    			'row1'=>$this->get_avatar($row1),
    			'row2'=>$this->get_avatar($row2),
    			'row3'=>$this->get_avatar($row3),
    			));
    }
    
    private function get_avatar($ids) {
    	$data = array();
    	foreach ($ids as $accountid) {
    		$profile = UserProfile::findByPk($accountid);    		
    		$data[] = $profile->user_avatar;
    	}
    	return $data;
    }
    
}
