<?php
/**
 * 任务完成
 * 
 * @property int $id
 * @property int $accountid
 * @property int $type
 * @property int $taskid
 * @property int $is_pickup
 * @property datetime $created
 *
 */
class TaskAccomplish extends Model {
    public static $useTable = 'task_accomplish';
    public static $useDbConfig = 'user';
    
    
}
