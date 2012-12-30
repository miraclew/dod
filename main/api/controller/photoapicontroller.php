<?php
class PhotoApiController extends ApiController {
	public function www_list() {
		$accountid = $this->_getParam('accountid', 0);
		$page = $this->_getParam('page',1, true);
		$count = $this->_getParam('count', 20, true);
		
		if ($accountid == 0) {
			$accountid = Auth::user('accountid');
		}
		
		$data = array();
		$data['items'] = Photo::query(array('conditions'=>"accountid=?",'order' => "id desc",'page'=>$page, 'limit'=>$count), array($accountid));
		$data['is_last_page'] = count($data['items']) < $count ? 1:0;
		
		$this->success($data);
	}
	
	public function www_upload() {
		$accountid = Auth::user('accountid');
		
		$id = $this->_getParam('id', 0);
		if($id != 0) { // update voice
			$photo = Photo::findByPk($id);
		
			$voiceUrl = $this->Uploader->uploadVoice($this->request->params['form']['voice'], 0);
			if ($voiceUrl !== false) {
				$photo->voice = $voiceUrl;
				$photo->voice_time = $this->_getParam('voice_time');
			}
		}
		else {
			$photo = new Photo();
			$photo->accountid = $accountid;			
			
			// 必选
			$photoUrl = $this->Uploader->uploadImage($this->request->params['form']['photo']);
			if ($photoUrl === false) {
				$this->failed($this->Uploader->result, " =".$this->Uploader->uploadErr);
			}
			$photo->photo = $photoUrl;
			
			// 可选
			$voiceUrl = $this->Uploader->uploadVoice($this->request->params['form']['voice'], 0);
			if ($voiceUrl !== false) {
				$photo->voice = $voiceUrl;
				$photo->voice_time = $this->_getParam('voice_time');
			}
		}
		
		if($photo->save()) {
			$data = Utility::arrayExtract($photo->attributes(),array(),array('accountid','created'));
			$this->success($data);
		}
		else {
			$this->failed(Err::$DATA_SAVE_ERROR, $photo->errors->fullMessages());
		}
	}
	
	public function www_destroy() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id', 0);
		if($id == 0) {
			$this->failed(Err::$INPUT_REQUIRED, "id不能为空");
		}
		
		$photo = Photo::first(array('conditions'=>"accountid=? and id=?"), array($accountid, $id));
		if(!$photo) {
			$this->failed(Err::$DATA_NOT_FOUND);
		}
		
		if(!$photo->destroy()) {
			$this->failed(Err::$DATA_SAVE_ERROR, $photo->errors->fullMessages());
		}
		
		$this->success();
	}	
	
}