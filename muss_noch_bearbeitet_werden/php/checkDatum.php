<?php
	function setDateFlag($date, $start_ende){
	}

    
	function checkDatum ($datum) {   
	/*
		Prüft, ob das datum ein bestimmtes Format hat.
		Rückgabe:
		1:	Das Datum ist vollständig.
		2:	Nur der Tag fehlt.
		4:	Tag und Monat fehlen.
		64:	Ungültiges Format.
	*/
		require_once("search_replace.php");
		$datum = str_replace("März",'-03-',$datum);
		$datum = str_replace('.','',$datum);
		$jhd = strpos($datum,"Jahrhundert");
		$vChr = strpos($datum,"v Chr");
		$datum = search_and_replace($datum);
		
		$regDatum = "/[\d]{1,2}+[\-]+[\d]{1,2}+[\-]+[\d]{1,4}/";
		$regMonat = "/[\d]{1,2}+[\-]+[\d]{1,4}/";
		$regJahr = "/[\d]{1,4}/";
		$result = array (64,"",false,false);
		
		$erg1 = preg_match($regDatum, $datum, $match);
		if($erg1 === 1){
			// Pattern gefunden
			$result =  array (1, $match[0],$jhd,$vChr);
		}
		else if($erg1 === 0){
			// Tag nicht in Zeichenkette
			$erg2 = preg_match($regMonat, $datum, $match);
			if($erg2 === 1){
				// Pattern gefunden
				$result =  array (2, $match[0],$jhd,$vChr);
			}
			else if($erg2 === 0){
				// Monat nicht in Zeichenkette
				$erg3 = preg_match($regJahr, $datum, $match);
				if($erg3 === 1){
					// Pattern gefunden
					$result =  array (4, $match[0],$jhd,$vChr);
				}
				else if($erg3 === 0){
					// Jahr nicht in Zeichenkette
				}
				else{
					// Es ist ein Fehler aufgetreten
				}
			}
			else{
				// Es ist ein Fehler aufgetreten
			}
		}
		else{
			// Es ist ein Fehler aufgetreten
		}
		
		return  $result;
	}
?>	