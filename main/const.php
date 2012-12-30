<?php
class ApiConst {
    const API_VERSION = 1;  // 

    const GENDER_FEMALE = 0;  // : 女
    const GENDER_MALE = 1;  // : 男

    const USER_TYPE_NORMAL = 1;  // 用户类型: 普通用户
    const USER_TYPE_GM = 2;  // 用户类型: GM用户

    const SECTION_TYPE_PUBLIC = 1;  // 分组类型: 广场
    const SECTION_TYPE_USER = 2;  // 分组类型: 用户中心

    const ROOM_TYPE_NORMAL = 1;  // 房间类型: 金币房间
    const ROOM_TYPE_GOLD = 2;  // 房间类型: 元宝(钻石)房间

    const ROOM_AWARD_TYPE_OWNER_AWARD = 1;  // 房间获奖类型: 房主奖励
    const ROOM_AWARD_TYPE_AUTO_AWARD = 2;  // 房间获奖类型: 系统自动奖励

    const ROOM_STATUS_NORMAL = 1;  // 房间状态: 进行中
    const ROOM_STATUS_CLEARING = 2;  // 房间状态: 结算中
    const ROOM_STATUS_CLOSED = 3;  // 房间状态: 已结束

    const PUBLIC_ROOM_LIST_TYPE_ALL = 0;  // 大厅房间列表类型: 所有
    const PUBLIC_ROOM_LIST_TYPE_FEATURED = 1;  // 大厅房间列表类型: 每日精华
    const PUBLIC_ROOM_LIST_TYPE_GOLD = 2;  // 大厅房间列表类型: 钻石舞台

    const USER_ROOM_LIST_TYPE_MY_ROOMS = 1;  // 用户房间列表类型: 我开的房间
    const USER_ROOM_LIST_TYPE_MY_JOINS = 2;  // 用户房间列表类型: 我参加的房间
    const USER_ROOM_LIST_TYPE_FOLLOWING_ROOMS = 3;  // 用户房间列表类型: 关注人开的房间
    const USER_ROOM_LIST_TYPE_FOLLOWING_JOINS = 4;  // 用户房间列表类型: 关注人参加的房间
    const USER_ROOM_LIST_TYPE_RECOMMEND_ROOMS = 5;  // 用户房间列表类型: 系统推荐

    const TALK_TYPE_CREATION = 1;  // 说话类型: 表演(创作)
    const TALK_TYPE_REVIEW = 2;  // 说话类型: 房主发言

    const MESSAGE_TYPE_PRIVATE = 1;  // 消息类型: 悄悄话
    const MESSAGE_TYPE_COMMENT = 2;  // 消息类型: @我的
    const MESSAGE_TYPE_SYSTEM = 3;  // 消息类型: 系统消息

    const MESSAGE_SUB_TYPE_PRIVATE_NORMAL = 101;  // 消息子类型: 普通悄悄话
    const MESSAGE_SUB_TYPE_PRIVATE_SPEAKER = 102;  // 消息子类型: 喇叭
    const MESSAGE_SUB_TYPE_COMMENT_VOICE = 201;  // 消息子类型: 语音@
    const MESSAGE_SUB_TYPE_COMMENT_EMOTION = 202;  // 消息子类型: 表情@
    const MESSAGE_SUB_TYPE_SYSTEM_UPGRADE = 301;  // 消息子类型: 升级提醒
    const MESSAGE_SUB_TYPE_SYSTEM_URGENT = 302;  // 消息子类型: 紧急通知
    const MESSAGE_SUB_TYPE_SYSTEM_ACTIVITY = 303;  // 消息子类型: 新活动
    const MESSAGE_SUB_TYPE_SYSTEM_REWARDS = 304;  // 消息子类型: 活动奖励
    const MESSAGE_SUB_TYPE_SYSTEM_ROOM_RECOMMEND = 305;  // 消息子类型: 房间被推荐
    const MESSAGE_SUB_TYPE_SYSTEM_TALK_DELETED = 306;  // 消息子类型: 房间被删除

    const MESSAGE_TYPE_Filter_ALL = 0;  // 用户消息类型过滤: 全部
    const MESSAGE_TYPE_Filter_SYSTEM = 1;  // 用户消息类型过滤: 系统消息
    const MESSAGE_TYPE_Filter_INVITATION = 2;  // 用户消息类型过滤: 邀请
    const MESSAGE_TYPE_Filter_ROOM_NOTIFY = 3;  // 用户消息类型过滤: 房间提醒

    const VOICE_TYPE_ROOM = 1;  // 语音类型: 房主语音
    const VOICE_TYPE_TALK = 2;  // 语音类型: 创作语音
    const VOICE_TYPE_COMMENT = 3;  // 语音类型: 评论语音

    const BAD_REASON_INSULT = 1;  // 不良内容举报理由: 语音攻击和侮辱
    const BAD_REASON_COPY = 2;  // 不良内容举报理由: 抄袭(非原创)
    const BAD_REASON_OTHER = 3;  // 不良内容举报理由: 其他

    const FAVORITE_TYPE_ROOM = 1;  // 收藏类型: 

    const INTERVIEW_TYPE_SYSTEM = 1;  // 采访项类型: 系统问题
    const INTERVIEW_TYPE_USER = 2;  // 采访项类型: 用户问题

    const ITEM_TYPE_GIFT = 1;  // 道具类型: 礼物道具
    const ITEM_TYPE_BONUS = 2;  // 道具类型: 捧场道具
    const ITEM_TYPE_VIP = 3;  // 道具类型: VIP道具
    const ITEM_TYPE_SPEAKER = 4;  // 道具类型: 喇叭

    const ITEM_SUB_TYPE_VIP_1 = 301;  // 道具子类型: VIP1道具
    const ITEM_SUB_TYPE_VIP_2 = 302;  // 道具子类型: VIP2道具

    const PROFILE_UPLOAD_TYPE_AVATAR = 1;  // 上传个人资料类型: 头像
    const PROFILE_UPLOAD_TYPE_BACKGROUND_IMAGE = 2;  // 上传个人资料类型: 背景图片(暂不支持)
    const PROFILE_UPLOAD_TYPE_VOICE_SIGN = 3;  // 上传个人资料类型: 语音签名

    const RANKINGS_TYPE_ALL = 0;  // 排行榜类型: 全部
    const RANKINGS_TYPE_MY = 1;  // 排行榜类型: 我的排行
    const RANKINGS_TYPE_TOP = 2;  // 排行榜类型: TOP排行榜
    const RANKINGS_TYPE_NEWBIE = 3;  // 排行榜类型: 新人榜
    const RANKINGS_TYPE_CONTRIBUTOR = 4;  // 排行榜类型: 我的人气

    const MONEY_GOLD = 1;  // 货币: 元宝
    const MONEY_POINTS = 2;  // 货币: 金币
    const MONEY_RMB = 10;  // 货币: 人民币

    const POINTS_TRANS_BUY_GOLD = 1;  // 金币交易类型: 购买元宝

    const GOLD_TRANS_BUY_GOLD = 1;  // 元宝交易: 购买元宝

    const ITEM_GET_TYPE_BUY = 1;  // 道具获取方式: 购买获得
    const ITEM_GET_TYPE_SYSTEM_GIVE = 2;  // 道具获取方式: 系统赠送
    const ITEM_GET_TYPE_ITEM_DERIVE = 3;  // 道具获取方式: 道具派生
    const ITEM_GET_TYPE_TASK_AWARDS = 4;  // 道具获取方式: 任务奖励

    const PAYMENT_APPLE = 1;  // 支付方式: 苹果

    const SHARE_OBJECT_TYPE_ROOM = 1;  // 分享对象类型: 房主语音
    const SHARE_OBJECT_TYPE_TALK = 2;  // 分享对象类型: 房间创作

    const BUTTON_STATE_NONE = 0;  // 按钮状态: 无按钮
    const BUTTON_STATE_ENABLED = 1;  // 按钮状态: 正常按钮
    const BUTTON_STATE_DISABLED = 2;  // 按钮状态: 灰按钮

    const PRIVATE_MSG_SEND_FLAG_NORMAL = 0;  // 私信发送标示: 普通
    const PRIVATE_MSG_SEND_FLAG_SPEAKER = 1;  // 私信发送标示: 大喇叭

    const DESTROY_PRIVATE_TYPE_ALL = 1;  // : 清空所有私信
    const DESTROY_PRIVATE_TYPE_ID = 2;  // : 删除指定ID私信
    const DESTROY_PRIVATE_TYPE_ACCOUNT_ID = 3;  // : 删除与某个用户的私信

    const DESTROY_PUBLIC_TYPE_COMMUNITY = 1;  // : 社区消息
    const DESTROY_PUBLIC_TYPE_ROOM = 2;  // : 房间消息

    const RELATION_TYPE_NONE = 0;  // : 未关注
    const RELATION_TYPE_FOLLOWED = 1;  // : 被关注
    const RELATION_TYPE_FOLLOWING = 2;  // : 已关注
    const RELATION_TYPE_BOTH = 3;  // : 相互关注

    const TASK_AWARD_TYPE_POINTS = 1;  // : 金币
    const TASK_AWARD_TYPE_FLOWER = 2;  // : 鲜花
    const TASK_AWARD_TYPE_POP_VALUE = 3;  // : 人气值

    const TASK_TYPE_ONCE = 1;  // : 新手任务
    const TASK_TYPE_DAILY = 2;  // : 每日任务

    const CLICK_ID_APP_RATING = 1;  // : 给应用评分

    const HIDE_BY_SELF = 1;  // : 自己
    const HIDE_BY_ROOM_OWNER = 2;  // : 房主
    const HIDE_BY_ADMIN = 3;  // : 后台管理

    const HIDE_FLAG_NONE = 0;  // : 未隐藏
    const HIDE_FLAG_OTHERS = 1;  // : 对所有人隐藏(仅本人能看)

    const AT_TYPE_USER = 1;  // : 用户
    const AT_TYPE_ROOM = 2;  // : 房间
    const AT_TYPE_TALK = 3;  // : 作品
    const AT_TYPE_COMMENT = 4;  // : 评论

    const COMMENT_TYPE_VOICE = 1;  // : 
    const COMMENT_TYPE_EMOTION = 2;  // : 

    const CONTENT_TYPE_TALK = 1;  // : 作品
    const CONTENT_TYPE_COMMENT_VOICE = 2;  // : 语音评论
    const CONTENT_TYPE_COMMENT_EMOTION = 3;  // : 表情评论

    const LAST_MESSAGE_TYPE_LEFT = 1;  // : 
    const LAST_MESSAGE_TYPE_RIGHT = 2;  // : 


}
?>