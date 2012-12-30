<?php
/**
 * 用户相册
 * @property int $id
 * @property int $accountid
 * @property string $photo
 * @property string $voice
 * @property int $voice_time
 * @property datetime $created
 */
class Photo extends Model {
    public static $useTable = 'photos';
    public static $useDbConfig = 'user';
    
}
