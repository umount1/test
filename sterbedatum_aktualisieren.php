<?php
/*!
 \file      sterbedatum_aktualisieren.php
 \author    J. Schönbohm (Vorlage von D. Tischler)
 \par Erstellt am:
            27.10.2016

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 27.10.2016
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 27.10.2016 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Sterbedatum ermitteln und eintragen
	
\details
	Personen, die beim letzten Aufruf noch lebten ( Sterbedatum 7777-12-31) werden 
	bei Wikipedia abgefragt und wenn sie zwischenzeitlich verstorben sind, wird das 
	Sterbedatum aktualisiert.
*/
header("Content-Type: text/html; charset=utf-8");
	require_once "checkDatum.php";
	require_once "search_replace.php";
	require_once "error_function.php";
	$reportfile="sterbedatum_aktualisieren.txt"; //!< Datei, in die die Meldungen geschrieben werden.
	
// DB Verbindung herstellen
$db = mysqli_connect("localhost","root","","timeline");
mysqli_set_charset($db,'utf8');
if(!$db)
{
	exit ("Verbindung konnte nicht hergestellt werden: ".mysqli_error());
}

$ergebnis = mysqli_query($db,"SELECT id, title, startdate, wiki_id, sortstart FROM daten WHERE enddate='7777-12-31'");
if( FALSE === $ergebnis || 0 === mysqli_num_rows($ergebnis) )
{
	// alles aufräumen
	mysqli_close($db);
	exit("Abfrage nicht erfolgreich! ".mysqli_error());
}


while($row = mysqli_fetch_object($ergebnis))
{
	$name = $row->title;
	$name = str_replace(" ", "_", $name);
	$id = $row->id;
	$start = $row->startdate;
	$sortstart = $row->sortstart;
	$wiki_id = $row->wiki_id;
	
	//$text = file_get_contents('https://de.wikipedia.org/wiki/'.$name);
	if(0 === $wiki_id)
	{
		report($reportfile,"-- Keine Person: ".$name." --");
		continue;
	}
	
	$text = file_get_contents('https://de.wikipedia.org/wiki?curid='.$wiki_id);
	
	if(FALSE === $text)
	{
		// Seite konnte nicht geöffnet werden
		report($reportfile,"-- Seite fehlt: ".$name." --");
		continue;	
	}
	
	// Datumsangaben extrahieren ****************************************
	// Zeichenfolge vor relevanten Einträgen

	$startstring2 = "STERBEDATUM";
	// bis zum nächsten html tag bzw. Zeichenfolge nach relevanten Einträgen
	$endstring = "|";
	$endstring2 = "STERBEORT";	

	// startstring2 = Sterbedatum
	if($rest = strstr($text,$startstring2)) {
		$rest = str_replace($startstring2, "", $rest);
		$rest = str_replace("<td>", "", $rest);
		$rest = str_replace("</td>", "", $rest);
		$rest = str_replace("<tr>", "", $rest);
		$rest = str_replace("</tr>", "", $rest);
		$endstueck = strstr($rest, $endstring);
		$endDatum = str_replace($endstueck,"",$rest);
		$endstueck = strstr($endDatum, $endstring2);
		$endDatum = str_replace($endstueck,"",$endDatum);
	}
	else
	{	
		//trigger_error("-- Person ".$name." lebt noch! --",E_USER_NOTICE);
		continue;
	}	
	
	// Funktion zum Prüfen des Datums einfügen und Flag wird gesetzt
	if(isset($endDatum)){
		$endDatum = str_replace("März","-03-",$endDatum);
		list($endDatumType, $endDatum, $endJhd, $endVChr) = checkDatum($endDatum);			
	}
	else{
		report($reportfile,"Kein Enddatum vorhanden!--");
	}
	
	// Datum aufteilen in Tag, Monat, Jahr
	if(isset($endDatum)){
		$endDatumParts = explode("-", $endDatum);	
	}	
	$endDatumDisplay = "day";
	$endTag = "01";
	$endMonat = "01";

	if(isset($endDatumParts)){	
		switch($endDatumType){
			case 1:
				$endTag = $endDatumParts[0];
				$endMonat = $endDatumParts[1];
				$endJahr = $endDatumParts[2];
			break;
			case 2:
				$endMonat = $endDatumParts[0];
				$endJahr = $endDatumParts[1];
				$endDatumDisplay = "month";
			break;			
			case 4:
				$endJahr = $endDatumParts[0];
				$endDatumDisplay = "year";
			break;			
		}
	}
	else
	{
		$endJahr = "7777";
	}	

/*	
	//	V.Chr.
	if($endVChr !== false){
		$endJahr = $endJahr * (-1);
	}
	// Monat und Tag auf das Ende des Jhd. setzen.
	if($endJhd !== false){
		$endMonat = 12;
		$endTag = 31;
	}	
*/	
// Datum zusammensetzen für die DB timeline

	if($endJahr === ""){
		$eventende = "";
		$sortende = "";
		$diff = 0;
	}
	else{
		$eventende = $endJahr ."-". $endMonat ."-". $endTag;
		$sortende = GregorianToJD($endMonat, $endTag, $endJahr);
		$diff = $sortende - $sortstart;
	}	
	
	$eintrag="UPDATE daten set enddate='".$eventende."', sortende=".$sortende.", differenz=".$diff." where id=".$id;
	report($reportfile,"-- ".$name.": ".$eintrag." --");

	$eintragen = mysqli_query($db, $eintrag);
	if($eintragen === FALSE){ 
		report($reportfile,"-- ".$name.": Eintrag in DB gescheitert! --");
	}		
}
mysqli_close($db);
?>