<?php

/**
 * 
 * 任务 
 * 
 * @property ResourceComponent $Resource
 */
class TaskApiController extends ApiController {
    
	public $components = array('Resource');
	
    /**
     * 任务列表
     */
    public function www_list() {
    	$accountid = Auth::user('accountid');    	
    	
    	//已完成待领
    	$accomplish = Task::queryBySql("select * from qyh_system.tasks where is_enabled=1 and id in 
    			(select taskid from qyh_user.task_accomplish where accountid=$accountid and (type=1 or (type=2 and DATE(created)=CURDATE())) and is_pickup is null)");
    	
    	//未完成的任务
    	$not_accomplish = Task::queryBySql("select * from qyh_system.tasks where is_enabled=1 and id not in 
    			(select taskid from qyh_user.task_accomplish where accountid=$accountid and (type=1 or (type=2 and DATE(created)=CURDATE())))");
    	 
    	$data = array('items'=>array());
    	foreach ($accomplish as $task) {
    		$task['is_accomplish'] = 1;
    		$data['items'][] = $task; 
    	} 
    	
    	foreach ($not_accomplish as $task) {    		
    		$task['is_accomplish'] = 0;
    		$data['items'][] = $task;
    	}
    	
    	foreach ($data['items'] as &$task) {
    		unset($task['created']);
    		$task['image'] = $this->Resource->getTaskImage($task['award_type']);
    		if ($task['id'] == Task::ID14_DAILY_LISTEN) {
    			$us = UserInfo::get($accountid);
    			$task_listen_value = LevelConfig::level_to_daily_listen_task_value($us->level);
    			$task['description'] = __($task['description'], $task_listen_value);    			
    		}
    		else if ($task['id'] == Task::ID12_DAILY_ROOM) {
    			$us = UserInfo::get($accountid);
    			$task['awards'] = LevelConfig::level_to_daily_room_awards($us->level);
    		}
    	}
    	
    	$this->success($data);        
	}
	
	public function www_pickup_awards() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		
		/* @var $ta TaskAccomplish */
		$ta = TaskAccomplish::first(array('conditions'=>"accountid=? and taskid=? and (type=1 or (type=2 and DATE(created)=CURDATE()))"),array($accountid, $id));
		if(!$ta) $this->failed(Err::$DATA_NOT_FOUND);
		
		
		// 给用户加奖励
		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
		
		$task = Task::findByPk($id); /* @var $task Task */
		if($task->award_type == ApiConst::TASK_AWARD_TYPE_POINTS) {
			$profile->points += $task->awards; // 是否要生成交易记录
			$profile->save();
		}
		else if($task->award_type == ApiConst::TASK_AWARD_TYPE_FLOWER) {
			$flower_item_id = 3;
			$bi = BagItem::first(array('conditions'=>"accountid=? and itemid=?"), array($accountid, $flower_item_id)); /* @var $bi BagItem */
			if ($bi) {
				$bi->quantity += $task->awards;
				$bi->save();
			}
			else {
				$bag = new BagItem();
				$bag->accountid = $accountid;
				$bag->itemid = $flower_item_id;
				$bag->quantity = $task->awards;
				$bag->quantity_init = $task->awards;
				$bag->get_type = ApiConst::ITEM_GET_TYPE_TASK_AWARDS;
				$bag->save();
			}
		}
		
		// 每日创建房间要加人气值
		if($id == Task::ID12_DAILY_ROOM) {
			$profile->add_pop_value(LevelConfig::level_to_daily_room_awards($profile->level));
		}

		$ta->is_pickup = 1;
		$ta->save();
		$this->success();
	}
}