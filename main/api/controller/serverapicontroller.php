<?php
/**
 * 服务端状态调试工具
 */
class ServerApiController extends ApiController {
	public function www_debug() {
		$gmclient= new GearmanClient();
		$gmclient->addServer();		
		$gmclient->doNormal("aloha_worker", array('job'=>'Fanout'));
		$ret = $gmclient->returnCode();
		if ($ret != GEARMAN_SUCCESS) {
			debug($ret);
		}
		
		echo "OK";
		
// 		UserProfile::update_all(array('pop_value'=> 0), "");
		
// 		$url = "http://redis.io/images/redis.png";
// 		$img = Image::load_from_url($url);
// 		$img->resize(Image::SIZE_80x80);
// 		$url2 = $img->get_url(Image::SIZE_80x80);
// 		echo "<a href='$url2'>$url2</a>";
		
// 		$room = Room::findByPk(928); /* @var $room Room */		
// 		$room->clearing();
		
// 		require_once MAIN_.'../console/job/fanoutjob.php';
// 		$job = new FanoutJob();
// 		$job->args = array('type'=>FanoutType::ROOM_CLOSE,'accountid'=>100007,'data'=>array('id'=>801));
// 		$job->perform();
	}
	
	private function gen() {
		$ts = "原创,清唱,不插电,high歌,英文歌,MC,摇滚,情歌,跑调,乐器,儿歌,戏曲,冷笑话,相声,糗事,幽默,职场,校园,整蛊,恐怖,吹牛,戏说,连载,爆笑,模仿,游戏,咆哮体,抬杠,方言,口技,怪腔,新手,朗诵,军人,社会,体育,纠结,心情,八卦,初恋,前任,婚姻,求安慰,心里话,非主流,心理咨询,星座运程,占卜算命";
		$tags = explode(',', $ts);
		foreach ($tags as $v) {
			$tag = new Tag();
			$tag->type = Tag::TYPE_ROOM_TAG;
			$tag->name = $v;
			$tag->count = 0;
			$tag->save(); 
		}
	}

	public function www_phpinfo() {
		echo phpinfo();
	}

	public function www_memcache_delete() {
		$key = $this->_getParam('key');
		Cache::delete($key);
	}

	public function www_memcache_get() {
		$key = $this->_getParam('key');
		var_dump(Cache::read($key));
		die;		
	}
	
	public function www_schema() {
		$modelName = $this->_getParam('model');
		/* @var $table Table */
		$table = $modelName::table();
		foreach ($table->schema() as $k => $v) {
			if($v['type']=='integer')
				$type = 'int';
			else {
				$type = $v['type'];
			}
// 			elseif (in_array($v['type'], array('string','datetime'))) {
// 				$type = 'string';
// 			}
			echo "<pre>";
			echo " * @property ".$type.' $'.$k;
			echo "</pre>";
		}	 
		die;
	}
}