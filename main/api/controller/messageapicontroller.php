<?php

/**
 * 
 * 消息类 公告 私信等
 * 
 * @property ResourceComponent $Resource
 * 
 */
class MessageApiController extends ApiController {
    public $components = array('Resource', 'Uploader');

    
    public function www_home() {
        $accountid = Auth::user('accountid');
        $status = MessageStatus::get($accountid);
        $data = array(
                'items' => array(
                        array(
                                'type' => ApiConst::MESSAGE_TYPE_PRIVATE,
                                'new_messages' => $status->new_private_msg,
                                'last_message' => $status->last_private_msg,
                                'last_time' => $this->formatDateTime($status->last_private_msg_time),
                        ),
                        array(
                                'type' => ApiConst::MESSAGE_TYPE_COMMENT,
                                'new_messages' => $status->new_comment,
                                'last_message' => $status->last_comment,
                                'last_time' => $this->formatDateTime($status->last_comment_time),
                        ),
                        array(
                                'type' => ApiConst::MESSAGE_TYPE_SYSTEM,
                                'new_messages' => $status->new_system,
                                'last_message' => $status->last_system,
                                'last_time' => $this->formatDateTime($status->last_system_time),
                        )
                        )
                );
        $this->_respond(Err::$SUCCESS, $data);
    }
    
    private function formatDateTime($dt) {    	
    	return Utility::day3_to_date(intval($dt));
    }
    
    /**
     * 获取当前用户的公告,系统信息，好友邀请，房间信息等
     * 先获取当前用户的id,和所要获取的信息的类型,根据用户id和类型,获取当前用户的该类型的信息.
     */
    public function www_public() {
        //获取当前用户的id
        $accountid = Auth::user('accountid');
        $type = $this->_getParam('type', '', true);
        $page = $this->_getParam('page',1, true);
        $count = $this->_getParam('count', 20, true);
        
        if (!$type) {
            $this->failed(Err::$INPUT_INVALID);
        }
        
        //数据库查询出当前用户的所有消息
        $result = SystemMessage::find(array(
        		'conditions'=>"toid=? AND type=? ", 'order'=>'created desc','page'=>$page, 'limit'=>$count), 
        		array($accountid, $type)
        	);
        
        $data = array('items'=>array());
        foreach ($result as $value) { /* @var $value SystemMessage */
//         	debug($value);
        	$item = $value->attributes();
        	$message_title = $value->get_message_and_title_and_avatar();
//         	$item['title'] = $message_title[0];
//         	$item['message'] = $message_title[1];
//         	$item['avatar'] = $message_title[2];
//         	$item['created'] = Utility::day3_to_date($item['created']);
//         	unset($item['toid']);
        	$item['annotations'] = $value->annotations;
        	$data['items'][]  = $item;
        }
        
//         die;
        
        $data['is_last_page'] = count($result) < $count ? 1:0;
        
        // 清总消息计数
        if ($type == ApiConst::MESSAGE_TYPE_SYSTEM) {
        	MessageStatus::get($accountid)->new_system = 0;
        }
        
        $this->success($data);
    }
    
    // TODO need improve
    public function www_new_comments() {
    	$accountid = Auth::user('accountid');
    	$page = $this->_getParam('page',1, true);
    	$count = $this->_getParam('count', 20, true);
    	 
    	$ucs = UserCounter::find(array('conditions'=>"accountid=? and (type=? or type=?) ",'order' => 'modified desc','page'=>$page, 'limit'=>$count), 
    			array($accountid, UserCounter::TYPE_TALK_NEW_COMMENTS,UserCounter::TYPE_ROOM_NEW_COMMENTS));
		
    	$data = array();
		$talkid = 0;
    	foreach ($ucs as $c) { /* @var $c UserCounter */
    		if($c->type == UserCounter::TYPE_TALK_NEW_COMMENTS) {
    			$talkid = $c->objectid;
    			$talk = Talk::findByPk($talkid); /* @var $talk Talk */
    			if(!$talk) continue;
    			$roomid = $talk->roomid;
    			$room = Room::findByPk($roomid); /* @var $room Room */
    			$talk_voice = $talk->voice;
    			$talk_voice_time = $talk->voice_time;
    			$image = $this->Resource->getThemePreview($talk->themeid);
    			
    			$voice = array('fid'=>$talk->voice_fid,'duration'=>$talk->voice_time, 'image'=>$talk->voice_image, 'voice'=>$talk->voice);
    			
    			$comment = Comment::last(array('conditions' => "talkid=?"), array($talkid));  /* @var $comment Comment */  			
    		}
    		else if ($c->type == UserCounter::TYPE_ROOM_NEW_COMMENTS) {
    			$roomid = $c->objectid;
    			$room = Room::findByPk($roomid); /* @var $room Room */
    			if(!$room) continue;
    			$talk_voice = $room->voice;
    			$talk_voice_time = $room->voice_time;
    			$image = $this->Resource->getRoomBackgroundImageURL($room->bg_image_id, true);
    			
    			$voice = array('fid'=>$room->voice_fid,'duration'=>$room->voice_time, 'image'=>$room->voice_image, 'voice'=>$room->voice);
    			
    			$comment = Comment::last(array('conditions' => "roomid=? and talkid is null"), array($roomid));
     		}
     		
     		if (!$comment) continue;
     		
     		$profile = UserProfile::findByPk($comment->accountid);
    		$item = array(
    				'accountid' => $profile->accountid,
    				'roomid' => $roomid,
    				'tags' => RoomTag::getRoomTags($roomid),
    				'title' => $room->title,
    				'talkid' => $talkid,
    				'image' => $image,
    				'talk_voice' => $voice,
    				'nickname' => $profile->nickname,
    				'badge' => $this->Resource->getBadge($profile->level),
    				'avatar' => $profile->avatar,
    				'comment_voice' => array('fid'=>$comment->voice_fid,'duration'=>$comment->voice_time, 'image'=>$comment->voice_image, 'voice'=>$comment->voice),
    				'comments' => $c->value
    			);
    		
    		$data['items'][]  = $item;
    	}
    	
    	$data['is_last_page'] = count($data['items']) < $count ? 1:0;    	 
    	
    	// reset counter
    	MessageStatus::get($accountid)->new_comment = 0;
    	
    	$this->success($data);
    }

    /**
     * 获取当前用户的私信
     * 先获取当前用户的id,根据用户id,获取当前用户的所有私信.
     */
    public function www_private() {
        $accountid = Auth::user('accountid');
        $other_id = $this->_getParam('accountid', '', true);
        $page = $this->_getParam('page',1, true);
        $count = $this->_getParam('count', 20, true);        
        
        if (!is_numeric($other_id) ) { 
            $this->failed(Err::$INPUT_FORMAT_INVALID);
        }
        
        $result = Message::find(array(
        		'conditions' => "(toid=? and fromid=? and to_flag <> 2) or (toid=? and fromid=? and from_flag <> 2)",
        		'fields' => array('id', 'fromid', 'message', 'voice', 'voice_time', 'created', 'to_flag as is_read', 'send_flag'),
        		'order' => 'created desc','page'=>$page, 'limit'=>$count),
        		array($accountid, $other_id, $other_id, $accountid));        
        
        $counter = NewMessageCounter::getPrivateNewMessageCounter($other_id, $accountid);
        $counter->count = 0;
        $counter->save();
        
        $data = array('items'=>array());
        foreach ($result as $value) {
        	$item = $value->attributes();
        	$item['created_at'] = $item['created']; 
        	$item['created'] = Utility::day3_to_date($value->created); 
        	 
        	$data['items'][]  = $item;
        }
         
        $data['is_last_page'] = count($result) < $count ? 1:0;
        $this->success($data);
    }
    
    /**
     * 发私信
     */
    public function www_send() {
        $accountid = Auth::user('accountid');
    	
        $toid  = $this->_getParam('accountid', '', true);
        $use_bag_item = $this->_getParam('use_bag_item', 0, true);
        $message = $this->_getParam('message', '', true);
        $voice_time = $this->_getParam('voice_time', 0, true);
        
        if (empty($toid) && empty($use_bag_item)) {
        	$this->failed(Err::$INPUT_REQUIRED);
        }        
        if (empty($voice_time) || empty($this->request->params['form']['voice'])) {
            $this->failed(Err::$INPUT_REQUIRED);
        }
        if ($accountid == $toid) {
        	$this->failed(Err::$OPERATE_NOT_PERMIT_ON_SELF);
        }
        
        $voice = $this->Uploader->uploadVoice($this->request->params['form']['voice'], $voice_time);
        if( false === $voice  ) {
        	$this->failed(Err::$UPLOAD_FAILED);
        }
        
        if (!empty($use_bag_item)) {
        	$bagitem = BagItem::findByPk($use_bag_item); /* @var $bagitem BagItem */
        	if (!$bagitem) {
        		$this->failed(Err::$DATA_NOT_FOUND);
        	}
        	if ($bagitem->accountid != $accountid) {
        		$this->failed(Err::$OPERATE_OWNNER_ONLY);
        	}
        	
        	$msg = array('accountid'=>$accountid,'bagitemid'=>$use_bag_item,'message'=>$message,'voice'=>$voice,'voice_time'=>$voice_time);        	
        	Resque::enqueue(QueueNames::ALOHA, JobNames::DELIVER_SPEAKER_MESSAGE, $msg);
        	
        	$bagitem->quantity--;
    		if ($bagitem->quantity <= 0) {
    			$bagitem->destroy();
    		}
    		else
    			$bagitem->save();
    		
        	$this->success();
        }
        else {
        	//$enabled = Preference::get($toid, Preference::STRANGER_PRIVATE_MSG);
        	$enabled = 1;
        	if ($enabled == 1 || (Follow::is_following($accountid, $toid) && Follow::is_following($toid, $accountid))) {
        		$msg = new Message();
        		$msg->toid = $toid;
        		$msg->fromid = $accountid;
        		$msg->message = $message;
        		$msg->voice = $voice;
        		$msg->voice_time = $voice_time;
        		$msg->send_flag = Message::SEND_FLAG_NONE;
        		$msg->save();
        		 
        		$item = $msg->attributes();
        		$item['created'] = Utility::day3_to_date(time());
        		
        		$this->success($item);
        	} 
        	else {
        		$this->failed(Err::$OPERATE_NOT_PERMIT);
        	}
        }
    }
    
    /**
     * 删除一条私信 软删除
     */
    public function www_destroy_private() {
        //获得需要删除的私信的类型和ID, 接受者id
        $type = $this->_getParam('type', '', true);
        $value = $this->_getParam('value', '', true);
        $accountid = Auth::user('accountid');
        //校验为空不,字段合法不
        if ( true == empty($type) || false == is_numeric($type) ) {
            $this->_respond(Err::$INPUT_INVALID);
        } else {
            $this->_respond(Message::remove($accountid, $type, $value));
        }
    }
    
    /**
     * 设置私信为已读
     */
    public function www_read_private() {
        $accountid = Auth::user('accountid');
        $id = $this->_getParam('id', '', true);
        
        $pm = Message::findByPk($id); /* @var $pm Message */
        if (!$pm) $this->failed(Err::$DATA_NOT_FOUND);
        if($pm->toid != $accountid) $this->failed(Err::$OPERATE_OWNNER_ONLY);
        
        $pm->read();
        
        $this->success();
    }
    
    /**
     * 系统消息回应
     */
    public function www_ack() {
        $id = $this->_getParam('id',0);
        $ack_value = $this->_getParam('ack_value', 1);
        $accountid = Auth::user('accountid');
        
        if($id == 0) $this->failed(Err::$INPUT_REQUIRED);
        
        $sm = SystemMessage::findByPk($id); /* @var $sm SystemMessage */
        if(!$sm) $this->failed(Err::$DATA_NOT_FOUND);
        
        switch ($sm->sub_type) {
        	
        } // switch end
        
        $sm->ack_status = $ack_value;
        $sm->save();
        $this->success();
    }
    
    public function www_private_summary() {
    	$accountid = Auth::user('accountid');
    	$keywords = $this->_getParam('keywords','',true);
    	$page = $this->pageParams();
    	
    	$kw = '';
    	if (!empty($keywords)) {
    		$kw = " and p.nickname like '%{$keywords}%' ";
    	}
    	$cs = NewMessageCounter::query(array(
    			'fields' => array('c.fromid','p.nickname','p.avatar','c.modified','c.count'),
    			'conditions'=>"c.toid=? and c.fromid!=? and c.type=? $kw",
    			'alias'=>'c', 
    			'joins' => array(array('type'  => 'inner','alias' => 'p','table' => 'qyh_user.user_profiles','conditions' => 'p.accountid = c.fromid')),
    			'page' => $page['page'],
    			'limit' => $page['count'],
    			), array($accountid,$accountid,ApiConst::MESSAGE_TYPE_PRIVATE));
    	
    	$items = array();
    	foreach ($cs as $value) {
    		$item = array('accountid'=>$value['fromid'],'nickname'=>$value['nickname'],'value'=>$value['count'],'avatar'=>$value['avatar'],
    				'is_following'=>Follow::is_following($accountid, $value['fromid'])?1:0,'send_flag'=>0,'last_time'=>$value['modified']);
    		$items[] = $item;
    	}
    	
    	MessageStatus::get($accountid)->new_private_msg = 0;
    	
    	$data['items'] = $items;
    	$data['is_last_page'] = count($items) < $page['count'] ? 1:0;
    	$this->success($data);
    }
    
    /**
     * 
     * 私信总计列表
     *
     * @return 
     */
    public function www_private_summary_deprecated() {
        //接收人的id
        $accountid = Auth::user('accountid');
        $page = $this->_getParam('page',1, true);
        $count = $this->_getParam('count', 20, true);
        
        $select_name = 'SELECT * FROM messages where to_flag <> 2 ORDER BY to_flag asc,created desc';
        $keywords = $this->_getParam('keywords','',true);
        if ( false == empty($keywords) ) {
            $select_name = "SELECT m.*
                               FROM messages m 
                           left join qyh_user.user_profiles u
                               on u.accountid = m.fromid
                           where m.to_flag <> 2 and u.nickname like '%{$keywords}%'
                               ORDER BY m.to_flag asc,m.created desc";
        }
        //一次 读出来的 所有给你 发过私信的人的列表 同时统计出来未读数量和 最后一条的类别 
        $message_reuslt  = Message::queryBySql("SELECT message.fromid as accountid,message.toid, (case `to_flag` when 0 then send_flag else '' end) as send_flag, u.level, u.nickname, u.avatar, n.count as value 
                                                    FROM ({$select_name}) AS message 
                                                left JOIN qyh_user.user_profiles AS u
                                                    ON (message.fromid = u.accountid) 
                                                LEFT JOIN new_message_counter as n
                                                    ON n.toid = message.toid and n.fromid = message.fromid and n.type = ?
                                                WHERE message.toid = ?
                                                    GROUP BY message.fromid
                                                LIMIT ".($page-1)*$count.", {$count} 
                                                ", array(ApiConst::MESSAGE_TYPE_PRIVATE, $accountid));
        
        foreach ( $message_reuslt as & $_message ) {
            $_message['badge'] = $this->Resource->getBadge($_message['level']);
            unset($_message['level']);
            //去找最后一条的创建时间  并格式化
            $message = Message::first(array('conditions' => 'fromid =? and toid=?',
                                            'field' => 'created', 
                                            'order' => 'created desc'),
                                      array($_message['accountid'], $accountid));
            $_message['last_time'] = Utility::day3_to_date($message->created);
        }
        
        MessageStatus::get($accountid)->new_private_msg = 0;
        
        $data['items'] = $message_reuslt;
        $data['is_last_page'] = count($message_reuslt) < $count ? 1:0;
        $this->success($data);
    }
    
    /**
     * 删除系统消息(官方公告除外)
     *
     * @return
     */
    public function www_destroy_public () {
        $accountid = Auth::user('accountid');
        $id = $this->_getParam('id', '', true);
        //查找本条信息
        $sm = SystemMessage::findByPk($id); /* @var $sm SystemMessage */
        if (!$sm ) {
            $this->failed(Err::$DATA_NOT_FOUND);
        }
        //如果是官方公告不让删除
        if ( ApiConst::MESSAGE_TYPE_SYSTEM == $sm->type || $sm->toid != $accountid) {
        	$this->failed(Err::$OPERATE_NOT_PERMIT);
        } 
        
        //设置删除销毁
        $sm->destroy();
        $this->success();
    }
    
    public function www_mentions() {
    	$accountid = Auth::user('accountid');
    	$page = $this->_getParam('page',1, true);
    	$count = $this->_getParam('count', 20, true);
    	
    	$items = array();
    	$mentions = Mention::find(array('conditions'=>'accountid=?', 'page'=>$page, 'limit'=>$count, 'order'=>"created desc"), array($accountid));
    	foreach ($mentions as $m) { /* @var $m Mention */
    		$room = Room::findByPk($m->roomid);
    		$user = UserProfile::findByPk($m->friendid); /* @var $user UserProfile */
    		$comment = Comment::findByPk($m->commentid);
    		
    		$talk_floor = 0;
    		$comment_emotion = 0;
    		if ($m->type == ApiConst::AT_TYPE_TALK) {
    			$talk = Talk::findByPk($m->objectid);
    			$type = ApiConst::CONTENT_TYPE_TALK;
    			$voice = $talk->voice_array;
    			$talk_floor = $talk->floor;
    		}
    		else if ($m->type == ApiConst::AT_TYPE_COMMENT) {
    			$type = ApiConst::CONTENT_TYPE_COMMENT_EMOTION;
    			$content = Comment::findByPk($m->objectid);
    			if ($content->emotion <= 0) {
    				$type = ApiConst::CONTENT_TYPE_COMMENT_VOICE;
    			}
    			$comment_emotion = $content->emotion;
    			$voice = $content->voice_array;
    		}
    		
    		
    		$item = array(
    				'roomid' => $m->roomid,
    				'title' => $room->title,
    				'user' => $user->user_avatar,
    				'comment' => array('id'=>$comment->id,'type'=>$comment->type,'voice'=>$comment->voice_array,'emotion'=>$comment->emotion),
    				'at_content' => array(
    						'id' => $m->objectid,
    						'type'=>$type,
    						'voice'=>$voice,
    						'talk_floor' => $talk_floor,
    						'comment_emotion' => $comment_emotion    						
    						),
    				'created' => Utility::day3_to_date(strtotime($m->created))
    				);
    		$items[] = $item;
    	}
    	
    	MessageStatus::get($accountid)->new_comment = 0;

    	$data = array('items'=>$items);
    	$data['is_last_page'] = count($items) < $count ? 1:0;
    	$this->success($data);
    }
    
    
    
    
    
}