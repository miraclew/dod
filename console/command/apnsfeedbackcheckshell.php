<?php
require(SHELLROOT_ . 'includes/setup.php');
require_once MAIN_.'lib/apns.php';

class ApnsFeedbackCheckShell extends Shell {

	public function cmd_main()
	{
		//$this->check();
		$this->pushTest();
	}

	private function check() {
		$apns = new ApnsFeedback(config('apns_env'), config('apns_cert_file'));
		$tokens = $apns->receive();

		debug($tokens);
		foreach ($tokens as $token) {
			UserProfile::instance()->query("UPDATE user_profiles set device_token is null where device_token=$token");
		}
	}

	private function pushTest() {
		$apns = new ApnsPush(config('apns_env'), config('apns_cert_file'));
        $alert = 'leihaiiiai';
        $deviceToken = "4a968c028216854da828ee16d89a701238f3cb4d0922498d5ff05c946fe2";
        $apns->add($deviceToken, array('aps' => array('alert'=>$alert, 'badge'=>1), 'data' => 'asdfdgfdgfgf'));
        $apns->send();
	}
}