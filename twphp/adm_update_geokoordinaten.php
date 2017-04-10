<?php
/*!
 \file      adm_update_geokoordinaten.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-01-19

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-19
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 2017-01-19 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Ermittelt die Geokoordinaten auf Basis der Ortsangabe.
 
 \details 	Ermittelt die Geokoordinaten auf Basis der Ortsangabe.
			Dabei werden die drei möglichen Server (google, google mit Schlüssel und 
			OSM) abgefragt. Fehler und Statusmeldungen werden in die Datei error_msg.txt 
			ausgegeben. 
\remark
			Es werden insgesamt 6500 Einträge in der DB untersucht
			und aktualisiert, falls die Geokoordinaten ermittelt werden konnten.
			Andernfalls wird nur das <b>flag</b> <em>place_checked</em> auf 1 gesetzt.

\remark Abhängigkeiten<br>
\li \link geo.php\endlink<br>
\li \link msg_function.php\endlink<br>
\li \link db_operation.php\endlink<br>
*/

/* globale Einstellungen */
require_once "geo.php";
require_once "msg_function.php";
require_once "db_operation.php";

$dbname="timeline";

/*!
	\fn function local($db, $erg, $path="google")
	\brief	Hilfsfunktion
	\param	db	Verbundenes Datenbankobjekt.
	\param	erg	Ergebnis der DB-Abfrage
	\param	path	\li google	Google-Server für die Geokoordinatenermittlung verwenden.
					\li googleKey	Goole-Server mit Key für die Geokoordinatenermittlung verwenden.
					\li OSM		Openstreetmap-Server für die Geokoordinatenermittlung verwenden.
*/
function local($db, $erg, $path="google")
{
	while( ($row = mysqli_fetch_object($erg))) {
		
		$lat = 0;
		$long = 0;
		if(strlen($row->place) > 2)
		{
			// Das weitere Vorgehen ist PHP-Versions abhängig
			if (version_compare(phpversion(), '7.0.0', '<')) {
				list($lat,$long) = getLatLong($row->place, $path);
			}		
			else
			{	// ab Version 7 erfolgt die Zuweisung in list() von rechts -> links
				list($long,$lat) = getLatLong($row->place, $path);
			}			
		}
		else
		{
			trigger_error("Ungültige Ortsangabe ".$row->place." bei id: ".$row->id,E_USER_NOTICE);
			continue;
		}	
		
		if($lat != 0 && $long != 0)
		{
			// im Erfolgsfall
			$abfrage ="UPDATE daten set latitude=".$lat.", longitude=".$long.", place_checked=1 where id=".$row->id;
			$erg1 = dbUpdate($db, $abfrage);
			if(0 >= $erg1) trigger_error("Lat/Long-Eintrag gescheitert für id: ".$row->id, E_USER_NOTICE);	
			else
				report("geo_update_msg.txt",$abfrage."\n");
		}
		else
		{
			trigger_error("Keine Geokoordinaten für ".$row->id.": ".$row->place, E_USER_NOTICE);
			$abfrage ="UPDATE daten set place_checked=1 where id=".$row->id;
			$erg1 = dbUpdate($db, $abfrage);
		}
	}
}

// DB verbinden
$db = dbOpen($dbname);

// 2500 Einträge mit Ortsangabe und ohne Geokoordinaten bei google suchen.
$str ="SELECT id, place FROM daten where length(place) >= 3 and (latitude=0 or longitude=0) and place_checked=0 LIMIT 2500";
$erg = dbSelect($db, $str);
echo mysqli_affected_rows($db)." Datensätze ermittelt und an Google (ohne Key) übertragen.<br>";
if(FALSE !== $erg) local($db, $erg);
else
{
	trigger_error("Die Select-Abfrage (google) ist gescheitert!",E_USER_WARNING);
}

$erg = dbSelect($db, $str);
echo mysqli_affected_rows($db)." Datensätze ermittelt und an Google (mit Key) übertragen.<br>";
if(FALSE !== $erg) local($db, $erg, "googleKey");
else
{
	trigger_error("Die Select-Abfrage (googleKey) ist gescheitert!",E_USER_WARNING);
}

// 1500 Einträge mit Ortsangabe und ohne Geokoordinaten bei OSM suchen.
$str ="SELECT id, place FROM daten where length(place) >= 3 and (latitude=0 or longitude=0) and place_checked=0 LIMIT 1500";
$erg = dbSelect($db, $str);
echo mysqli_affected_rows($db)." Datensätze ermittelt und an OSM übertragen.<br>";
if(FALSE !== $erg) local($db, $erg, "OSM");
else
{
	trigger_error("Die Select-Abfrage (osm) ist gescheitert!",E_USER_WARNING);
}
echo "Geokoordinaten bestimmt.<br>";
?>