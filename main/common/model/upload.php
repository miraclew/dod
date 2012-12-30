<?php
/**
 * 文件上传
 * @property int $fid
 * @property int $type
 * @property int $ftype
 * @property int $accountid
 * @property int $objectid
 * @property string $url
 * @property string $annotations
 * @property datetime $created
 *
 */
class Upload extends Model {
    public static $useTable = 'uploads';
    public static $useDbConfig = 'system';
    
    // 上传业务类型
    const TYPE_ROOM_VOICE 		= 1;
    const TYPE_TALK_VOICE 		= 2;
    const TYPE_COMMENT_VOICE	= 3;
    
    // 文件类型
    const FTYPE_VOICE 			= 1;
    const FTYPE_IMAGE			= 2;
    
    const MAX_VOICE_DURATION 	= 120;
    const MAX_IMAGE_SIZE 		= 2097152; // 2M
    const MAX_VOICE_SIZE 		= 2097152; // 2M
    
    public $file = null;
    public $length = 0;
    
//     public function save($validate=true) {
//     	if($this->accountid==null || $this->file==null) return false;
    	 
//     	if($this->ftype == self::FTYPE_VOICE) {
//     		$uploader = new Uploader();
//     		$this->url = $uploader->uploadVoice($this->file, $this->length);
//     		if($this->url === false) 
//     			return false;
    		 
// 			return parent::save($validate);
//     	}
//     	else if($this->ftype == self::FTYPE_IMAGE) {
//     		$uploader = new Uploader();
//     		$this->url = $uploader->uploadImage($this->file);
//     		if($this->url === false)
//     			return false;
    		
//     		return true;
//     	}
//     	else {
//     		return false;
//     	}
//     }
}

class Uploader {
	static $ALLOW_IMAGE_EXTS = array('jpg','png','bmp');
	
	/**
	 * 文件上传错误
	 * ApiConst中定义的上传错误代码
	 * @var int
	 */
	public $result = 0;
	
	/**
	 * 错误代码 ($_FILES 中的 error)
	 * @var int
	 */
	public $uploadErr = 0;
	
	/**
	 * 上传语音
	 * @param $_FILES 数组 $file
	 * @param int $voice_time
	 * @return boolean|string 成功返回文件URL,失败返回false
	 */
	public function uploadVoice($file, $voice_time) {
		if(!$this->validate($file, self::MAX_VOICE_SIZE)) return false;
	
		$dir = VOICE_URL_RELATIVE_.date('y/m/d/', time());
		$filename = 'v_'.md5(uniqid(rand(), true)) . strrchr($file['name'], '.');
	
		if(!$this->move($file, VOICE_STORAGE_ROOT_.$dir, $filename))
			return false;
	
		return HTTP_PATH.$dir.$filename;
	}
	
	/**
	 * 上传图片
	 * @param $_FILES 数组 $file
	 * @return boolean|string 成功返回文件URL,失败返回false
	 */
	public function uploadImage($file) {
		if(!$this->validate($file, self::MAX_IMAGE_SIZE)) return false;
	
		$dir = IMG_URL_RELATIVE_.date('y/m/d/', time());
		$filename = 'p_'.md5(uniqid(rand(), true)) . strrchr($file['name'], '.');
	
		if(!$this->move($file, IMG_STORAGE_ROOT_.$dir, $filename))
			return false;
	
		return HTTP_PATH.$dir.$filename;
	}
	
	private function validate($file, $maxsize) {
		$this->uploadErr = $file['error'];
		$this->result = 0;
	
		if($file['error'] != 0) {
			$this->result = Err::$UPLOAD_FAILED;
			return false;
		}
		else if($file['size'] >$maxsize) {
			$this->result = Err::$UPLOAD_MAX_SIZE_EXCEED;
			return false;
		}
		else if(!$this->checkExtension($file)){
			$this->result = Err::$UPLOAD_EXT_NOT_SUPPORT;
			return false;
		}
	
		return true;
	}
	
	private function checkExtension($file) {
		return true;
	}
	
	private function move($file, $dir, $filename) {
		if (!File::ensureDir($dir)) {
			return false;
		}
	
		// 		if ($file['error'] == 0 && is_uploaded_file($file['tmp_name'])) {
		// 			if (!move_uploaded_file($file['tmp_name'], $dir.DS.$filename)) {
		// 				return false;
		// 			}
		// 		}
	
		return move_uploaded_file($file['tmp_name'], $dir.DS.$filename);
	}
}