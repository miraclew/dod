<?php
/**
 * 举报
 * 
 * @property int $id
 * @property int $reporter_id
 * @property int $owner_id
 * @property int $type
 * @property int $objectid
 * @property int $reason
 * @property int $voice_fid
 * @property string $content
 * @property int $status
 * @property datetime $created
 * 
 */
class Report extends Model {
    public static $useTable = 'reports';
    public static $useDbConfig = 'system';
    
    // 状态 0: 提交 1: 隐藏 3: 取消隐藏 4: 忽略
    const STATUS_SUBMIT 		= 0;
    const STATUS_HIDE 			= 1;
    const STATUS_CANCEL_HIDE	= 2;
	const STATUS_INGORE 		= 3;
	
	const TYPE_ROOM 			= 1;
	const TYPE_TALK 			= 2;
	const TYPE_COMMENT 			= 3;

	public function process($action) {
		if ($action == 1) {
			
		}
		else if($action == 2) {
			
		}
	}
}
