<?php 
session_start();
?>
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "login";
$request['email'] = $_GET['email'];
$_SESSION['email']= $_GET['email'];
$password = $_GET['password'];
$request['password'] = md5($password);

$request['message'] = $msg;
$response = $client->send_request($request);
if ($response=="MESSAGE FAIL"){
       	$client = new rabbitMQClient("testRabbitMQ_Backup.ini","testServer");
        $response = $client->send_request($request);
}




//$response = $client->publish($request);

switch ($response){
case true:
        header ('Location: homepage2.php' );
        exit();
case false:
        header ('Location: index.html');
        exit();
}

?>
