#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
function requestProcessor($request){
	echo "received request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type']))
	{
		return "ERROR: unsupported message type";
	}
	switch($request['type'])
	{
		case "network":
			return reportNetworkLog($request['message']);
		case "error":
			return reportErrorLog($request['message']);
	}
	return array("returncode" => '0', 'message'=>"Server recieved request and processed");
}
function reportNetworkLog($message){
	$file = 'networkLog.txt';
	$report = time()." => $message";
	file_put_contents($file,$report);
}
function reportErrorLog($message){
	$file = 'errorLog.txt';
	$report = time()." => $message";
	file_put_contents($file,$report);
}
$server = new rabbitMQServer("networkLogger_Backup.ini","testServer");
$server->process_requests('requestProcessor');
/*if($serverResponse = "FAIL PROCESSOR"){

        $server = new RabbitMQServer("networkLogger_Backup.ini","testServer");
        $serverResponse = $server->process_requests('requestProcessor');
}
*/
exit();
?>
