<!DOCTYPE html>
<hmtl lang="de">
<head>
<meta charset="UTF-8">
<title>Administration Excel auslesen</title>
</head>
<body>
<article style="font-family: Calibri, Arial">
	<?php
	/*!
	 \file      adm_auslesen_excel.php
	 \author    J. Schönbohm
	 \par Erstellt am:
				2017-01-19

	 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-19
	 \par Versionshistorie:
				 1.0.0 &nbsp; Datum: &nbsp; 2017-01-19 <br>
					<em>Erstes Release (J. Schönbohm)</em><br>

	 \brief	Benutzerschnittstelle für den Import von Daten aus einer Excel-Tabelle.
	 
	 \details 	Das GUI bietet eine Formularfeld zur Auswahl der Datenbank 
				(aus den lokal vorhandenen) und eine Schaltfläche für die Auswahl der 
				Excel-Datei. Es sind nur .xls oder .xlsx Dateien erlaubt.
	\remark Es wird keine Fehlerüberprüfung durchgeführt! Fehler werden erst in der Datei
			auslesen_excel.php ermittelt.

	\remark Abhängigkeiten<br>
	\li \link db_operation.php\endlink<br>
	\li \link auslesen_excel.php\endlink<br>

	*/
	
	/*Abhängigkeiten */
	require_once("db_operation.php");

	$sys = dbOpen("mysql");
	$str="SELECT distinct(database_name) FROM `mysql`.`innodb_table_stats` WHERE `innodb_table_stats`.`table_name` = 'daten'";
	$dblist = dbSelect($sys,$str,false);

	echo '<form method="post" action="auslesen_excel.php" enctype="multipart/form-data">';
	echo '<label for="dbname">Datenbank: </label>';
	echo '<select id="dbname" name="dbname" size="1">';
	echo "<option value=''>-- Leer --</option>";
	while($dbobj=mysqli_fetch_object($dblist))
	{	
		echo "<option value='".$dbobj->database_name."'> ".$dbobj->database_name." </option><br>";
	}
	echo '</select>';
?>
	<p />
	<label for="fname"> Wählen Sie eine Excel-Datei (*.xls, *.xlsx) von Ihrem Rechner aus.</label><br>
	<input name="fname" id="fname"	type="file" size="50" accept="application/msexcel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" style="margin-left: 5px;"> 
	<p />
	<input id="btnsub" type="submit" size="15" value="senden" style=";" />
	</form>			
</article>
</body>
</html>