#!/usr/bin/php
<?php

require_once('/home/samg/git/rabbitmqphp_example/path.inc');
require_once('/home/samg/git/rabbitmqphp_example/get_host_info.inc');
require_once('/home/samg/git/rabbitmqphp_example/rabbitMQLib.inc');

$client = new rabbitMQClient("weatherServer.ini","testServer");

function sendRequest($requestedInfo, $brokerINI)
{
	$client = new rabbitMQClient($brokerINI ,"testServer");
	$request=$requestedInfo;
	$response=$client->send_request($request);
	return $response;
}

function getWeatherByZipCode($zipCode,$countryCode){
	$request = array();
	$request['type'] = 'getWeatherFromZip';
	$request['zipCode'] = $zipCode;
	$request['countryCode'] = $countryCode;
	return sendRequest($request,'weatherServer.ini');
}
function getWeatherByName($city,$countryCode){
	$request = array();
	$request['type'] = 'getWeatherFromName';
	$request['city'] = $city;
	$request['countryCode'] = $countryCode;
	return sendRequest($request,'weatherServer.ini');	
}
function getWeatherByCoords($lat,$lon){
	$request = array();
	$request['type'] = 'getWeatherFromCoordinates';
	$request['lat'] = $lat;
	$request['lon'] = $lon;
	return sendRequest($request,'weatherServer.ini');
}

function saveRecord($email, $lat, $lon, $tempThresh){
	$request = array();
	$request['type'] = 'saveRecord';
	$request['email'] = $email;
	$request['lat'] = $lat;
	$request['lon'] = $lon;
	$request['tempThres'] = $tempThresh;

	return sendRequest($request, 'testRabbitMQ.ini');
}

function getRecords($email){
	$request = array();
	$request['type'] = 'getRecords';
	$request['email'] = $email;
	return sendRequest($request, 'testRabbitMQ.ini');

}


?>
