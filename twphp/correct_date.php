<?php
/*!
 \file      correct_date.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-01-19

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-19
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 2017-01-19 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Funktionen zur Datumsüberprüfung aller DB Einträge.

\remark Abhängigkeiten<br>
\li \link gregorian2num.php\endlink<br>
\li \link db_operation.php\endlink<br>
\li \link twdate.php\endlink<br>
\li \link msg_function.php\endlink<br>	 

*/

/* globale Einstellungen */
require_once("gregorian2num.php");
require_once("db_operation.php");
require_once("twdate.php");
require_once("msg_function.php");

/*!
	\fn function correctDate($db)
	\brief	Überprüfung der Datumsangaben in der DB
	\details	Die Funktion ermittelt die Einträge mit sortstart oder sortend = 0
				und versucht startdate und enddate erneut in einen gültigen Wert
				umzuwandeln.
				Probleme stehen in der Datei error_msg.txt und zusätzlich wird 
				ein Meldungsfenster angezeigt.
	\param	db	Verbundenes Datenbankobjekt, das untersucht werden soll.
	\return	FALSE	wenn keine Daten zu ändern sind.
	\return	n	Anzahl der geänderten Datensätze.
*/ 
function correctDate($db)
{
	// Daten korrigieren
	$str="Select `id`, `title`, `startdate`, `enddate` from `daten` where `sortstart`=0 OR `sortend`=0;";
	$erg = dbSelect($db,$str);
	$anzSelect = mysqli_affected_rows($db);
	
	if(FALSE === $erg)
	{
	// Wenn keine Datensätze gefunden wurden
	// erscheint zusätzlich folgender Text am Bildschirm.
		echo "<script>".
			"alert('Es wurden keine passenden Datensätze gefunden! Details siehe error_msg.txt');".
//			"window.history.back();".
			"</script>".
			"</body></html>";
		return FALSE;
	}
	else
	{
		$edat = new twdate;
		$sdat = new twdate;
		$anzahl = 0;
		while($result=mysqli_fetch_object($erg))
		{
			try{
				$sdat->str2twdate($result->startdate);
				$edat->str2twdate($result->enddate);
			}
			catch(Exception $e)
			{
				echo "<script>".
					"alert('Das Datum ".$e->getMessage()." ist unzulässig! Details siehe error_msg.txt');".
					"</script>";
				continue;
			}
			$snum = gregorian2num($sdat->year, $sdat->month, $sdat->day);
			$enum = gregorian2num($edat->year, $edat->month, $edat->day);
			$diff = $enum - $snum;
			// Falls das Enddatum vor dem Anfangsdatum liegt
			if(0 > $diff)
			{
				trigger_error("Ende: ".$edat->year."/".$edat->month."/".$edat->day
					." < Anfang: ".$sdat->year."/".$sdat->month."/".$sdat->day,E_USER_WARNING);
				echo "<script>".
					"alert('Ende: ".$edat->year."/".$edat->month."/".$edat->day
					." < Anfang: ".$sdat->year."/".$sdat->month."/".$sdat->day
					." ! Details siehe error_msg.txt');".
					"</script>";
				continue;				
			}
			
			// Update der Daten in der DB
			$str="UPDATE `daten` SET `sortstart` = ".$snum
										.", `sortend` = ".$enum
										.", `difference` = ".$diff
										." WHERE `daten`.`id` = ".$result->id.";";
			$anz = dbUpdate($db,$str);
			if(0 < $anz) $anzahl = $anzahl + $anz;
		}
		echo "<script>".
			"alert('Es wurden ".$anzahl." von ".$anzSelect." Datensätzen aktualisiert! Details siehe error_msg.txt');".
//			"window.history.back();".
			"</script>".
			"</body></html>";	
	}
	return $anzahl;
}
?>
