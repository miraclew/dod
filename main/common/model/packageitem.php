<?php
/**
 * 房间表
 * 
 * @property int $id
 * @property int $itemid
 * @property int $child_itemid
 * @property string $quantity
 * @property datetime $created
 */
class PackageItem extends Model {
    public static $useTable = 'package_items';
    public static $useDbConfig = 'store';
}
