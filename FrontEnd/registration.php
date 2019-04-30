<?php 
//session_start();
?>
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("dbServer.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "User Registration";
}

$request = array();
$request['type'] = "register_user";
$request['username'] = $_GET['username'];
$_SESSION['username']= $_GET['username'];
$password = $_GET['password'];
$request['password'] = md5($password);

$request['message'] = $msg;
$response = $client->send_request($request);

if ($response=="MESSAGE FAIL"){
       	$client = new rabbitMQClient("testRabbitMQ_Backup.ini","testServer");
        $response = $client->send_request($request);
}

switch ($response){
case true:
	echo "User Created";
        header ('Location: index.html');
        exit();
case false:
        header ('Location: registration.html');
        exit();
}

?>
