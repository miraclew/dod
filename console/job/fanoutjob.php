<?php
require_once __DIR__.'/job.php';
/**
 * 扇出任务
 * 
 * 把新信息投递到粉丝的时间线上
 */
class FanoutJob extends Job {
	public function perform()
	{
		Log::write("perform: ".date('Y-m-d H:i:s')." ".print_r($this->args, true)."\n", __CLASS__);
		$type = $this->args['type'];		
		$accountid = $this->args['accountid'];
		$data = $this->args['data'];
		
		$method = "on_$type";
		if (!method_exists($this, $method)) {
			Log::write(__FUNCTION__." method not found: $method",  __CLASS__);
			return;
		}
		
		$followers = Follow::get_follower_ids($accountid);
		$res = $this->$method($data, $followers);
		if ($res != Err::$SUCCESS) {
			Log::write("$method error: code=".$res[0]." msg=".$res[1],  __CLASS__);
		}
	}
	
	private function on_room_create($data, $followers) {
		$id = $data['id'];
		$accountid = $this->args['accountid'];
		$room = Room::findByPk($id); /* @var $room Room */
		if (!$room) return Err::$DATA_NOT_FOUND;
		
		$room_tag = HtmlTag::room_link($id, $room->title);
		$text = "我刚开了派对 {$room_tag}，粉丝们我在等着你们！";
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		
		foreach ($followers as $follower_id) {
			HomeStatus::create($follower_id, $accountid, $text);
		}
		
		return Err::$SUCCESS;
	}
	
	private function on_room_recommend($data, $followers) {
		$id = $data['id'];
		$accountid = $this->args['accountid'];
		$room = Room::findByPk($id); /* @var $room Room */
		if (!$room) return Err::$DATA_NOT_FOUND;
		
		$room_tag = HtmlTag::room_link($id, $room->title);
		$text = "我的派对 {$room_tag}，被官方推荐了！粉丝们快来为我加油鼓劲吧！";
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		
		foreach ($followers as $follower_id) {
			HomeStatus::create($follower_id, $accountid, $text);
		}
		
		return Err::$SUCCESS;
	}
	
	private function on_room_invite($data, $followers) {
		$accountid = $this->args['accountid'];
		$id = $data['id'];
		$p2_ids = $data['accountids'];
		
		$room = Room::findByPk($id); /* @var $room Room */
		if (!$room) return Err::$DATA_NOT_FOUND;
		$room_tag = HtmlTag::room_link($id, $room->title);
		$p1_merge = count($p2_ids) > 3; // 是否需要合并p1的动态
		$p1_merg_users = array();
		
		foreach ($p2_ids as $p2_id) {
			$p2 = UserProfile::findByPk($p2_id);		
			$user_tage = HtmlTag::user_link($p2->accountid, $p2->nickname);
			$text = "我邀请了{$user_tage}参加派对{$room_tag}";

			if ($p1_merge) { 
				if (count($p1_merg_users) <= 3) {
					$p1_merg_users[] = $user_tage;
				}
			}
			else { 
				// p1
				UserStatus::create($accountid, $text);
				HomeStatus::create($accountid, $accountid, $text);
			}
			
			// p2
			$text = "hi,{$p2->nickname}！我隆重邀请您来参加派对{$room_tag}";
			UserStatus::create($p2->accountid, $text);
			HomeStatus::create($p2->accountid, $accountid, $text);
		}
		
		// p1		
		if ($p1_merge) {
			$text = "我邀请了".implode(',', $p1_merg_users)."等人参加派对{$room_tag}";
			UserStatus::create($accountid, $text);
			HomeStatus::create($accountid, $accountid, $text);
		}
		
		return Err::$SUCCESS;
	}
	
	private function on_room_close($data, $followers) {		
		$id = $data['id'];
	
		$room = Room::findByPk($id); /* @var $room Room */
		if (!$room) return Err::$DATA_NOT_FOUND;
		$accountid = $room->accountid;
		
		$room_tag = HtmlTag::room_link($id, $room->title);
		if ($room->has_talk) {
			$voice_tag = HtmlTag::audio($room->conclude, $room->conclude_time);
			$text = "我的派对{$room_tag}已经圆满结束，感谢大家的参与，有空听听我的谢幕辞吧！{$voice_tag}";
			
			// audiences
			$audiences = array_merge($followers, $room->get_participant_ids());
			$audiences = array_unique($audiences);
			
			foreach ($audiences as $follower_id) {
				if ($follower_id == $accountid) {
					continue;
				}
				HomeStatus::create($follower_id, $accountid, $text);
			}			
		}
		else {
			$text = "我的派对{$room_tag}结束了，没有任何表演内容。";			
		}
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
	}
	
	private function on_room_clearing($data, $followers) {
		$accountid = $this->args['accountid'];
		$id = $data['id'];
	
		$room = Room::findByPk($id); /* @var $room Room */
		if (!$room) return Err::$DATA_NOT_FOUND;
	
		$room_tag = HtmlTag::room_link($id, $room->title);
		$text = "我正在为我的派对{$room_tag}进行颁奖，请耐心等待哦，结果马上揭晓！";
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
	}
	
	private function on_talk_create($data, $followers) {
		$id = $data['id'];
		$accountid = $this->args['accountid'];
		$talk = Talk::findByPk($id); /* @var $talk Talk */
		if (!$talk) return Err::$DATA_NOT_FOUND;
		
		$room = Room::findByPk($talk->roomid);		
		$room_tag = HtmlTag::room_link($talk->roomid, $room->title);
		$voice_tag = HtmlTag::audio($talk->voice, $talk->voice_time);
		$text = "我刚参加了派对 {$room_tag}，并进行了表演。{$voice_tag}";
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		
		// p1 fans
		foreach ($followers as $follower_id) {
			HomeStatus::create($follower_id, $accountid, $text);
		}
		
		// p2 (room owner)
		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
		$user_tage = HtmlTag::user_link($accountid, $profile->nickname);
		$text = "{$user_tage}刚在我的派对里进行了表演";
		HomeStatus::create($room->accountid, $accountid, $text);
		return Err::$SUCCESS;		
	}
	
	private function on_comment_create($data, $followers) {
		$accountid = $this->args['accountid'];
		$id = $data['id'];
		$comment = Comment::findByPk($id); /* @var $comment Comment */
		if (!$comment) return Err::$DATA_NOT_FOUND;
		
		$room = Room::findByPk($comment->roomid);		
		$room_tag = HtmlTag::room_link($comment->roomid, $room->title);
		if ($comment->emotion == 0) {
			$voice_tag = HtmlTag::audio($comment->voice, $comment->voice_time);
			$text = "我刚参加了派对 {$room_tag}，并对发表了评论。{$voice_tag}";			
		}
		else {
			$emotion_tag = HtmlTag::image(ResourceComponent::instance()->get_emotion_image($comment->emotion));
			$text = "我刚参加了派对 {$room_tag}，发表了心情。$emotion_tag";			
		}
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		
		foreach ($followers as $follower_id) {
			HomeStatus::create($follower_id, $comment->accountid, $text);
		}		
		
		// @ stuff
		
		
		
		return Err::$SUCCESS;
	}
	
	// 只有介绍人和被介绍人会有新动态
	private function on_introduction($data, $followers) {
		$accountid = $this->args['accountid'];
		$targetid = $data['targetid'];
		$target = UserProfile::findByPk($targetid);
		$user_tag = HtmlTag::user_link($targetid, $target->nickname);
		$text = "我把新朋友{$user_tag}介绍给了粉丝们认识";
		
		// p1
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		// p2
		$text = "Hi, $target->nickname!我把你介绍给了我的粉丝们，你要加油表现哦！";		
		HomeStatus::create($targetid, $accountid, $text);
		
		//$followers = explode(',', $data['followers']);
		foreach ($followers as $follower_id) {
			$follower = UserProfile::findByPk($follower_id);
			$text = "hi，{$follower->nickname}！我介绍了新朋友{$user_tag}给你认识，相信你会喜欢Ta的。";			
			HomeStatus::create($follower_id, $accountid, $text);				
		}		
	}
	
	// 不生成动态，生成消息
	private function on_relation_create($data, $followers) {
		$id = $data['id'];
		$follow = Follow::findByPk($id); /* @var $follow Follow */
		if (!$follow) return Err::$DATA_NOT_FOUND;
		
		$profile = UserProfile::findByPk($follow->targetid);
		$text = "我关注了{$profile->nickname}（$profile->title）";
		foreach ($followers as $follower_id) {
			HomeStatus::create($follower_id, $follow->accountid, $text);			
		}
		return Err::$SUCCESS;
	}
	
	private function on_favorite_create($data, $followers) {
		$id = $data['id'];
		$accountid = $this->args['accountid'];
		$favorite = Favorite::findByPk($id); /* @var $favorite Favorite */
		if (!$favorite) return Err::$DATA_NOT_FOUND;
		
		$profile = UserProfile::findByPk($favorite->accountid); /* @var $profile UserProfile */
		
		if ($favorite->type == ApiConst::FAVORITE_TYPE_ROOM) {
			$room = Room::findByPk($favorite->objectid);
			if (!$room) return Err::$SUCCESS;
			
			$room_tag = HtmlTag::room_link($room->id, $room->title);
			$text = "我收藏了派对 {$room_tag}。";
			// p1
			UserStatus::create($accountid, $text);
			HomeStatus::create($accountid, $accountid, $text);
			
			// p1 fans
			foreach ($followers as $follower_id) {
				HomeStatus::create($follower_id, $favorite->accountid, $text);
			}
			
			// p2			
			$text = "{$profile->nickname}收藏了我的派对{$room_tag}。";
			UserStatus::create($room->accountid, $text);
			HomeStatus::create($room->accountid, $accountid, $text);
			
			// p2 fans
			$p2_followers = Follow::get_follower_ids($room->accountid);			
			foreach ($p2_followers as $follower_id) {
				$follower = UserProfile::findByPk($follower_id);
				$text = "hi,{$follower->nickname}！我的派对{$room_tag}刚被人收藏了！";
				HomeStatus::create($follower_id, $room->accountid, $text);
			}
		}
		
		return Err::$SUCCESS;
	}
	
	private function on_share_create($data, $followers) {
		$id = $data['id'];
		$accountid = $this->args['accountid'];
		$share = Share::findByPk($id); /* @var $share Share */
		if (!$share) return Err::$DATA_NOT_FOUND;
		
		$profile = UserProfile::findByPk($share->accountid); /* @var $profile UserProfile */		
		
		if ($share->type == ApiConst::SHARE_OBJECT_TYPE_TALK) {
			$talk = Talk::findByPk($share->objectid);
			if (!$talk) return Err::$SUCCESS;
			
			$room = Room::findByPk($talk->roomid);
			$room_tag = HtmlTag::room_link($talk->roomid, $room->title);			
			//$text = "我刚参加了派对{$room_tag}，并分享了第{$talk->floor}席的表演到我的 {$share->platform_name}。";
			$text = "我刚参加了派对{$room_tag}，并分享了第{$talk->floor}席的表演。";
			
			// p1
			$p1 = UserProfile::findByPk($accountid);
			UserStatus::create($accountid, $text);
			HomeStatus::create($accountid, $accountid, $text);
			
			// p2
			//$text = "{$p1->nickname}刚分享了我在派对{$room_tag}中的第{$talk->floor}席的表演到他的{$share->platform_name}";
			$text = "{$p1->nickname}刚分享了我在派对{$room_tag}中的第{$talk->floor}席的表演";
			UserStatus::create($room->accountid, $text);
			HomeStatus::create($room->accountid, $accountid, $text);
			
			$p2_followers = Follow::get_follower_ids($room->accountid);
			foreach ($p2_followers as $follower_id) {
				$follower = UserProfile::findByPk($follower_id);
				//$text = "hi,{$follower->nickname}！{$p1->nickname}刚分享了我在派对{$room_tag}中的第{$talk->floor}席的表演到他的{$share->platform_name}";
				$text = "hi,{$follower->nickname}！{$p1->nickname}刚分享了我在派对{$room_tag}中的第{$talk->floor}席的表演";
				HomeStatus::create($follower_id, $accountid, $text);
			}
		}
	}
	
	private function on_level_up($data, $followers) {
		$accountid = $this->args['accountid'];
		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */		
		
		// p1
		$text = "我刚升级为{$profile->title}，大家快跟上哟！";		
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		
		foreach ($followers as $follower_id) {			
			HomeStatus::create($follower_id, $accountid, $text);
		}
	}
	
	private function on_app_upgrade($data, $followers) {
		$accountid = $this->args['accountid'];
		
		// p1
		$text = "我刚更新了最新版的口袋派对，新版本最大的特点是能够发视频啦！大家快跟上哟！";
		UserStatus::create($accountid, $text);
		HomeStatus::create($accountid, $accountid, $text);
		
		foreach ($followers as $follower_id) {
			HomeStatus::create($follower_id, $accountid, $text);
		}
	}
	
}