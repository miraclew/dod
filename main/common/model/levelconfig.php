<?php
/*
 * 用户等级参数表.存放不同等级和会员级别对应的参数值
 * 参数名以字段形式存放
 */
class LevelConfig {
    
    /**
     * 获得配置文件 并 重组数组 未来可以设置缓存
     *
     * @return
     */
    public static function get_title_config (){
       //引入配置文件
       require_once CONFIG_ . 'appconfig.php';
       global $level_arr;
       ksort($level_arr);
       return $level_arr;
    }
    
    /**
     * 获得 等级开房间所需金币的关联数组
     *
     * @return
     */
    public static function get_room_cost_config (){
       //引入配置文件
       require_once CONFIG_ . 'appconfig.php';
       ksort($room_point_cost_arr);
       return $room_point_cost_arr;
    }
    
    /**
     * 由人气 获得 等级
     * @param int $point 声望
     *
     * @return level 等级
     */
    public static function pop_value_to_level ($pop_value) {
        $level_and_title = self::pop_value_to_level_and_title($pop_value);
        return $level_and_title['level'];
    }
    
    /**
     * 由人气 获得 称谓
     * @param int $point 声望
     *
     * @return title 称谓名称
     */
    public static function pop_value_to_title ($pop_value) {
        $level_and_title = self::pop_value_to_level_and_title($pop_value);
        return $level_and_title['title'];
    }
    
    /**
     * 由人气 获得 等级+称谓
     * @param int $point 声望
     *
     * @return array($level, $title) 返回 等级和称谓 
     */
    public static function pop_value_to_level_and_title ($pop_value=0) {
        if ( !is_numeric($pop_value) ) {
            $pop_value = 0;
        }
        $pop_value = (int)$pop_value;
        //获得配置数组
        $level_arr = self::get_title_config();
        //等级 声望
        $level = $title = '';
        //循环数组
        foreach ( $level_arr as $key => $value ) {
            if ( $value['pop_value'] > $pop_value ) {
                return array(
                             'level' => $level,
                             'title' => $title
                             );
            }
            $level = $key;
            $title = $value['title'];
        }
        return array(
                             'level' => $level,
                             'title' => $title
                             );
    }
    
    /**
     * 根据等级 计算出来 所需开房间的金币值
     *
     * @return
     */
    public static function level_to_open_room_point_cost ($level) {
        if ( !is_numeric($level) || $level <= 0 ) {
            $level = 1;
        }
        $room_point_cost = self::get_room_cost_config();
        return $room_point_cost[$level];
    }
    
    public static function level_to_daily_room_awards($level) {
    	require_once CONFIG_ . 'appconfig.php';
    	global $daily_room_awards_arr;
    	return $daily_room_awards_arr[$level];
    }
    
    public static function level_to_max_pop_value_incr($level) {
    	require_once CONFIG_ . 'appconfig.php';
    	global $daily_max_pop_value_incr_arr;
    	return $daily_max_pop_value_incr_arr[$level];
    }
    
    // 
    public static function level_to_daily_listen_task_value($level) {
    	$task_listen_value = 1200;
    	if ($level <= 40) {
    		$task_listen_value = 30*$level;
    	}
    	return $task_listen_value;
    }
    
    public static function pop_value_to_emotion_level($pop_value) {
    	if ($pop_value >= 3600) {
    		return 4;
    	}
    	else if ($pop_value >= 1200) {
    		return 3;
    	}
    	else if ($pop_value >= 90) {
    		return 2;
    	}
    	else {
    		return 1;
    	}
    }
    
    public static function get_rooms_bid($rooms) {
    	$rooms = $rooms + 1;
    	if ($rooms < 1) {
    		$rooms = 1;
    	}
    	if ($rooms > 20) {
    		$rooms = 20;
    	}
    	$room_bids = array(
    			1=>0,2=>200,3=>350,4=>500,5=>650,6=>800,7=>950,8=>1100,9=>1250,10=>1400,
    			11=>1550,12=>1700,13=>1850,14=>2000,15=>2150,16=>2300,17=>2450,18=>2600,19=>2750,20=>2900
    			);
    	return $room_bids[$rooms];
    }
}

