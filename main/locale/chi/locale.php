<?php

global $hdLocale;
$hdLocale = array(
    'day'               => '天',
    'min'               => '分',
    'sec'               => '秒',
    'hour'              => '小时',
    'over'              => '已结束',
    'before'            => '前',
    'after'             => '后',
    'today'             => '今天',
    'yesterday'         => '昨天',
    'thedaybeforeyesterday' => '前天',
    'forget_password_mail' => '已经向您的邮箱发送了新的密码，请及时查看并修改',
);

class Str {

	const SHARE_MESSAGE = '（分享自@口袋派对社区）';
    
    public static $MESSAGE_CONTENT_TEXT = array(
    		201 => '%s在%s派对中@了您评论', // AT_VOICE
    		202 => '%s在%s派对中@了您心情', // AT_EMOTION
    		301 => '版本介绍文字+更新版本按键', // SYSTEM_UPGRADE
    		302 => '后台自定义文字，多为停机或重启', // SYSTEM_URGENT
    		303 => '文字+可领取的道具+对应舞台', // SYSTEM_ACTIVITY
    		304 => '文字+可领取的道具+对应舞台', // SYSTEM_REWARDS
    		305 => '您的派对 %s 具有引领潮流的潜质，获得了广场配图推荐！', // SYSTEM_ROOM_RECOMMEND
    		306 => '你在派对 %s 中第 %d 席的作品被房主用户名删除，请根据房主的开场白进行表演，并遵守网络文明规范！', // SYSTEM_TALK_DELETED
    		);

    public static $MESSAGE_TITLE_TEXT = array(
    		201 => '@我的评论', // AT_VOICE
    		202 => '@我的心情', // AT_EMOTION
    		301 => '好消息！口袋派对升级了！', // SYSTEM_UPGRADE
    		302 => '紧急通知！！', // SYSTEM_URGENT
    		303 => '快来参加新活动～', // SYSTEM_ACTIVITY
    		304 => '请及时领取活动奖励', // SYSTEM_REWARDS
    		305 => '您的舞台获得官方推荐', // SYSTEM_ROOM_RECOMMEND
    		306 => '您的作品被删除', // SYSTEM_TALK_DELETED
    		);
}