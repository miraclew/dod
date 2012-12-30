<?php
/**
 * 用户控制器
 * @property ResourceComponent $Resource
 *
 */
class UserApiController extends ApiController {
    public $components = array('Resource');
    
    public function www_home() {
        $accountid = $this->_getParam('accountid', 0);
        if ($accountid == 0) {
            $accountid = Auth::user('accountid');
        }
        
        /* @var $profile UserProfile */
        $profile = UserProfile::findByPk($accountid);        
        $info = UserInfo::get($accountid);
        
        $data = $profile->attributes();
        unset($data['status']);
        $data['vip'] = $profile->vip;
        $data['avatar'] = Image::load_from_url($data['avatar'])->get_url(Image::SIZE_ORIGINAL); 
        $data['vip_expire_time'] = Utility::secondsToNow($data['vip_expire_time']);
        $data['following'] = $info->following;
        $data['followers'] = $info->followers;
        $data['rooms'] = $info->rooms;
        $data['talks'] = $info->talks;
        $data['statuses'] = UserStatus::count("accountid=?",array($accountid));
        $data['favorites'] = Favorite::count("accountid=?",array($accountid));
        $data['photos'] = Photo::count("accountid=?",array($accountid));
        $data['location'] = $info->location;
        $data['pop_title'] = LevelConfig::pop_value_to_title($info->pop_value);
        $data['pop_value_change_yesterday'] = $info->pop_value_change_yesterday;
        
        if($accountid != Auth::user('accountid')) {
            //如果是我关注的
            $num = Follow::is_following(Auth::user('accountid'), $accountid) ? 1 : 0 ;
            //如果是关注我的
            $num += Follow::is_following($accountid, Auth::user('accountid')) ? 2 : 0;
            switch ( $num ) {
                case 0 :
                    $data['relation'] = ApiConst::RELATION_TYPE_NONE;
                    break;
                case 1 :
                    $data['relation'] = ApiConst::RELATION_TYPE_FOLLOWING;
                    break;
                case 2 :
                    $data['relation'] = ApiConst::RELATION_TYPE_FOLLOWED;
                    break;
                case 3 :
                    $data['relation'] = ApiConst::RELATION_TYPE_BOTH;
                    break;
                default;
            }
            
        }
        
        if($data['birthday']) {
            $birthday = strtotime($data['birthday']);
            $m = date('m', $birthday);$d = date('d', $birthday);
            $constellation = Utility::getConstellation(intval($m), intval($d));
            if(!empty($constellation)) $constellation .= '座';
            $data['constellation'] = $constellation;
        }
        
//         $data['badge'] = $this->Resource->getBadge($status->level);
        $data['bg_image'] = array('id'=>$profile->bg_image_id, 'image' => $this->Resource->getBackgroundImageURL($profile->bg_image_id));
        
//         if($accountid == Auth::user('accountid')) {
//         	$gifts = ItemUsing::find(array(
// 	        			'conditions' => 'itemusing.to_accountid = ? and itemusing.created > ? and itemusing.status=1',
// 	        			'limit' => 4,
// 	        			'fields' => array('u.nickname', 'i.id as itemid', 'i.image', 'itemusing.quantity','itemusing.id', 'itemusing.from_accountid as accountid'),
// 	        			'joins' => array(
// 	        					array('type' => 'left', 'alias' => 'u', 'table' => 'qyh_user.user_profiles', 'conditions' => "itemusing.from_accountid = u.accountid"),
// 	        					array('type' => 'left', 'alias' => 'i', 'table' => 'items', 'conditions' => "itemusing.itemid = i.id"))
// 	        			),
//         				array($accountid, date('Y-m-d H:i:s', UserInfo::get($accountid)->last_pull_gift_time))
//         			);
        	
//         	foreach ($gifts as $value ) {
//         		$g = $value->attributes();
//         		$g['is_new'] = 1;
//         		$data['new_gifts'][] = $g;
//         	}
//         }

        $data['visitors_today'] = Visitor::count('accountid=? and DATE(modified) = CURDATE()',array($accountid));
        $data['visitors_history'] = Visitor::count('accountid=?',array($accountid));
        $data['visitors'] = Visitor::find_all($accountid, 1, 7);       
        
        $this->_respond(Err::$SUCCESS, $data);
    }
    
    public function www_rankings() {
        $type = $this->_getParam('type', ApiConst::RANKINGS_TYPE_MY);
        $accountid = $this->_getParam('accountid', 0);
        if ($accountid == 0) {
            $accountid = Auth::user('accountid');
        }
        
        $type = $this->_getParam('type',0);
        if($type == 0) {
            $types = array(ApiConst::RANKINGS_TYPE_MY,ApiConst::RANKINGS_TYPE_NEWBIE,ApiConst::RANKINGS_TYPE_CONTRIBUTOR,ApiConst::RANKINGS_TYPE_TOP);
        }
        else {
            $types = array($type);
        }
        
        $items = array();
        
        foreach ($types as $t) {
            $data = $this->get_ranking($t, $accountid);
            $rankings = array();
            foreach ($data as &$v) {
            	$accountid2 = $v['member'];
            	$profile = UserProfile::findByPk($accountid2); /* @var $profile UserProfile */
            	$value = array();
            	$value['accountid'] = $accountid2;
            	$value['avatar'] = $profile->avatar;
            	$value['nickname'] = $profile->nickname;            	
            	$value['badge'] = $this->Resource->getBadge($profile->level);
            	$value['value'] = $v['score'];
            	$value['rank'] = $v['rank'];
            	
                if($t == ApiConst::RANKINGS_TYPE_MY && $accountid2 == $accountid) {
                	$status = UserInfo::get($accountid);                	
                    $value['yesterday_change'] = $status->pop_value_change_yesterday;
                    $value['rank_surpass'] = SystemRankings::instance()->getRankSurpass($accountid);
                }
                
                if($t == ApiConst::RANKINGS_TYPE_TOP) {
                	$us = UserInfo::get($accountid2);
                	$value['change'] = $us->rank_yesterday;
                }
                elseif ($t == ApiConst::RANKINGS_TYPE_NEWBIE) {
                	$us = UserInfo::get($accountid2);
                	$value['change'] = $us->pop_value_change_yesterday; // 新手昨日人气值增长 
                }
                $rankings[] = $value;
            }
            
            $item = array('type'=> $t, 'rankings'=>$rankings);
            $items[] = $item;
        }
        
		$this->success(array('items'=>$items));
    }
    
   	public function www_top_ranking() {
   		$page = $this->pageParams();
   		
   		$items = array();

   		$profiles = UserProfile::find(array('limit'=>$page['count'], 'page'=>$page['page'], 'order'=>"pop_value desc"));
   		$rank = 1;
   		foreach ($profiles as $profile) { /* @var $profile UserProfile */
   			$talk = Talk::first(array('conditions'=>"accountid=?", 'order'=>'pop_value desc'), array($profile->accountid)); /* @var $talk Talk */
   			if (!$talk) continue;
   			
   	   		$item = array('user'=>array('accountid'=>$profile->accountid, 'nickname'=>$profile->nickname, 'avatar'=>$profile->avatar),
   	   				'rank' => $rank,
   	   				'pop_value' => $profile->pop_value,
   	   				'voice' => array('fid'=>$talk->voice_fid, 'duration'=>$talk->voice_time,'voice'=>$talk->voice,'image'=>'')
   	   				);
	   		$items[] = $item;
	   		$rank++;
   		}
   		
   		$data = array('items'=>$items);
   		$data['is_last_page'] = count($items) < $page['count'] ? 1:0;
   		
   		$this->success($data);
   	}
    
    private function get_ranking($type, $accountid) {
    	switch ($type) {
    		case ApiConst::RANKINGS_TYPE_MY:
    			$data = SystemRankings::instance()->getMyRanking(1, 100);
    			break;
    		case ApiConst::RANKINGS_TYPE_TOP:
    			$data = SystemRankings::instance()->getTopRanking(1, 100);
    			break;
    		case ApiConst::RANKINGS_TYPE_NEWBIE:
    			$data = SystemRankings::instance()->getNewbieRanking(1, 100);
    			break;
    		case ApiConst::RANKINGS_TYPE_CONTRIBUTOR:
    			$is = InteractionStat::getPopContributors($accountid);
    			$data = array();
    			$rank = 1;
    			foreach ($is as $v) {
    				$data[] = array('member'=>$v['accountid'],'rank'=>$rank,'score'=>$v['value']);
    				$rank++;
    			}
    			break;
    		default:
    			;
    			break;
    	}
    	if(empty($data))
    		$data = array();
    	return $data;
    }
    
    /**
     * 我的称号
     *
     * @return
     */
    public function www_my_title () {
        $data = array();
        //获得用户的ID 和 昵称
        $accountid = Auth::user('accountid');
        $data['nickname'] = Auth::user('nickname');
        $rank = '';
        $account_result = UserProfile::findByPk($accountid);
        $status = UserInfo::get($accountid);
        $data['rank'] = UserInfo::get($accountid)->rank;
        $data['pop_value'] = (int)$account_result->pop_value;
        $data['pop_title'] = LevelConfig::pop_value_to_title($data['pop_value']);
        $data['badge'] = $this->Resource->getBadge($status->level);
        $level_arr = LevelConfig::get_title_config();
        $last_num = count($level_arr);
        foreach ( $level_arr as $key => & $_level ) {
            $_level['description'] = $_level['pop_value'];
            $_level['badge'] = $this->Resource->getBadge($key);
            if ( $data['pop_title'] == $_level['title'] ) {
                $data['level'] = $key;
                if ( $last_num <= $key ) {
                    $data['upgrade_remains'] = 0;
                    $data['upgrade_progress'] = 1;
                } else {
                    $data['upgrade_remains'] = $level_arr[$key+1]['pop_value'] - $data['pop_value'];
                    $data['upgrade_progress'] = round(($data['pop_value'] - $_level['pop_value']) / ($level_arr[$key+1]['pop_value'] - $_level['pop_value']), 2);
                }
                $_level['is_current'] = 1;
            }
        }
        $data['items'] = array_values($level_arr);
        $this->_respond(Err::$SUCCESS, $data);
    }
    
    public function www_creations() {
        $accountid = $this->_getParam('accountid', 0);
        if ($accountid == 0) {
            $accountid = Auth::user('accountid');
        }
        
        $page = $this->_getParam('page', 1);
        $count = $this->_getParam('count', 20);
        
        $talks = Talk::query(array(
                    'fields' => array('r.title','talk.*'),
                    'conditions'=>"talk.accountid=? and talk.type=?", 
        			'order' => 'created desc',
        			'page' => $page,
        			'limit' => $count,
                    'joins'=>array(
                        array('type' => 'left','alias' => 'r','table' => 'rooms','conditions' => "r.id = talk.roomid")
                    )
                ),array($accountid, ApiConst::TALK_TYPE_CREATION));
        
        $data = array('items'=>array());
        foreach ($talks as $talk) {
            $item = array(
                    'id' => $talk['id'],
            		'room_id' => $talk['roomid'],
                    'room_title' => $talk['title'], 
            		'room_tags' => RoomTag::getRoomTags($talk['roomid']),
                    'voice' => $talk['voice'],
                    'voice_time' => $talk['voice_time'],
            		'voice_fid' => $talk['voice_fid'],
                    'bg_image' => $this->Resource->getThemeBanner($talk['themeid']), 
                    'pop_value' => $talk['pop_value'],
                    'created' => $talk['created']
                    );
            
            $data['items'][] = $item;
        }
        
        $data['is_last_page'] = count($data['items']) < $count ? 1:0;
        
        $this->success($data);
    }    
    
    /**
     * 收到礼物列表
     * 
     * @return
     */
    public function www_gifts() {
        //查看如果输入了ID 就看这个人,如果没有输入就看当前登录用户
        $accountid = $this->_getParam('accountid', '', true);
        if ( true == empty($accountid) ) {
            $accountid = Auth::user('accountid');
        }

        $result = ItemUsing::find(array(
        		'conditions' => 'itemusing.to_accountid = ?',
                'fields' => array('u.nickname', 'i.id as itemid', 'i.image','itemusing.id','itemusing.status','itemusing.quantity', 'itemusing.from_accountid as accountid','itemusing.created'),
                'joins' => array(
                		array('type' => 'left', 'alias' => 'u', 'table' => 'qyh_user.user_profiles', 'conditions' => "itemusing.from_accountid = u.accountid"),
                		array('type' => 'left', 'alias' => 'i', 'table' => 'items', 'conditions' => "itemusing.itemid = i.id"))
        		),
        		array($accountid)
        	);
        $data = array();
        foreach ( $result as $value ) {
        	$g = $value->attributes();
        	$g['is_new'] = 0;
        	if($g['status'] == ItemUsing::STATUS_NORMAL) {
        		$g['is_new'] = 1;
        	}
        	        	
        	$data['items'][] = $g;
        }
        
        if($accountid == Auth::user('accountid')) {
        	UserInfo::get($accountid)->last_pull_gift_time = time();
        }        
        
        $this->success($data);        
    }
    
    public function www_status() {
    	$accountid = Auth::user('accountid');
        $ms = MessageStatus::get($accountid);
        $us = UserInfo::get($accountid);        
        
        $profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
        
        $data = array(
        		'new_messages' => $ms->new_messages,
        		'last_message' => $ms->last_message,
        		'bid' => $profile->room_bid,
        		'last_room_id' => SystemStatus::get()->last_room_id,
        		'last_home_status_id' => $ms->last_home_status_id
        	);
    	
        $this->success($data);
    }
    
    public function www_visitors() {
    	$page = $this->_getParam('page', 1);
        $count = $this->_getParam('count', 20);
        
    	$accountid = Auth::user('accountid');
    	$visitors = Visitor::find(array('conditions'=>"accountid=?", 'order'=>'modified desc',
    			'page' => $page,'limit' => $count), array($accountid));
    	$items = array();
    	foreach ($visitors as $value) { /* @var $value Visitor */
    		$profile = UserProfile::findByPk($value->visitorid);
    		$is_following = Follow::is_following($accountid, $profile->accountid)?1:0;
    		$created = Utility::day3_to_date(strtotime($value->modified));
    		$items[] = array('user'=>array('accountid'=>$profile->accountid,'avatar'=>$profile->avatar,'nickname'=>$profile->nickname),'created'=>$created,'is_following'=>$is_following);
    	}
    	
    	$data = array('items' => $items);
    	$data['is_last_page'] = count($items) < $count ? 1:0;
    	
    	$this->success($data);
    }

}