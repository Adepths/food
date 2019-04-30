#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


include 'networkLogger.php';

function doLogin($username,$password)
{			
	$db = mysqli_connect("localhost","login","toorIT490!","userInfo");
	mysqli_select_db($db, "userInfo");
	$s = "SELECT * FROM users WHERE userName = '$username' AND pwd = '$password'";
	$q = mysqli_query($db,$s);
	echo $s;
	$rowCount = mysqli_num_rows($q);	
	if($rowCount == 1){
		sendMessage($username . " logged in",'info');
		echo "true\n";
		return true;
	}
	else
	{
		sendMessage($username . " attempted to log in",'info');
		echo "false\n";
		return false;
	}
}

function doRegistration($username, $password)
{	
        $db = mysqli_connect("localhost","login","toorIT490!","userInfo");
        mysqli_select_db($db, "userInfo");

        $verify = checkUserExists($username);
        if ($verify == true){	
		sendMessage($username . " failed to create user",'info');	
                return false;
        }
        $i = "INSERT INTO users VALUES('$username','$password')";        
        $q = mysqli_query($db,$i);
	echo "User Created";
		sendMessage($username . " successfully created user",'info');        
                return true;        
}

function doSearch($username, $search)
{	
        $db = mysqli_connect("localhost","login","toorIT490!","userInfo");
        mysqli_select_db($db, "userInfo");

        $i = "INSERT INTO searches VALUES('$username','$search')";        
        $q = mysqli_query($db,$i);
	echo "Search Added";
		sendMessage($username . " successfully added search ". $search,'info');        
                return true;
        
}

function getSearches($username)
{			
	$db = new mysqli("localhost","login","toorIT490!","userInfo");
	//mysqli_select_db($db, "userInfo");
	$s = "SELECT search FROM searches WHERE user = '$username'";
	$q = $db->query($s);
	$search = array();
	if ($q->num_rows > 0) {
    		// output data of each row
    		while($row = $q->fetch_assoc()) {
        		array_push($search,$row["search"]);
    		}
	} 	
	echo$s;
	return $search;
}

function checkUserExists($username)
{
	$db = mysqli_connect("localhost", "login", "toorIT490!","userInfo");
	mysqli_select_db($db,"userInfo");
		
        $s = "SELECT * FROM users WHERE userName ='$username'";
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
   		return doLogin($request['username'],$request['password']);
  	case "register_user":
		return doRegistration($request['username'],$request['password']);
	case "search":
		return doSearch($request['username'],$request['search']);
	case "getsearches":
		return getSearches($request['username']);
    
  }
  reportCommuncation('DBSERVER: WARNING INVALID REQUEST TYPE'); 
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}
$server = new rabbitMQServer("dbServer.ini","testServer");
$server->process_requests('requestProcessor');

exit();
?>


