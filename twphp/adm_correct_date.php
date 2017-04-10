<?php
	/*!
	 \file	adm_correct_date.php
	 \author    J. Schönbohm
	 \par Erstellt am:
				2017-01-19

	 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-01-19
	 \par Versionshistorie:
				 1.0.0 &nbsp; Datum: &nbsp; 2017-01-19 <br>
					<em>Erstes Release (J. Schönbohm)</em><br>

	 \brief	Benutzerschnittstelle für die Korrektur von Datumsangaben in der DB.
	 
	 \details 	Das Skript durchsucht die DB (timeline_v2) nach Einträgen, bei denen
				sortstart oder sortende = 0 sind und versucht die Einträge 
				startdate und enddate mit der neuen Funktion gregorian2num() 
				umzurechnen.
	\remark Das Skript wird vermutlich nicht mehr benötigt.

	\remark Abhängigkeiten<br>
	\li \link db_operation.php\endlink<br>
	\li \link twdate.php\endlink<br>
	\li \link gregorian2num.php\endlink<br>
	\li \link correct_date.php\endlink<br>

	*/
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Datum korrigieren</title>
</head>
<body>
<?php
require_once("correct_date.php");
require_once("gregorian2num.php");
require_once("db_operation.php");
require_once("twdate.php");

$db=dbOpen("timeline");


$anz = correctDate($db);

if(FALSE === $anz)
{
	echo "Korrektur gescheitert!<br>";
}
else
{
	echo $anz." Datensätze korrigiert!<br>";
}
mysqli_close($db);

?>

</body>
</html>