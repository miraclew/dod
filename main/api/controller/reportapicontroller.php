<?php

/**
 * 上报的类
 * @author Administrator
 *
 */
class ReportApiController extends ApiController {
    
    /**
     * 上报地址
     * 上传 经纬度 和 位置 存入 mongodb 文件系统
     */
    public function www_location() {
        $accountid = Auth::user('accountid');
        
        $lat = $this->_getParam('lat'); //纬度        
        $lng = $this->_getParam('lng'); //经度        
        $location = $this->_getParam('location'); //地址
        $us = UserInfo::get($accountid);
        $us->lat = $lat;
        $us->lng = $lng;
        $us->location = $location;
                
//         if( true == LBS::validatLatlng($lat, $lng) ) {
//             if ( true === LBS::setUserLocation($accountid, $lat, $lng, $location) ) {
//                 $this->_respond(Err::$SUCCESS);
//             } else {
//                 $this->_respond(Err::$FAIL);
//             }
//         } else {
//             $this->_respond(Err::$FAIL);
//         }
		$this->success();
    }
    
    public function www_device_token() {
    	$accountid = Auth::user('accountid');
    	
    	$device_token = $this->_getParam('device_token');
    	$login = LastLogin::findByPk($accountid); /* @var $login LastLogin */
    	$login->device_token = $device_token;
    	$login->save();

    	$this->success();
    }
    
    /**
     * 上报语音收听
     * 输入 json封装过的voices数组 
     */
    public function www_voice_listen() {
        $listenerId = Auth::user('accountid');
        $voices = $this->_getParam('voices', "");
        
        Log::write("accountid=$listenerId voices=$voices", 'voice_listen');
        
        $data = json_decode($voices, true);
        if (empty($voices) || empty($data)) {
            $this->failed(Err::$INPUT_INVALID);
        }
        
        $total_duration = 0;

        foreach ($data as $v) {
			$duration = $v['duration'];        	
            $upload = Upload::findByPk($v['fid']); /* @var $upload Upload */
            if($upload && $this->validateVoiceListen($upload, $listenerId, $duration)) {            	
            	if($upload->type != Upload::TYPE_COMMENT_VOICE) { // 评论不计数
            		$us = UserInfo::get($listenerId);
            		$us->listen_value += $duration;
            		$total_duration += $duration;
            		
            		$ownerId = $upload->accountid;
            		$is = InteractionStat::get(InteractionStat::STAT_TYPE_LISTEN, $listenerId, $ownerId);
            		$is->value += $duration;
            		$is->save();
            		
            		Log::write("fid=".$v['fid']." ownerid=$ownerId duration=$duration", 'voice_listen');
            		
            		Visitor::create($ownerId, $listenerId);
            		
            		// 每日任务
            		if ($us->listen_value_change > 60) {
	            		$task = Task::findByPk(Task::ID9_DAILY_LOGIN); /* @var $task Task */
	            		$task->accomplish($listenerId);
            		}            		
            		// 任务
            		if($us->followers>1) {
            			$task = Task::findByPk(Task::ID3_FIRST_FOLLOW); /* @var $task Task */
            			$task->accomplish($listenerId);            			 
            		}            		
            		// 任务： 每日听取作品时间累计达到T秒
            		$task_listen_value = LevelConfig::level_to_daily_listen_task_value($us->level);            		
            		if ($us->listen_value_change >= $task_listen_value) {
            			$task2 = Task::findByPk(Task::ID14_DAILY_LISTEN); /* @var $task2 Task */
            			$task2->accomplish($listenerId);
            		}
            	}
            }
        }
        
        $profile = UserProfile::findByPk($listenerId); /* @var $profile UserProfile */
        $profile->add_pop_value($total_duration);
        
        $this->success();
    }
    
    private function validateVoiceListen(Upload $upload, $listenerId, $duration) {    	
		// TODO: $duration 不能大于实际时间(和上次听得时间比较)
		if($duration > Upload::MAX_VOICE_DURATION) {
			return  false;
		}
		
		// 忽略收听的情况
		if($listenerId == $upload->accountid) {
			//return false;
		}
		
		// TODO 根据房间的状态进行判断		
		return true;
	}
    
    
    /**
     * 用户反馈
     */
    public function www_feedback() {
        //获得登录用户的id
        $accountid = Auth::user('accountid');
        //获得语音时长
        $voice_time = $this->_getParam('voice_time');
        //语音文件
        $voice = $this->_getParam('voice');
        //如果语音文件存在
        if( false == empty($voice_time) && false == empty($voice) ) {
            //调用插入feedback model程序
            $result = Feedback::_save($accountid, $voice, $voice_time);
            $this->_respond($result);		
        } else {
            //语音不存在
            $this->_respond(Err::$INPUT_FORMAT_INVALID);
        }
    }
    
    /**
     * 举报不良内容
     */
    public function www_bad_voice() {
        //获得登录用户的id 设置为举报人
        $reporter_id = Auth::user('accountid');
        
        //获得举报的ID 举报的类型  举报的理由
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        $reason = $this->_getParam('reason');
        if ( false == is_numeric($id) || false == is_numeric($type) || false == is_numeric($reason) ) {
            $this->failed(Err::$INPUT_INVALID);
        } 
        
        //检查类型是 房主开房语音 还是话题 还是评论
        switch ( $type ) {
        	case ApiConst::VOICE_TYPE_ROOM :
        		$voice = Room::findByPk($id);
        		break;
        	case ApiConst::VOICE_TYPE_TALK :
        		$voice = Talk::findByPk($id);
        		break;
        	case ApiConst::VOICE_TYPE_COMMENT :
        		$voice = Comment::findByPk($id);
        		break;
        	default;
        }
        
        if (!$voice) {
        	$this->failed(Err::$DATA_NOT_FOUND);
        }
        
        if (Report::exsit('reporter_id=? and owner_id=? and type=? and objectid=? and reason=?',
        		array($reporter_id, $voice->accountid, $type, $id, $reason))) {
        	$this->failed(Err::$DATA_EXIST);        	
        }
        
        $report = new Report();
        $report->reporter_id = $reporter_id;
        $report->owner_id = $voice->accountid;
        $report->type = $type;
        $report->objectid = $id;
        $report->status = 1;
        $report->reason = $reason;
        $report->save();
        
        $this->success();
    }
    
    public function www_click() {
    	$accountid = Auth::user('accountid');
    	$id = $this->_getParam('id');
    	
    	if($id == ApiConst::CLICK_ID_APP_RATING) {
    		$task = Task::findByPk(Task::ID7_APP_RATING); /* @var $task Task */
    		$task->accomplish($accountid);
    	}
    	
    	$this->success();
    }
}