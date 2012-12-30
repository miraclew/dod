<?php
/*
 * 消息队列.由于后台任务处理
 */
class MessageQueue {
	const MESSAGE_QUEUE = 'messagequeue';
    private static $__instance;
    
   /**
	 * Return the instance
	 * 
	 * @return MessageQueue
	 */
    public static function instance() {
        if (self::$__instance == null) {
            self::$__instance = new self();
        }
        return self::$__instance;
    }

    //操作string
    public static function read($key, $config = 'default') {
    	return Cache::read($key, $config);
    }
    
    //操作 string
    public static function write($key, $value, $duration, $config = 'default') {
    	return Cache::write($key, $value, $duration, $config);

    }
    
    //操作list，缓存为redis时可以调用本方法
    public static function readList($key, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->lPop($key);
    	}
    	
    }
    
    //操作list，缓存为redis时可以调用本方法
    public static function writeList($key, $value, $config = 'default') {
    	//var_dump($key,$value,$config);
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    		
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis')//若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->rPush($key, $value);
    	}

    }
    
    //操作sets，缓存为redis时可以调用本方法
    public static function sAdd($key, $value, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->sAdd($key, $value);
    	}
    }
    
    //操作sets，缓存为redis时可以调用本方法
    public static function sRem($key, $value, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->sRem($key, $value);
    	}
    }
    
    //操作sets，缓存为redis时可以调用本方法 随机返回一个元素，不删除
    public static function sRandMember($key, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->sRandMember($key);
    	}
    }    

    //操作sets，缓存为redis时可以调用本方法 返回所有元素
    public static function sMembers($key, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->sMembers($key);
    	}
    }

    //设置key的timeout
    public static function setTimeout($key, $timeout, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->setTimeout($key, $timeout);
    	}
    }

    //设置key的timeout
    public static function expireAt($key, $timeout, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Redis') //若缓存服务为Redis
    	{
    		$cacheEngine = $engine->getRedisInstance();
    		return $cacheEngine->expireAt($key, time()+$timeout);
    	}
    }
    
    //生成消息，并送到消息队列给后台服务程序处理
    public function sendMsg($type, $accountId, $annotations, $key) {
        //构建消息报文
        $value['type'] = $type;
        $value['accountid'] = $accountId;
        $value['annotation'] = $annotations;      
        $valueJson =  json_encode($value); 
        $this->writeList($key, $valueJson, MESSAGE_QUEUE_NAME);
    } 
    
    //发送弹窗消息
    public function sendPopupMsg($type, $accountId, $annotations, $key, $timeout) {
        //构建消息报文
        $value['type'] = $type;
        $value['accountid'] = $accountId;
        $value['annotation'] = $annotations;      
        $valueJson =  json_encode($value); 
        $this->writeList($key, $valueJson, MESSAGE_QUEUE_NAME);
        //$this->setTimeout($key, $timeout, MESSAGE_QUEUE_NAME);
        $this->expireAt($key, $timeout, MESSAGE_QUEUE_NAME);
        
    } 
    
    //缓存为memcache时可以调用本方法
    public static function add($key, $value, $timeout, $config = 'default') {
    	$engine = Cache::getEngineByConfig($config);//获得缓存对象
    	if ($engine === false)
    		return false;
    	
    	$settings = $engine->settings;
        if ($settings['engine'] == 'Memcache') //若缓存服务为memcache
    	{
    		$cacheEngine = $engine->getInstance();
    		return $cacheEngine->add($key, $value, false, $timeout);
    	}
    	
    }
        
}
