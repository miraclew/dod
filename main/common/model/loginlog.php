<?php
/**
 * 登录 记录表
 * 
 * @property int $id
 * @property int $accountid
 * @property int $type
 * @property string $udid
 * @property string $channelid
 * @property string $equipmentid
 * @property string $applicationversion
 * @property string $cellbrand
 * @property string $cellmodel
 * @property string $systemversion
 * @property string $ip
 * @property datetime $created
 */
class LoginLog extends Model {
    public static $useTable = 'login_logs';
    public static $useDbConfig = 'log';
    
    const LOG_TYPE_1 = 1;//登录
    const LOG_TYPE_2 = 2;//登出
}