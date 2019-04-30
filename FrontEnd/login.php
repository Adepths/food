<?php 
session_start();
?>
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include 'networkLogger.php';

$client = new rabbitMQClient("dbServer.ini","testServer");

if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "log in";
}

$request = array();
$request['type'] = "login";
$request['username'] = $_GET['username'];
$_SESSION['username']= $_GET['username'];
$password = $_GET['password'];
$request['password'] = md5($password);

$request['message'] = $msg;
$response = $client->send_request($request);

reportCommuncation('FRONTEND: Response Recieved Type - '.$request['type']);

switch ($response){
case true:
        header ('Location: search.php');
        exit();
case false:
        header ('Location: index.html');
        exit();
}

?>
