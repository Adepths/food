#!/usr/bin/php
<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=2">
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
    background: #FFFF00

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
  max-width: 1000px;
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
    background-color: #FFFF00;
    clear: left;
    text-align: center;
}

</style>

<script type=text/javascript> 


</script>

</head>
<body>
<div class="content">
  <div class="container">
  <?php
   $username = $_SESSION['username']; 
  ?>
  <header><h1><center>Recipe Repository <br> USER: <?php echo $username;?> </center></h1></header>
    <br>
	<a href="logout.php"> Logout</a>

<p>                         </p>



<h2><center><br>Saved Recipies</center><br></h2> 


<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('networkLogger.php');

$dbclient = new rabbitMQClient("dbServer.ini","testServer");
$dbrequest = array();
$dbrequest['type'] = "getsearches";
$dbrequest['username'] = $_SESSION['username'];
$dbresponse = $dbclient->send_request($dbrequest);
echo "<ul style='text-align:left'>";
foreach($dbresponse as $search){
echo "<li>".$search."</li>";
}
echo "</ul>";

?>

<p>                      </p>
<h2><center><br> Search for recipes based on ingredients!<br><br> </center></h2>

<form class="example" action="search.php" style="margin:auto;max-width:300px"> 

Type in ingredients:</br> <input type="text" id="ingredients" name="ingredients" placeholder="Enter ingredients" required>
<br>	
<br><input type=submit value="Search">
</form>

<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('networkLogger.php');




$dbclient = new rabbitMQClient("dbServer.ini","testServer");
$dbrequest = array();
$dbrequest['type'] = "search";
$dbrequest['username'] = $_SESSION['username'];
$dbrequest['search'] = $_GET['ingredients'];
$dbresponse = $dbclient->send_request($dbrequest);

reportCommuncation('FrontEnd Search: Request Sent Type - '.$dbrequest['type']);


$dmzclient = new rabbitMQClient("dmzServer.ini","testServer");
$dmzrequest=$_GET['ingredients'];
$dmzresponse = $dmzclient->send_request($dmzrequest);
$dmzresponse = json_decode($dmzresponse, true);

$ingr = explode(" ", $dmzrequest);
echo "<script src='https://yvoschaap.com/ytpage/ytembed.js'></script>";
foreach ($dmzresponse as $key => $value){
	if(isset($value[0]['recipe'])){
		foreach($value as $key1 => $value2){
			$mising = array();
				
			//echo ($value[$key1]['recipe']['uri']."\n");			
			$image = $value[$key1]['recipe']['image'];
			echo "<img src=\"".$image."\">"."<br>";
			echo "<h1>" . $value[$key1]['recipe']['label'] . "<h1>";
			$recipename = $value[$key1]['recipe']['label'];
			

			//echo $value[$key1]['recipe']['yield'];
			echo "<h1>Diet Labels</h1> <ul style='text-align:left'>";
			foreach($value[$key1]['recipe']['dietLabels'] as $dietLabels){
				echo "<li>".$dietLabels."</li>";
			}
			echo "</ul>";
			echo "<h1>Health Labels</h1> <ul style='text-align:left'>";
			foreach($value[$key1]['recipe']['healthLabels'] as $healthLabels){
				echo "<li>".$healthLabels."</li>";
			}
			echo "</ul>";
			echo "<h1>Cautions</h1> <ul style='text-align:left'>";
			foreach($value[$key1]['recipe']['cautions'] as $cautions){
				echo "<li>".$cautions."</li>";
			}
			echo "</ul>";
			echo "<h1>Ingredients</h1> <ul style='text-align:left'>";
			foreach($value[$key1]['recipe']['ingredientLines'] as $ingredientLines){
				echo "<li>".$ingredientLines."</li>";
				$missinging = false;
				for($i = 0; $i < sizeof($ingr) ;$i++){
					if(stripos($ingredientLines, $ingr[$i]) === false){
						$missinging = true;
					}else{
						$missinging = false;
						break;
					}
				}
				if($missinging == true){
					array_push($mising, $ingredientLines);					
				}				
			}
			echo "</ul>";
			//echo ($value[$key1]['recipe']['calories']);
			//echo ($value[$key1]['recipe']['totalTime']);
			echo "<h1>Nutrient</h1> <ul style='text-align:left'>";
			foreach($value[$key1]['recipe']['totalNutrients'] as $totalNutrients => $ammount){
				echo "<li>";
				foreach($value[$key1]['recipe']['totalNutrients'][$totalNutrients] as $nutrient){			
					if(is_numeric($nutrient)){
						echo round($nutrient, 2);
					}
					else{
						echo $nutrient . "\n";
					}//AIzaSyC2z1Vm9Pi4qCt3tzBFTBTzatRBtcGuVCY
				}
				echo "</li>";
			}
			echo "</ul>";
					echo "<div id='ytThumbs$key1'></div>

<script>
	ytEmbed.init({'block':'ytThumbs$key1','key':'your-youtube-developer-key','q':'$recipename','type':'search','results':1,'meta':false,'player':'embed','layout':'full'});
</script>";

			echo "<h1>Shopping List</h1><ul style='text-align:left'>";
			for($i = 0; $i < sizeof($mising) ;$i++){
				echo "<li>".$mising[$i]." <a href='https://www.amazon.com/s?k=$mising[$i]&rh=n%3A11825099011&ref=nb_sb_noss' target='_blank'> Get Ingrdient Here </a></li>";
			}
			echo "</ul>";
			
			echo "______________________________________________________________<br>";
		}
	}
}



?>

</body>
</html> 






