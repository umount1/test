<?php
/*!
 \file      twdate.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-01-19

 \version   1.0.1 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-02-27
 \par Versionshistorie:
            1.0.1 &nbsp; Datum: &nbsp; 2017-02-27 <br>
                <em>Versionsbehandlung für explode entfernt, sie ist nicht erforderlich.  (Sb)</em><br> 
            1.0.0 &nbsp; Datum: &nbsp; 2017-01-19 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Datums-Klasse.
\remark Abhängigkeiten<br>
\li \link check_date.php\endlink<br>

*/

/* globale Einstellungen */
require_once("check_date.php");


/*!
	\class	twdate
	\brief	Datums-Klasse
	\details	Die Klasse speichert ein gültiges Datum. Bei der Konstruktion wird
				die Gültigkeit überprüft. Die Konstruktion ist über den Konstruktor
				__construct($year=7777, $month=12, $day=31) oder die Funktion
				str2twdate($str) mit $str = [-]yyyy-mm-dd möglich.
				Sollte das Datum ungültig sein, so wird eine Exception mit dem 
				falschen Datum als Meldung geworfen und das Datum wird auf
				7777-12-31 gesetzt.
	\throw	Exception	Ungültiges Datum.
*/ 
class twdate{
	
	public $year;	//!< Jahreszahl
	public $month;	//!< Monatsangabe aus [1; 12]
	public $day;	//!< Tag aus [1; 31] aber abhängig vom Monat und Jahr

/*!
	\fn function __construct($year=7777, $month=12, $day=31)
	\brief	Konstruktor
	\details	Erzeugt eine Instanz der Klasse twdate oder wirft eine Ausnahme, 
				wenn das Datum ungültig ist.
	\param	year	Jahreszahl (default 7777)
	\param	month	Monatsangabe [1;12]	(default 12)
	\param	day		Tag [1;31] (default 31)
	\return	Instanz von twdate oder NULL falls ein unzulässiges Datum angegeben wird.
*/	
    function __construct($year=7777, $month=12, $day=31) {
		if(!is_dateCorrect($year, $month, $day))
		{
			// Das Objekt ist beim Verlassen des Konstruktors NULL.
			throw new Exception($year."-".$month."-".$day);
		}
        $this->year = $year;
		$this->month = $month;
		$this->day = $day;
    }	
/*!
	\fn public function str2twdate($str)
	\brief	Wandelt eine Zeichenkette der Form "yyyy-mm-dd" in ein numerisches 
			Datum um und weist die Werte der Instanz zu.
	\details	Falls das Datum ungültig ist, wird eine Ausnahme geworfen und die
				Instanz behält ihre bisherigen Werte.
	\param	str	Datumsangabe "yyyy-mm-dd"
	\return	TRUE oder wirft eine Ausnahme.
*/	
	public function str2twdate($str)
	{
		// Format [-]yyyy-mm-dd
		// String zerlegen
		
		// Vorzeichen ermitteln
		$faktor = 1; 
		if(0 === stripos($str,'-') )
		{
			$str = substr($str, 1);
			$faktor = -1;
		}
		
		list($year, $month, $day)=explode('-', $str);
		
		$year = $year * $faktor;
		$month = (int)$month;
		$day = (int)$day;
		
		// Gültigkeit prüfen
		if(!is_dateCorrect($year, $month, $day))
		{
			throw new Exception($year."-".$month."-".$day);
		}
		else
		{
			$this->year = $year;
			$this->month = $month;
			$this->day = $day;
		}	
		return TRUE;		
	}
/*!
	\fn public function twdate2str()
	\brief	Wandelt das numerische Datum der Instanz in eine Zeichenkette 
			der Form "yyyy-mm-dd" um.
	\return	Zeichenkette der Form "yyyy-mm-dd".
*/	
	public function twdate2str()
	{
		return $this->year."-".$this->month."-".$this->day;
	}
}

?>