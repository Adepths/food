#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


include 'networkLogger.php';


function getApi($request)
{			
	$searchtmp = explode(" ", $request);
	$search = "";
		for($i = 0; $i < sizeof($searchtmp) ;$i++){
			if($i==0){
				$search = $searchtmp[$i];
			}
			else{
				
				$search = $search ."+". $searchtmp[$i];
			}
		}
	
	echo $search;
	$url = "https://api.edamam.com/search?q=".$search."&app_id=ab0018c0&app_key=93090bf86951f2fd6e4fc8b54f045bba";
	$result = file_get_contents($url);
	return $result;
}

function requestProcessor($request)
{
  reportCommuncation('DMZ: Request Recieved ' . $request);
  var_dump($request);
  return getApi($request);
}

$server = new rabbitMQServer("dmzServer.ini","testServer");
$server->process_requests('requestProcessor');

exit();
?>


