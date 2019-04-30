<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("weatherServer.ini","testServer");

function sendRequest($requestedInfo, $brokerINI, $backupINI)
{
	$client = new rabbitMQClient($brokerINI,"testServer");
	$request=$requestedInfo;
	$response=$client->send_request($request);
	//echo $response.PHP_EOL;
	if ($response=="MESSAGE FAIL"){
		$client = new rabbitMQClient($backupINI,"testServer");
		$request = $requestedInfo;
		$response = $client->send_request($request);
	}
	
	return $response;
}

function getWeatherByZipCode($zipCode,$countryCode){
	$request = array();
	$request['type'] = 'getWeatherFromZip';
	$request['zipCode'] = $zipCode;
	$request['countryCode'] = $countryCode;
	return sendRequest($request, 'weatherServer.ini',"weatherServer_Backup.ini" );
}
function getWeatherByName($city,$countryCode){
	$request = array();
	$request['type'] = 'getWeatherFromName';
	$request['city'] = $city;
	$request['countryCode'] = $countryCode;
	return sendRequest($request, 'weatherServer.ini',"weatherServer_Backup.ini");	
}
function getWeatherByCoords($lat,$lon){
	$request = array();
	$request['type'] = 'getWeatherFromCoordinates';
	$request['lat'] = $lat;
	$request['lon'] = $lon;
	return sendRequest($request, 'weatherServer.ini',"weatherServer_Backup.ini");
}

function saveRecord($email, $lat, $lon, $tempThresh){
	$request = array();
	$request['type'] = 'saveRecord';
	$request['email'] = $email;
	$request['lat'] = $lat;
	$request['lon'] = $lon;
	$request['tempThres'] = $tempThresh;
	return sendRequest($request, 'testRabbitMQ.ini',"testRabbitMQ_Backup.ini");

}

function getRecords($email){
	$request = array();
	$request['type']= 'getRecords';
	$request['email']= $email;
	return sendRequest($request,'testRabbitMQ.ini','testRabbitMQ_Backup.ini');
}

//saveRecord('test@gmail.com','123','456',75);
//saveRecord('poop@test.com','45','67','43');
?>
