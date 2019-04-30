<?php
session_start();
?>
<?php

require_once('weatherClient.php');

$latitude=$_GET['lat'];
$longitude=$_GET['lon'];
$email=$_GET['email'];
$desiredTemp=$_GET['tempThres'];

saveRecord($email, $latitude, $longitude, $desiredTemp);

header ('Location: homepage2.php' );
?>
