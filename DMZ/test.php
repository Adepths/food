<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

include 'networkLogger.php';

$url = "https://api.edamam.com/search?q=chicken&app_id=ab0018c0&app_key=93090bf86951f2fd6e4fc8b54f045bba";
$file = 'json.txt';

$result = file_get_contents($file);

$test = json_decode($result,true);

foreach ($test as $key => $value){	
	if(isset($value[0]['recipe'])){
		foreach($value as $key1 => $value2){	
			echo ($value[$key1]['recipe']['uri']."\n");
			echo ($value[$key1]['recipe']['label']."\n");
			echo ($value[$key1]['recipe']['image']."\n");
			echo ($value[$key1]['recipe']['yield']."\n");
			foreach($value[$key1]['recipe']['dietLabels'] as $dietLabels){
				echo ($dietLabels . "\n");
			}
			foreach($value[$key1]['recipe']['healthLabels'] as $healthLabels){
				echo ($healthLabels . "\n");
			}
			foreach($value[$key1]['recipe']['cautions'] as $cautions){
				echo ($cautions . "\n");
			}
			foreach($value[$key1]['recipe']['ingredientLines'] as $ingredientLines){
				echo ($ingredientLines . "\n");
			}
			echo ($value[$key1]['recipe']['calories']."\n");
			echo ($value[$key1]['recipe']['totalTime']."\n");
			foreach($value[$key1]['recipe']['totalNutrients'] as $totalNutrients => $ammount){
				foreach($value[$key1]['recipe']['totalNutrients'][$totalNutrients] as $nutrient){
					echo ($nutrient . "\n");
				}
			}
echo ("----------------------\n");
		}
	}
}


?>
