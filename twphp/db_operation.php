<?php
/*!
 \file      db_operation.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-01-16

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-16
 \par Versionshistorie:
            1.0.0 &nbsp; Datum: &nbsp; 2017-01-16 <br>
               <em>Erstes Release (J. Schönbohm)</em><br>

 \brief     Funktionen zur Interaktion mit einer DB.
	
\details	Dieses Programm stellt Operationen zur Interaktion
			mit Datenbanken bereit. Dabei werden die möglichen
			Fehlerzustände berücksichtigt.
			
\remark Abhängigkeiten<br>
\li \link msg_function.php\endlink<br>			
*/

require_once("msg_function.php");

/*!
	\fn function dbOpen($dbName)
	\brief	Öffnet eine DB.
	\details	Öffnet eine DB oder schreibt Fehlermeldungen in die log-Datei.
				Bei Erfolg ist die Anweisung mysqli_close($db); erforderlich.
	\param	dbName	Name der DB, die geöffnet werden soll.
	\return	Verbindung zu der DB
	\return	FALSE 	Wenn die Verbindung nicht aufgebaut werden konnte.
*/
function dbOpen($dbName)
{
	$db=mysqli_connect("localhost","root","",$dbName);
	if(mysqli_connect_errno())
	{
		// Wenn die Verbindung nicht aufgebaut werden kann.
		$msg="Failed! ".$dbName."\n";
		$msg = $msg."Number: ".mysqli_connect_errno();
		$msg = $msg." | ".mysqli_connect_error()."\n";	
		trigger_error($msg,E_USER_WARNING);
		return FALSE;
	}
	else
	{
		mysqli_set_charset($db,'utf8');
		return $db;
	}
}


/*!
	\fn function dbUpdate($db, $str)
	\brief	Aktualisiert Einträge in der DB.
	\param	db		Handle der DB.
	\param	str		Update-Anweisung
	\return	n >= 0	Anzahl der aktualisierten Zeilen
	\return	n < 0	Die Anweisung konnte nicht ausgeführt werden.
*/
function dbUpdate($db, $str)
{
	// Perform an update, check for error
	if (!$erg=mysqli_query($db,$str))
	{
		// Wenn mysqli_query === FALSE ist, wird dieser Teil abgearbeitet.
		/*
			- Die Tabelle oder Spalte ist nicht vorhanden.
			- Die Syntax der Anweisung ist fehlerhaft.
		*/
		
		$err=mysqli_error_list($db);
		$msg = "Failed! ".$str."\n";
		$msg = $msg."Errno: ".$err[0]['errno'];
		$msg = $msg."  SQLstate: ".$err[0]['sqlstate'];
		$msg = $msg."  MSG     : ".$err[0]['error']."\n";
		trigger_error($msg,E_USER_WARNING);
	}
	return mysqli_affected_rows($db);
}
/*!
	\fn function dbInsert($db, $str)
	\brief	Fügt neue Einträge in die DB ein.
	\param	db		Handle der DB.
	\param	str		Insert-Anweisung
	\return	n > 0	Anzahl der aktualisierten Zeilen
	\return	n <= 0	Die Anweisung konnte nicht ausgeführt werden.
*/
function dbInsert($db, $str)
{
	// Perform an insert, check for error
	if (!$erg=mysqli_query($db,$str))
	{
		// Wenn mysqli_query === FALSE ist, wird dieser Teil abgearbeitet
		/*
			- Tabelle oder Spalte nicht vorhanden.
			- Syntax der Anweisung ist fehlerhaft.
			- Eintrag bereits vorhanden (UNIQUE oder PRIMARY).
		*/
		$err=mysqli_error_list($db);
		$msg = "Failed! ".$str."\n";
		$msg = $msg."Errno: ".$err[0]['errno'];
		$msg = $msg."  SQLstate: ".$err[0]['sqlstate'];
		$msg = $msg."  MSG     : ".$err[0]['error']."\n";
		trigger_error($msg,E_USER_WARNING);
	}

	return mysqli_affected_rows($db);	// Wert > 0

}
/*!
	\fn function dbSelect($db, $str, $silent=TRUE)
	\brief	Fragt die Einträge der DB ab.
	\param	db		Handle der DB.
	\param	str		Select-Anweisung
	\param	silent	TRUE (default) -> keine Statusmeldung in error_msg.txt
	\return	FALSE	Anzahl der gefundenen Datensätze <= 0.
	\return	erg		Die gefundenen Datensätze.
*/
function dbSelect($db, $str, $silent=TRUE)
{
	// Perform a query, check for error
	if (!$erg=mysqli_query($db,$str))
	{
		// Wenn mysqli_query === FALSE ist oder keine Einträg, wird dieser Teil abgearbeitet
		/*
			- Tabelle oder Spalte ist nicht vorhanden.
			- Syntax der Anweisung ist fehlerhaft.
		*/
		$err=mysqli_error_list($db);
		$msg = "Failed! ".$str."\n";
		$msg = $msg."Errno: ".$err[0]['errno'];
		$msg = $msg."  SQLstate: ".$err[0]['sqlstate'];
		$msg = $msg."  MSG     : ".$err[0]['error']."\n";
		trigger_error($msg,E_USER_WARNING);
		return FALSE;
	}
	else if(mysqli_affected_rows($db) === 0)
	{
		if(FALSE === $silent){
			$msg = "Keine passenden Datensätze! ".$str."\n";
			trigger_error($msg,E_USER_NOTICE);
		}
		return FALSE;
	}
	else
		return $erg;
}
	

?>
