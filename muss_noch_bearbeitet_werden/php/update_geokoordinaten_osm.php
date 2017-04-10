<?php
require_once "geo_osm.php";
require_once "error_function.php";

$db1 = mysqli_connect("localhost","root","","timeline");

// 2500 Einträge mit Ortsangabe und ohne Geokoordinaten suchen.
$abfrage ="SELECT id, place FROM daten where length(place) >= 3 and latitude=0 and longitude=0 and place_checked=0 LIMIT 1500";
$ergebnis = mysqli_query($db1, $abfrage);
if(FALSE === $ergebnis) exit("DB Abfrage gescheitert");

while( ($row = mysqli_fetch_object($ergebnis))) {
	
	$lat = 0;
	$long = 0;
	if(strlen($row->place) > 2){
		list($lat,$long) = getLatLong($row->place);
	}	
	
	if($lat != 0 && $long != 0)
	{
		$abfrage ="UPDATE daten set latitude=".$lat.", longitude=".$long.", place_checked=1 where id=".$row->id;
		trigger_error($abfrage, E_USER_NOTICE);
		$erg = mysqli_query($db1, $abfrage);
		if(FALSE === $erg) trigger_error("Eintrag gescheitert ".$row->id, E_USER_NOTICE);		
	}
	else
	{
		trigger_error("Keine Geokoordinaten fuer ".$row->id.": ".$row->place, E_USER_NOTICE);
		$abfrage ="UPDATE daten set place_checked=1 where id=".$row->id;
		trigger_error($abfrage, E_USER_NOTICE);
		$erg = mysqli_query($db1, $abfrage);
		if(FALSE === $erg) trigger_error("Eintrag gescheitert ".$row->id, E_USER_NOTICE);		
	}
}


?>