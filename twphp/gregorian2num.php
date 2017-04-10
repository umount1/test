<?php
/*!
 \file      gregorian2num.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-01-16

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-16
 \par Versionshistorie:
            1.0.0 &nbsp; Datum: &nbsp; 2017-01-16 <br>
               <em>Erstes Release (J. Schönbohm)</em><br>

 \brief     Umrechnung eines Datums in eine ganze Zahl.
	
\details	Dieses Programm bestimmt die Zahl der Tage im julianischen Kalender
			vor dem 31.12.-4714. Der 25.11.-4714 hat den Wert 1. 
			Schaltjahre gibt es dort nicht mehr und jedes Jahr hat 365 Tage.
			Weitere Testwerte
			\li 24.11.-4714 -> 0
			\li 31.12.-4715 -> -328
			\li 01.01.-4715 -> -692
			\li 15.11.-4716 -> -739
\remark Abhängigkeiten<br>
\li \link check_date.php\endlink<br>
\li \link msg_function.php\endlink<br>

*/
require_once("msg_function.php");
require_once("check_date.php");

/*!
	\fn function gregorian2Num($year, $month, $day)
	\brief	Umrechnung eines Datums in eine ganze Zahl.
	\param	day		Tag des Datums.
	\param	month	Monat des Datums 1-12
	\param	year	Jahr des Datums
	\return	Anzahl der Tage vor/nach dem -4714-11-24
	\return	FALSE 	Wenn ein ungültiges Datum vorliegt.
*/ 
function gregorian2Num($year, $month, $day)
{
	// Datumsprüfung
	if(is_dateCorrect($year, $month, $day) === FALSE) return FALSE;
	
	if($year > -4714)
	{
		$days=gregoriantojd($month,$day,$year);
	}
	else
	{
		$days=($year + 4714)*365;
		switch($month)
		{
			case 1:
				$days = $days - 31 + $day;
			case 2:
				if(2 == $month) $days = $days - 28 + $day;
				else $days = $days - 28;
			case 3:
				if(3 == $month) $days = $days - 31 + $day;
				else $days = $days - 31;
			case 4:
				if(4 == $month) $days = $days - 30 + $day;
				else $days = $days - 30;
			case 5:
				if(5 == $month) $days = $days - 31 + $day;
				else $days = $days - 31;
			case 6:
				if(6 == $month) $days = $days - 30 + $day;
				else $days = $days - 30;
			case 7:
				if(7 == $month) $days = $days - 31 + $day;
				else $days = $days - 31;
			case 8:
				if(8 == $month) $days = $days - 31 + $day;
				else $days = $days - 31;
			case 9:
				if(9 == $month) $days = $days - 30 + $day;
				else $days = $days - 30;
			case 10:
				if(10 == $month) $days = $days - 31 + $day;
				else $days = $days - 31;
			case 11:
				if(11 == $month) $days = $days - 30 + $day;
				else $days = $days - 30;
			case 12:
				if(12 == $month) $days = $days - 31 + $day;
				else $days = $days - 31;			
		}
	}
	return $days+37;
}

?>
