#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function requestProcessor($request)
{
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
		case "info":
			return reportInfoLog($request['message']);
	}
	return array("returncode" => '0', 'message'=>"Server recieved request and processed");
}

function reportNetworkLog($message)
{
	$file = 'networkLog.txt';
	$handle = fopen($file, 'a');
	$report = date('Y-m-d H:i:s')." => $message\n";
	fwrite($handle,$report);
	fclose($handle);
	reportCentLog($message);
}
function reportErrorLog($message)
{
	$file = 'errorLog.txt';
	$handle = fopen($file, 'a');
	$report = date('Y-m-d H:i:s')." => $message\n";
	fwrite($handle,$report);
	fclose($handle);
	reportCentLog($message);
}
function reportInfoLog($message)
{
	$file = 'infoLog.txt';
	$handle = fopen($file, 'a');
	$report = date('Y-m-d H:i:s')." => $message\n";
	fwrite($handle,$report);
	fclose($handle);
	reportCentLog($message);
}
function reportCentLog($message)
{
	$file = 'centLog.txt';
	$handle = fopen($file, 'a');
	$report = date('Y-m-d H:i:s')." => $message\n";
	fwrite($handle,$report);
	fclose($handle);
}
$server = new rabbitMQServer("networkLogger.ini","testServer");
$server->process_requests('requestProcessor');

exit();
?>
