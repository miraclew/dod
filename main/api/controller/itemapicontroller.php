<?php
use Predis\Pipeline\FireAndForgetExecutor;
class ItemApiController extends ApiController {
    
    /**
     * 获得背包的列表
     *
     * @return
     */
    public function www_bag() {
        $accountid = Auth::user('accountid');
        //调用获取背包的列表
        $result = BagItem::query(array(
        		'conditions' => 'accountid = ?',
        		'fields' => array('bagitem.id', 'i.id AS itemid', 'i.name','i.type as item_type', 'bagitem.quantity', 'i.image', 'i.money_type', 'p.child_itemid', 'p.quantity as p_quantity', 'i.description'),
        		'joins' => array(
        				array('type' => 'left', 'alias' => 'i', 'table' => 'items', 'conditions' => 'bagitem.itemid = i.id'),
        				array('type' => 'left', 'alias' => 'p', 'table' => 'package_items', 'conditions' => 'p.itemid = bagitem.itemid')
        				),
        		'order' => 'i.is_package asc, i.id asc'
        		),
        		array($accountid)
        	);
        
//         debug($result);
        
        $data = array('items'=>array());
        $itemid_arr = array();
        foreach ( $result as $item ) { /* @var $value BagItem */
        	if($item['item_type'] == ApiConst::ITEM_TYPE_VIP || $item['item_type'] == ApiConst::ITEM_TYPE_SPEAKER) {
        		$item['can_use'] = 1;
        	}
        	else {
        		$item['can_use'] = 0;
        	}
        	//查看子背包
        	$type_num = 0;
        	if ( false == empty($item['child_itemid']) ) {
        	    $type_num += 1;
        	}
            if ( true == in_array($item['child_itemid'], $itemid_arr) || true == in_array($item['itemid'], $itemid_arr) ) {
        	    $type_num += 2;
        	}
        	switch ( $type_num ) {
        	    case 0 : //假如包里的这个道具 既不是组合道具 而且不在道具数组里面
        	        $data['items'][$item['itemid']] = $item;
        	        $itemid_arr[] = $item['itemid'];
        	        break;
        	    case 1 : //假如包里的这个道具  是组合道具 但是子道具不在道具数组里面
        	        $data['items'][$item['child_itemid']]['quantity'] = $item['p_quantity'] * $item['quantity'];
        	        $itemid_arr[] = $item['child_itemid'];
        	        break;
        	    case 2 : //假如包里的这个道具  不是组合道具 但是已经在道具数组里面
        	        $data['items'][$item['itemid']]['quantity'] += $item['quantity'];
        	        break;
        	    case 3 : //假如包里的这个道具 既是组合道具 而且子道具也在道具数组里面
        	        $data['items'][$item['child_itemid']]['quantity'] += $item['p_quantity'] * $item['quantity'];
        	        break;
        	    default;
        	}
        }
        $data['items'] = array_values($data['items']);
        $this->success($data);        
    }
    
    /**
     * 领取礼物
     *
     * @return
     */
    public function www_pickup () {
        //带领道具的ID
        $pengdingitemid = $this->_getParam('pengdingitemid', '', true);
        //登录用户的ID
        $acccountid = Auth::user('accountid');
        if ( true == is_numeric($pengdingitemid) ) {
            $ds = PendingItem::table()->getDataSource();
            $ds->begin();
            try {
                $pengdingitem = PendingItem::first(array('conditions' => "id = ? AND accountid = ? AND is_pickup = 0 AND expire_time > '".date('Y-m-d H:i:s', time())."'"), array($pengdingitemid,$acccountid));
                if ( null == $pengdingitem ) {
                    throw new ErrRtnException(Err::$DATA_NOT_FOUND);
                }
                $pengdingitem->is_pickup = 1;
                $pengdingitem->save() ;
                if ( false == $pengdingitem->save() ) {
                    throw new ErrRtnException(Err::$FAILED);
                }
                $bagitem = new BagItem();
                $bagitem->accountid = $acccountid;
                $bagitem->itemid = $pengdingitem->itemid;
                $bagitem->quantity = $pengdingitem->quantity;
                $bagitem->quantity_init = $pengdingitem->quantity;
                $bagitem->expire_time = $pengdingitem->expire_time;
            //$bagitem->pickup_time = date('Y-m-d H:i:s', time());
                $bagitem->get_type = 2;
                if ( false == $bagitem->save() ) {
                    throw new ErrRtnException(Err::$FAILED);
                }
                $ds->commit();
                $this->_respond(Err::$SUCCESS);
            } catch ( ErrRtnException $e ) {
                $ds->rollback();
                Log::write($e->getMessage());
                $this->_respond(Err::$FAILED);
            }
        } else {
            $this->_respond(Err::$INPUT_FORMAT_INVALID);
        }
    }
    
    /**
     * 选择礼物
     *
     * @return
     */
    public function www_select_gift() {
        //当前登录用户
        $accountid = Auth::user('accountid');
        //选择自己的背包列表
        $result = BagItem::get_bag_list($accountid, ApiConst::ITEM_TYPE_GIFT);
        $this->_respond($result[0], $result[1]);
    }
    
    /**
     * 选择捧场道具
     *
     * @return
     */
    public function www_select_bonus() {
        //当前登录用户
        $accountid = Auth::user('accountid');
        //选择自己的背包列表
        $result = BagItem::get_bag_list($accountid, ApiConst::ITEM_TYPE_BONUS);
        $this->_respond($result[0], $result[1]);
    }
    
    /**
     * 打赏，送礼品
     *
     * @return
     */
    public function www_give_gift () {
        //获得参量: 发送人ID,获得人ID,话题的ID,道具的ID,数量
        $from_accountid = Auth::user('accountid');
        $to_accountid   = $this->_getParam('accountid', '', true);
        $talkid         = $this->_getParam('talkid', '', true);
        $itemid         = $this->_getParam('itemid', '', true);
        $quantity       = intval($this->_getParam('quantity', 1, true));
        
        if($from_accountid == $to_accountid) $this->failed(Err::$OPERATE_NOT_PERMIT_ON_SELF);       
        if(empty($to_accountid) || empty($itemid)) $this->failed(Err::$INPUT_REQUIRED);
        if ($quantity <= 0) {
        	$this->failed(Err::$INPUT_INVALID);
		}
        
        $roomid = 0;
        if(!empty($talkid)) {
        	$talk = Talk::findByPk($talkid); /* @var $talk Talk */
        	if(!$talk) {
        		$this->failed(Err::$DATA_NOT_FOUND);
        	}
        	$roomid = $talk->roomid;
        	if($talk->accountid != $to_accountid) $this->failed(Err::$OPERATE_NOT_PERMIT);
        }
        
        $result = BagItem::send_gift ($from_accountid, $to_accountid, $roomid, $talkid, $itemid, $quantity);
        
        if ($result == Err::$SUCCESS) {
	        $item = Item::findByPk($itemid); /* @var $item Item */
	        $p1_add = $item->p1_add * $quantity; // 赠送者
	        $p2_add = $item->p2_add * $quantity; // 收礼者
	        
	        $this->success(array('p1_pop_value_incr'=>$p1_add,'p2_pop_value_incr'=>$p2_add));        
        }
        else {
        	$this->failed($result);
        }
    }
    
    /**
     * 感谢礼物
     */
    public function www_thank_gift() {
    	$accountid = Auth::user('accountid');
    	$id = $this->_getParam('id', 0, true);
    	
    	$iu = ItemUsing::findByPk($id); /* @var $iu ItemUsing */
    	if(!$iu) $this->failed(Err::$DATA_NOT_FOUND);
    	if($accountid != $iu->to_accountid) $this->failed(Err::$OPERATE_NOT_PERMIT);

    	// 检查是否已经感谢过
    	if(SystemMessage::exsit("sub_type=? and objectid=?", array(ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_GIFT_THANKS, $id)))
    		$this->failed(Err::$OPERATE_ALREADY_DONE);

    	// 修改新礼物消息应答状态
    	$sm = SystemMessage::first(array("conditions" => "sub_type=? and objectid=?"), array(ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_GIFT, $id)); 
    	if($sm) {
    		$sm->ack_status = SystemMessage::ACK_STATUS_1;
    	}
    	else {
    		Log::writeError(__FILE__.' '.__LINE__. "thank_gift error: accountid=$accountid, id=$id");
    	}
    	$sm->save();
    	
    	$iu->status = ItemUsing::STATUS_THANK;
    	$iu->save();
    	
    	// 生成感谢消息
//     	$sm2 = new SystemMessage();
//     	$sm2->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
//     	$sm2->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_GIFT_THANKS;
//     	$sm2->fromid= $accountid;
//     	$sm2->toid = $iu->from_accountid;
//     	$sm2->objectid = $id;
//     	$sm2->ack_status = SystemMessage::ACK_STATUS_NONE;
//     	$sm2->save();

    	$this->success();
    }
    
    /**
     * 捧场道具
     *
     * @return
     */
    public function www_give_bonus () {
        //获得参量: 发送人ID,房间的ID,道具的ID,数量
        $from_accountid = Auth::user('accountid');
        $roomid         = $this->_getParam('roomid', '', true);
        $itemid         = $this->_getParam('itemid', '', true);
        $quantity       = $this->_getParam('quantity', '', true);
        if ( false == empty($roomid) && is_numeric($roomid) 
             && false == empty($itemid) && is_numeric($itemid) 
             && false == empty($quantity) && is_numeric($quantity) 
        ) {
            $room       = Room::findByPk($roomid);
            if ( false == is_object($room) ) {
                return $this->_respond(Err::$INPUT_FORMAT_INVALID);
            } else {
                //转增函数
                $result = BagItem::send_gift ($from_accountid, $room->accountid, $roomid, '', $itemid, $quantity);
                $this->_respond($result);
            }
        } else {
            $this->_respond(Err::$INPUT_FORMAT_INVALID);
        }
    }
    
    /**
     * 道具商店
     *
     * @return
     */
    public function www_item_store () {
        //获得所有的 在售上架的商品
        $result = Item::find(array('conditions' => ' is_on_sale = 1',
                                   'fields'     => array('id', 'name', 'image', 'type', 'money_type', 'money', 'description', 'discount','voice','voice_time'),
                                    )
                            );
        if ( null == $result ) {
            $this->_respond(Err::$DATA_NOT_FOUND);
        } else {
            $data = array();
            foreach ( $result as $value ) {
                $item = $value->attributes();
                unset($item['type']);
                $data['items'][] = $item;
            }
            $this->_respond(Err::$SUCCESS, $data);
        }
    }
    
    /**
     * 元宝商店
     *
     * @return
     */
    public function www_gold_store () {
        //获得所有的 在售上架的商品
        $result = GoldStore::find(array('conditions' => ' is_on_sale = 1',
                                        'fields'     => array('id', 'name', 'image', 'description', 'discount', 'product_id', 'price as money'),
                                        )
                                  );
        if ( null == $result ) {
            $this->_respond(Err::$DATA_NOT_FOUND);
        } else {
            $data = array();
            foreach ( $result as $value ) {
                $item = $value->attributes();
                $data['items'][] = $item;
            }
            $this->_respond(Err::$SUCCESS, $data);
        }
    }
    
    /**
     * 购买道具
     *
     * @return
     */
    public function www_buy_item () {
        $accountid = Auth::user('accountid');
        $itemid = $this->_getParam('itemid');
        $quantity = $this->_getParam('quantity');
        
        $result = Item::buy_item($accountid, $itemid, $quantity);
        $this->_respond($result[0], $result[1]);
    }
    
    /**
     * 使用背包中的功能道具
     */
    public function www_use_item() {
    	$accountid = Auth::user('accountid');
    	$id = $this->_getParam('id');

    	$bagitem = BagItem::findByPk($id); /* @var $bagitem BagItem */
    	if(!$bagitem) $this->failed(Err::$DATA_NOT_FOUND);
    	if ($bagitem->accountid != $accountid) {
    		$this->failed(Err::$OPERATE_OWNNER_ONLY);
    	}
    	
    	$base = time();
    	$item = Item::findByPk($bagitem->itemid); /* @var $item Item */
    	if ($item->type == ApiConst::ITEM_TYPE_VIP) {
    		$vip = intval($item->sub_type) - intval($item->type)*100;
    		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
    		if ($profile->vip > 0) {
    			$old = strtotime($profile->vip_expire_time);
    			if($old>$base) $base = $old; 
    			
    			$vip_expire_time = $base + intval($item->vip_days)*86400;
    			
    			$profile->vip_expire_time = date('Y-m-d H:i:s', $vip_expire_time);
    		}
    		else {
    			$profile->vip = $vip;
    			
    			$vip_expire_time = $base + intval($item->vip_days)*86400;
    			$profile->vip_expire_time = date('Y-m-d H:i:s', $vip_expire_time);
    		}
    		
    		$profile->save();
    		
			$bagitem->quantity--;
    		if ($bagitem->quantity <= 0) {
    			$bagitem->destroy();
    		}
    		else
    			$bagitem->save();
    		
    		$this->success();
    	}
    	else {
    		$this->failed(Err::$FAILED);
    	}
    }
    
    public function www_app_store_buy() {
    	$accountId = Auth::user('accountid');
    	$json = $this->_getParam('receipt', '');
    	$receipt = json_decode($json, true);
    	
    	$curl_handle=curl_init();
    	curl_setopt($curl_handle, CURLOPT_URL, APPLE_CREDIT_PRODUCTION_URL);
    	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl_handle, CURLOPT_HEADER, 0);
    	curl_setopt($curl_handle, CURLOPT_POST, true);
    	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $json);
    	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
    	$response_json = curl_exec($curl_handle);
    	$response = json_decode($response_json, true);
    	
    	curl_close($curl_handle);
    	Log::writeFile("end accountid: $accountId response: $response_json", 'buygold');
    	if ($response && $response['status'] == 21007) { //连正式服务失败，则重连测试服务器
    		$curl_handle=curl_init();
    		curl_setopt($curl_handle, CURLOPT_URL, APPLE_CREDIT_DEVELOPMENT_URL);
    		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($curl_handle, CURLOPT_HEADER, 0);
    		curl_setopt($curl_handle, CURLOPT_POST, true);
    		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $json);
    		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
    		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
    		$response_json = curl_exec($curl_handle);
    		$response = json_decode($response_json, true);
    			
    		curl_close($curl_handle);
    		Log::writeFile("sandbox:end accountid: $accountId response: $response_json", 'buygold');
    	}
    	
    	if($response && $response['status'] == 0){
    		$productId = $response['receipt']['product_id'];
    		$transactionId = $response['receipt']['transaction_id'];
    		$uniqueIdentifier = $response['receipt']['unique_identifier'];
    		
    		// check transaction
    		if(GoldTrans::exsit("transaction_id=?",array($transactionId)))
    			$this->failed(Err::$TRANS_OPERATE_FAILED);
    		// check product 
    		$gold = GoldStore::first(array("conditions"=>"product_id=?"),array($productId)); /* @var $gold GoldStore */
    		if(!$gold) $this->failed(Err::$DATA_NOT_FOUND);
    	
  			if(GoldTrans::exsit("transaction_id=?", array($transactionId))) {
   				$this->failed(Err::$TRANS_OPERATE_INVALID);
   			}   			
   			   			
    		// TODO 加锁处理，防止并发问题
   			$profile = UserProfile::findByPk($accountId); /* @var $profile UserProfile */
   			$profile->golds += $gold->quantity;
   			$profile->save();

   			$gt = new GoldTrans();
   			$gt->accountid = $accountId;
   			$gt->type = GoldTrans::GT_BUY_GOLD;
   			$gt->amount = $gold->quantity;
   			$gt->balance = $profile->golds;
   			$gt->objectid = $gold->id;
   			$gt->pay_method = ApiConst::PAYMENT_APPLE;
   			$gt->transaction_id = $transactionId;
   			$gt->product_id = $gold->product_id;
   			$gt->save();
   			
    		return $this->_respond(Err::$SUCCESS, array('golds' => $profile->golds));
    	}
    	else {
    		Log::writeFile("end accountid: $accountId error: $response_json", 'buygold');
    		return $this->_respond(Err::$FAIL);
    	}
    }
}