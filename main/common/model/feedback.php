<?php
// 用户反馈
class Feedback extends Model {
    public static $useTable = 'feedbacks';
    public static $useDbConfig = 'system';
    
    public static $cacheEnabled = true;
    public static $cacheDuration = 30;
    
    /**
     * 用户反馈信息
     * @param int $accountid 用户ID
     * @param string $voice 语音文件地址
     * @param int $voice_time 语音时长
     *
     * @return array(    
     *                 code       => int,                           //状态代码
     *                 msg        => string                         //提示信息
     *                 )
     */
    public static function _save($accountid, $voice, $voice_time) {
        //获得该表的字段类型等内容 数组
        $table_field_arr = static::table()->schema();
        $attributes = array('accountid' => $accountid, 'voice' => $voice, 'voice_time' => $voice_time);
        //基础校验字段
        $result                = Utility::check_table_field($table_field_arr, $attributes);
        if ( $result[0] < 0 ) {
            return $result;
        }
        //插入数据
        if( null == self::create($attributes) ) {
            return Err::$FAILED;
        } else {
            return Err::$SUCCESS;
        }
    }
}