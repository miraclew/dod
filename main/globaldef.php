<?php

define('APP_ID', 4);//appid
define('APP_NAME', '口袋派对');//appid
define('APP_WEBSITE', 'http://www.htapp.cn');//appid
define('WEIBO', 'wb');
define('QQ', 'qq');
define('RENREN', 'rr');

define('MSG_OUT', 1);
define('MSG_IN', 0);
define('IMG_URL_RELATIVE_', 'p/');
define('VOICE_URL_RELATIVE_', 'v/');//语音文件上传路径
define('IMG_FILE_TYPE', '1');//图片文件类型
define('VOICE_FILE_TYPE', '2');//语音文件类型
define('DATETIMEFORMAT', 'Y-m-d H:i:s');
define('DATEFORMAT', 'Y-m-d');
define('TIMEFORMAT', 'H:i:s');
define('DATEHMFORMAT', 'm-d H:i');
define('TIMEHMFORMAT', 'H:i');
define('DATEMDFORMAT', 'm-d');

define('MESSAGE_QUEUE_NAME' , 'messagequeue');
define('USER_SERVICE_SUCCESS' , 0);
define('XOR_KEY' , 'M32$52MLs32332!2');

define('CLIENT_DATA_COOKIE' , 'clientdata');
define('REQUESTID_COOKIE' , 'ric');
define('BIZTYPE_COOKIE' , 'btc');

define('NICKNEMA_SHORT', 4);//注册是昵称的长度限制 汉字为准
define('NICKNEMA_LONG', 12);//注册是昵称的长度限制 汉字为准
define('TITLE_SHORT', 4);//舞台名长度的限制 汉字为准
define('TITLE_LONG', 32);//舞台名长度的限制 汉字为准

define('MSG_BACK_KEY' , 'msgback');//后台消息key定义
define('MSG_REGISTER_TYPE' , '100');//用户注册消息
define('MSG_LOGIN_TYPE' , '110');//用户登录消息
define('MSG_LOGOUT_TYPE' , '120');//用户注销消息
define('MSG_FRIEND_AGAINST' , '125');//好友抬杠消息
define('MSG_IOS_PUSH' , '126');//IOS消息推送


define('MSG_SENDMSG_KEY' , 'sendmessage');//后台消息key定义
define('MSG_SENDSYSTEMMSG_KEY' , 'sendsystemmessage');//后台生成系统消息key定义
define('MSG_SENDMESSAGE_TYPE' , '130');//客服发送私信给用户
define('MSG_SENDSYSTEMMESSAGE_TYPE' , '150');//群发系统消息给用户
define('MSG_SENDFRIENDMESSAGE_TYPE' , '160');//群发来电消息给用户
define('MSG_SENDPOPUPMESSAGE_TYPE' , '170');//群发弹窗消息给用户
define('MSG_SENDGIFT_TYPE' , '180');//官方送礼物

define('MSG_THUMBNAIL_KEY' , 'thumbnail');//后台消息生成缩略图key定义
define('MSG_THUMBNAIL' , '200');//生成用户头像缩略图

define('HOME_ROOM_BIG_NUM' , '5');//广场页面 大图数量

define('CACHE_KEY_FILTER_WORDS' , 'filterwords');//敏感词数组缓存key
define('MSG_CHECK_KEY' , 'questioncheck');//待审核消息key定义

define('QUESTION_AREA', 1);//抬杠区
define('TOPIC_AREA', 2);//非杠区
define('POPUPKEY_D', 'popupkey_%d');//弹窗key。D为accountid

define('MSG_POPUP_INVITED_TYPE' , '500');//被申请好友
define('MSG_POPUP_CHECK_FAIL' , '510');//您发的杠题没有通过审核
define('MSG_POPUP_CHECK_SUCCESS' , '520');//您发的杠题已经通过审核
define('MSG_POPUP_QUESTION_AGAINSTED' , '530');//您发的杠题被XX抬了
define('MSG_POPUP_QUESTION_TOPPED' , '550');//您发的杠题被管理员加精了
define('MSG_POPUP_UPGRADE_TYPE' , '560');//抬杠版本已升级到XX版本。（官方主动发布类型,全服可见）
define('MSG_POPUP_BE_JUDGE_TYPE' , '570');//XX经过官方，成为抬杠评审。（官方主动发布类型,全服可见）
define('POPUP_TIMEOUT' , '100');//弹窗消息redis生存时间
define('VERSION_006', '0.7.6');//版本号定义 该定义固定不变，用于区分新增消息类型要求的客户端版本
define('VERSION_090', '0.9.0');//版本号定义 该定义固定不变，用于区分新增消息类型要求的客户端版本
define('POPUP_SHOWTIME' , '0');//弹窗消息客户端显示时间
define('QUESTION_ORDER_RULE' , '500');//对抬杠区列表启用A500排序算法。即我发起的，好友发起的，其他（按影响力）
define('TOPIC_ORDER_RULE' , '500');//对靓杠区列表启用A500排序算法。即我发起的，好友发起的，其他（按影响力）
define('ORDER_RULE_LIMIT' , '500');//当杠题数量小于该值时，才可能对抬杠区列表启用A500排序算法。

define('VOICE_TYPE_ASK', 1);//发杠语音
define('VOICE_TYPE_AGAINST', 2);//抬杠语音
define('VOICE_TYPE_COMMENT', 3);//评论语音
define('VOICE_TYPE_NONE', 10);//暗杠 ，无评论语音

define('OFFICIAL_VOICE', 'v/voice_cover_0.mp3');//官方置换语音
define('OFFICIAL_VOICETIME', 7);//语音长度
define('OFFICIAL_VOICE_FEMALE', 'v/voice_cover_0.mp3');//官方置换语音(女）
define('OFFICIAL_VOICETIME_FEMALE', 7);//语音长度
define('OFFICIAL_VOICE_MALE', 'v/voice_cover_1.mp3');//官方置换语音(男）
define('OFFICIAL_VOICETIME_MALE', 7);//语音长度

define('OFFICIAL_AVATAR', 'img/official/official_avatar.png');//官方头像

define('OFFICIAL_CONCLUDE_VOICE_TIME', 10); //
define('OFFICIAL_CONCLUDE_VOICE', 'http://dev.hoodinn.com/aloha/v/12/10/26/v_8d8984d942010c93d2dd24485f865b82.caf'); //


define('CACHEKEY_TIPLOCK_DD', 'tiplock_%d_%d');//举报置换加锁key questiontype+commentid
define('MAX_ID', 4294967295);//无符号整数最大值

define('MONEY_TYPE_GOLD', 1);//元宝货币
define('MONEY_TYPE_POINT', 2);//分贝货币
define('CACHE_KEY_EVENT_QUEUE' , 'event_queue');//大事记缓存队列key

define('PICTURE_TYPE_AVATAR', 10);//上传头像
define('PICTURE_TYPE_ASK', 1);//发杠上传图片
define('PICTURE_TYPE_AGAINST', 2);//抬杠上传图片

define('THUMBNAIL_SIZE', 60);//发杠和抬杠图片的缩略图尺寸
define('THRIFT_ROOT', MAIN_ . 'lib/third/thrift');//
define('USERSTHRIFT_ROOT', MAIN_ . 'lib/usersthrift');//

define("APPLE_CREDIT_DEVELOPMENT_URL","https://sandbox.itunes.apple.com/verifyReceipt");//测试
define("APPLE_CREDIT_PRODUCTION_URL","https://buy.itunes.apple.com/verifyReceipt");//生产

define('MALE', 1);
define('FEMALE', 0);
define('MSG_SENDGIFTMSG_KEY', 'officialgifts');

// 系统事件名
class EventNames {
	const USER_REGISTER 	= 'User.Register';
	const USER_LOGIN 		= 'User.Login';
	const USER_LOGOUT 		= 'User.Logout';
	const RELATION_FOLLOW 	= 'Relation.Follow';
	const RELATION_UNFOLLOW = 'Relation.Unfollow';
	const ROOM_CREATE 		= 'Room.Create';
	const ROOM_PAY 			= 'Room.Pay';
	const ROOM_CONCLUDE		= 'Room.Conclude';
	const TALK_CREATE 		= 'Talk.Create';
	const TALK_SHARE		= 'TALK.Share';
	const COMMENT_CREATE 	= 'Comment.Create';
	const ITEM_USING		= 'Item.Using';
}

// 离线任务名
class JobNames {
	const DELIVER_SPEAKER_MESSAGE 	= "SpeakerJob";// 喇叭消息
	const DELIVER_ROOM_CONCLUDE		= "DELIVER_ROOM_CONCLUDE";
	const APNS_PUSH 				= "ApnsPushJob"; // apple's notification service push
	const FANOUT 					= "FanoutJob"; // fanout
	const THUMBNAIL 				= 'ThumbnailJob'; // 图片缩略图
}

class FanoutType {
	const ROOM_CREATE 		= 'room_create';
	const ROOM_CLEARING 	= 'room_clearing';
	const ROOM_CLOSE 		= 'room_close';
	const ROOM_INVITE 		= 'room_invite';
	const ROOM_RECOMMEND 	= 'room_recommend';
	const TALK_CREATE 		= 'talk_create';
	const COMMENT_CREATE 	= 'comment_create';
	const SHARE_CREATE 		= 'share_create';
	const FAVORITE_CREATE 	= 'favorite_create';
	const RELATION_CREATE 	= 'relation_create';
	const INTRODUCTION 		= 'introduction';
	const LEVEL_UP 			= 'level_up';	
	const APP_UPGRADE 		= 'app_upgrade';
}

// 任务队列名称
class QueueNames {
	const ALOHA	= 'aloha';
}
