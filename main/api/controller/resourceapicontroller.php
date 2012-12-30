<?php
/**
 * 资源控制器
 * @property ResourceComponent $Resource
 */
class ResourceApiController extends ApiController {
	public $components = array('Resource');
	
	public function www_stage_themes() {
		$resources = $this->Resource->getStages();
		$data = array('items'=>array());
		foreach ($resources['stages'] as $value) {
			$data['items'][] =  Utility::arrayExtract($value, array('id','name','preview','package'));			
		}		
		
// 		$data['modified'] = time();
// 		$data['expire_time'] = time()+10*3600;
		$this->_respond(Err::$SUCCESS, $data);
	}
	
	public function www_room_backgrounds() {		
		$data = array('items'=>array());
		for ($i = 1; $i <= 5; $i++) {			
			$data['items'][] = array(
				'id'=>$i, 
				'image'=> $this->Resource->getRoomBackgroundImageURL($i), 
				'thumb'=> $this->Resource->getRoomBackgroundImageURL($i, true)
			);
		}
		$this->_respond(Err::$SUCCESS, $data);
	}
	
	public function www_home_backgrounds() {
		$data = array('items'=>array());
		for ($i = 1; $i <= 9; $i++) {
			$data['items'][] = array(
				'id'=>$i, 
				'image'=> $this->Resource->getBackgroundImageURL($i), 
				'thumb'=> $this->Resource->getBackgroundImageURL($i, true)
			);
		}
		$this->_respond(Err::$SUCCESS, $data);
	}
}