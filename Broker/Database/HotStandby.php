#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('networkLogger.php');


function getEmailList(){
	$db = mysqli_connect("localhost", "admin", "password", "test");
	mysqli_select_db($db, "test");
	$s = "SELECT email  FROM accounts";
	$q = mysqli_query($db, $s);
	$emailList = array();
	foreach($q as $x){
		$email = $x['email'];
		array_push($emailList, $email);
	}


	return $emailList;

}

function getRecords($email){
	$db = mysqli_connect("localhost", "admin", "password", "test");
	mysqli_select_db($db, "test");
	$s = "SELECT latitude, longitude, tempThres FROM records WHERE user ='$email'";
	$q = mysqli_query($db, $s);
	$recordList = array();
	foreach($q as $x){
		array_push($recordList, $x);

	}
	
	return $recordList;


}


function saveRecord($email, $lat, $lon, $tempThresh){
        $db = mysqli_connect("localhost","admin","password","test");
        mysqli_select_db($db,"test");
        $s = "INSERT INTO records (user, latitude, longitude, tempThres) VALUES ('$email','$lat','$lon','$tempThresh')";
        $q = mysqli_query($db,$s);
}

function doLogin($email,$password)
{
	$db = mysqli_connect("localhost","admin","password","test");
	mysqli_select_db($db, "test");
	$s = "SELECT * FROM accounts WHERE email = '$email' AND password = '$password'";
	$q = mysqli_query($db,$s);
	echo $s;
	$rowCount = mysqli_num_rows($q);
	
	if($rowCount == 1){
		echo "true";
		return true;
	}
	else
	{
		echo "false";
		return false;
	}
}
function doRegistration($username, $password, $email,$phone){
        $db = mysqli_connect("localhost","admin","password","test");
        mysqli_select_db($db, "test");

        $verify = checkUserExists($email);
        if ($verify == true){
                return false;
        }
        $i = "INSERT INTO accounts VALUES('$email','$username','$password','$phone')";
        //Make Query to SQL
        $q = mysqli_query($db,$i);

        //$verify = checkUserExists($email);
        //if ($verify == true){
                return true;
        //}
        //else {
        //        return false;
        //}

}

function checkUserExists ($email){
	$db = mysqli_connect("localhost", "admin", "password","test");
	mysqli_select_db($db,"test");
		
        $s = "SELECT * FROM accounts WHERE email ='$email'";
        $q = mysqli_query($db,$s);
        $rowCount = mysqli_num_rows($q);
        if ($rowCount == 1){
                return true;
        }
        else {
                return false;
        }
}

function requestProcessor($request)
{
  reportCommuncation('DBSERVER: Request Recieved Type - '.$request['type']);
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
  case "login":
   	return doLogin($request['email'],$request['password']);
    case "validate_session":
    	return doValidate($request['sessionId']);
    case "register_user":
	return doRegistration($request['username'],$request['password'],$request['email'],$request['phone']);
    case "saveRecord":
	return saveRecord($request['email'], $request['lat'], $request['lon'],$request['tempThres']);   
    case "getEmailList":
	return getEmailList();
    case "getRecords":
        return getRecords($request['email']);

  }
  reportCommuncation('DBSERVER: WARNING INVALID REQUEST TYPE'); 
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}
$server = new rabbitMQServer("HotStandby.ini","testServer");
$server->process_requests('requestProcessor');
/*if($serverResponse = "FAIL PROCESSOR"){

        $server = new RabbitMQServer("testRabbitMQ_Backup.ini","testServer");
        $serverResponse = $server->process_requests('requestProcessor');
}*/
exit();
?>


