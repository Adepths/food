#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function reportError($message){
	$report=sendMessage($message,'error');
	if($report==false){
		echo 'failed communicating to broker'; 
	}
}
function reportCommuncation($message){
	sendMessage($message,'network');
	//if($report==false){
	//	echo 'failed communicating to broker'; 
	//}	
}
function sendMessage($message,$type){
	$client = new rabbitMQClient("networkLogger.ini","testServer");
	$request=array();
	$request['type']=$type;
	$request['message']=$message;
	$response=$client->send_request($request);
	return $response;
}


?>
