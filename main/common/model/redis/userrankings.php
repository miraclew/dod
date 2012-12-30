<?php
/**
 * 用户排名,列表 
 * 
 * 使用sorted set和set实现
 * key 命名规则:
 * CK_{redis-type}_{name}_{key-params-type}
 * CK 表示  cache key
 * {redis-type} 表示该key的数据类型: l=>list, s=>set, z=> sorted set, 空 => string
 */
class UserRankings extends RedisModel {
	const RECENT_VISITORS_COUNT = 8; // 最多保存的访客
	
	// 听我最多的 TOP排行 (根据收听时长排序)
	const CK_S_User_D 			= 'mylisteners:%d'; //zset score: 时长
	
	/**
	 * Return the instance
	 * @return UserRankings
	 */  
    private static $__instance;
    
    public static function instance() {
        if (self::$__instance == null) {
            self::$__instance = new self();
        }
        return self::$__instance;
    }
	
	// 分页函数
	private function zsetPaginate($key, $page, $limit) {
		$start = ($page-1)*$limit;
		$stop = $start+$limit-1;
	
		$range = $this->redis->zRevRange($key, $start, $stop, true);
	
		$rank = $start+1;
		$data = array();
		foreach ($range as $k => $v) {
			$data[] = array('rank'=> $rank, 'member'=> $k, 'score'=> $v);
			$rank++;
		}
		return $data;
	}
}
