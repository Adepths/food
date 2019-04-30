<?php
session_start();
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
h1 {
    color: #443366 ;
    font-size: 34px;
    font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif;
    background: #CCCCFF

}h2 {
    color: #443366 ;
    font-size: 24px;
    font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif;
    background: #CCCCFF

} 
h1 {
    color: #443366 ;
    font-size: 24px;
    font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif;
    background: #CCCCFF

} 
p {
    color: #443366 ;
    font-family: "Palatino Linotype", "Book Antiqua", Palatino, serif;

}
body { 
  background: #e6e6e6;
  text-align: center;
  }

.content {
  max-width: 500px;
  margin: auto;
  background: white;
  padding: 10px;
}
div.container {
    width: 100%;
    border: 1px solid gray;
}

header, footer {
    padding: 1em;
    color: white;
    background-color: #CCCCFF;
    clear: left;
    text-align: center;
}
</style>
</head>

<div class="content">
  <div class="container">

<header>
   <h1>Forecastr</h1>
</header>

<h2><center><table>
<?php

require_once('weatherClient.php');

$zipCode = $_GET['zipCode'];
$countryCode = $_GET['countryCode'];
$info=getWeatherByZipCode($zipCode, $countryCode);
$city=$info['city'];
$currentTemp=$info['currentTemp'];
//$country=$info['country'];
$minTemp=$info['minTemp'];
$maxTemp=$info['maxTemp'];
$precipitation=$info['precipitation'];
$latitude=$info['latitude'];
$longitude=$info['longitude'];
$email = $_SESSION['email'];
//echo $currentTemp;

$table = "<center><table border='1' ><tr><th>City</th><th>Min Temp</th><th>Max Temp</th></tr>";

$table.= "<tr><td>" .  $city . "</td><td>" . $minTemp ." </td><td> " .$maxTemp . "</td></tr></table>";

print_r($table);


?>
</h2></table>
<br></br>
<form action="save.php" >
<h2>Type a desired temperature: </h2><input type="text" id="tempThres" name="tempThres" required value=<?php   ?>>
<br></br> <input type="hidden" id="email" name="email" value=<?php echo $email; ?> >
<input type="hidden" id="lat" name="lat" value=<?php echo $latitude;?>>
<input type="hidden" id="lon" name="lon" value=<?php echo $longitude;?>>
<input type="submit"></form> <br>

</html>
