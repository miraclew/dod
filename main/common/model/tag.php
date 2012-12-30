<?php
/**
 * 标签
 * @property int $id
 * @property string $name
 * @property int $count1 打上标签的内容数
 * @property int $count2 点击标签进行搜索次数
 * @property int $type
 * @property datetime $created
 */
class Tag extends Model {
    public static $useTable = 'tags';
    public static $useDbConfig = 'room';
    
    const TYPE_ROOM_TAG = 1;
    
}
