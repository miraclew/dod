<?php
/**
 * 道具表
 * 
 * @property int $id
 * @property string $name
 * @property int $type
 * @property int $sub_type
 * @property string $image
 * @property string $voice
 * @property string $short_description
 * @property string $description
 * @property int $money_type
 * @property int $money
 * @property int $p1_add_type
 * @property int $p1_add
 * @property int $p2_add_type
 * @property int $p2_add
 * @property int $vip_days
 * @property int $duration
 * @property boolean $is_package
 * @property boolean $is_on_sale
 * @property int $discount
 * @property int $show_order
 * @property int $status
 * @property datetime $created
 * 
 */
class Item extends Model {
    public static $useTable = 'items';
    public static $useDbConfig = 'store';
    
    /**
     * 购买道具
     * @param int $accountid 购买者id
     * @param int $itemid 道具id
     * @param int $quantity 数量
     * @param int $buy_type 是否在背包中保存
     */
    public static function buy_item ($accountid, $itemid, $quantity, $buy_type=true) {
        $profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
        $item = Item::findByPk($itemid); /* @var $item Item */

        if(!$profile || !$item) $this->failed(Err::$DATA_NOT_FOUND);

        $money = $item->money * $quantity;//floor($item->money * $quantity * $item->discount);
        // check money
      	if( ($item->money_type == ApiConst::MONEY_POINTS && $money>$profile->points) || ($item->money_type == ApiConst::MONEY_GOLD && $money>$profile->golds) ) {
      	    return array(Err::$TRANS_BALANCE_INSUFFICIENT, '');
      	}
      	
        $ds = Item::table()->getDataSource();
		$ds->begin();
		try {
			// 处理余额，交易记录
			$description = '购买道具'.$item->name.',单价:'.$item->money.',数量:'.$quantity.',总价:'.$money;
			if($item->money_type == ApiConst::MONEY_POINTS) {
				$profile->points -= $money;
				
				$points = new PointsTrans();
				$points->accountid = $accountid;
				$points->type = PointsTrans::PT_BUY_ITEM;
				$points->amount = 0-$money;
				$points->balance = $profile->points;
				$points->description = $description;
				if (!$points->save() ) {
					return array(Err::$DATA_SAVE_ERROR, '');
				}
							
				$profile->save();
			} elseif ($item->money_type == ApiConst::MONEY_GOLD) {
				$profile->golds -= $money;
				
				$gold = new GoldTrans();
				$gold->accountid = $accountid;
				$gold->type = GoldTrans::GT_BUY_ITEM;
				$gold->amount = 0-$money;
				$gold->balance = $profile->golds;
				$gold->description = $description;
				$gold->pay_method = ApiConst::PAYMENT_APPLE;
				if (!$gold->save() ) {
					return array(Err::$DATA_SAVE_ERROR, '');
				}
				$profile->save();
			}
            
			//如果是在赠送的时候道具不够 需要直接购买送 就不用放入背包了
			if ( true == $buy_type ) {
    			// 物品加入包裹中
    			if(!$item->is_package) {
    				$bag = new BagItem();
    				$bag->accountid = $accountid;
    				$bag->itemid = $item->id;
    				$bag->quantity = $quantity;
    				$bag->quantity_init = $quantity;
    				$bag->get_type = ApiConst::ITEM_GET_TYPE_BUY;
    				$bag->save();
    			}
    			else {
    				//如果是组合道具  需要找到最根本的道具进行分解后
    				$item_child = PackageItem::find(array('conditions' => 'itemid=?'),array($item->id));
    				if (!$item_child) {
    					throw new ErrRtnException(Err::$FAILED);
    				}
    				foreach ( $item_child as $_child ) {
    					$bag = new BagItem();
    					$bag->accountid = $accountid;
    					$bag->itemid = $_child->child_itemid;
    					$bag->quantity = $quantity * $_child->quantity;
    					$bag->quantity_init = $bag->quantity;
    					$bag->get_type = ApiConst::ITEM_GET_TYPE_BUY;
    					$bag->save();
    				}
    			}
			}
			
			$ds->commit();
			return array(Err::$SUCCESS, array('points'=>$profile->points, 'golds'=>$profile->golds));
		} 
		catch ( ErrRtnException $e ) {
			$ds->rollback();
			Log::write($e->getMessage());
			return array(Err::$FAILED, '');
		}
    }
}
