<?php

/**
 * 
 * 黑名单
 * @author Administrator
 *
 */
class BlackApiController  extends ApiController {

    /**
     * 黑名单列表
     * @param 
     *
     * @return
     */
    public function www_list() {
        //调取黑名单的model 查询find函数
        $result                    = BlackList::find();
        if ( false == is_array($result) ) {
            $this->_respond(Err::$FAILED);
        } else {
            $data             = array();
            foreach ($result as $value) {
                $data['items'][] = $value->attributes();
            }
            $this->_respond(Err::$SUCCESS, $data);
        }
    }
    
    public function www_show() {
        echo __FUNCTION__;
    }
    
    /**
     * 添加黑名单
     * 直接调用新建收藏语句
     */
    public function www_create() {
        //插入到数组
        $result        = BlackList::_favorite($this->_getParam('accountid'));
        $this->_respond($result);
    }
    
    public function www_destroy() {
        echo __FUNCTION__;
    }    
    
}