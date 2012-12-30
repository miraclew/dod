<?php
/**
 * 背包中的道具
 * 
 * @property int $id
 * @property int $accountid
 * @property int $itemid
 * @property int $quantity
 * @property int $quantity_init
 * @property datetime $expire_time
 * @property int $get_type
 * @property datetime $modified
 * @property datetime $created
 */
class BagItem extends Model {
    public static $useTable = 'bag_items';
    public static $useDbConfig = 'store';
    
    /**
     * 赠送的礼物的时候 得到背包的物品 列表 同时获得是否够赠送的状态
     * 
     * @param $accountid int 登录用户名
     * @param $type int 种类 1.礼物  2.道具
     * 
     * return array(
     *              Err   //错误代码
     *              data  //查询出来的数据
     *              )
     */
    public static function get_bag_list ($accountid, $type=null) {
        if ( true == empty($accountid) || !is_numeric($accountid) ) {
            return array(Err::$INPUT_FORMAT_INVALID, '');
        }
        if ( ApiConst::ITEM_TYPE_GIFT == $type ) {
            $sql_type = ' AND i.type = '. ApiConst::ITEM_TYPE_GIFT;
        } elseif ( ApiConst::ITEM_TYPE_BONUS == $type ) {
            $sql_type = ' AND i.type = '.ApiConst::ITEM_TYPE_BONUS;
        } else {
            $sql_type = '';
        }
        $result = Item::find(array('conditions' => "item.type = ?",
                                  'fields' => array('item.id', 'item.name', 'item.image', 'item.is_package', 'item.money_type', 'item.money', 'item.discount', 'item.is_on_sale', 'sum(case WHEN `expire_time` < now() or accountid <> ? then 0 else `quantity` end ) as quantity'),
                                  'joins' => array(array('type' => 'left', 'alias' => 'b', 'table' => 'bag_items', 'conditions' => 'b.itemid = item.id')),
                                  'group' => 'item.id'),
                            array($accountid, ApiConst::ITEM_TYPE_GIFT));
        if ( false == is_array($result) ) {
            return array(Err::$FAILED, '');
        } else {
            $data = array();
            $available = 0;
            $delete_key = array();
            //循环以获取 总数量 和 每个item道具自身所需量
            foreach ( $result AS $key => & $value ) {
                if ( true == $value->is_package ) {
                    $item = Item::first(array('conditions' => "item.id = ?",
                                              'fields' => array('p.quantity'),
                                              'joins' => array(array('type' => 'left', 'alias' => 'p', 'table' => 'package_items', 'conditions' => 'p.itemid = item.id'))),
                                        array($value->id));
                    if ( false == $item ) {
                        $quantity = 1;
                    } else {
                        $quantity = $item->quantity;
                    }
                } else {
                    $quantity = 1;
                }
                if ( false == $value->is_on_sale ) {
                    $delete_key[] = $key;
                }
                $available += $value->quantity * $quantity;
                $value->quantity = $quantity;
                $data['items'][$key] = $value->attributes();
            }
            //删除不在售的
            foreach ( $delete_key as $_delete ) {
                unset($data['items'][$_delete]);
            }
            //循环判断自己可用不
            if ( false == empty($data['items']) ) {
                foreach ( $data['items'] AS & $value ) {
                    $value['can_use'] = $value['quantity'] <= $available ? 1 : 0 ;
                    $user = UserProfile::findByPk($accountid);
                    $value['money'] = $value['money'];//floor( $value['money'] * $value['discount'] );
                    if (  ApiConst::MONEY_POINTS == $value['money_type'] ) {
                        if ( $value['money'] > $user->points ) {
                            $value['can_buy'] = 0;
                        } else {
                            $value['can_buy'] = 1;
                        }
                    } elseif ( ApiConst::MONEY_GOLD == $value['money_type'] ) {
                        if ( $value['money'] > $user->golds ) {
                            $value['can_buy'] = 0;
                        } else {
                            $value['can_buy'] = 1;
                        }
                    }
                    unset($value['quantity']);
                    unset($value['is_package']);
                    unset($value['is_on_sale']);
                    //unset($value['discount']);
                }
            }
            $data['items'] = array_values($data['items']);
            $data['available'] = $available;
            //是否买过大礼包 这个暂时设置都买过了
            $data['already_buy_super_package'] = 1;
            return array(Err::$SUCCESS, $data);
        }
    }
    
    /**
     * 
     * 打赏 和 送礼 
     * 两者均是易耗品 获得者不能再送.
     * 1. 循环统计所有鲜花礼物 计算出总数(分两种 A是有过期时间的.B是无过期时间的).
     * 2. 先计算有过期时间的够不够.如果够就只减去有过期时间的.如果不够再去减去无过期时间的.
     * 3. 如果都不够那就 去查看看够不够买 如果能全部买就不看背包的,直接全买.如果都不够就返回金额不足.
     * @param int $from_accountid 赠送人
     * @param int $to_accountid 接受人
     * @param int $roomid 房间ID
     * @param int $talkid 话题ID
     * @param int $itemid 道具ID
     * @param int $quantity 数量
     * @throws ErrRtnException
     *
     * @return array(
     *              Err   //错误代码
     *              data  //查询出来的数据
     *              )
     */
    public static function send_gift ($from_accountid, $to_accountid, $roomid=null, $talkid=null, $itemid, $quantity) {
        if ( true == empty($from_accountid) || !is_numeric($from_accountid) 
            || true == empty($to_accountid) || !is_numeric($to_accountid) 
            || true == empty($itemid) || !is_numeric($itemid) 
            || true == empty($quantity) || !is_numeric($quantity) ) {
            return array(Err::$INPUT_FORMAT_INVALID, '');
        }
        $quantity_old = $quantity;
        $itemid_old = $itemid;
        //判断是房间还是 话题
        if ( null != $talkid ) {
            $type = ItemUsing::TYPE_TALK;
        } elseif( null != $roomid ) {
            $type = ItemUsing::TYPE_ROOM;
        } else {
            $type = ItemUsing::TYPE_USER;
        }
        //查看该道具是组合还是单一的
        $item = Item::findByPk($itemid);
        if ( true == $item->is_package ) {
            $item_child = PackageItem::first(array('conditions' => 'itemid=?','fields' => array('child_itemid', 'quantity')),
                                             array($itemid));
            $itemid = $item_child->child_itemid;
            $quantity = $quantity * $item_child->quantity;
        }
        //调出所有这种道具的信息 遍历查一下够不够
        $result = self::find(array('conditions' => " itemid = ? and accountid =? ", 'fields'    => array('id', 'quantity', 'expire_time'), 'order' => 'expire_time asc'),
                             array($itemid, $from_accountid)
                             );
        if ( false == empty($result) ) {
            $count = 0;
            //有过期时间的 bag ID 的二维数组 其中0位 是需要删除的ID的数组 1位是需要修改的ID一维数组的 (后面循环时无过期时间的也放进去)
            $expire = array();
            //没有过期时间的bag ID的数组
            $noExpire = array();
            
            //校验 数量够不够 1.快过期的够不够 2 如果快过期的不够去看看没有期限的够不够
            self::check_item_quantity ($result, $expire, $noExpire, $count, $from_accountid, $to_accountid, $type, $itemid_old, $quantity_old, $quantity, $roomid, $talkid);
            //如果设置过过期时间+没设置过期时间的 都不够
            if ( $quantity > $count ) {
                //再去看看自己的金币够不够买的 如果都不够直接返回不足
                $result = Item::buy_item($from_accountid, $itemid, $quantity, '');
                if ( false == $result[0] ) {
                    return Err::$TRANS_BALANCE_INSUFFICIENT;
                } else {
                    self::save_itemUsing($from_accountid, $to_accountid, $type, $itemid_old, $quantity_old, $quantity, $roomid, $talkid);
                    return Err::$SUCCESS;
                }
            }
            
            $ds = static::table()->getDataSource();
            $ds->begin();
            try {
                //如果有需要删除的,删除 bag 里面的信息
                if ( false == empty($expire[0]) ) {
                    $remove_result = self::delete_all(' id IN ('.implode(',', $expire[0]).')');
                    if ( $remove_result != count($expire[0]) ) {
                        throw new ErrRtnException(Err::$FAILED);
                    }
                }
                //循环是为了获得 $KEY 和 $VALUE
                foreach ( $expire[1] as $key => $value ) {
                    
                }
                //如果需要修改的有信息
                if ( false == empty($expire[1]) ) {
                    $modify_result = self::update_all(array('quantity'=> $value), "id = ?", array($key));
                    if ( true != $modify_result ) {
                        throw new ErrRtnException(Err::$FAILED);
                    }
                }
                //保存记录到item_using
                $ItemUsing_result = self::save_itemUsing($from_accountid, $to_accountid, $type, $itemid_old, $quantity_old, $quantity, $roomid, $talkid);
                if ( false == $ItemUsing_result ) {
                    throw new ErrRtnException(Err::$FAILED);
                }
                $ds->commit();
                return Err::$SUCCESS;
            } catch ( ErrRtnException $e ) {
                $ds->rollback();
                Log::write($e->getMessage());
                return Err::$FAILED;
            }
        } else {
            //再去看看自己的金币够不够买的 如果都不够直接返回不足
            $result = Item::buy_item($from_accountid, $itemid, $quantity, '');
            if ( false == $result[0] ) {
                return Err::$TRANS_BALANCE_INSUFFICIENT;
            } else {
                self::save_itemUsing($from_accountid, $to_accountid, $type, $itemid_old, $quantity_old, $quantity, $roomid, $talkid);
                return Err::$SUCCESS;
            }
        }
    }
    
    /**
     * 写入道具使用记录表
     * @param int $from_accountid
     * @param int $to_accountid
     * @param int $type
     * @param int $itemid
     * @param int $quantity_old
     * @param int $quantity
     * @param int $roomid
     * @param int $talkid
     *
     * @return
     */
    private static function save_itemUsing ($from_accountid, $to_accountid, $type, $itemid, $quantity_old, $quantity, $roomid=null, $talkid=null) {
        $ItemUsing = new ItemUsing();
        $ItemUsing->from_accountid = $from_accountid;
        $ItemUsing->to_accountid = $to_accountid;
        $ItemUsing->type = $type;
        $ItemUsing->itemid = $itemid;
        $ItemUsing->quantity = $quantity_old;
        $ItemUsing->total_quantity = $quantity;
        $ItemUsing->roomid = $roomid;
        $ItemUsing->talkid = $talkid;
        
        return $ItemUsing->save();
    }
    
    /**
     * 校验背包的道具够不够送
     */
    private static function check_item_quantity (&$result, &$expire, &$noExpire, &$count, $from_accountid, $to_accountid, $type, $itemid_old, $quantity_old, $quantity, $roomid, $talkid) {
        $time = time();
        foreach ( $result as $value ) {
            $item = $value->attributes();
            //没有过期的之和 不足以支付
            if ( null != $item['expire_time'] && strtotime($item['expire_time']) > $time ) {
                $count += $item['quantity'];
                if ( $count > $quantity ) {
                    //如果加上这个正好超标 那么这个 需要修改
                    $expire[1][$item['id']] = $item['quantity'] - ( $quantity - ( $count - $item['quantity'] ) );
                    break;
                } elseif ( $count == $quantity ) {
                    //如果加上这个正好相等  那么这个也需要删除
                    $expire[0][] = $item['id'];
                    break;
                } else {
                    //如果加上这个还不够 那么接着循环下去 同时自己也要删除的
                    $expire[0][] = $item['id'];
                }
            } elseif ( null == $item['expire_time'] ) {
                //先把没设置过期时间的 保存起来 等下好循环利用
                $noExpire[$item['id']] = $item['quantity'];
            }
        }
        //有过期时间的不够
        if ( $count < $quantity ) {
            //如果设置过 过期的都加了还不够 只能再去加没有过期之前的
            foreach ( $noExpire as $key => $item ) {
                $count += $item;
                if ( $count > $quantity ) {
                    //如果加上这个正好超标 那么这个 需要修改
                    $expire[1][$key] = $item - ( $quantity - ( $count - $item ) );
                    break;
                } elseif ( $count == $quantity ) {
                    //如果加上这个正好相等  那么这个也需要删除
                    $expire[0][] = $key;
                    break;
                } else {
                    //如果加上这个还不够 那么接着循环下去 同时自己也要删除的
                    $expire[0][] = $key;
                }
            }
        }
    }
 }
