<?php
// Funktion zur Fehlerbehandlung
// error_level wird von php vergeben und charakterisiert den Fehler
// error_message ist die Fehlermeldung
// error_file ist die Datei, in der der Fehler aufgetreten ist
// error_line Programmzeile, die den Fehler verursacht hat.
// error_context Ein Array mit allen Variableninhalten zum Fehlerzeitpunkt
// Wichtige Parameter werden in der Datei error_db.txt gespeichert. Bei den wichtigen 
// Fehlern wird der Speicherinhalt ausgegeben.
function error_function($error_level, $error_message, $error_file, $error_line, $error_context) 
{
	$file=fopen("error_geboren_2.txt","a");
	fwrite($file, "Level [".$error_level."] Line<".$error_line.">:   ".$error_message."\n");
	if($error_level == E_USER_ERROR) fwrite($file, print_r($error_context,1)); // erzeugt zu viele Ausgaben

	fwrite($file,"\n\n");
	fclose($file);
}
?>
<?php
// Aufruf mit test.php?table=<tabname>&row_num=<n>
// Dieses Skript liest aus der angegebenen Tabelle <tabname> beginnend ab 
// Eintrag <n> Datensätze. Aus diesen wird aus dem Quelltext das Datum nach 
// "GEBURTSDATUM" und eine Ortsangabe extrahiert und nachfolgend in das 
// erforderliche Format transformiert.
// Sollte ein Datum nicht ermittelt werden können, so wird eine Meldung ausgegeben 
// und mit dem nächsten Datensatz fortgefahren.
// Besondere Behandlung erfahren Daten, bei denen nur das Jahrhundert angegeben ist.
// Hier wird das Startdatum auf den 1. Tag des Jahrhunderts und das Enddatum auf den 
// 31.12.7777 gesetzt. 
// Für jedes Datum wird ein Typ ermittelt, d.h. es wird bestimmt, ob der Tag, 
// der Monat oder nur das Jahr angegeben ist. Dies wirkt sich auf die Eigenschaft
// display_date aus.
// Sind für ein Datum mehrere Angaben enthalten, z.B. "2. Jhd oder 3. Jhd.", so 
// wird immer das 1. genommen.
// Andere Angaben wie "getauft" usw. werden als Startdatum gewählt.
function geboren_2($table = "set1", $row_num = "0"){
	// Die Funktion "error_function" schreibt auftretende Fehler in eine Log-Datei
	set_error_handler("error_function");

// Verbinden mit der Datenbank source
	$db = mysqli_connect("localhost","root","","source");
	mysqli_set_charset($db,'utf8_unicode_ci');
	if(!$db) exit("Es kann keine Verbindung zur Datenbank hergestellt werden :".mysqli_error());

	// Verbinden mit der Datenbank timeline	
	$db2 = mysqli_connect("localhost","root","","timeline");
	mysqli_set_charset($db2,'utf8_unicode_ci');
	if(!$db2) exit("Es kann keine Verbindung zur Datenbank hergestellt werden :".mysqli_error());

	session_start();

	$err = false;
		
// Funktionen zum Überprüfen des Datumformats und zur Ermittlung der Geodaten
	require_once "checkDatum.php";
	require_once "search_replace.php";
	require_once "geo.php";

// Überflüssige Datensätze löschen
	$del4 = mysqli_query($db,"DELETE FROM ".$table." WHERE page_title LIKE 'Geboren_%'");
	
// Datenbankabfrage für den Artikelinhalt
	$sqlstr = "SELECT * FROM ".$table;
	$set1 = mysqli_query($db, $sqlstr);
	if(!$set1) exit("ERROR: Abfrage '$sqlstr' gescheitert!\n");
//DEBUG
	// Setzt den Datensatzzeiger auf die Zeile row_num
//@mysqli_data_seek($set1,$row_num);
//DEBUG_ENDE
	
	// Hauptschleife über alle Tabelleneinträge
	while($row = mysqli_fetch_array($set1)){
		
// DEBUG		
//	$row_num++;
//	if($row_num == 425) exit("STOP");
// DEBUG_ENDE

	// Extrahiert einige Daten und speichert sie in Variablen
	$org_id = $row["page_id"];	// Entspricht der ID der Person
	$org_latest = $row["page_latest"];	// ID der Textseite

	// Holt den Text der Wikipedia-Seite zu der betrachteten Person
	$sqlstr = "SELECT old_text FROM text WHERE text.old_id = ".$org_latest." LIMIT 1";	
	$textAll = mysqli_query($db, $sqlstr);
	
//  Verändern des Inhalts und Unterteilung in Arrays	
	// Quelltext der Seite aus dem Abfrage-Objekt extrahieren
	$zeile1 = mysqli_fetch_array( $textAll, MYSQLI_ASSOC );
	$text=$zeile1['old_text'];

// Datumsangaben extrahieren ****************************************
	// Zeichenfolge vor relevanten Einträgen
	$startstring1 = "GEBURTSDATUM";
	$startstring3 = "GEBURTSORT";
	
	// bis zum nächsten html tag bzw. Zeichenfolge nach relevanten Einträgen
	$endstring = "|"; 

	if($rest = strstr($text,$startstring1)) { // rest enthält den Text vom Suchstring bis zum Ende
		$rest = str_replace($startstring1, "", $rest); // der Suchstring wird entfernt
		$endstueck = strstr($rest, $endstring);
		$startDatum = str_replace($endstueck,"",$rest); // endstueck wird im rest gelöscht
		$startOrg = $startDatum;
		
		// wie zuvor nur für startstring3 = Geburtsort
		if($rest = strstr($rest,$startstring3)) {
			$rest = str_replace($startstring3, "", $rest);
			$endstueck = strstr($rest, $endstring);
			$startOrt = str_replace($endstueck,"",$rest);
		}
		else
		{	
			//kein Geburtsort";
			trigger_error("Kein Ort vorhanden!--§§§§",E_USER_ERROR);
			$startOrt = "";
		}
		$startOrt = search_and_replace($startOrt);		
	}
	else
	{	
		//echo "Es konnte kein Geburtsdatum gefunden werden!<br>";
		//echo ("naja".$rest);
		trigger_error("|".$org_id."|".$row[2]."|Es konnte kein Geburtsdatum gefunden werden!--".$rest."--§§§§",E_USER_NOTICE);
		$err = true;
		continue;
	}

	// Einen Ort als "den" Ort festlegen.
	if(strlen($startOrt) < 3){ $ort = ""; }
	else {$ort = $startOrt; }
	

	$lat = "";
	$long = "";
	if(strlen($ort) > 2){
		list($lat,$long) = getLatLong($ort);
	}

	$ort = str_replace("'","\'", $ort);
	$ort = str_replace("´","\´", $ort);
	$ort = str_replace("`","\`", $ort);	
	$ort = str_replace("\n","", $ort);
	$ort = str_replace("\r","", $ort);	
	
	// Ersetzt viele Zeichen(-folgen) im Originaltext
	// Umlaute etc. werden nicht ersetzt, wenn die Zeichenkette
	// in einer Funktion steht (????).
	$startDatum = str_replace("März","-03-",$startDatum);
	list ($startDatumType, $startDatum, $startJhd, $startVChr) = checkDatum($startDatum);
	
	// Datum aufteilen in Tag, Monat, Jahr
	$startDatumParts = explode("-", $startDatum);
		
// Ende der Datumsbehandlung ******************************************
	
	// Titel der geladenen Seite holen und formatieren
	$name="";	
	$name2 = $row[2];
	$name = str_replace("_"," ", $name2);
	// Die Hochkommata führen zu Fehlern beim Einfügen in die Tabelle
	$name = str_replace("'","\'", $name);
	$name = str_replace("´","\´", $name);
	$name = str_replace("`","\`", $name);

// Eintragen anfang
	$name_eintrag = $name;	
	
	$startDatumDisplay = "day";
	$startTag = "01";
	$startMonat = "01";	
	switch($startDatumType){
		case 1:
			$startTag = $startDatumParts[0];
			$startMonat = $startDatumParts[1];
			$startJahr = $startDatumParts[2];
		break;
		case 2:
			$startMonat = $startDatumParts[0];
			$startJahr = $startDatumParts[1];
			$startDatumDisplay = "month";
		break;			
		case 4:
			$startJahr = $startDatumParts[0];
			$startDatumDisplay = "year";
		break;			
	}
	$endDatumDisplay = "year";
	$endTag = "31";
	$endMonat = "12";
	$endJahr = "7777";

	$dd = $startDatumDisplay;

	//	V.Chr. sollte eigentlich nicht vorkommen
	if($startVChr != false){
		$startJahr = $startJahr * (-1);
		if($startJhd != false) $startJahr = $startJahr + 99;
	}
	else
	{
		if($startJhd != false) $startJahr = $startJahr - 99;
	}

//$kat=$cat;
	$cat = "";
	$eventende="";
	
//	Standardwerte
	$event_type = "event";
	$low_threshold = "1";
	$high_threshold = "60";
	$importance = "60";
	$link = 'http://de.wikipedia.org/w/index.php?title=Spezial%3ASuche&profile=default&search=' . $name_eintrag . '&fulltext=Search';
	
// Datum zusammensetzen für die DB timeline
	$eventstart = $startJahr ."-". $startMonat ."-". $startTag;
	$sortstart = GregorianToJD($startMonat, $startTag, $startJahr);

	$eventende = $endJahr ."-". $endMonat ."-". $endTag;
	$sortende = GregorianToJD($endMonat, $endTag, $endJahr);
	$diff = $sortende - $sortstart;

	// Ein gültiger Datensatz wurde gefunden und wird nun in die DB timeline eingetragen

	// Hier werden die Kategorien als Beschreibung (für die Stichwortsuche) 
	// in ein Textfeld eingefügt.
	$step="select group_concat(cl_to) from categorylinks where cl_from='$org_id'";
	$res2=mysqli_query($db, $step); 
	list($cat2) = mysqli_fetch_row($res2);
	$cat = str_replace("_"," ", $cat2);
	$cat = str_replace(",",", ", $cat); // Leerzeichen hinter jedem Komma, so dass ein Umbruch in der Anzeige möglich ist.
	$cat = str_replace("'","\'", $cat);	// Hochkommata führen zu Fehlern im SQL-Befehl
	$cat = str_replace("´","\´", $cat);
	$cat = str_replace("`","\`", $cat);		
	// Alle Daten werden in die DB timeline eingetragen.
// DEBUG - DB timeline mit startOrg und endeOrg Splate für eine Überprüfung des Datums
//		$eintrag = "INSERT INTO daten (id, title, startdate, enddate, date_display, description, event_type, link, low_threshold, high_threshold, importance, sortstart, sortende, differenz, startOrg, endeOrg) VALUES ('$org_id','$name_eintrag','$eventstart','$eventende','$dd','$cat','$event_type','$link','$low_threshold','$high_threshold','$importance','$sortstart','$sortende','$diff','$startOrg','$endOrg')";	
// DEBUG-ENDE		
	$eintrag = "INSERT INTO daten (wiki_id, title, startdate, enddate, date_display, description, event_type, link, 
			low_threshold, high_threshold, importance, sortstart, sortende, differenz, place, latitude, longitude) 
			VALUES ('$org_id','$name_eintrag','$eventstart','$eventende','$dd','$cat','$event_type','$link','$low_threshold',
			'$high_threshold','$importance','$sortstart','$sortende','$diff','$ort','$lat','$long')";			
	$eintragen = mysqli_query($db2, $eintrag);
	if($eintragen == true){ 
		// Außerdem wird der Datensatz aus den beteiligten Tabellen entfernt.
		// Dadurch wird die Suche nach den folgenden Datensätzen beschleunigt.
		$del1 = mysqli_query($db,"DELETE FROM categorylinks WHERE cl_from = '$org_id'");
		$del2 = mysqli_query($db,"DELETE FROM page WHERE page_id = '$org_id'");
		$del3 = mysqli_query($db,"DELETE FROM text WHERE old_id = '$org_latest'");
		// An set1 lässt sich während der Suche der Fortschritt erkennen. Die Anzahl
		// der Datensätze nimmt fortwährend ab.
		$del4 = mysqli_query($db,"DELETE FROM ".$table." WHERE page_id = '$org_id'");			
	}
	else
	{
		trigger_error("Eintrag in DB gescheitert!--§§§§",E_USER_ERROR);
		echo("FEHLER: $name_eintrag<br>");
	}
} // Ende der while-Schleife

mysqli_close($db);
mysqli_close($db2);
}
?>		 
