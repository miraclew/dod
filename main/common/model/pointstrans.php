<?php
/**
 * 用户分贝交易流水
 *
 * @property int $id
 * @property int $accountid
 * @property int $type
 * @property int $amount
 * @property int $balance
 * @property int $objectid
 * @property int $add_experience
 * @property string $description
 * @property datetime $created
 * 
 */
class PointsTrans extends Model {
    public static $useTable = 'points_trans';
    public static $useDbConfig = 'store';
    
    /* 交易类型 */    
    // 购买
    // 房间交易
    const PT_ROOM_CREATE			= 1; // 购买
    const PT_ROOM_TAX 				= 2; // 系统服务费
    const PT_ROOM_OWNER_PAY			= 3; // 房主发奖
    const PT_ROOM_SYSTEM_PAY		= 4; // 系统发奖
    const PT_ROOM_SYSTEM_WITHDRAW 	= 5; // 系统收回
    
    // 系统奖励
    const PT_LOGIN_REWARD 			= 11; // 登录奖励
    const PT_ACCOUNT_BIND 			= 12; // 账户绑定	
    const PT_ADMIN_GIVE 			= 13; // 后台加分贝
    const PT_LISTEN_REWARD 			= 14; // 收听奖励
    
    // 返还
    const PT_ROOM_LOCK_REFUND 		= 21;	// 房间锁定
    const PT_ROOM_DELETE_REFUND 	= 22;	// 房间删除
    const PT_ROOM_NO_TALK_REFUND 	= 23; 	// 房间没有作品退回房主
    
    // 购买
    const PT_BUY_VIP 				= 31; // 购买vip
    const PT_BUY_ITEM 				= 32;	// 购买道具
    
    // 道具，礼物
    const PT_GIVE_ITEM 				= 41;
    const PT_RECV_ITEM 				= 42;
    
    // 其他
    const PT_EXTRA_REWARD 			= 51; // 系统额外奖励
    const PT_POINTS_BUY 				= 52; // 金币购买
}
