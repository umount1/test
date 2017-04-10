<?php
/*!
 \file      auslesen_excel.php
 \author    J. Schönbohm
 \par Erstellt am:
            2016-08-01

 \version   2.0.2 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-03-17
 \par Versionshistorie:
  			2.0.2 Datum 2017-03-17<br>
				<em>Funktion für die Kodierung ausgelagert nach coding.php (Sb)</em><br>
 			2.0.1 Datum 2017-02-27<br>
				<em>An sql-Export ein ';' angehängt. (Sb)</em><br>
			2.0.0 Datum 20.01.2017<br>
				<em>Alles komplett umgestellt auf neue DB-Struktur. (Sb)</em><br>
			1.0.1 Datum: 2016-11-11 <br>
					<em>Diverse Anpassungen bei der Datumsüberprüfung und den 
					Fehlermeldungen. (Sb)</em><br>
            1.0.0 &nbsp; Datum: &nbsp; 2016-08-01 <br>
               <em>Erstes Release (J. Schönbohm)</em><br>

 \brief     Einlesen der Excel-Tabellen.
	
\details	Das Skript liest die Exceltabellen ein und speichert die Datensätze 
			in der DB DBWiki. Zusätzlich werden die ausgeführten SQL-Anweisungen
			in einer Datei <Datum>_excelImport.sql gespeichert. 
			Der Name der Excel-Tabelle muss in $tmpfname gespeichert werden.
			Außerdem prüft das Programm die Datumsangaben und gibt im Fehlerfall
			eine Meldung in der Datei error_msg.txt aus.
\remark Das Skript muss über ein HTML-Formular (Methode POST) mit den Daten versorgt
		werden. Dabei kann das Skript auch auf einem entfernten Rechner laufen
		und sich auf eine lokale Datei beziehen. Diese wird über das Netz übertragen
		und dann eingelesen.

\remark Abhängigkeiten<br>
\li \link geo.php\endlink<br>
\li \link msg_function.php\endlink<br>
\li \link db_operation.php\endlink<br>
\li \link twdate.php\endlink<br>
\li \link gregorian2num.php\endlink<br>
\li \link PHPExcel-1.8/Classes/PHPExcel.php\endlink<br>
\li \link coding.php\endlink<br>
*/
	require_once "PHPExcel-1.8/Classes/PHPExcel.php";
	require_once "msg_function.php";
	require_once "geo.php";
	require_once "db_operation.php";
	require_once "twdate.php";
	require_once "gregorian2num.php";
	require_once "coding.php";


	$getGeo = FALSE; //!< Steuert, ob die Geokoordinaten bestimmt werden. FALSE = GeoServer werden NICHT abgefragt!
	if(empty($_POST["dbname"]))$dbname="test";
	else $dbname=$_POST["dbname"];  //!< Der Name der Datenbank, in die die Werte eingetragen werden sollen (z.B. "timeline_v2").
	if(empty($_FILES["fname"]["tmp_name"]))$tmpfname = "2017_03_28_DatenerfassungFebruar2017.xlsx";
	else $tmpfname=$_FILES["fname"]["tmp_name"];//!< Der Name der Excel-Datei, die eingelesen werden soll (z.B. "../entwicklung/DatenerfassungTest.xlsx" ).

	/* nur für den Test */
	echo "Datenbank: ".$dbname."<br>Exceldatei: ".$_FILES["fname"]["name"]."<p />";


	if($dbname === "") exit("Bitte erste die Datei auslesenExcel.php konfigurieren<br>und eine DB eintragen!");

	$db = dbOpen($dbname);
	$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
	$excelObj = $excelReader->load($tmpfname);

	//mysqli_query($db,"Delete from `test`.`daten` where `daten`.`weight`=42;");
	$categoryDefault='Keine Angabe';

	$title='';
	$place='';
	$startdate='';
	$enddate='';
	$date_display='day';
	$keywords='';
	$description='';
	$category=$categoryDefault;
	$link='';
	$weight=42;
	$ausgabe[0] = 0;
	$ausgabe[1] = 0;	
	
// Die DB-Operationen werden hier protokolliert.	
	$reportFile = date("Y_m_d_").time()."_excelImport.sql";

		
	$sheetCount = $excelObj->getSheetCount();
	
    for ($i = 0; $i < $sheetCount ; $i++)
	{
		// Über die Tabelle iterieren
		
		$worksheet = $excelObj->getSheet($i);
		//echo $worksheet->getRowIterator()->current()->getRowIndex()."<p>";
		echo "Tabellenblatt: ".$worksheet->getTitle()."<p />";
		echo "<table border='1' style='margin-top: 15px;'>";		
		foreach ($worksheet->getRowIterator() as $row) 
		{
			echo "<tr>";
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			foreach ($cellIterator as $cell) 
			{
				if($cell->getRow() === 1)
				{ // Spaltenüberschriften ermitteln
					// Überschriften den DB-Spalten zuordnen
					switch($cell->getValue())
					{
						case "title":
							$coltitle = $cell->getColumn();
						break;
						case "place":
							$colplace = $cell->getColumn();
						break;
						case "startdate":
							$colstartdate = $cell->getColumn();
						break;
						case "enddate":
							$colenddate = $cell->getColumn();
						break;
						case "date_display":
							$colddisplay = $cell->getColumn();
						break;
						case "keywords":
							$colkeywords = $cell->getColumn();
						break;
						case "category":
							$colcategory = $cell->getColumn();
						break;
						case "link":
							$collink = $cell->getColumn();
						break;
						case "description":
							$coldesc = $cell->getColumn();
						break;	
						case "weight":
							$colweight = $cell->getColumn();
						break;	
					}
				}
				else
				{ // Werte aus den Zellen auslesen
					switch($cell->getColumn())
					{
						case $coltitle:
							$title = encodeStr4DB($cell->getValue());
						break;
						case $colplace:
							$place = encodeStr4DB($cell->getValue());
						break;
						case $colstartdate:
							$startdate = $cell->getValue();
						break;
						case $colenddate:
							$enddate = $cell->getValue();
						break;
						case $colddisplay:
							$date_display = $cell->getValue();
						break;
						case $colkeywords:
							$keywords = encodeStr4DB($cell->getValue());
						break;
						case $colcategory:
							$category = encodeStr4DB($cell->getValue());
						break;
						case $collink:
							$link = encodeStr4DB($cell->getValue());
						break;
						case $coldesc:
							$description = encodeStr4DB($cell->getValue());
						break;	
						case $colweight:
							$weight = $cell->getValue();
						break;							
					}
					//$data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();	
				}
				if("" !== $cell->getValue()) echo "<td>".$cell->getValue()."</td>";
				else echo "<td></td>";
			} // Ende cellIterator
			if($row->getRowIndex() === 1) continue;
			
			echo "</tr>";
			// Nun ist die Zeile eingelesen und kann verarbeitet werden.
			// D.h. alle Ausnahmefälle werden untersucht.
			
			// Titel ist leer
			if($title == '' || $title == ' ')
			{
				trigger_error("Leerer Eintrag: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex(),E_USER_NOTICE);
				continue;
			}
			
// Datumsbehandlung 
			if($date_display==NULL||$date_display=='')
			{
				   $date_display="day";
			}	   
	
			$dat = new twdate();
			try
			{
				$dat->str2twdate($startdate);
			}
			catch(Exception $e)
			{
				trigger_error("Falsches Datum: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title.": ".$startdate,E_USER_NOTICE);
				continue;
			}
			$sortstart=gregorian2num($dat->year, $dat->month, $dat->day);
			if(FALSE === $sortstart) 
			{
				trigger_error("Falsches Datum: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title.": ".$enddate,E_USER_NOTICE);
				continue;
			}
			if($enddate==NULL||$enddate=='')
			{
			   $enddate=$startdate;
			   $sortend = $sortstart;
			   $difference = 0;
			}
			else
			{	
				try
				{
					$dat->str2twdate($enddate);
				}
				catch(Exception $e)
				{
					trigger_error("Falsches Datum: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title.": ".$enddate,E_USER_NOTICE);
					continue;
				}
				$sortend=gregorian2num($dat->year, $dat->month, $dat->day);
				if(FALSE === $sortend) 
				{
					trigger_error("Falsches Datum: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title.": ".$enddate,E_USER_NOTICE);
					continue;
				}				
				$difference=$sortend-$sortstart; 
				if(0 > $difference)
				{
					trigger_error("Negative Differenz: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title.": ".$enddate." < ".$startdate,E_USER_NOTICE);
					continue;					
				}
			}				
			 
// Ortsangaben 
			 if($place!='' && $getGeo)
			 {
				$ausgabe=getLatLong($place);
			
				if(!is_array($ausgabe))
				{
					
					$ausgabe[0] = 0;
					$ausgabe[1] = 0;					
				}
				
			 }
			 else
			 {
				 $ausgabe[0] = 0;
				 $ausgabe[1] = 0;
			 }
			 
			if($link=='')
			{
				$link = "http://de.wikipedia.org/w/index.php?title=Spezial%3ASuche&profile=default&search=".$title."&fulltext=Search";
			}
			
// Kategorie 
			if($category == '')
			{
				$category = $categoryDefault;
			}
			else
			{
				$categoryExists = dbSelect($db, "select category from category where category='".$category."'");
				if(FALSE === $categoryExists)	// Die Kategorie existiert noch nicht, daher kann der Datensatz nicht eingefügt werden.
				{
					trigger_error("Unbekannte Kategorie: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title.": ".$category,E_USER_NOTICE);
					continue;					
				}
			}
// Eintrag in die DB			
			$titleExists = dbSelect($db, "select id from daten where title='".$title."'");
						
			if( FALSE !== $titleExists )
			{	// Überschreiben: Es gibt schon einen Eintrag mit dem gleichen Titel.
				trigger_error("Eintrag vorhanden: Blatt ".$worksheet->getTitle()." Zeile: ".$row->getRowIndex()." Titel: ".$title,E_USER_NOTICE);
				$id = mysqli_fetch_object($titleExists)->id;
				$sql="UPDATE daten SET
				title='$title', place='$place', startdate='$startdate', enddate='$enddate', 
				date_display='$date_display',	keywords='$keywords', description='$description',
				category='$category', link='$link',	sortstart='$sortstart', sortend='$sortend', 
				difference='$difference', latitude='$ausgabe[0]', longitude='$ausgabe[1]', weight='$weight'
				WHERE id = '$id';";
				$sql_befehl = dbInsert($db,$sql);		  
				if(FALSE === $sql_befehl)
				{
					trigger_error("DB Eintrag nicht möglich: ".$sql, E_USER_NOTICE);
				}
				else
				{
					report($reportFile, $sql);
				}				
			}
			else
			{	
				// Einfügen in die DB
				if(!($title==NULL||$title=='')) {
					$sql="INSERT INTO daten(
					title, place, startdate, enddate, date_display,	keywords, description,
					category, link,	sortstart, sortend, difference,	latitude, longitude, weight) 
					VALUES	(   				 
					'$title', '$place',  '$startdate', '$enddate', '$date_display', '$keywords', '$description',
					'$category', '$link', '$sortstart', '$sortend', '$difference', '$ausgabe[0]', '$ausgabe[1]', '$weight');";
					$sql_befehl = dbInsert($db,$sql);		  
					if(FALSE === $sql_befehl)
					{
						trigger_error("DB Eintrag nicht möglich: ".$sql, E_USER_NOTICE);
					}
					else
					{
						report($reportFile, $sql);
					}
				}
			}		
// Re-Initialisierung für nächsten Eintrag			
			$title = '';
			$place = '';
			$startdate = '';
			$enddate = '';
			$date_display = '';
			$keywords = '';
			$description = '';
			$category = '';
			$link = '';
			$sortstart = 0;
			$sortend = 0;
			$difference = 0;
			$ausgabe[0] = 0;
			$ausgabe[1] = 0;
			$weight = 42;
		} // Ende von rowIterator
		echo "</table>";
	} // Ende Iteration über die Tabellenblätter
	mysqli_close($db);
?>


