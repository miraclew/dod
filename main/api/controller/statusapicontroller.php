<?php
/**
 * 用户动态控制器
 */
class StatusApiController extends ApiController {
	public $components = array('Uploader','Validator','Clearing','Resource');

	public function www_home_timeline() {
		$accountid = Auth::user('accountid');
		$page = $this->pageParams();
		$statuses = HomeStatus::find(array('conditions'=>"accountid=?", 'order'=>'modified desc','page'=>$page['page'], 'limit'=> $page['count']),array($accountid));
		
		$items = array();
		foreach ($statuses as $status) { /* @var $status HomeStatus */
			$profile = UserProfile::findByPk($status->friendid); /* @var $profile UserProfile */
			$item = array();
			$item['id'] = $status->id;
			$item['text'] = base64_encode($status->text);			
			$item['user'] = array('accountid'=>$profile->accountid,'avatar'=>$profile->avatar, 'nickname'=>$profile->nickname);
			$item['updated'] = date('m-d', strtotime($status->modified));
			$items[] = $item;
		}
		
		$data = array('items'=>$items);
		$data['is_last_page'] = count($items) < $page['count'] ? 1:0;
		
		$this->success($data);
	}
	
	public function www_user_timeline() {
		$accountid = $this->_getParam('accountid', 0);
		if ($accountid == 0) {
			$accountid = Auth::user('accountid');
		}
		$page = $this->pageParams();
		$statuses = UserStatus::find(array('conditions'=>"accountid=?", 'order'=>'created desc','page'=>$page['page'], 'limit'=> $page['count']),array($accountid));
		
		$items = array();
		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
		foreach ($statuses as $status) { /* @var $status UserStatus */			
			$item = array();
			$item['id'] = $status->id;
			$item['text'] = base64_encode($status->text);			
			$item['user'] = array('accountid'=>$profile->accountid,'avatar'=>$profile->avatar, 'nickname'=>$profile->nickname);
			$item['updated'] = date('m-d', strtotime($status->created));
			$items[] = $item;
		}
		
		$data = array('items'=>$items);
		$data['is_last_page'] = count($items) < $page['count'] ? 1:0;
		
		$this->success($data);				
	}	
}
