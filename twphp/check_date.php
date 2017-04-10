<?php
/*!
 \file      check_date.php
 \author    J. Schönbohm
 \par Erstellt am:
            2016-11-14

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2016-11-14
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 2016-11-14 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Funktionen zur Datumsüberprüfung 
	
\details
	Die folgende Funktion prüft, ob ein eingegebenes Datum ein gültiger Tag im
	gregorianischen Kalender ist.
	- Tage zwischen dem 4.10.1582 und dem 15.10.1582 existieren nicht.
	- Schaltjahre werden erst ab dem Jahr 1582 berechnet.
	- Das Jahr 0 existiert nicht.

\remark Abhängigkeiten<br>
\li \link msg_function.php\endlink<br>
	
*/

/* globale Einstellungen */
require_once("msg_function.php");


/*!
	\fn function is_dateCorrect($year, $month, $day)
	\brief	Funktion Datumsüberprüfung
	\details	is_dateCorrect() prüft, ob das Datum gültig ist und gibt eine Meldung 
				in die Fehlerdatei aus, wenn es ungültig ist.
	\param	year	Jahreszahl
	\param	month	Monatsangabe 1-12
	\param	day		Tag 1-(31)
	\return	TRUE	wenn das Datum gültig ist.
	\return	FALSE	falls es ungültig ist.
*/   
	function is_dateCorrect($year, $month, $day) 
	{   		
		if(0 == $year)
		{
			trigger_error($year."-".$month."-".$day." ungültiges Jahr", E_USER_WARNING);
			return FALSE;			
		}
		$leapyear = FALSE;	//<! Schaltjahr ja oder nein? Berechnung des Schaltjahres ab 1582.
		
		if($month < 1 || $month > 12) 
		{
			trigger_error($year."-".$month."-".$day." ungültiger Monat", E_USER_WARNING);
			return FALSE;
		}
		if($day < 1 || $day > 31) 
		{
			trigger_error($year."-".$month."-".$day." ungültiger Tag", E_USER_WARNING);
			return FALSE;
		}
		if($year === 1582 && $month === 10 && ($day >4 && $day < 15))
		{
			trigger_error($year."-".$month."-".$day." ungültiger Tag (s. gregorianischer Kalender)", E_USER_WARNING);
			return FALSE;			
		}
		
		if($year >= 1582 && $year%4 === 0)
		{
			$leapyear = TRUE;
			if($year%100 === 0) $leapyear = FALSE;
			if($year%400 === 0) $leapyear = TRUE;
		}
		switch($month)
		{
			case 2:
			if($day > 29 || ($day > 28 && $leapyear === FALSE) )
			{
				trigger_error($year."-".$month."-".$day." ungültiger Tag", E_USER_WARNING);
				return FALSE;
			}
			break;
			case 4:
			case 6:
			case 9:
			case 11:
				if($day > 30)
				{
					trigger_error($year."-".$month."-".$day." ungültiger Tag", E_USER_WARNING);
					return FALSE;
				}
			break;
		}
		
		return TRUE;
	}
?>	