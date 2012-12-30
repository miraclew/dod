<?php
require_once __DIR__.'/job.php';
require_once MAIN_.'lib/apns.php';
/**
 * Apple's notification service push
 *
 */
class ApnsPushJob extends Job {
	
	public function perform()
	{
		//$this->validate($this->args);
	
		Log::write("perform: ".date('Y-m-d H:i:s')." ".print_r($this->args, true)."\n", __CLASS__);
		//$type = $this->args['type'];
		$accountid = $this->args['accountid'];
		$message = $this->args['message'];
		
		$apns = new ApnsPush(config('apns_env'), config('apns_cert_file'));
		
		$lastLogin = LastLogin::findByPk($accountid); /* @var $lastLogin LastLogin */
		$apns->add($lastLogin->device_token, array('aps' => array('alert' => $message, 'badge'=>1), 'data' => ''));
		$apns->send();
	}
	
	private function validate($args)
	{
		$presence = $this->validatePresence($args, array('type', 'attrs', 'created'));
		if(!$presence[0])
			throw new Exception('bad queue data: '.$presence[1].' not present');
	
		// validate attrs
		$type = $args['type'];
		$attrs = $args['attrs'];
	}
}