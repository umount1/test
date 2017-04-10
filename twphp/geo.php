<?php
/*!
 \file      geo.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-01-19

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-19
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 2017-01-19 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Ermittelt die Geokoordinaten (Breiten- und Längengrad) eines Ortes.
 
\remark Abhängigkeiten<br>
\li \link msg_function.php\endlink<br>


*/

/* globale Einstellungen */
require_once("msg_function.php");

/*!
	\fn function getLatLong($ort, $p="google")
	\brief	Bestimmt den Breiten- und Längengrad eines Ortes.
	\param	ort	Ortsangabe (Stadt[, Land, Kontinent]).
	\param	p	\li google	Defaultwert, die Suche wird bei google ohne Schlüssel durchgeführt.
				\li	googleKey	Die Suche wird bei google mit Schlüssel durchgeführt.
				\li	osm		Die Suche wird bei Openstreetmap durchgeführt.
	\return	array	Enthält den Breiten- und den Längengrad im Erfolgsfall, andernfalls
					die Werte 0, 0.
*/
function getLatLong($ort, $p="google")
{
	// https://developers.google.com/maps/documentation/geocoding/intro#BYB

	//report("meldung.txt","------- Untersuche Ort: ".$ort."\n");
	$ausgabe = array(0,0);
	if($p == "googleKey")
	{
		//report("meldung.txt","if $p = googleKey\n");
		$path = 'https://maps.google.com/maps/api/geocode/json?address='.urlencode($ort).'&key=AIzaSyC3E46KYcugkgAEBDNyrTTVe1-lvZBc10w';
	}
	else if($p == "google")
	{
		//report("meldung.txt","if $p = google\n");
		$path = 'https://maps.google.com/maps/api/geocode/json?address='.urlencode($ort);
	}
	else // OSM Daten auslesen
	{
		//report("meldung.txt","if $p = OSM\n");
		$path = 'http://nominatim.openstreetmap.org/search?q='.urlencode($ort).'&format=json&limit=1';
	}
	$geocode = file_get_contents($path);
	
	// Falls kein gültiges json-File erstellt wurde, brechen wir hier ab.
	if( FALSE!==strpos($geocode, "ZERO_RESULTS") )
	{
		//report("meldung.txt","if ZEERO_RESULT\n");
		trigger_error("Keine Koordinaten fuer ".$ort."   ".$output->error_message ,E_USER_NOTICE);
		return $ausgabe; // keine Koordinaten
	}
	
	$output = json_decode($geocode);
	
	if(FALSE !== strpos($p,"google"))
	{
		//report("meldung.txt","if strpos google\n");
		if(isset($output->error_message))
		{
			//report("meldung.txt","isset error_message\n");
			trigger_error("Keine Koordinaten fuer ".$ort."   ".$output->error_message ,E_USER_NOTICE);
		}
		else
		{
			//report("meldung.txt","else isset error_message\n");
			$latitude  = $output->results[0]->geometry->location->lat;
			$longitude = $output->results[0]->geometry->location->lng;
			$ausgabe = array($latitude,$longitude);
		}
	}
	else
	{
		//report("meldung.txt","else if strpos google\n");
		if(count($output) > 0)
		{
			//report("meldung.txt","if output > 0\n");
			$latitude =$output[0]->{'lat'};
			$longitude=$output[0]->{'lon'};	
			$ausgabe = array($latitude,$longitude);
		}		
	}
	return $ausgabe;
}	
?>