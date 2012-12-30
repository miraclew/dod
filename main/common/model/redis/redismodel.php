<?php
/**
 * Redis 数据模型基类
 *
 */
class RedisModel {
	public static $serializer = Redis::SERIALIZER_NONE;
	public static $cacheConfig = 'aloha';
	
	/**
	 * @var Redis
	 */
	protected $redis;
	
	public function __construct()
	{
		$this->redis = static::redis();
	}
	
	public static function redis() {
		$setting = Cache::settings(static::$cacheConfig);
		if ($setting === false || !isset($setting['servers']) || count($setting['servers']) <= 0)
			throw new Exception("load redis config failed");		 
		
		$pieces = explode(':', $setting['servers'][0]);
		if(count($pieces) > 2) throw new Exception("load redis config failed");
		
		$redis = new Redis();
		if(!$redis->connect($pieces[0], $pieces[1])) {
			throw new Exception("can't connect to redis server");
		}
		
		$redis->setOption(Redis::OPT_SERIALIZER, static::$serializer);
		return $redis;
	}

	/*
	 * 构造缓存的key值
	* $keyFormat
	* 可变参数1,2...
	*/
	public function k($keyFormat, $args = null) {
		if ($args === null) { //无可变参数
			return $keyFormat;
		} elseif (!is_array($args)) {
			$args = array_slice(func_get_args(), 1);
		}
		return vsprintf($keyFormat, $args);
	}

}
