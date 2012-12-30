<?php
require_once __DIR__.'/job.php';
/**
 * Test
 *
 */
class TestJob extends Job {
	
	public function perform()
	{
		Log::write("perform: ".date('Y-m-d H:i:s')." ".print_r($this->args, true)."\n", __CLASS__);
		//$type = $this->args['type'];
// 		$accountid = $this->args['accountid'];
// 		$message = $this->args['message'];
		
	}
}