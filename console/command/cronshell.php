<?php
require(SHELLROOT_ . 'includes/setup.php');

abstract class CronShell extends Shell {
	// 任务执行结果
	const RESULT_SUCCESS 			= 1; // 成功
	const RESULT_FAIL_CRITICAL		= 2; // 失败
	const RESULT_FAIL_WARNING		= 3; // 失败
	const RESULT_FAIL_INFO			= 4; // 失败
	const RESULT_FAIL_DEBUG			= 5; // 失败
	const RESULT_UNKNOWN			= 6; // 未知
	
	//Critical,Warning,Info,Debug
	// 结果储存方式
	const STORAGE_MYSQL 	= 1; // mysql
	const STORAGE_FILE 		= 2; // 文件
	
	protected $saveOutput = true; // 是否保存输出
	protected $resultStorage = self::STORAGE_MYSQL;
	
	protected $resultSaved = false;
	protected $result = array('result'=>self::RESULT_UNKNOWN);
	
	public function cmd_main() {
		$this->beforeRun();
		
		try {
			$ret = $this->run();
			if($ret) {
				$this->result['result'] = $ret;
			}
		} catch (Exception $e) {
			$this->out($e->getMessage());
			$this->out($e->getTraceAsString());
		}
		
		$this->afterRun();
	}
	
	protected function beforeRun() {		
		$this->result['startat'] = date('Y-m-d H:i:s');
				
		if($this->saveOutput)
			ob_start();		
	}
	
	protected function afterRun() {
		if($this->saveOutput) {
			$output = ob_get_clean();			
			$this->result['output'] = $output;
		}		
	}
	
	protected function terminated() {
		$this->result['stopat'] = date('Y-m-d H:i:s');
		if (!$this->resultSaved) {
			$this->saveResult();
		}
	}
	
	function __construct() {
		parent::__construct();
		$this->result['jobname'] = $this->name;
	}
	
	function __destruct() {
		$this->terminated();
   	}
   	
   	function saveResult() {
   		if ($this->resultStorage == self::STORAGE_MYSQL) {
   			$logger = new CronJobLogger_Mysql();
   		}
   		else {
   			$logger = new CronJobLogger_File($this->name."CronJobLog.log");
   		}
   		
   		$logger->write($this->result);
   		$this->resultSaved = true;
   	}
	
	/**
	 * 执行任务
	 * @return int 执行结果
	 */
	public abstract function run();
}

class CronJobLogger_Mysql {
	public function write($log) {
		CronJobLog::create($log);
	}
}

class CronJobLogger_File {
	private $fileName;
	private $fileDir;
	public function __construct($fileName=null, $fileDir=null) {
		$this->fileName = $fileName;
		if($fileDir == null) {
			$fileDir = sys_get_temp_dir(); 
		}
		$this->fileDir = $fileDir;
	}
	
	public function write($log) {
		$message = "\nCronJob:".$log['jobname'].' Time= '.$log['startat'].' - '.$log['stopat']."\n";
		$message .= "Result=".$log['result']."\n";
		$message .= "Output<<<\n".$log['output']."\n".">>>Output\n";
		
		file_put_contents($this->fileDir.DIRECTORY_SEPARATOR.$this->fileName, $message, FILE_APPEND);
	}	
}