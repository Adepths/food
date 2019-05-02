<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function reportError($message)
{
	sendMessage($message,'error');	
}
function reportCommuncation($message)
{
	sendMessage($message,'network');		
}
function sendMessage($message,$type)
{
	$client = new rabbitMQClient("networkLogger.ini","testServer");
	$request=array();
	$request['type']=$type;
	$request['message']=$message;
	$response=$client->send_request($request);
	return $response;
}


?>
