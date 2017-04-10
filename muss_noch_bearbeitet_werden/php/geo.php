<?php
require_once "error_function.php";

function getLatLong($ort){
	// https://developers.google.com/maps/documentation/geocoding/intro#BYB
	// AIzaSyC3E46KYcugkgAEBDNyrTTVe1-lvZBc10w 
	$ausgabe = array(0,0);
	$path = 'https://maps.google.com/maps/api/geocode/json?address='.urlencode($ort).'&key=AIzaSyC3E46KYcugkgAEBDNyrTTVe1-lvZBc10w';
	$geocode = file_get_contents($path);
	//trigger_error($geocode, E_USER_NOTICE);
	
	// Falls kein gültiges json-File erstellt wurde, brechen wir hier ab.
	if( FALSE!==strpos($geocode, "ZERO_RESULTS") )
	{
//		trigger_error("Vorzeitiger Ausstieg " ,E_USER_NOTICE);
		return $ausgabe; // keine Koordinaten
	}
	
	$output = json_decode($geocode);

	//	debug_zval_dump($output);
	
	if(isset($output->error_message))
	{
		trigger_error("Keine Koordinaten fuer ".$ort."   ".$output->error_message ,E_USER_NOTICE);
	}
	else
	{
		$latitude  = $output->results[0]->geometry->location->lat;
		$longitude = $output->results[0]->geometry->location->lng;
		
		$ausgabe = array($latitude,$longitude);
	}
	return $ausgabe;
}	
?>