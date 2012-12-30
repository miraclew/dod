<?php
/**
 * 用户金币交易流水
 * 
 * @property int $id
 * @property int $accountid
 * @property int $type
 * @property int $amount
 * @property int $balance
 * @property int $objectid
 * @property string $description
 * @property int $pay_method
 * @property string $transaction_id
 * @property string $product_id
 * @property datetime $created
 */
class GoldTrans extends Model {
    public static $useTable = 'gold_trans';
    public static $useDbConfig = 'store';
    
    /* 交易类型 */
    // 房间交易 (+)
    const GT_BUY_GOLD 				= 1; // 购买元宝
    
    // 房间交易 (-)
    const GT_BUY_POINTS				= 2; // 购买金币
    const GT_BUY_ITEM 				= 3; // 购买道具    
    
}
