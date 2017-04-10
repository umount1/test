<?php

function getLatLong($ort){
	require_once "error_function.php";
	// Das Auslesen der Geokoordinaten Erfolg durch "http://nominatim.openstreetmap.org/search"
	$ausgabe = array(0,0);
	$path = 'http://nominatim.openstreetmap.org/search?q='.urlencode($ort).'&format=json&limit=1';
	$geocode = file_get_contents($path);
	$output=json_decode($geocode);
	// debug_zval_dump($ort);
	// echo "<br>";
	// debug_zval_dump($path);
	// echo "<br>";
	// debug_zval_dump($geocode);
	// echo "<br>";
	// debug_zval_dump($output);
	// echo "<br> ".count($output)."-----------------------------------------------------------------------<br>";
	// echo "<br>";
	if(count($output) > 0)
	{
		$latitude =$output[0]->{'lat'};
		$longitude=$output[0]->{'lon'};	
		$ausgabe = array($latitude,$longitude);
	}
	
	return $ausgabe;
}	
?>