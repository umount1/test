<?php
/*!
 \file      coding.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-03-17

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-03-17
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 2017-03-17 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	encodeStr4DB($str) eine Funktion, um Zeichenketten in UTF-8 zu konvertieren und für eine SQL-Anweisung 
 vorzuvereiten.
 str2json($str) eine Funktion, um eine Zeichenkette in einen gültigen JSON-String zu konvertieren.

*/

/*!
	\fn function encodeStr4DB($str)
	\brief	Ermittelt die Kodierung einer Zeichenkette und wandelt sie in UTF-8.
	\param	str	Zeichenkette, die umkodiert werden soll.
	\return	Zeichenkette $str kodiert in UTF-8.
*/	
	function encodeStr4DB($str)
	{
	/*
		_ANSI		ANSI 
		_UCSbE		UCS mit big Endian
		_UTF8BOM	UTF-8 mit BOM
		_UTF8oBOM	UTF-8 ohne BOM
		_W1252		Windows-1252
	*/		
	
	// UTF-8 muss vorne stehen, sonst werden UTF-8 Dateien als ISO-8859-1 erkannt!
	// Es gibt noch viele weitere Kodierungen (s. mb_list_encodings()).
		$enclist = "UTF-8, Windows-1252, ISO-8859-1"; 

		$enc = mb_detect_encoding($str,$enclist,true);
		trigger_error("Kodierung von: ".$str." ist ".$enc, E_USER_NOTICE);
		
		// Zwischen Iso und Win kann nicht unterschieden werden, so dass jeweils Iso als Ergebnis kommt.
		// Da Win im Bereich von 0x80 bis 0x9F zusätzlich Zeichen (z.B. das € Symbol) definiert hat und 
		// Iso nicht, wird in diesen Fällen mit Win konvertiert.
		if($enc === "ISO-8859-1")
			$str = mb_convert_encoding($str, "UTF-8", "Windows-1252");//$enc);
		else
			$str = mb_convert_encoding($str, "UTF-8", $enc);
		
		// Zum Einfügen der Daten in die DB müssen die Anführungszeichen noch auskommentiert werden.
		$str = str_replace("\\","\\\\",$str);
		$str = str_replace("'","\'",$str);
		$str = str_replace('"','\"',$str);
		return $str;	
	}
/*!
	\fn function str2json($str)
	\brief	Formt die Zeichenkette in einen gültigen JSON-String um.
	\param	str	Zeichenkette, die umgeformt werden soll.
	\return	Gültige JSON-Zeichenkette.
*/	
	function str2json($str)
	{
		// Wenn ein \ im Text enthalten ist, wird er durch \\ ersetzt.
		$str = str_replace("\\","\\\\",$str);
		// Wenn ein " im Text enthalten ist, muss es für einen zulässigen JSON-String 
		// durch \" ersetzt werden. Einfache ' sind in JSON kein Problem.
		$str = str_replace('"','\"',$str);
		return $str;	
	}	

?>