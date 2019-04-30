<?php

require_once('/home/jovana/git/rabbitmqphp_example/path.inc');
require_once('/home/jovana/git/rabbitmqphp_example/get_host_info.inc');
require_once('/home/jovana/git/rabbitmqphp_example/rabbitMQLib.inc');
//require_once('/home/jovana/git/rabbitmqphp_example/networkLogger.php');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

$request = array();
$request['type'] = 'register_user';
$request['username'] = $_GET['username'];
$request['password'] = $_GET['password'];
$request['email'] = $_GET['email'];

$response = $client->send_request($request);

switch ($response){
case true:
	header ('Location: homepage.html' );
	exit();
case false:
	header ('Location: register.html');
	exit();
}

?>
