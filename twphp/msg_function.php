<?php
/*!
 \file      msg_function.php
 \author    J. Schönbohm (Sb)
 \par Erstellt am:
            2016-08-01

 \version   1.0.2 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-02-27
 \par Versionshistorie:
 			1.0.2	Datum 	2017-02-27 <br>
				<em>Probleme mit fopen() behoben (Sb)</em><br>
			1.0.1	Datum 	2016-10-27 <br>
				<em>Neue Ausgabefunktionen report() (Sb)</em><br>
             1.0.0 &nbsp; Datum: &nbsp; 2016-08-01 <br>
                <em>Erstes Release (Sb)</em><br>

 \brief	Funktionen zur Dateiausgabe von Meldungen
	
\details
	Die folgenden Funktionen geben Meldungen in eine Datei aus. Über den Aufruf von
	trigger_error() werden Fehlermeldungen des Benutzers und automatisch 
	Fehlermeldungen des Systems gespeichert. Mittels report() werden normale Statusmeldungen
	oder Protokollausgaben in eine Datei geschrieben. Die Dateien werden fortlaufend
	beschrieben.
*/


/*!
	\fn function error_function($error_level, $error_message, $error_file, $error_line, $error_context) 
	\brief	Funktion zur Fehlerbehandlung
	\details	error_function() schreibt Fehlermeldungen in die Datei error_msg.txt.
	\param	error_level wird von php vergeben und charakterisiert den Fehler
	\param	error_message ist die Fehlermeldung
	\param	error_file ist die Datei, in der der Fehler aufgetreten ist
	\param	error_line Programmzeile, die den Fehler verursacht hat.
	\param	error_context Ein Array mit allen Variableninhalten zum Fehlerzeitpunkt
*/
function error_function($error_level, $error_message, $error_file, $error_line, $error_context) 
{
	if(file_exists("./twlog/")){
		$fp=fopen("./twlog/error_msg.txt","a");
	}else{
		if(file_exists("../twlog/")){
			$fp=fopen("../twlog/error_msg.txt","a");
		}else{
			$fp=fopen("./error_msg.txt","a");
		}
	}
	if($error_level == E_USER_NOTICE) 
		fwrite($fp, "Level [".$error_level."] File <".$error_file."> Line<".$error_line.">:   ".$error_message."\n");
	if($error_level == E_USER_WARNING) 
		fwrite($fp, "Level [".$error_level."] File <".$error_file."> Line<".$error_line.">:   ".$error_message."\n");
	if($error_level == E_USER_ERROR) 
		fwrite($fp, print_r($error_context,1)); // erzeugt sehr viele Ausgaben

	fwrite($fp,"\n\n");
	fclose($fp);
}

// Setzt die Funktion zur Fehlerbehandlung
set_error_handler("error_function");

/*!
	\fn function report($file, $msg)
	\brief	Dateiausgabe von Meldungen.
	\details	report() schreibt Meldungen fortlaufend in eine Datei.
	\param	$file		Dateiname
	\param	$msg		Meldung, die gespeichert wird
	\return	FALSE		Die Datei konnte nicht geöffnet werden. 
	\return	TRUE 		sonst
*/
function report($file, $msg)
{
	if(file_exists("./twlog/")){
		$fp=fopen("./twlog/".$file,"a");
	}else{
		if(file_exists("../twlog/")){
			$fp=fopen("../twlog/".$file,"a");
		}else{
			$fp=fopen("./".$file,"a");
		}
	}	
	
	if(FALSE !== $fp)
	{
		fwrite($fp, $msg."\n");
		fclose($fp);	
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}
	
?>