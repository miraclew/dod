<?php
/**
 * 语音计数
 *	
 */
class VoiceCounter {
	// 语音所有者
	const CK_VoiceOwner_S				= 'voiceowner:%s';
	
	/**
	 * 用户收听语音
	 * @param int $accountId
	 * @param string $voiceId
	 * @param int $duration
	 * 
	 * @return 本次收听导致时长增加值 
	 */
	public static function listen($accountId, $voiceId, $duration) {		
		$vl = new VoiceListen($voiceId);// 加总计数
		$vl->listen($duration);
		
		$uvl = new UserVoiceLisen($accountId, $voiceId); // 加用户收听
		$added = $uvl->incrListenDuration($duration);
		
		return $added;
	}
	
	public static function submit($accountId, $voiceId) {
		$key = $this->k(self::CK_VoiceOwner_S, $voiceId);
		$this->redis->set($key, $accountId);
	}
	
	public static function getOwner($voiceId) {
		$key = $this->k(self::CK_VoiceOwner, $voiceId);
		return $this->redis->get($key);
	}
	
	public static function getVoiceListenCount($voiceId) {
		$vl = new VoiceListen($voiceId);
		return $vl->listencount;
	}
	
	public static function getVoiceListenDuration($voiceId) {
		$vl = new VoiceListen($voiceId);
		return $vl->listenduration;		
	}
}

// 语音收听总数
class VoiceListen extends HashCounterModel {	
	public function __construct($id) {
		parent::__construct($id);
	}
	
	protected $defaultData = array(
		'listencount' => 0,
		'listenduration' => 0,
	);
	
	public function listen($duration) {
		$this->listencount++;
		$this->listenduration += $duration;
	}	
}

/**
 * 用户收听语音计数
 * 
 * 用户增加的倾听力和语音收听总计数都使用10分钟防刷机制
 */
class UserVoiceLisen extends RedisModel {	
	const UserListenExpire			= 600; // 过期时间: 10 分钟
	const CK_VoiceUserListen_SD		= 'voiceuserlisten:%s:%d';
	
	public function __construct($accountId, $voiceId) {
		parent::__construct();
		$this->accountId = $accountId;
		$this->voiceId = $voiceId;
		$this->key = $this->k(self::CK_VoiceUserListen_SD, $this->voiceId, $this->accountId);
		$this->value = $this->redis->Get($this->key);
		if($this->value === false) { //key 不存在时, 设置默认值
			$this->value = 0;
			$this->redis->set($this->key, $this->value);
			$this->redis->expire($this->key, self::UserListenExpire);
		}
	}
	
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * 增加语音收听时长
	 * @param int $duration 收听时长
	 * @return number 实际计入系统的时长
	 */
	public function incrListenDuration($duration) {
		$added = 0;
		if($duration > $this->value) {
			$added = $duration - $this->value;
			$this->value = $duration;
			$this->redis->incr($this->key, $added);			 
		}
		return $added;
	}
}

