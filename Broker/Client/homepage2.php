<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="utf-8">
<style>
h1 {
    color: #443366 ;
    font-family:"Palatino Linotype", "Book Antiqua", Palatino, serif;	

} 
h2 {
    color: #443366;
    font-size: 20px;
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
<body>
<div class="content">
  <div class="container">
  <?php
 
  require_once('weatherClient.php');
  $email = $_SESSION['email']; ?>
  <header><h1><center>Forcastr <br> USER: <?php echo $email;?> </center></h1></header>
    <br>
	<a href="logout.php"> Logout</a>

<p>                         </p>



<h2><center><br>Saved locations</center><br></h2> 

<?php

require_once('weatherClient.php');
$email = $_SESSION['email'];
$records = getRecords($email);
$table = "<center><table border='1' >";
$table.= "<tr><th>" .'City' . "</th><th>" . 'Current Temp' . "</th><tr>";
foreach($records as $record){
	$weather = getWeatherByCoords($record['latitude'],$record['longitude']);
	$table.= "<tr><td>" . $weather['city'] . "</td><td>" . $weather['currentTemp'] . "</td><tr>";
}
$table.= "</table></center>";

echo $table;

?>

<p>                      </p>
<h2><center><br>Search for new locations to add by the ZIP code <br><br></center></h2>

<form class="example" action="search.php" style="margin:auto;max-width:300px"> 
Type in the ZIP code:</br> <input type="text" id="zipCode" name="zipCode" placeholder="Enter ter ZIP" required>
<br>	<br>	<br>Type in the Country code:</br> <input type="text" id="countryCode" name="countryCode" placeholder="Enter Country Code" required><br>
<br><input type=submit>
</form>
<p>            </p>
<hr>
</body>
</html> 
