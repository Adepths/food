#!/usr/bin/php
<?php

function deserializeXML(){
	$xml = simplexml_load_file("deployValues_copy.xml") or die("Error: Cannot load XML Files");
	return $xml;
}

function getMachineDetails($xml,$machineType, $environment){
	for($i=0; $i<12; $i++){
		if($xml -> machine[$i]['machine_type'] == $machineType AND $xml -> machine[$i]['environment'] == $environment){
			//echo $xml->machine[$i]['password'];
			return array("machineType"=>$xml -> machine[$i]['machine_type'],"environment"=>$xml -> machine[$i]['environment'],"file_location"=>$xml -> machine[$i] ['file_location'],"ip"=>$xml -> machine[$i] ['IP'],"username"=> $xml -> machine[$i]['username'],"password"=> $xml -> machine[$i]['password']);
		}
	}	
}

function sendFiles($to,$from){
	$password = $to['password'];
        $ip = $to['ip'];
	$fileLocation = $to['file_location'];
	$fromPath = $from['file_location'];
        $command = "sshpass -p $password scp -r $fromPath* $ip:~/ForecastrFiles/";
	echo $command.PHP_EOL;
	exec($command);

}

function getFiles($type){
	$password = $type['password'];
	$ip = $type['ip'];
	$fileLocation = $type['file_location'];
	$command = "sshpass -p $password scp -r $ip:~/ForecastrFiles/* $fileLocation";
	exec($command);
}
/*
 *0 = machine type
 *1 = environment
 *2 = file location
 *3 = destination IP
 *4 = username
 *5 = password
 */
function saveVersion(){
	$date = date("m.d.Y_his");
	echo "New Version: Forecastr_$date".PHP_EOL;
	exec("mkdir ~/ForecastrFiles/Versions/Forecastr_$date");
	exec("cp -r ~/ForecastrFiles/Tested/* ~/ForecastrFiles/Versions/Forecastr_$date");
}

function masterPull($cluster){
	$clusterTypes = array('DEV','QA');
	$pushTo = null;
	if(!in_array($cluster,$clusterTypes)){
		return "Unacceptable cluster type. Please try again".PHP_EOL;
	} else {
		switch($cluster) {
			case "DEV":
				$pushTo='QA';
				break;
			case "QA":
				$pushTo='PROD';
				break;
		}
	}
		
	$xml = deserializeXML(); 
	$machineTypes = array('DMZ','BROKER','CLIENT','DATABASE');
	echo "Obtaining files from: $cluster".PHP_EOL.PHP_EOL;
	foreach($machineTypes as $type){
		echo "Collecting ".$type.PHP_EOL;
		$info=getMachineDetails($xml,$type, $cluster);
		getFiles($info);
	}
	if($cluster=="QA"){ saveVersion(); }
	echo "Distributing files to: $pushTo".PHP_EOL.PHP_EOL;
	foreach($machineTypes as $type){
		echo "Sending to".$type.PHP_EOL;
		$from =getMachineDetails($xml,$type, $cluster);
		$to = getMachineDetails($xml,$type, $pushTo);
		sendFiles($to,$from);
		specialInstructions($to);
	}
	return "Successful Upload";
}

function pushFiles($xml,$cluster,$type){
	$from =getMachineDetails($xml,$type,"QA");
	$to = getMachineDetails($xml,$type, $cluster);
	sendFiles($to,$from);
	specialInstructions($to);
}

function allMightyPush($xml){
	$types = array('DMZ', 'BROKER', 'CLIENT', 'DATABASE');
        $to = getMachineDetails($xml,"MASTER", "MASTER");
	foreach($types as $type){
		$from =getMachineDetails($xml,$type,"QA");
		$password = $to['password'];
		$ip = $to['ip'];
        	$fromPath = $from['file_location'];
        	$command = "sshpass -p password scp -r $fromPath* dmz@192.168.1.104:~/ForecastrMaster/";
        	echo $command.PHP_EOL;
	        exec($command);

		
		specialInstructions($to);
	}
}

function specialInstructions($machine){
	$type = $machine['machineType'];
	$password = $machine['password'];
	$ip = $machine['ip'];
	$command = "";
	switch($type){
		case "CLIENT":
			$command = "sshpass -p $password ssh -t $ip 'echo $password | sudo -S cp -r ~/ForecastrFiles/* /var/www/html/'";
			exec($command);
		break;
		case "DATABASE":
			$command = "sshpass -p $password ssh -t $ip 'mysql -u admin -ppassword test < ~/ForecastrFiles/Records.sql'";
			exec($command);
		break;	
	}
}

$xml = deserializeXML();
//masterPull("QA");
pushFile($xml,"PROD","DMZ");
exit();
?>
