<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (!isset($_POST))
{
	$msg = "...enter your credentials. It's not optional";
	echo json_encode($msg);
	exit(0);
}
else 
$request = $_POST;
$response = "Please leave";
switch ($request["type"])
{
	case "login":
		$response = "login accepted";
	break;
}
echo json_encode($response);
exit(0);
?>
