<?php
require_once __DIR__.'/job.php';
/**
 * 图片后台任务
 *
 */
class ImageJob extends Job {
	
	const AVATAR 	= 1; // 80X80
	const VOCIE_IMG = 2; // 80X80
	const PHOTO 	= 3; // 80X80
	
	public function perform()
	{
		//Log::write("perform: ".date('Y-m-d H:i:s')." ".print_r($this->args, true)."\n", __CLASS__);
		$url = $this->args['url'];
		$type = $this->args['type']; 
		$output = '';
		
		switch ($type) {
			case AVATAR:
			case VOCIE_IMG:
			case PHOTO:
				$new_size = Image::SIZE_80x80;
				break;
		}
		
		$image = Image::load_from_url($url);
		$image->resize($new_size);
	}
}
