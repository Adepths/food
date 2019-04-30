#!/usr/bin/php
<?php

function pingMachine($ip){
        $pingresult = exec("ping -c 3 $ip");
        if($pingresult=="pipe 3"){
                $status = "Offline";
        } else {
                $status = "Online";
        }

        return $status;

}
function serviceRunning($hotstandbyIP,$databaseUN,$databasePW){

	$command = "sshpass -p $databasePW ssh -t $databaseUN@$hotstandbyIP -p 22 -t 'pgrep -fl HotStandby.php'";
	$status = exec($command);
	if(strpos($status,"HotStandby.php")){
		$service = "Started";
	} else {
		$service = "Stopped";
	}
	return $service;
}

//Ping Main Database Machine
$databasePW = "Ananya12!";
$databaseUN = "ananyakudugi";
$databaseIP = "192.168.1.8";
$hotstandbyIP = "192.168.1.13"; 

$ping = pingMachine($databaseIP);

if($ping=="Online"){
	$command = "sshpass -p $databasePW ssh -t $databaseUN@$databaseIP 'mysqldump -u admin -ppassword test > ~/HotStandby/backup.sql'"; 
	exec($command);

	$command = "sshpass -p $databasePW scp $databaseUN@$databaseIP:~/HotStandby/backup.sql ~/DatabaseBackups/";
	exec($command);

	$command = "sshpass -p $databasePW scp ~/DatabaseBackups/backup.sql $databaseUN@$hotstandbyIP:~/HotStandby/";
	exec($command);

	$command = "sshpass -p $databasePW ssh -t $databaseUN@$hotstandbyIP 'mysql -u admin -ppassword test < ~/HotStandby/backup.sql'";
	exec($command);

}
$check = serviceRunning($hotstandbyIP,$databaseUN,$databasePW);

if($ping=="Offline" AND $check=="Stopped"){
	
	$command = "sshpass -p $databasePW ssh ananyakudugi@192.168.1.13 'echo $databasePW | sudo -S  systemctl start HotStandby.service'";
	print_r( $command);
	exec($command);

}




//	scp the file to the backup machine
//	
//
//ELSE
//
//	start listener testRabbitMQServer_Hotstandby.php located on db_backup
//
//
//





?>
