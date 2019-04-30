<?php
session_start();
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
//require_once('/home/jovana/git/rabbitmqphp_example/networkLogger.php');

$request = array();
$request['type'] = 'register_user';
$request['username'] = $_GET['username'];
//$request['password'] = $_GET['password'];
$mypassword = $_GET['password'];
$request['password'] = md5($mypassword);
$request['email'] = $_GET['email'];
$_SESSION['email'] = $_GET['email'];

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
$response = $client->send_request($request);

if($response==1){
	$registrationSuccess = "good";
} else {
	$registrationSuccess = "MESSAGE FAIL";
}

if($registrationSuccess=="MESSAGE FAIL"){
	echo "yup";
        $client = new rabbitMQClient("testRabbitMQ_Backup.ini","testServer");
        $response = $client->send_request($request);
}


switch ($response){
case true:
	header ('Location: homepage2.php' );
	exit();
case false:
	header ('Location: register.html');
	exit();
}

?>
