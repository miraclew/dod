<?php
/**
 * 任务列表
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $type
 * @property int $award_type
 * @property int $awards
 * @property datetime $created
 *
 */
class Task extends Model {
    public static $useTable = 'tasks';
    public static $useDbConfig = 'system';
    
    const ID1_FIRST_LOGIN 		= 1;
    const ID2_PROFILE_COMPLIETE = 2;
    const ID3_FIRST_FOLLOW 		= 3;
    const ID4_10_FOLLOWS 		= 4;
    const ID5_FIRST_ROOM 		= 5;
    const ID6_FIRST_CREATION 	= 6;
    const ID7_APP_RATING		= 7;
    const ID8_FIRST_GIVE_GIFT 	= 8;
    
    const ID9_DAILY_LOGIN 		= 9;
    const ID10_ROOM_JOIN		= 10;
    const ID11_DAILY_GIVE_GIFT  = 11;
    const ID12_DAILY_ROOM 		= 12;
    const ID13_DAILY_SHARE 		= 13;
    const ID14_DAILY_LISTEN 	= 14;
    
    /**
     * 完成任务
     * @param int $accountid
     */
    public function accomplish($accountid) {
    	if($this->type == ApiConst::TASK_TYPE_ONCE) {
    		if (!TaskAccomplish::exsit("taskid=? and accountid=?", array($this->id, $accountid))) {
    			$ta = new TaskAccomplish();
    			$ta->type = $this->type;
    			$ta->accountid = $accountid;
    			$ta->taskid = $this->id;
    			$ta->save();
    		}    		
    	}
    	else if($this->type == ApiConst::TASK_TYPE_DAILY) {
    		if (!TaskAccomplish::exsit("taskid=? and accountid=? and DATE(created)=CURDATE()", array($this->id, $accountid))) {
    			$ta = new TaskAccomplish();
    			$ta->type = $this->type;
    			$ta->accountid = $accountid;
    			$ta->taskid = $this->id;
    			$ta->save();
    		}    		
    	}
    }
}
