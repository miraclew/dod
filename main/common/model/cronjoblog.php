<?php
class CronJobLog extends Model {	
	public static $useTable = 'cronjob_logs';
	public static $useDbConfig = 'log';
	
	// TODO 自动清理一周前的log
}
