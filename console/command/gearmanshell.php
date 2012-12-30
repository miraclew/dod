<?php
/**
 * Gearman Worker 
 */ 
//设置包含目录（类所在的全部目录）,PATH_SEPARATOR 分隔符号 Linux(:) Windows(;)
$job_path = realpath(__DIR__.'/../job');
$include_path = get_include_path();
$include_path .= PATH_SEPARATOR.$job_path;
set_include_path($include_path);

function gearman_worker_autoload($className){
	$className = strtolower($className);
	if(endsWith($className,"job")){
		require_once $className.".php";;
	}
}
spl_autoload_register('gearman_worker_autoload');


class GearmanShell extends Shell {
	
	public function cmd_main() {
		echo "Starting\n";
		
		$gmworker= new GearmanWorker();		
		$gmworker->addServer();
		$gmworker->addFunction("aloha_worker", "aloha_worker");
		echo "Waiting for job...\n";
		
		while($gmworker->work())
		{
			if ($gmworker->returnCode() != GEARMAN_SUCCESS)
			{
				echo "return_code: " . $gmworker->returnCode() . "\n";
				break;
			}
		}
	}
}

function aloha_worker($job) {
	echo "Received job: " . $job->handle() . "\n";
	$workload = $job->workload();

	echo "Workload: $workload\n";
	
	
	$result = "";
	return $result;
}

function reverse_fn($job)
{
	echo "Received job: " . $job->handle() . "\n";

	$workload = $job->workload();
	$workload_size = $job->workloadSize();

	echo "Workload: $workload ($workload_size)\n";

	# This status loop is not needed, just showing how it works
	for ($x= 0; $x < $workload_size; $x++)
	{
		echo "Sending status: " . ($x + 1) . "/$workload_size complete\n";
		$job->sendStatus($x, $workload_size);
		sleep(1);
	}

	$result= strrev($workload);
	echo "Result: $result\n";

	# Return what we want to send back to the client.
	return $result;
}

