<?php

global $hdApiList;
global $hdCommonDef;

$hdCommonDef = array(
    'GENDER' => array('enum', array(
	    'FEMALE' => array('value' => '0', 'desc' => '女'),
	    'MALE' => array('value' => '1', 'desc' => '男'),
	     
    )),
    'USER_TYPE' => array('enum', array(
	    'NORMAL' => array('value' => '1', 'desc' => '普通用户'),
	    'GM' => array('value' => '2', 'desc' => 'GM用户'),
	     
    )),
    'SECTION_TYPE' => array('enum', array(
	    'PUBLIC' => array('value' => '1', 'desc' => '广场'),
	    'USER' => array('value' => '2', 'desc' => '用户中心'),
	     
    )),
    'ROOM_TYPE' => array('enum', array(
	    'NORMAL' => array('value' => '1', 'desc' => '金币房间'),
	    'GOLD' => array('value' => '2', 'desc' => '元宝(钻石)房间'),
	     
    )),
    'ROOM_AWARD_TYPE' => array('enum', array(
	    'OWNER_AWARD' => array('value' => '1', 'desc' => '房主奖励'),
	    'AUTO_AWARD' => array('value' => '2', 'desc' => '系统自动奖励'),
	     
    )),
    'ROOM_STATUS' => array('enum', array(
	    'NORMAL' => array('value' => '1', 'desc' => '进行中'),
	    'CLEARING' => array('value' => '2', 'desc' => '结算中'),
	    'CLOSED' => array('value' => '3', 'desc' => '已结束'),
	     
    )),
    'PUBLIC_ROOM_LIST_TYPE' => array('enum', array(
	    'ALL' => array('value' => '0', 'desc' => '所有'),
	    'FEATURED' => array('value' => '1', 'desc' => '每日精华'),
	    'GOLD' => array('value' => '2', 'desc' => '钻石舞台'),
	     
    )),
    'USER_ROOM_LIST_TYPE' => array('enum', array(
	    'MY_ROOMS' => array('value' => '1', 'desc' => '我开的房间'),
	    'MY_JOINS' => array('value' => '2', 'desc' => '我参加的房间'),
	    'FOLLOWING_ROOMS' => array('value' => '3', 'desc' => '关注人开的房间'),
	    'FOLLOWING_JOINS' => array('value' => '4', 'desc' => '关注人参加的房间'),
	    'RECOMMEND_ROOMS' => array('value' => '5', 'desc' => '系统推荐'),
	     
    )),
    'TALK_TYPE' => array('enum', array(
	    'CREATION' => array('value' => '1', 'desc' => '表演(创作)'),
	    'REVIEW' => array('value' => '2', 'desc' => '房主发言'),
	     
    )),
    'MESSAGE_TYPE' => array('enum', array(
	    'PRIVATE' => array('value' => '1', 'desc' => '悄悄话'),
	    'COMMENT' => array('value' => '2', 'desc' => '@我的'),
	    'SYSTEM' => array('value' => '3', 'desc' => '系统消息'),
	     
    )),
    'MESSAGE_SUB_TYPE' => array('enum', array(
	    'PRIVATE_NORMAL' => array('value' => '101', 'desc' => '普通悄悄话'),
	    'PRIVATE_SPEAKER' => array('value' => '102', 'desc' => '喇叭'),
	    'COMMENT_VOICE' => array('value' => '201', 'desc' => '语音@'),
	    'COMMENT_EMOTION' => array('value' => '202', 'desc' => '表情@'),
	    'SYSTEM_UPGRADE' => array('value' => '301', 'desc' => '升级提醒'),
	    'SYSTEM_URGENT' => array('value' => '302', 'desc' => '紧急通知'),
	    'SYSTEM_ACTIVITY' => array('value' => '303', 'desc' => '新活动'),
	    'SYSTEM_REWARDS' => array('value' => '304', 'desc' => '活动奖励'),
	    'SYSTEM_ROOM_RECOMMEND' => array('value' => '305', 'desc' => '房间被推荐'),
	    'SYSTEM_TALK_DELETED' => array('value' => '306', 'desc' => '房间被删除'),
	     
    )),
    'MESSAGE_TYPE_Filter' => array('enum', array(
	    'ALL' => array('value' => '0', 'desc' => '全部'),
	    'SYSTEM' => array('value' => '1', 'desc' => '系统消息'),
	    'INVITATION' => array('value' => '2', 'desc' => '邀请'),
	    'ROOM_NOTIFY' => array('value' => '3', 'desc' => '房间提醒'),
	     
    )),
    'VOICE_TYPE' => array('enum', array(
	    'ROOM' => array('value' => '1', 'desc' => '房主语音'),
	    'TALK' => array('value' => '2', 'desc' => '创作语音'),
	    'COMMENT' => array('value' => '3', 'desc' => '评论语音'),
	     
    )),
    'BAD_REASON' => array('enum', array(
	    'INSULT' => array('value' => '1', 'desc' => '语音攻击和侮辱'),
	    'COPY' => array('value' => '2', 'desc' => '抄袭(非原创)'),
	    'OTHER' => array('value' => '3', 'desc' => '其他'),
	     
    )),
    'FAVORITE_TYPE' => array('enum', array(
	    'ROOM' => array('value' => '1', 'desc' => ''),
	     
    )),
    'INTERVIEW_TYPE' => array('enum', array(
	    'SYSTEM' => array('value' => '1', 'desc' => '系统问题'),
	    'USER' => array('value' => '2', 'desc' => '用户问题'),
	     
    )),
    'ITEM_TYPE' => array('enum', array(
	    'GIFT' => array('value' => '1', 'desc' => '礼物道具'),
	    'BONUS' => array('value' => '2', 'desc' => '捧场道具'),
	    'VIP' => array('value' => '3', 'desc' => 'VIP道具'),
	    'SPEAKER' => array('value' => '4', 'desc' => '喇叭'),
	     
    )),
    'ITEM_SUB_TYPE' => array('enum', array(
	    'VIP_1' => array('value' => '301', 'desc' => 'VIP1道具'),
	    'VIP_2' => array('value' => '302', 'desc' => 'VIP2道具'),
	     
    )),
    'PROFILE_UPLOAD_TYPE' => array('enum', array(
	    'AVATAR' => array('value' => '1', 'desc' => '头像'),
	    'BACKGROUND_IMAGE' => array('value' => '2', 'desc' => '背景图片(暂不支持)'),
	    'VOICE_SIGN' => array('value' => '3', 'desc' => '语音签名'),
	     
    )),
    'RANKINGS_TYPE' => array('enum', array(
	    'ALL' => array('value' => '0', 'desc' => '全部'),
	    'MY' => array('value' => '1', 'desc' => '我的排行'),
	    'TOP' => array('value' => '2', 'desc' => 'TOP排行榜'),
	    'NEWBIE' => array('value' => '3', 'desc' => '新人榜'),
	    'CONTRIBUTOR' => array('value' => '4', 'desc' => '我的人气'),
	     
    )),
    'MONEY' => array('enum', array(
	    'GOLD' => array('value' => '1', 'desc' => '元宝'),
	    'POINTS' => array('value' => '2', 'desc' => '金币'),
	    'RMB' => array('value' => '10', 'desc' => '人民币'),
	     
    )),
    'POINTS_TRANS' => array('enum', array(
	    'BUY_GOLD' => array('value' => '1', 'desc' => '购买元宝'),
	     
    )),
    'GOLD_TRANS' => array('enum', array(
	    'BUY_GOLD' => array('value' => '1', 'desc' => '购买元宝'),
	     
    )),
    'ITEM_GET_TYPE' => array('enum', array(
	    'BUY' => array('value' => '1', 'desc' => '购买获得'),
	    'SYSTEM_GIVE' => array('value' => '2', 'desc' => '系统赠送'),
	    'ITEM_DERIVE' => array('value' => '3', 'desc' => '道具派生'),
	    'TASK_AWARDS' => array('value' => '4', 'desc' => '任务奖励'),
	     
    )),
    'PAYMENT' => array('enum', array(
	    'APPLE' => array('value' => '1', 'desc' => '苹果'),
	     
    )),
    'SHARE_OBJECT_TYPE' => array('enum', array(
	    'ROOM' => array('value' => '1', 'desc' => '房主语音'),
	    'TALK' => array('value' => '2', 'desc' => '房间创作'),
	     
    )),
    'BUTTON_STATE' => array('enum', array(
	    'NONE' => array('value' => '0', 'desc' => '无按钮'),
	    'ENABLED' => array('value' => '1', 'desc' => '正常按钮'),
	    'DISABLED' => array('value' => '2', 'desc' => '灰按钮'),
	     
    )),
    'PRIVATE_MSG_SEND_FLAG' => array('enum', array(
	    'NORMAL' => array('value' => '0', 'desc' => '普通'),
	    'SPEAKER' => array('value' => '1', 'desc' => '大喇叭'),
	     
    )),
    'DESTROY_PRIVATE_TYPE' => array('enum', array(
	    'ALL' => array('value' => '1', 'desc' => '清空所有私信'),
	    'ID' => array('value' => '2', 'desc' => '删除指定ID私信'),
	    'ACCOUNT_ID' => array('value' => '3', 'desc' => '删除与某个用户的私信'),
	     
    )),
    'DESTROY_PUBLIC_TYPE' => array('enum', array(
	    'COMMUNITY' => array('value' => '1', 'desc' => '社区消息'),
	    'ROOM' => array('value' => '2', 'desc' => '房间消息'),
	     
    )),
    'RELATION_TYPE' => array('enum', array(
	    'NONE' => array('value' => '0', 'desc' => '未关注'),
	    'FOLLOWED' => array('value' => '1', 'desc' => '被关注'),
	    'FOLLOWING' => array('value' => '2', 'desc' => '已关注'),
	    'BOTH' => array('value' => '3', 'desc' => '相互关注'),
	     
    )),
    'TASK_AWARD_TYPE' => array('enum', array(
	    'POINTS' => array('value' => '1', 'desc' => '金币'),
	    'FLOWER' => array('value' => '2', 'desc' => '鲜花'),
	    'POP_VALUE' => array('value' => '3', 'desc' => '人气值'),
	     
    )),
    'TASK_TYPE' => array('enum', array(
	    'ONCE' => array('value' => '1', 'desc' => '新手任务'),
	    'DAILY' => array('value' => '2', 'desc' => '每日任务'),
	     
    )),
    'CLICK_ID' => array('enum', array(
	    'APP_RATING' => array('value' => '1', 'desc' => '给应用评分'),
	     
    )),
    'HIDE_BY' => array('enum', array(
	    'SELF' => array('value' => '1', 'desc' => '自己'),
	    'ROOM_OWNER' => array('value' => '2', 'desc' => '房主'),
	    'ADMIN' => array('value' => '3', 'desc' => '后台管理'),
	     
    )),
    'HIDE_FLAG' => array('enum', array(
	    'NONE' => array('value' => '0', 'desc' => '未隐藏'),
	    'OTHERS' => array('value' => '1', 'desc' => '对所有人隐藏(仅本人能看)'),
	     
    )),
    'AT_TYPE' => array('enum', array(
	    'USER' => array('value' => '1', 'desc' => '用户'),
	    'ROOM' => array('value' => '2', 'desc' => '房间'),
	    'TALK' => array('value' => '3', 'desc' => '作品'),
	    'COMMENT' => array('value' => '4', 'desc' => '评论'),
	     
    )),
    'COMMENT_TYPE' => array('enum', array(
	    'VOICE' => array('value' => '1', 'desc' => ''),
	    'EMOTION' => array('value' => '2', 'desc' => ''),
	     
    )),
    'CONTENT_TYPE' => array('enum', array(
	    'TALK' => array('value' => '1', 'desc' => '作品'),
	    'COMMENT_VOICE' => array('value' => '2', 'desc' => '语音评论'),
	    'COMMENT_EMOTION' => array('value' => '3', 'desc' => '表情评论'),
	     
    )),
    'LAST_MESSAGE_TYPE' => array('enum', array(
	    'LEFT' => array('value' => '1', 'desc' => ''),
	    'RIGHT' => array('value' => '2', 'desc' => ''),
	     
    )),
    'CLIENT_INFO' => array('struct', array(
	    'appid' => array('type' => 'int', 'sample' => '3', 'desc' => '应用ID'),
	    'channelid' => array('type' => 'int', 'sample' => '1', 'desc' => '渠道ID'),
	    'equipmentid' => array('type' => 'string', 'sample' => 'iphone', 'desc' => '设备ID'),
	    'applicationversion' => array('type' => 'string', 'sample' => '0.7.8.1', 'desc' => '应用版本'),
	    'systemversion' => array('type' => 'string', 'sample' => 'ios 5.1', 'desc' => '系统版本'),
	    'cellbrand' => array('type' => 'string', 'sample' => 'iphone4', 'desc' => ''),
	    'cellmodel' => array('type' => 'string', 'sample' => '5', 'desc' => ''),
	    'device_token' => array('type' => 'string', 'sample' => '', 'isoptional'=>'1', 'desc' => '设备令牌'),

    )),
    'PROFILE' => array('struct', array(
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'gender' => array('type' => 'enum','desc' => ' GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
	    'navtive_place' => array('type' => 'string', 'sample' => '', 'desc' => '籍贯'),
	    'occupation' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'dialect' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'birthday' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'introduction' => array('type' => 'string', 'sample' => '', 'isoptional'=>'1', 'desc' => '自我介绍'),
	    'bg_image_id' => array('type' => 'int', 'sample' => '', 'desc' => '背景图片ID'),

    )),
    'USER_AVATAR' => array('struct', array(
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),

    )),
    'USER_ITEM' => array('struct', array(
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
	    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
	    'value' => array('type' => 'int', 'sample' => '', 'desc' => '值(附加)'),
	    'is_following' => array('type' => 'int', 'sample' => '', 'desc' => '是否关注'),

    )),
    'ROOM_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '房主ID'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '房主昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '房主头像'),
	    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
	    'type' => array('type' => 'enum','desc' => '房间类型 ROOM_TYPE_NORMAL(1):金币房间, ROOM_TYPE_GOLD(2):元宝(钻石)房间, ', 'reference' => 'ROOM_TYPE'),
	    'title' => array('type' => 'string', 'sample' => '', 'desc' => '房间标题'),
	    'time_remains' => array('type' => 'string', 'sample' => '', 'desc' => '剩余时间'),
	    'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '人气值'),
	    'bid' => array('type' => 'int', 'sample' => '', 'desc' => '房主出价'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '房主语音'),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '语音长度'),
	    'voice_image' => array('type' => 'string', 'sample' => '', 'desc' => '语音附加图片'),
	    'voice_image_origin' => array('type' => 'string', 'sample' => '', 'desc' => '原始图片'),
	    'voice_fid' => array('type' => 'string', 'sample' => '', 'desc' => '语音文件fid'),
	    'bg_image' => array('type' => 'string', 'sample' => '', 'desc' => '背景图片'),
	    'bg_image_id' => array('type' => 'int', 'sample' => '', 'desc' => '背景图ID'),
	    'like_count' => array('type' => 'string', 'sample' => '', 'desc' => '被赞数'),
	    'listen_count' => array('type' => 'string', 'sample' => '', 'desc' => '被听数'),
	    'status' => array('type' => 'enum','desc' => '状态 ROOM_STATUS_NORMAL(1):进行中, ROOM_STATUS_CLEARING(2):结算中, ROOM_STATUS_CLOSED(3):已结束, ', 'reference' => 'ROOM_STATUS'),
	    'tags' => array('type' => 'string', 'sample' => '', 'desc' => '标签名'),
	    'invite_by' => array('type' => 'string', 'sample' => '', 'desc' => '邀请人'),

    )),
    'ROOM_ITEM2' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '房主ID'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '房主昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '房主头像'),
	    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
	    'type' => array('type' => 'enum','desc' => '房间类型 ROOM_TYPE_NORMAL(1):金币房间, ROOM_TYPE_GOLD(2):元宝(钻石)房间, ', 'reference' => 'ROOM_TYPE'),
	    'title' => array('type' => 'string', 'sample' => '', 'desc' => '房间标题'),
	    'time_remains' => array('type' => 'string', 'sample' => '', 'desc' => '剩余时间'),
	    'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '人气值'),
	    'bid' => array('type' => 'int', 'sample' => '', 'desc' => '房主出价'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '房主语音'),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '语音长度'),
	    'voice_image' => array('type' => 'string', 'sample' => '', 'desc' => '语音附加图片'),
	    'voice_image_origin' => array('type' => 'string', 'sample' => '', 'desc' => '原始图片'),
	    'voice_fid' => array('type' => 'string', 'sample' => '', 'desc' => '语音文件fid'),
	    'bg_image' => array('type' => 'string', 'sample' => '', 'desc' => '背景图片'),
	    'bg_image_id' => array('type' => 'int', 'sample' => '', 'desc' => '背景图ID'),
	    'like_count' => array('type' => 'string', 'sample' => '', 'desc' => '被赞数'),
	    'listen_count' => array('type' => 'string', 'sample' => '', 'desc' => '被听数'),
	    'status' => array('type' => 'enum','desc' => '状态 ROOM_STATUS_NORMAL(1):进行中, ROOM_STATUS_CLEARING(2):结算中, ROOM_STATUS_CLOSED(3):已结束, ', 'reference' => 'ROOM_STATUS'),
	    'tags' => array('type' => 'string', 'sample' => '', 'desc' => '标签名'),
	    'invite_by' => array('type' => 'string', 'sample' => '', 'desc' => '邀请人'),
	    'is_award' => array('type' => 'int', 'sample' => '', 'desc' => '是否获奖'),
	    'is_most_pop' => array('type' => 'int', 'sample' => '', 'desc' => '是否人气最高'),

    )),
    'STAGE_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'name' => array('type' => 'string', 'sample' => '', 'desc' => '名字'),
	    'preview' => array('type' => 'struct', 'desc' => '', array(
	        'image' => array('type' => 'string', 'sample' => '', 'desc' => '预览图片'),
	        'music' => array('type' => 'string', 'sample' => '', 'desc' => '预览音乐'),

	    )),
	    'package' => array('type' => 'string', 'sample' => '', 'desc' => '资源包地址'),

    )),
    'GIFT_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => '礼物ID'),
	    'image' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => ''),

    )),
    'GIFT_ITEM2' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => '礼物ID'),
	    'image' => array('type' => 'string', 'sample' => '', 'desc' => '礼物图片'),
	    'quantity' => array('type' => 'int', 'sample' => '', 'desc' => '礼物数量'),
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '送礼人ID'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '送礼人昵称'),
	    'is_new' => array('type' => 'int', 'sample' => '', 'desc' => '是否新礼物'),

    )),
    'TALK_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'type' => array('type' => 'enum','desc' => '类型 TALK_TYPE_CREATION(1):表演(创作), TALK_TYPE_REVIEW(2):房主发言, ', 'reference' => 'TALK_TYPE'),
	    'floor' => array('type' => 'int', 'sample' => '', 'desc' => '楼层'),
	    'theme_bg_image' => array('type' => 'string', 'sample' => '', 'desc' => '主题背景图片'),
	    'pop_value' => array('type' => 'string', 'sample' => '', 'desc' => '人气值'),
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '说话人'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '说话人昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
	    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音'),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'voice_image' => array('type' => 'string', 'sample' => '', 'desc' => '语音附加图片'),
	    'voice_fid' => array('type' => 'string', 'sample' => '', 'desc' => '语音文件fid'),
	    'comments' => array('type' => 'int', 'sample' => '', 'desc' => '评论数'),
	    'gift' => array('type' => 'struct', 'desc' => '', array(
	        'image' => array('type' => 'string', 'sample' => '', 'desc' => '礼物图片'),
	        'count' => array('type' => 'int', 'sample' => '', 'desc' => '礼物数'),
	        'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '最后一个送礼人的昵称'),

	    )),
	    'created' => array('type' => 'string', 'sample' => '', 'desc' => ''),

    )),
    'INTERVIEW_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'type' => array('type' => 'enum','desc' => ' INTERVIEW_TYPE_SYSTEM(1):系统问题, INTERVIEW_TYPE_USER(2):用户问题, ', 'reference' => 'INTERVIEW_TYPE'),
	    'ask_accountid' => array('type' => 'int', 'sample' => '', 'desc' => '提问人的ID'),
	    'ask_avatar' => array('type' => 'string', 'sample' => '', 'desc' => '提问人的头像'),
	    'ask_voice' => array('type' => 'string', 'sample' => '', 'desc' => '提问问题语音'),
	    'ask_voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '提问语音'),
	    'ask_time' => array('type' => 'string', 'sample' => '', 'desc' => '提问时间'),
	    'answer_accountid' => array('type' => 'int', 'sample' => '', 'desc' => '回答人的ID'),
	    'answer_avatar' => array('type' => 'string', 'sample' => '', 'desc' => '回答人的头像'),
	    'answer_voice' => array('type' => 'string', 'sample' => '', 'desc' => '回答的语音'),
	    'answer_voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '回答语音'),
	    'answer_time' => array('type' => 'string', 'sample' => '', 'desc' => '提问时间'),

    )),
    'PRIVATE_MESSAGE_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'fromid' => array('type' => 'int', 'sample' => '', 'desc' => '发送者ID'),
	    'message' => array('type' => 'string', 'sample' => '', 'isoptional'=>'1', 'desc' => '文本消息'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音'),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '语音时长'),
	    'send_flag' => array('type' => 'enum','desc' => '发送标示 PRIVATE_MSG_SEND_FLAG_NORMAL(0):普通, PRIVATE_MSG_SEND_FLAG_SPEAKER(1):大喇叭, ', 'reference' => 'PRIVATE_MSG_SEND_FLAG'),
	    'is_read' => array('type' => 'int', 'sample' => '', 'desc' => '是否已读'),
	    'created' => array('type' => 'string', 'sample' => '', 'desc' => '发送时间'),

    )),
    'SYSTEM_MESSAGE_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'fromid' => array('type' => 'int', 'sample' => '', 'desc' => '发送者ID'),
	    'objectid' => array('type' => 'int', 'sample' => '', 'desc' => '关联对象ID'),
	    'sub_type' => array('type' => 'enum','desc' => ' MESSAGE_SUB_TYPE_PRIVATE_NORMAL(101):普通悄悄话, MESSAGE_SUB_TYPE_PRIVATE_SPEAKER(102):喇叭, MESSAGE_SUB_TYPE_COMMENT_VOICE(201):语音@, MESSAGE_SUB_TYPE_COMMENT_EMOTION(202):表情@, MESSAGE_SUB_TYPE_SYSTEM_UPGRADE(301):升级提醒, MESSAGE_SUB_TYPE_SYSTEM_URGENT(302):紧急通知, MESSAGE_SUB_TYPE_SYSTEM_ACTIVITY(303):新活动, MESSAGE_SUB_TYPE_SYSTEM_REWARDS(304):活动奖励, MESSAGE_SUB_TYPE_SYSTEM_ROOM_RECOMMEND(305):房间被推荐, MESSAGE_SUB_TYPE_SYSTEM_TALK_DELETED(306):房间被删除, ', 'reference' => 'MESSAGE_SUB_TYPE'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
	    'title' => array('type' => 'string', 'sample' => '', 'desc' => '标题'),
	    'message' => array('type' => 'string', 'sample' => '', 'desc' => '消息文本'),
	    'ack_status' => array('type' => 'int', 'sample' => '', 'desc' => '应答状态(值和具体消息类型相关)'),
	    'annotations' => array('type' => 'struct', 'desc' => '', array(
	        'roomid' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),

	    )),
	    'created' => array('type' => 'string', 'sample' => '', 'desc' => '消息时间'),

    )),
    'ITEM_ENTRY' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'name' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'image' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'description' => array('type' => 'string', 'sample' => '', 'desc' => '描述'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音介绍'),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '语音时长'),
	    'discount' => array('type' => 'float', 'sample' => '', 'desc' => '折扣'),
	    'product_id' => array('type' => 'string', 'sample' => '', 'isoptional'=>'1', 'desc' => 'app store产品ID'),
	    'money_type' => array('type' => 'enum','desc' => ' MONEY_GOLD(1):元宝, MONEY_POINTS(2):金币, MONEY_RMB(10):人民币, ', 'reference' => 'MONEY'),
	    'money' => array('type' => 'int', 'sample' => '', 'desc' => ''),

    )),
    'PHOTO_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'photo' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => ''),

    )),
    'VOICE' => array('struct', array(
	    'fid' => array('type' => 'string', 'sample' => '', 'desc' => 'fid'),
	    'duration' => array('type' => 'int', 'sample' => '', 'desc' => '时长'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音url'),
	    'image' => array('type' => 'string', 'sample' => '', 'desc' => '附加图片url'),

    )),
    'TAG_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'name' => array('type' => 'string', 'sample' => '', 'desc' => '标签名'),

    )),
    'TITLE_ITEM' => array('struct', array(
	    'title' => array('type' => 'string', 'sample' => '', 'desc' => '称号名'),
	    'description' => array('type' => 'string', 'sample' => '', 'desc' => '称号描述'),
	    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
	    'is_current' => array('type' => 'int', 'sample' => '', 'desc' => '是否选中'),

    )),
    'ROOM_DATA' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
	    'type' => array('type' => 'enum','desc' => ' ROOM_TYPE_NORMAL(1):金币房间, ROOM_TYPE_GOLD(2):元宝(钻石)房间, ', 'reference' => 'ROOM_TYPE'),
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '房主ID'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '房主昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
	    'bg_image' => array('type' => 'string', 'sample' => '', 'desc' => '背景图'),
	    'bg_image_id' => array('type' => 'int', 'sample' => '', 'desc' => '背景图ID'),
	    'title' => array('type' => 'string', 'sample' => '', 'desc' => '房间标题'),
	    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音'),
	    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'voice_image' => array('type' => 'string', 'sample' => '', 'desc' => '语音附带图片'),
	    'voice_fid' => array('type' => 'string', 'sample' => '', 'desc' => 'fid'),
	    'bid' => array('type' => 'int', 'sample' => '', 'desc' => '出价'),
	    'time_remains' => array('type' => 'string', 'sample' => '', 'desc' => '剩余时间'),
	    'like_count' => array('type' => 'string', 'sample' => '', 'desc' => '被赞数'),
	    'listen_count' => array('type' => 'string', 'sample' => '', 'desc' => '被听数'),
	    'rank' => array('type' => 'string', 'sample' => '', 'desc' => '排名'),
	    'comments' => array('type' => 'int', 'sample' => '', 'desc' => '房间的总评论数'),
	    'is_favorite' => array('type' => 'int', 'sample' => '', 'desc' => '是否收藏'),
	    'favoriteid' => array('type' => 'int', 'sample' => '', 'desc' => '被收藏的ID,0表示未收藏'),
	    'is_liked' => array('type' => 'int', 'sample' => '', 'desc' => '是否赞过'),
	    'status' => array('type' => 'enum','desc' => '房间状态 ROOM_STATUS_NORMAL(1):进行中, ROOM_STATUS_CLEARING(2):结算中, ROOM_STATUS_CLOSED(3):已结束, ', 'reference' => 'ROOM_STATUS'),
	    'has_result' => array('type' => 'int', 'sample' => '', 'desc' => '是否有结算结果'),
	    'number' => array('type' => 'int', 'sample' => '', 'desc' => '房间号'),
	    'messages' => array('type' => 'string', 'sample' => '', 'isarray'=>'1', 'desc' => '顶部消息'),

    )),
    'COMMENT_ITEM' => array('struct', array(
	    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
	    'type' => array('type' => 'enum','desc' => '类型 COMMENT_TYPE_VOICE(1):, COMMENT_TYPE_EMOTION(2):, ', 'reference' => 'COMMENT_TYPE'),
	    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '说话人'),
	    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '说话人昵称'),
	    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '说话人头像'),
	    'voice' => array('type' => 'struct', 'desc' => '语音', 'reference' => 'VOICE'),        
	    'emotion' => array('type' => 'int', 'sample' => '', 'desc' => '表情'),
	    'at_text' => array('type' => 'string', 'sample' => '', 'desc' => '@的内容'),
	    'created' => array('type' => 'string', 'sample' => '', 'desc' => ''),

    )),
    'DEFAULT_OUTPUT' => array('struct', array(
	    'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

    )),
    'PAGE_INPUT' => array('struct', array(
	    'page' => array('type' => 'int', 'sample' => '', 'desc' => '页码'),
	    'count' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '每页数量限制'),
	    'sinceid' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
	    'maxid' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

    )),
    'PAGE_OUTPUT' => array('struct', array(
	    'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'data' => array('type' => 'struct', 'desc' => '', array(
	        'page' => array('type' => 'int', 'sample' => '', 'desc' => '页码'),
	        'count' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '每页数量限制'),
	        'sinceid' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
	        'maxid' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
	        'total' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '总数'),
	        'is_last' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '是否到最后'),

	    )),

    )),
    'LOGIN_OUTPUT' => array('struct', array(
	    'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'data' => array('type' => 'struct', 'desc' => '', array(
	        'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '账号ID'),
	        'profile' => array('type' => 'struct', 'desc' => '', array(
	            'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
	            'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
	            'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音签名'),
	            'gender' => array('type' => 'enum','desc' => '性别 GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
	            'occupation' => array('type' => 'string', 'sample' => '', 'desc' => '职务'),
	            'dialect' => array('type' => 'string', 'sample' => '', 'desc' => '乡音(方言)'),
	            'birthday' => array('type' => 'string', 'sample' => '', 'desc' => '生日'),
	            'introduction' => array('type' => 'string', 'sample' => '', 'desc' => '自我介绍'),
	            'vip' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	            'vip_expire' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	            'type' => array('type' => 'enum','desc' => '用户类型 USER_TYPE_NORMAL(1):普通用户, USER_TYPE_GM(2):GM用户, ', 'reference' => 'USER_TYPE'),
	            'level' => array('type' => 'int', 'sample' => '', 'desc' => '等级'),
	            'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
	            'title' => array('type' => 'string', 'sample' => '', 'desc' => '称号'),
	            'points' => array('type' => 'int', 'sample' => '', 'desc' => '金币'),
	            'golds' => array('type' => 'int', 'sample' => '', 'desc' => '元宝(钻石)'),
	            'is_first_login' => array('type' => 'int', 'sample' => '', 'desc' => '是否是第一次登陆'),

	        )),
	        'update' => array('type' => 'struct', 'desc' => '', array(
	            'version' => array('type' => 'string', 'sample' => '', 'desc' => '版本'),
	            'url' => array('type' => 'string', 'sample' => '', 'desc' => '更新地址'),
	            'description' => array('type' => 'string', 'sample' => '', 'desc' => '更新说明'),

	        )),
	        'default_image' => array('type' => 'string', 'sample' => '', 'desc' => '默认图片'),
	        'binding_platforms' => array('type' => 'string', 'sample' => '', 'isarray'=>'1', 'desc' => '绑定的平台'),

	    )),

    )),
    'UPLOAD_OUTPUT' => array('struct', array(
	    'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
	    'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
	    'data' => array('type' => 'struct', 'desc' => '', array(
	        'id' => array('type' => 'int', 'sample' => '', 'desc' => '创建新记录ID (具体应用中定义ID所指) '),
	        'file_url' => array('type' => 'string', 'sample' => '', 'desc' => ''),

	    )),

    )),
    
);

$hdApiList = array(
'account' => array (
    'register' => array(
        'description' => '用户注册',
        'params' => array(
            'appid' => array('default' => '3', 'type' => '', 'desc' => '应用ID'),
            'channelid' => array('default' => '1', 'type' => '', 'desc' => '渠道ID'),
            'equipmentid' => array('default' => 'iphone', 'type' => '', 'desc' => '设备ID'),
            'applicationversion' => array('default' => '0.7.8.1', 'type' => '', 'desc' => '应用版本'),
            'systemversion' => array('default' => 'ios 5.1', 'type' => '', 'desc' => '系统版本'),
            'cellbrand' => array('default' => 'iphone4', 'type' => '', 'desc' => ''),
            'cellmodel' => array('default' => '5', 'type' => '', 'desc' => ''),
            'device_token' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '设备令牌'),
            'name' => array('default' => 'zzw2@hoodinn.com', 'type' => '', 'desc' => '用户名'),
            'password' => array('default' => '123456', 'type' => '', 'desc' => '密码'),
            'nickname' => array('default' => 'aloha', 'type' => '', 'desc' => '昵称'),
            'gender' => array('default' => '', 'type' => '', 'desc' => ' GENDER_FEMALE(0):女, GENDER_MALE(1):男, '),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '账号ID'),
                'profile' => array('type' => 'struct', 'desc' => '', array(
                    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音签名'),
                    'gender' => array('type' => 'enum','desc' => '性别 GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
                    'occupation' => array('type' => 'string', 'sample' => '', 'desc' => '职务'),
                    'dialect' => array('type' => 'string', 'sample' => '', 'desc' => '乡音(方言)'),
                    'birthday' => array('type' => 'string', 'sample' => '', 'desc' => '生日'),
                    'introduction' => array('type' => 'string', 'sample' => '', 'desc' => '自我介绍'),
                    'vip' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'vip_expire' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                    'type' => array('type' => 'enum','desc' => '用户类型 USER_TYPE_NORMAL(1):普通用户, USER_TYPE_GM(2):GM用户, ', 'reference' => 'USER_TYPE'),
                    'level' => array('type' => 'int', 'sample' => '', 'desc' => '等级'),
                    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '称号'),
                    'points' => array('type' => 'int', 'sample' => '', 'desc' => '金币'),
                    'golds' => array('type' => 'int', 'sample' => '', 'desc' => '元宝(钻石)'),
                    'is_first_login' => array('type' => 'int', 'sample' => '', 'desc' => '是否是第一次登陆'),

                )),
                'update' => array('type' => 'struct', 'desc' => '', array(
                    'version' => array('type' => 'string', 'sample' => '', 'desc' => '版本'),
                    'url' => array('type' => 'string', 'sample' => '', 'desc' => '更新地址'),
                    'description' => array('type' => 'string', 'sample' => '', 'desc' => '更新说明'),

                )),
                'default_image' => array('type' => 'string', 'sample' => '', 'desc' => '默认图片'),
                'binding_platforms' => array('type' => 'string', 'sample' => '', 'isarray'=>'1', 'desc' => '绑定的平台'),

            )),

        )
    ),
    'login' => array(
        'description' => '用户登陆',
        'params' => array(
            'appid' => array('default' => '3', 'type' => '', 'desc' => '应用ID'),
            'channelid' => array('default' => '1', 'type' => '', 'desc' => '渠道ID'),
            'equipmentid' => array('default' => 'iphone', 'type' => '', 'desc' => '设备ID'),
            'applicationversion' => array('default' => '0.7.8.1', 'type' => '', 'desc' => '应用版本'),
            'systemversion' => array('default' => 'ios 5.1', 'type' => '', 'desc' => '系统版本'),
            'cellbrand' => array('default' => 'iphone4', 'type' => '', 'desc' => ''),
            'cellmodel' => array('default' => '5', 'type' => '', 'desc' => ''),
            'device_token' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '设备令牌'),
            'name' => array('default' => 'zzw2@hoodinn.com', 'type' => '', 'desc' => '用户名'),
            'password' => array('default' => '123456', 'type' => '', 'desc' => '密码'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '账号ID'),
                'profile' => array('type' => 'struct', 'desc' => '', array(
                    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音签名'),
                    'gender' => array('type' => 'enum','desc' => '性别 GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
                    'occupation' => array('type' => 'string', 'sample' => '', 'desc' => '职务'),
                    'dialect' => array('type' => 'string', 'sample' => '', 'desc' => '乡音(方言)'),
                    'birthday' => array('type' => 'string', 'sample' => '', 'desc' => '生日'),
                    'introduction' => array('type' => 'string', 'sample' => '', 'desc' => '自我介绍'),
                    'vip' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'vip_expire' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                    'type' => array('type' => 'enum','desc' => '用户类型 USER_TYPE_NORMAL(1):普通用户, USER_TYPE_GM(2):GM用户, ', 'reference' => 'USER_TYPE'),
                    'level' => array('type' => 'int', 'sample' => '', 'desc' => '等级'),
                    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '称号'),
                    'points' => array('type' => 'int', 'sample' => '', 'desc' => '金币'),
                    'golds' => array('type' => 'int', 'sample' => '', 'desc' => '元宝(钻石)'),
                    'is_first_login' => array('type' => 'int', 'sample' => '', 'desc' => '是否是第一次登陆'),

                )),
                'update' => array('type' => 'struct', 'desc' => '', array(
                    'version' => array('type' => 'string', 'sample' => '', 'desc' => '版本'),
                    'url' => array('type' => 'string', 'sample' => '', 'desc' => '更新地址'),
                    'description' => array('type' => 'string', 'sample' => '', 'desc' => '更新说明'),

                )),
                'default_image' => array('type' => 'string', 'sample' => '', 'desc' => '默认图片'),
                'binding_platforms' => array('type' => 'string', 'sample' => '', 'isarray'=>'1', 'desc' => '绑定的平台'),

            )),

        )
    ),
    'auto_login' => array(
        'description' => '自动登陆',
        'params' => array(
            'appid' => array('default' => '3', 'type' => '', 'desc' => '应用ID'),
            'channelid' => array('default' => '1', 'type' => '', 'desc' => '渠道ID'),
            'equipmentid' => array('default' => 'iphone', 'type' => '', 'desc' => '设备ID'),
            'applicationversion' => array('default' => '0.7.8.1', 'type' => '', 'desc' => '应用版本'),
            'systemversion' => array('default' => 'ios 5.1', 'type' => '', 'desc' => '系统版本'),
            'cellbrand' => array('default' => 'iphone4', 'type' => '', 'desc' => ''),
            'cellmodel' => array('default' => '5', 'type' => '', 'desc' => ''),
            'device_token' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '设备令牌'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '账号ID'),
                'profile' => array('type' => 'struct', 'desc' => '', array(
                    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音签名'),
                    'gender' => array('type' => 'enum','desc' => '性别 GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
                    'occupation' => array('type' => 'string', 'sample' => '', 'desc' => '职务'),
                    'dialect' => array('type' => 'string', 'sample' => '', 'desc' => '乡音(方言)'),
                    'birthday' => array('type' => 'string', 'sample' => '', 'desc' => '生日'),
                    'introduction' => array('type' => 'string', 'sample' => '', 'desc' => '自我介绍'),
                    'vip' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'vip_expire' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                    'type' => array('type' => 'enum','desc' => '用户类型 USER_TYPE_NORMAL(1):普通用户, USER_TYPE_GM(2):GM用户, ', 'reference' => 'USER_TYPE'),
                    'level' => array('type' => 'int', 'sample' => '', 'desc' => '等级'),
                    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '称号'),
                    'points' => array('type' => 'int', 'sample' => '', 'desc' => '金币'),
                    'golds' => array('type' => 'int', 'sample' => '', 'desc' => '元宝(钻石)'),
                    'is_first_login' => array('type' => 'int', 'sample' => '', 'desc' => '是否是第一次登陆'),

                )),
                'update' => array('type' => 'struct', 'desc' => '', array(
                    'version' => array('type' => 'string', 'sample' => '', 'desc' => '版本'),
                    'url' => array('type' => 'string', 'sample' => '', 'desc' => '更新地址'),
                    'description' => array('type' => 'string', 'sample' => '', 'desc' => '更新说明'),

                )),
                'default_image' => array('type' => 'string', 'sample' => '', 'desc' => '默认图片'),
                'binding_platforms' => array('type' => 'string', 'sample' => '', 'isarray'=>'1', 'desc' => '绑定的平台'),

            )),

        )
    ),
    'logout' => array(
        'description' => '用户登陆',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'update_password' => array(
        'description' => '用户修改密码',
        'params' => array(
            'old_pwd' => array('default' => '', 'type' => '', 'desc' => ''),
            'new_pwd' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'forget_password' => array(
        'description' => '用户申请重置密码',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => '重置密码方式 1: email'),
            'email' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'renew_password' => array(
        'description' => '设置新密码(申请重置后)',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => '重置密码方式 1: email'),
            'email' => array('default' => '', 'type' => '', 'desc' => 'email'),
            'token' => array('default' => '', 'type' => '', 'desc' => '令牌'),
            'password1' => array('default' => '', 'type' => '', 'desc' => '新密码'),
            'password2' => array('default' => '', 'type' => '', 'desc' => '确认密码'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    't_login' => array(
        'description' => '第三方用户登陆  {"appid":4,"appSecret":"7e9aee1ac8257150ed776e8db8134d4e","channelid":1,"equipmentid":"htc_andriod_2.3_20120301_taiwan_232","applicationversion":"1.0","systemversion":"Andriod 2.4","cellbrand":"HuaWei","cellmodel":"u880","language":"Chinese","platform":"QQ"}',
        'params' => array(
            'loginParam' => array('default' => '', 'type' => 'textarea', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    't_bind' => array(
        'description' => '第三方用户绑定 {"platform":"QQ"}',
        'params' => array(
            'bindParam' => array('default' => '', 'type' => 'textarea', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    't_unbind' => array(
        'description' => '第三方用户解除绑定',
        'params' => array(
            'platform' => array('default' => '', 'type' => '', 'desc' => ' WEIBO RENREN T_QQ QQ '),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    't_init_nickname' => array(
        'description' => '第三方用户首次设置昵称',
        'params' => array(
            'applicationversion' => array('default' => '', 'type' => '', 'desc' => '客户端版本'),
            'requestid' => array('default' => '', 'type' => '', 'desc' => '第三方请求ID'),
            'nickname' => array('default' => '', 'type' => '', 'desc' => '昵称'),
            'platform' => array('default' => '', 'type' => '', 'desc' => '第三方平台WEIBO RENREN T_QQ QQ'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'profile' => array(
        'description' => '获取基本信息',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                'avatar' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'voice' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'gender' => array('type' => 'enum','desc' => ' GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
                'navtive_place' => array('type' => 'string', 'sample' => '', 'desc' => '籍贯'),
                'occupation' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'dialect' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'birthday' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'introduction' => array('type' => 'string', 'sample' => '', 'isoptional'=>'1', 'desc' => '自我介绍'),
                'bg_image_id' => array('type' => 'int', 'sample' => '', 'desc' => '背景图片ID'),

            )),

        )
    ),
    'update_profile' => array(
        'description' => '修改用户信息',
        'params' => array(
            'nickname' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '昵称'),
            'gender' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '性别 GENDER_FEMALE(0):女, GENDER_MALE(1):男, '),
            'native_place' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '籍贯'),
            'occupation' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '职务'),
            'dialect' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '方言'),
            'birthday' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '格式: 1999-09-11'),
            'introduction' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '自我介绍'),
            'bg_image_id' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '背景图片ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'update_profile_upload' => array(
        'description' => '修改用户信息(上传文件)',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => ' PROFILE_UPLOAD_TYPE_AVATAR(1):头像, PROFILE_UPLOAD_TYPE_BACKGROUND_IMAGE(2):背景图片(暂不支持), PROFILE_UPLOAD_TYPE_VOICE_SIGN(3):语音签名, '),
            'upload' => array('default' => '', 'type' => 'file', 'desc' => '上传文件'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'id' => array('type' => 'int', 'sample' => '', 'desc' => '创建新记录ID (具体应用中定义ID所指) '),
                'file_url' => array('type' => 'string', 'sample' => '', 'desc' => ''),

            )),

        )
    ),
    'preference' => array(
        'description' => '获取用户设置',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'push_private_msg' => array('type' => 'int', 'sample' => '', 'desc' => '推送悄悄话消息'),
                'push_community_msg' => array('type' => 'int', 'sample' => '', 'desc' => '推送社区消息'),
                'push_room_msg' => array('type' => 'int', 'sample' => '', 'desc' => '推送舞台动态消息'),
                'push_comment_msg' => array('type' => 'int', 'sample' => '', 'desc' => '推送新增评论消息'),
                'stranger_private_msg' => array('type' => 'int', 'sample' => '', 'desc' => '陌生人悄悄话'),

            )),

        )
    ),
    'update_preference' => array(
        'description' => '修改用户设置',
        'params' => array(
            'push_private_msg' => array('default' => '', 'type' => '', 'desc' => '推送悄悄话消息'),
            'push_community_msg' => array('default' => '', 'type' => '', 'desc' => '推送社区消息'),
            'push_room_msg' => array('default' => '', 'type' => '', 'desc' => '推送舞台动态消息'),
            'push_comment_msg' => array('default' => '', 'type' => '', 'desc' => '推送新增评论消息'),
            'stranger_private_msg' => array('default' => '', 'type' => '', 'desc' => '陌生人悄悄话'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'user' => array (
    'home' => array(
        'description' => '用户首页',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                'gender' => array('type' => 'enum','desc' => '性别 GENDER_FEMALE(0):女, GENDER_MALE(1):男, ', 'reference' => 'GENDER'),
                'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音签名'),
                'birthday' => array('type' => 'string', 'sample' => '', 'desc' => '生日'),
                'introduction' => array('type' => 'string', 'sample' => '', 'desc' => '自我介绍'),
                'constellation' => array('type' => 'string', 'sample' => '', 'desc' => '星座'),
                'location' => array('type' => 'string', 'sample' => '', 'desc' => '位置'),
                'dialect' => array('type' => 'string', 'sample' => '', 'desc' => '方言'),
                'pop_title' => array('type' => 'string', 'sample' => '', 'desc' => '称号'),
                'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '心情值'),
                'vip' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                'vip_expire_time' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'following' => array('type' => 'int', 'sample' => '', 'desc' => '关注人数'),
                'followers' => array('type' => 'int', 'sample' => '', 'desc' => '粉丝人数'),
                'rooms' => array('type' => 'int', 'sample' => '', 'desc' => '房间数'),
                'talks' => array('type' => 'int', 'sample' => '', 'desc' => '作品数'),
                'statuses' => array('type' => 'int', 'sample' => '', 'desc' => '动态数'),
                'favorites' => array('type' => 'int', 'sample' => '', 'desc' => '收藏数'),
                'photos' => array('type' => 'int', 'sample' => '', 'desc' => '相片数'),
                'visitors_today' => array('type' => 'int', 'sample' => '', 'desc' => '今日访客'),
                'visitors_history' => array('type' => 'int', 'sample' => '', 'desc' => '历史访客'),
                'bg_image' => array('type' => 'struct', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '图片ID'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '图片'),

                )),
                'relation' => array('type' => 'enum','isoptional'=>'1', 'desc' => '和自己的关系 RELATION_TYPE_NONE(0):未关注, RELATION_TYPE_FOLLOWED(1):被关注, RELATION_TYPE_FOLLOWING(2):已关注, RELATION_TYPE_BOTH(3):相互关注, ', 'reference' => 'RELATION_TYPE'),
                'points' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '金币(仅本人可见)'),
                'golds' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '元宝(仅本人可见)'),
                'visitors' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
                    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                    'created' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),

                )),

            )),

        )
    ),
    'creations' => array(
        'description' => '获取我的作品',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
                    'room_id' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
                    'room_title' => array('type' => 'string', 'sample' => '', 'desc' => '房间标题'),
                    'room_tags' => array('type' => 'string', 'sample' => '', 'desc' => '房间标签'),
                    'voice' => array('type' => 'string', 'sample' => '', 'desc' => '语音'),
                    'voice_time' => array('type' => 'int', 'sample' => '', 'desc' => '语音长度'),
                    'voice_fid' => array('type' => 'string', 'sample' => '', 'desc' => 'fid'),
                    'bg_image' => array('type' => 'string', 'sample' => '', 'desc' => '背景'),
                    'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '人气值'),
                    'created' => array('type' => 'string', 'sample' => '', 'desc' => ''),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'rankings' => array(
        'description' => '排行榜',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'type' => array('default' => '', 'type' => '', 'desc' => ' RANKINGS_TYPE_ALL(0):全部, RANKINGS_TYPE_MY(1):我的排行, RANKINGS_TYPE_TOP(2):TOP排行榜, RANKINGS_TYPE_NEWBIE(3):新人榜, RANKINGS_TYPE_CONTRIBUTOR(4):我的人气, '),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'type' => array('type' => 'int', 'sample' => '', 'desc' => '排行榜类型'),
                    'rankings' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                        'accountid' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                        'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                        'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                        'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                        'value' => array('type' => 'int', 'sample' => '', 'desc' => '值(附加)'),
                        'is_following' => array('type' => 'int', 'sample' => '', 'desc' => '是否关注'),
                        'rank' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '名次(可选)'),
                        'change' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '变化(可选)'),
                        'rank_surpass' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '排名超越(可选,我的排名自己)'),
                        'yesterday_change' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '昨日变化 (可选,我的排名自己)'),

                    )),

                )),

            )),

        )
    ),
    'top_ranking' => array(
        'description' => '乐°排行榜',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'user' => array('type' => 'struct', 'desc' => '用户', 'reference' => 'USER_AVATAR'),        
                    'rank' => array('type' => 'int', 'sample' => '', 'desc' => '排名'),
                    'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '乐度'),
                    'voice' => array('type' => 'struct', 'desc' => '', 'reference' => 'VOICE'),        

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'my_title' => array(
        'description' => '我的称号',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'nickname' => array('type' => 'string', 'sample' => '', 'desc' => ''),
                'rank' => array('type' => 'int', 'sample' => '', 'desc' => '人气排名'),
                'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '人气值'),
                'pop_title' => array('type' => 'string', 'sample' => '', 'desc' => '人气称号'),
                'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                'level' => array('type' => 'int', 'sample' => '', 'desc' => '等级'),
                'upgrade_remains' => array('type' => 'int', 'sample' => '', 'desc' => '升级还需人气值'),
                'upgrade_progress' => array('type' => 'float', 'sample' => '', 'desc' => '升级进度'),
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'TITLE_ITEM'),        

            )),

        )
    ),
    'visitors' => array(
        'description' => '最近访客',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'user' => array('type' => 'struct', 'desc' => '用户', 'reference' => 'USER_AVATAR'),        
                    'created' => array('type' => 'string', 'sample' => '', 'desc' => '来访时间'),
                    'is_following' => array('type' => 'int', 'sample' => '', 'desc' => '是否关注'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'gifts' => array(
        'description' => '获取礼物',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'GIFT_ITEM2'),        

            )),

        )
    ),
    'status' => array(
        'description' => '用户最新状态',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'new_messages' => array('type' => 'int', 'sample' => '', 'desc' => '新的消息数'),
                'last_message' => array('type' => 'struct', 'desc' => '', array(
                    'type' => array('type' => 'enum','desc' => ' LAST_MESSAGE_TYPE_LEFT(1):, LAST_MESSAGE_TYPE_RIGHT(2):, ', 'reference' => 'LAST_MESSAGE_TYPE'),
                    'user' => array('type' => 'struct', 'desc' => '', 'reference' => 'USER_AVATAR'),        
                    'text' => array('type' => 'string', 'sample' => '', 'desc' => '消息文本'),

                )),
                'new_followers' => array('type' => 'int', 'sample' => '', 'desc' => '新粉丝'),
                'bid' => array('type' => 'int', 'sample' => '', 'desc' => '开房间需要的金币数'),
                'last_room_id' => array('type' => 'int', 'sample' => '', 'desc' => '新房间'),
                'last_home_status_id' => array('type' => 'int', 'sample' => '', 'desc' => '新动态'),

            )),

        )
    ),
),
'status' => array (
    'home_timeline' => array(
        'description' => '获取当前登录用户及其所关注用户的动态',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'user' => array('type' => 'struct', 'desc' => '', 'reference' => 'USER_AVATAR'),        
                    'text' => array('type' => 'string', 'sample' => '', 'desc' => '文本内容'),
                    'updated' => array('type' => 'string', 'sample' => '', 'desc' => '更新日期'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'user_timeline' => array(
        'description' => '获取用户的动态',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'user' => array('type' => 'struct', 'desc' => '', 'reference' => 'USER_AVATAR'),        
                    'text' => array('type' => 'string', 'sample' => '', 'desc' => '文本内容'),
                    'updated' => array('type' => 'string', 'sample' => '', 'desc' => '更新日期'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
),
'room' => array (
    'list_hot' => array(
        'description' => '获取热门推荐节目',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '节目id'),
                    'bg_image' => array('type' => 'string', 'sample' => '', 'desc' => '背景图片'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '标题'),
                    'voice' => array('type' => 'struct', 'desc' => '房主语音', 'reference' => 'VOICE'),        

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'list_new' => array(
        'description' => '获取最新节目',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '节目id'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '节目标题'),
                    'number' => array('type' => 'string', 'sample' => '', 'desc' => '牌号'),
                    'time' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),
                    'user' => array('type' => 'struct', 'desc' => '', array(
                        'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '用户ID'),
                        'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '用户头像'),
                        'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '用户呢称'),

                    )),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'list_by_tags' => array(
        'description' => '根据tag查询房间列表',
        'params' => array(
            'tagid' => array('default' => '', 'type' => '', 'desc' => ''),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'ROOM_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'list_my_join' => array(
        'description' => '我参与的房间',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'roomid' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '房间标题'),
                    'type' => array('type' => 'enum','desc' => '语音类型 VOICE_TYPE_ROOM(1):房主语音, VOICE_TYPE_TALK(2):创作语音, VOICE_TYPE_COMMENT(3):评论语音, ', 'reference' => 'VOICE_TYPE'),
                    'voice' => array('type' => 'struct', 'desc' => '语音', 'reference' => 'VOICE'),        
                    'created' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'show' => array(
        'description' => '显示房间',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '房间ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', 'reference' => 'ROOM_DATA'),        

        )
    ),
    'create' => array(
        'description' => '创建房间',
        'params' => array(
            'title' => array('default' => '', 'type' => '', 'desc' => ''),
            'type' => array('default' => '', 'type' => '', 'desc' => ' ROOM_TYPE_NORMAL(1):金币房间, ROOM_TYPE_GOLD(2):元宝(钻石)房间, '),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => ''),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => ''),
            'voice_image' => array('default' => '', 'isoptional'=>'1', 'type' => 'file', 'desc' => '语音附加图片'),
            'bg_image_id' => array('default' => '', 'type' => '', 'desc' => '背景图片ID'),
            'tags' => array('default' => '', 'type' => '', 'desc' => '逗号分隔tagid'),
            'bid' => array('default' => '', 'type' => '', 'desc' => '出价'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', 'reference' => 'ROOM_DATA'),        

        )
    ),
    'like' => array(
        'description' => '赞房间',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '房间ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'pay' => array(
        'description' => '给玩家发奖',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '房间ID'),
            'accountid' => array('default' => '', 'type' => '', 'desc' => '给谁发奖'),
            'talkid' => array('default' => '', 'type' => '', 'desc' => '作品ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'invite' => array(
        'description' => '邀请',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '房间ID'),
            'accountids' => array('default' => '', 'type' => '', 'desc' => '逗号分割的被邀请人的accountid'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'conclude' => array(
        'description' => '结案陈词',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '房间ID'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => ''),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'result' => array(
        'description' => '房间结束的结果',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '房间ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'conclude' => array('type' => 'string', 'sample' => '', 'desc' => '房主结束语'),
                'conclude_time' => array('type' => 'int', 'sample' => '', 'desc' => '结束语时长'),
                'winner' => array('type' => 'struct', 'desc' => '派队之星', 'reference' => 'USER_AVATAR'),        
                'talk_floor' => array('type' => 'int', 'sample' => '', 'desc' => '楼层'),
                'talk_voice' => array('type' => 'struct', 'desc' => '', 'reference' => 'VOICE'),        

            )),

        )
    ),
    'random_title' => array(
        'description' => '摇一摇房间标题',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'title' => array('type' => 'string', 'sample' => '', 'desc' => '标题'),

            )),

        )
    ),
    'tags' => array(
        'description' => '获取房间标签',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'TAG_ITEM'),        

            )),

        )
    ),
),
'talk' => array (
    'list' => array(
        'description' => '获取创作列表',
        'params' => array(
            'roomid' => array('default' => '', 'type' => '', 'desc' => '房间ID'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '作品ID'),
                    'user' => array('type' => 'struct', 'desc' => '', 'reference' => 'USER_AVATAR'),        
                    'voice' => array('type' => 'struct', 'desc' => '', 'reference' => 'VOICE'),        
                    'pop_value' => array('type' => 'int', 'sample' => '', 'desc' => '心情值'),
                    'floor' => array('type' => 'int', 'sample' => '', 'desc' => '楼层'),
                    'has_emotion' => array('type' => 'int', 'sample' => '', 'desc' => '是否发过表情(当前用户)'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'create' => array(
        'description' => '创作或房主发言',
        'params' => array(
            'roomid' => array('default' => '', 'type' => '', 'desc' => '房间ID'),
            'themeid' => array('default' => '', 'type' => '', 'desc' => '主题ID'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => ''),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => ''),
            'voice_image' => array('default' => '', 'isoptional'=>'1', 'type' => 'file', 'desc' => '语音附加图片'),
            'at' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '@的作品ID'),
            'type' => array('default' => '', 'type' => '', 'desc' => ' TALK_TYPE_CREATION(1):表演(创作), TALK_TYPE_REVIEW(2):房主发言, '),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'block' => array(
        'description' => '屏蔽此发言并禁言该用户',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '发言ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'destroy' => array(
        'description' => '隐藏该发言',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '发言ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'gifts' => array(
        'description' => '创作收到的礼物',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '发言ID'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'GIFT_ITEM'),        

            )),

        )
    ),
),
'comment' => array (
    'list' => array(
        'description' => '获取评论列表',
        'params' => array(
            'roomid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '房间ID (显示房间的评论)'),
            'talkid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '创作ID (显示作品的评论)'),
            'type' => array('default' => '', 'type' => '', 'desc' => '评论过滤 0:全部, 1:语音 2: 表情'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'COMMENT_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'create' => array(
        'description' => '发表评论',
        'params' => array(
            'roomid' => array('default' => '', 'type' => '', 'desc' => '房间ID'),
            'talkid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '创作ID (给创作评论时指定))'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => ''),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => ''),
            'voice_image' => array('default' => '', 'isoptional'=>'1', 'type' => 'file', 'desc' => '语音附加图片'),
            'emotion' => array('default' => '0', 'type' => '', 'desc' => '选择的表情'),
            'at_type' => array('default' => '2', 'type' => '', 'desc' => '@ 类型 AT_TYPE_USER(1):用户, AT_TYPE_ROOM(2):房间, AT_TYPE_TALK(3):作品, AT_TYPE_COMMENT(4):评论, '),
            'at' => array('default' => '', 'type' => '', 'desc' => '@对象的ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', 'reference' => 'COMMENT_ITEM'),        

        )
    ),
    'destroy' => array(
        'description' => '隐藏该评论',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'relation' => array (
    'following' => array(
        'description' => '关注列表',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'keywords' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '搜索关键字'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'followers' => array(
        'description' => '粉丝列表',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '用户ID,不填显示自己'),
            'keywords' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '搜索关键字'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'follow' => array(
        'description' => '关注用户',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '单个用户'),
            'ids' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '多个用户ID,逗号分隔'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'unfollow' => array(
        'description' => '取消关注',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'introduce' => array(
        'description' => '介绍朋友(给自己的粉丝)',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => '介绍谁'),
            'followers' => array('default' => '', 'type' => '', 'desc' => '介绍给谁(逗号分隔的accountid)'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'recommend_following' => array(
        'description' => '推荐关注列表',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'row1' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'row2' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'row3' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        

            )),

        )
    ),
),
'message' => array (
    'home' => array(
        'description' => '消息中心',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'type' => array('type' => 'enum','desc' => '消息类型 MESSAGE_TYPE_PRIVATE(1):悄悄话, MESSAGE_TYPE_COMMENT(2):@我的, MESSAGE_TYPE_SYSTEM(3):系统消息, ', 'reference' => 'MESSAGE_TYPE'),
                    'new_messages' => array('type' => 'int', 'sample' => '', 'desc' => '新消息数'),
                    'last_message' => array('type' => 'string', 'sample' => '', 'desc' => '最新消息'),
                    'last_time' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),

                )),

            )),

        )
    ),
    'public' => array(
        'description' => '公告,系统信息',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => '消息类型 MESSAGE_TYPE_PRIVATE(1):悄悄话, MESSAGE_TYPE_COMMENT(2):@我的, MESSAGE_TYPE_SYSTEM(3):系统消息, '),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'SYSTEM_MESSAGE_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'new_comments' => array(
        'description' => '评论总计列表',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'roomid' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '标题'),
                    'tags' => array('type' => 'string', 'sample' => '', 'desc' => '标签'),
                    'talkid' => array('type' => 'int', 'sample' => '', 'isoptional'=>'1', 'desc' => '创作ID'),
                    'talk_voice' => array('type' => 'struct', 'desc' => '作品语音', 'reference' => 'VOICE'),        
                    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '等级徽章'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '背景图'),
                    'comment_voice' => array('type' => 'struct', 'desc' => '评论语音', 'reference' => 'VOICE'),        
                    'comments' => array('type' => 'int', 'sample' => '', 'desc' => '新评论数'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'private_summary' => array(
        'description' => '私信总计列表',
        'params' => array(
            'keywords' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '搜索关键字'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'accountid' => array('type' => 'int', 'sample' => '', 'desc' => ''),
                    'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '昵称'),
                    'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '头像'),
                    'badge' => array('type' => 'string', 'sample' => '', 'desc' => '徽章'),
                    'value' => array('type' => 'int', 'sample' => '', 'desc' => '值(附加)'),
                    'is_following' => array('type' => 'int', 'sample' => '', 'desc' => '是否关注'),
                    'send_flag' => array('type' => 'enum','desc' => '发送标示 PRIVATE_MSG_SEND_FLAG_NORMAL(0):普通, PRIVATE_MSG_SEND_FLAG_SPEAKER(1):大喇叭, ', 'reference' => 'PRIVATE_MSG_SEND_FLAG'),
                    'last_time' => array('type' => 'string', 'sample' => '', 'desc' => '最后发送时间'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'private' => array(
        'description' => '私信',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => '对方accountid'),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'PRIVATE_MESSAGE_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'send' => array(
        'description' => '发私信',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '接收者ID,使用道具时不填'),
            'use_bag_item' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '使用背包道具ID'),
            'message' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '文本消息'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => '语音'),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => '语音时长'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', 'reference' => 'PRIVATE_MESSAGE_ITEM'),        

        )
    ),
    'destroy_private' => array(
        'description' => '删除私信',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => '删除类型 DESTROY_PRIVATE_TYPE_ALL(1):清空所有私信, DESTROY_PRIVATE_TYPE_ID(2):删除指定ID私信, DESTROY_PRIVATE_TYPE_ACCOUNT_ID(3):删除与某个用户的私信, '),
            'value' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '参数值'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'destroy_public' => array(
        'description' => '删除系统信息',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '消息ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'read_private' => array(
        'description' => '设置私信为已读',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '私信ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'ack' => array(
        'description' => '系统消息应答',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '系统消息ID'),
            'ack_value' => array('default' => '', 'type' => '', 'desc' => '应答值'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'mentions' => array(
        'description' => '@我的',
        'params' => array(
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'roomid' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '房间标题'),
                    'user' => array('type' => 'struct', 'desc' => '', 'reference' => 'USER_AVATAR'),        
                    'comment' => array('type' => 'struct', 'desc' => '', array(
                        'id' => array('type' => 'int', 'sample' => '', 'desc' => '评论ID'),
                        'type' => array('type' => 'enum','desc' => ' COMMENT_TYPE_VOICE(1):, COMMENT_TYPE_EMOTION(2):, ', 'reference' => 'COMMENT_TYPE'),
                        'voice' => array('type' => 'struct', 'desc' => '语音', 'reference' => 'VOICE'),        
                        'emotion' => array('type' => 'int', 'sample' => '', 'desc' => '表情'),

                    )),
                    'at_content' => array('type' => 'struct', 'desc' => '', array(
                        'id' => array('type' => 'int', 'sample' => '', 'desc' => '内容ID'),
                        'type' => array('type' => 'enum','desc' => '类型 CONTENT_TYPE_TALK(1):作品, CONTENT_TYPE_COMMENT_VOICE(2):语音评论, CONTENT_TYPE_COMMENT_EMOTION(3):表情评论, ', 'reference' => 'CONTENT_TYPE'),
                        'voice' => array('type' => 'struct', 'desc' => '语音', 'reference' => 'VOICE'),        
                        'talk_floor' => array('type' => 'int', 'sample' => '', 'desc' => '作品楼层'),
                        'comment_emotion' => array('type' => 'int', 'sample' => '', 'desc' => '评论表情'),

                    )),
                    'created' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
),
'photo' => array (
    'list' => array(
        'description' => '显示用户照片',
        'params' => array(
            'accountid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => ''),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'PHOTO_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'upload' => array(
        'description' => '上传用户照片',
        'params' => array(
            'id' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '更新照片ID,上传新照片时不填'),
            'photo' => array('default' => '', 'type' => 'file', 'desc' => '上传文件'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => ''),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', 'reference' => 'PHOTO_ITEM'),        

        )
    ),
    'destroy' => array(
        'description' => '删除用户照片',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'search' => array (
    'users' => array(
        'description' => '搜用户',
        'params' => array(
            'keywords' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'users_nearby' => array(
        'description' => '搜索附近的用户',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'open_platform_users' => array(
        'description' => '开放平台用户',
        'params' => array(
            'platform' => array('default' => '', 'type' => '', 'desc' => 'WEIBO,RENREN,T_QQ'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'rooms' => array(
        'description' => '搜房间',
        'params' => array(
            'keywords' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '节目id'),
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '节目标题'),
                    'number' => array('type' => 'string', 'sample' => '', 'desc' => '牌号'),
                    'time' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),
                    'user' => array('type' => 'struct', 'desc' => '', array(
                        'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '用户ID'),
                        'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '用户头像'),
                        'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '用户呢称'),

                    )),

                )),

            )),

        )
    ),
),
'favorite' => array (
    'rooms' => array(
        'description' => '收藏房间列表',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => '类型 FAVORITE_TYPE_ROOM(1):, '),
            'page' => array('default' => '', 'type' => '', 'desc' => '页码'),
            'count' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '每页数量限制'),
            'sinceid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),
            'maxid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '若指定此参数，则返回ID比sinceid大的数据（即比sinceid时间晚的数据），默认为0。'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'title' => array('type' => 'string', 'sample' => '', 'desc' => '节目标题'),
                    'number' => array('type' => 'string', 'sample' => '', 'desc' => '牌号'),
                    'time' => array('type' => 'string', 'sample' => '', 'desc' => '时间'),
                    'user' => array('type' => 'struct', 'desc' => '', array(
                        'accountid' => array('type' => 'int', 'sample' => '', 'desc' => '用户ID'),
                        'avatar' => array('type' => 'string', 'sample' => '', 'desc' => '用户头像'),
                        'nickname' => array('type' => 'string', 'sample' => '', 'desc' => '用户呢称'),

                    )),
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '收藏ID'),
                    'roomid' => array('type' => 'int', 'sample' => '', 'desc' => '房间ID'),

                )),
                'is_last_page' => array('type' => 'int', 'sample' => '', 'desc' => '是否是最后一页'),

            )),

        )
    ),
    'create' => array(
        'description' => '收藏',
        'params' => array(
            'type' => array('default' => '', 'type' => '', 'desc' => '类型 FAVORITE_TYPE_ROOM(1):, '),
            'objectid' => array('default' => '', 'type' => '', 'desc' => '被收藏对象的ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'id' => array('type' => 'int', 'sample' => '', 'desc' => '新创建数据ID'),

            )),

        )
    ),
    'destroy' => array(
        'description' => '收藏',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'interview' => array (
    'list' => array(
        'description' => '显示所有采访（问和答) ',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => '用户ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'INTERVIEW_ITEM'),        

            )),

        )
    ),
    'ask' => array(
        'description' => '提问',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => '给谁提问'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => '语音'),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => '时长'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'answer' => array(
        'description' => '回答',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '回答问题ID'),
            'voice' => array('default' => '', 'type' => 'file', 'desc' => '语音'),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => '时长'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'destroy' => array(
        'description' => '删除',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'item' => array (
    'bag' => array(
        'description' => '背包中的物品',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
                    'name' => array('type' => 'string', 'sample' => '', 'desc' => '名字'),
                    'itemid' => array('type' => 'int', 'sample' => '', 'desc' => '道具ID'),
                    'item_type' => array('type' => 'enum','desc' => '道具类型 ITEM_TYPE_GIFT(1):礼物道具, ITEM_TYPE_BONUS(2):捧场道具, ITEM_TYPE_VIP(3):VIP道具, ITEM_TYPE_SPEAKER(4):喇叭, ', 'reference' => 'ITEM_TYPE'),
                    'can_use' => array('type' => 'int', 'sample' => '', 'desc' => '是否可直接使用'),
                    'quantity' => array('type' => 'int', 'sample' => '', 'desc' => '数量'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '图片'),
                    'money_type' => array('type' => 'enum','desc' => ' MONEY_GOLD(1):元宝, MONEY_POINTS(2):金币, MONEY_RMB(10):人民币, ', 'reference' => 'MONEY'),
                    'description' => array('type' => 'string', 'sample' => '', 'desc' => '描述'),

                )),

            )),

        )
    ),
    'pending' => array(
        'description' => '待领的道具(暂时不需要)',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'ITEM_ENTRY'),        

            )),

        )
    ),
    'pickup' => array(
        'description' => '领取礼物',
        'params' => array(
            'pengdingitemid' => array('default' => '', 'type' => '', 'desc' => '待领道具ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'select_gift' => array(
        'description' => '选择礼物',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'available' => array('type' => 'int', 'sample' => '', 'desc' => '有多少可用'),
                'already_buy_super_package' => array('type' => 'int', 'sample' => '', 'desc' => '是否购买过超级礼包'),
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '礼物ID'),
                    'name' => array('type' => 'string', 'sample' => '', 'desc' => '礼物名称'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '图片'),
                    'money_type' => array('type' => 'enum','desc' => '购买货币类型 MONEY_GOLD(1):元宝, MONEY_POINTS(2):金币, MONEY_RMB(10):人民币, ', 'reference' => 'MONEY'),
                    'money' => array('type' => 'int', 'sample' => '', 'desc' => '价格'),
                    'can_use' => array('type' => 'int', 'sample' => '', 'desc' => '是否可立即使用'),
                    'can_buy' => array('type' => 'int', 'sample' => '', 'desc' => '是否有足够金币/钻石购买'),

                )),

            )),

        )
    ),
    'give_gift' => array(
        'description' => '打赏，送物品',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => '送给谁'),
            'talkid' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '创作ID'),
            'itemid' => array('default' => '', 'type' => '', 'desc' => '礼物ID'),
            'quantity' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '数量'),
            'buy_and_give' => array('default' => '', 'isoptional'=>'1', 'type' => '', 'desc' => '数量不足时是否先购买'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'p1_pop_value_incr' => array('type' => 'int', 'sample' => '', 'desc' => '我的人气增长'),
                'p2_pop_value_incr' => array('type' => 'int', 'sample' => '', 'desc' => '对方的人气增长'),

            )),

        )
    ),
    'thank_gift' => array(
        'description' => '感谢礼物',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '礼物ID(ItemUsing id)'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'use_item' => array(
        'description' => '使用背包中的功能道具',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '道具ID(BagItem id)'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'item_store' => array(
        'description' => '道具商店',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'ITEM_ENTRY'),        

            )),

        )
    ),
    'gold_store' => array(
        'description' => '元宝商店',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'ITEM_ENTRY'),        

            )),

        )
    ),
    'buy_item' => array(
        'description' => '购买道具',
        'params' => array(
            'itemid' => array('default' => '', 'type' => '', 'desc' => '道具ID'),
            'quantity' => array('default' => '', 'type' => '', 'desc' => '数量'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'points' => array('type' => 'int', 'sample' => '', 'desc' => '剩余金币数'),
                'golds' => array('type' => 'int', 'sample' => '', 'desc' => '剩余元宝数(钻石)'),

            )),

        )
    ),
    'app_store_buy' => array(
        'description' => 'app store 购买验证',
        'params' => array(
            'receipt' => array('default' => '', 'type' => '', 'desc' => 'app store收据'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'report' => array (
    'location' => array(
        'description' => '上报位置',
        'params' => array(
            'lng' => array('default' => '', 'type' => '', 'desc' => '经度'),
            'lat' => array('default' => '', 'type' => '', 'desc' => '纬度'),
            'location' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'device_token' => array(
        'description' => '上报设备令牌',
        'params' => array(
            'device_token' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'voice_listen' => array(
        'description' => '上报语音收听',
        'params' => array(
            'voices' => array('default' => '', 'type' => 'textarea', 'desc' => 'json字符串'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'feedback' => array(
        'description' => '用户反馈',
        'params' => array(
            'voice' => array('default' => '', 'type' => '', 'desc' => ''),
            'voice_time' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'bad_voice' => array(
        'description' => '举报不良语音',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '被举报对象ID'),
            'type' => array('default' => '', 'type' => '', 'desc' => '语音类型 VOICE_TYPE_ROOM(1):房主语音, VOICE_TYPE_TALK(2):创作语音, VOICE_TYPE_COMMENT(3):评论语音, '),
            'reason' => array('default' => '', 'type' => '', 'desc' => '举报理由 BAD_REASON_INSULT(1):语音攻击和侮辱, BAD_REASON_COPY(2):抄袭(非原创), BAD_REASON_OTHER(3):其他, '),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'click' => array(
        'description' => '上报点击',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '点击按钮ID CLICK_ID_APP_RATING(1):给应用评分, '),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'resource' => array (
    'stage_themes' => array(
        'description' => '舞台资源包列表',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'modified' => array('type' => 'int', 'sample' => '', 'desc' => '修改时间'),
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'STAGE_ITEM'),        

            )),

        )
    ),
    'room_backgrounds' => array(
        'description' => '房间背景图',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '图片URL'),
                    'thumb' => array('type' => 'string', 'sample' => '', 'desc' => ''),

                )),

            )),

        )
    ),
    'home_backgrounds' => array(
        'description' => '个人首页背景图',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => 'ID'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '图片URL'),
                    'thumb' => array('type' => 'string', 'sample' => '', 'desc' => ''),

                )),

            )),

        )
    ),
),
'share' => array (
    'create' => array(
        'description' => '分享内容',
        'params' => array(
            'plaftforms' => array('default' => '', 'type' => '', 'desc' => '"RENREN","WEIBO","T_QQ", "WECHAT"'),
            'type' => array('default' => '', 'type' => '', 'desc' => '分享对象类型 SHARE_OBJECT_TYPE_ROOM(1):房主语音, SHARE_OBJECT_TYPE_TALK(2):房间创作, '),
            'objectid' => array('default' => '', 'type' => '', 'desc' => '分享对象的ID'),
            'message' => array('default' => '', 'type' => '', 'desc' => '文字内容'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'task' => array (
    'list' => array(
        'description' => '任务列表',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', array(
                    'id' => array('type' => 'int', 'sample' => '', 'desc' => '任务ID'),
                    'name' => array('type' => 'string', 'sample' => '', 'desc' => '任务名'),
                    'image' => array('type' => 'string', 'sample' => '', 'desc' => '左侧图片'),
                    'description' => array('type' => 'string', 'sample' => '', 'desc' => '任务描述'),
                    'is_accomplish' => array('type' => 'int', 'sample' => '', 'desc' => '是否完成'),
                    'type' => array('type' => 'enum','desc' => '任务类型 TASK_TYPE_ONCE(1):新手任务, TASK_TYPE_DAILY(2):每日任务, ', 'reference' => 'TASK_TYPE'),
                    'award_type' => array('type' => 'enum','desc' => '奖励类型 TASK_AWARD_TYPE_POINTS(1):金币, TASK_AWARD_TYPE_FLOWER(2):鲜花, TASK_AWARD_TYPE_POP_VALUE(3):人气值, ', 'reference' => 'TASK_AWARD_TYPE'),
                    'awards' => array('type' => 'int', 'sample' => '', 'desc' => '奖励数'),

                )),

            )),

        )
    ),
    'pickup_awards' => array(
        'description' => '领取任务奖金',
        'params' => array(
            'id' => array('default' => '', 'type' => '', 'desc' => '任务ID'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'block' => array (
    'list' => array(
        'description' => '黑名单',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),
            'data' => array('type' => 'struct', 'desc' => '', array(
                'items' => array('type' => 'struct', 'isarray'=>'1', 'desc' => '', 'reference' => 'USER_ITEM'),        

            )),

        )
    ),
    'create' => array(
        'description' => '加入黑名单',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'destroy' => array(
        'description' => '移出黑名单',
        'params' => array(
            'accountid' => array('default' => '', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),
'server' => array (
    'debug' => array(
        'description' => 'debug',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'phpinfo' => array(
        'description' => 'phpinfo',
        'params' => array(
        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'memcache_delete' => array(
        'description' => '清除缓存',
        'params' => array(
            'key' => array('default' => 'model:UserProfile:101017', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'memcache_get' => array(
        'description' => '读取缓存',
        'params' => array(
            'key' => array('default' => 'model:UserProfile:101017', 'type' => '', 'desc' => ''),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
    'schema' => array(
        'description' => '获取schema',
        'params' => array(
            'model' => array('default' => '', 'type' => '', 'desc' => 'model名'),

        ),
        'output' => array(
            'code' => array('type' => 'int', 'sample' => '', 'desc' => ''),
            'message' => array('type' => 'string', 'sample' => '', 'desc' => ''),

        )
    ),
),

);
?>