<?php
date_default_timezone_set("Europe/Berlin");
$timestamp = time();
$start = date("d.m.Y - H:i:s", $timestamp);
?>
<?php

	$db = mysqli_connect("localhost","root","","source");
	
	// Nur notwendige Datens�tze kopieren.
	// category.categorylinks bleibt stets unver�ndert. 
	// source.categorylinks hingegen wird sp�ter angepasst.
	$step0="insert into source.categorylinks select * from category.categorylinks where cl_from between (select min(page_id) from page) and (select max(page_id) from page)";
	$step0a="CREATE INDEX cl_to on categorylinks(cl_to)";
	mysqli_query($db,$step0);
	mysqli_query($db,$step0a);
	
	// Hilfstabelle act erzeugen (ehem. gest bzw. geb)
	$step1 = "CREATE TABLE act (
	id INT AUTO_INCREMENT PRIMARY KEY,
	cl_from INT UNSIGNED)";
	$step1a="CREATE INDEX cl_from on act(cl_from)";
	
	echo "Create table act (aktuelle Kategorie)";
	
	if(mysqli_query($db,$step1)){
		mysqli_query($db,$step1a);
	
		// Hilfstabelle f�llen
		$step2 = "INSERT INTO act (cl_from) SELECT categorylinks.cl_from FROM categorylinks WHERE categorylinks.cl_to LIKE 'Gestorben%' ORDER BY cl_from ASC";
		echo "<br>Insert into act...<br>";	
		if(mysqli_query($db, $step2)) {
			// Weitere Hilfstabelle set1 erzeugen und mit Daten f�llen
			// Datens�tze, die sp�ter erfolgreich in die DB timeline 
			// �bertragen werden konnten, werden hier gel�scht.
			// Es verbleiben nur Datens�tze in der Tabelle, die zwar zur 
			// gew�nschten Kategorie geh�ren, bei denen aber kein eindeutiges
			// Ende-Datum erkannt werden konnte. Diese Datens�tze m�ssen sp�ter
			// von Hand �bertragen werden.
			$step3a = "create table set1 ( id int NOT NULL PRIMARY KEY, page_id int unsigned NOT NULL, page_title varchar(255) NOT NULL, page_latest int unsigned NOT NULL)";
			$step3b = "insert into set1 select act.id, page.page_id, page.page_title, page.page_latest from page, act where page.page_id = act.cl_from order by act.id asc";
			echo "Create table set1...<br>";	
			mysqli_query($db,$step3a);
			if(mysqli_query($db,$step3b)) {
				// In gestorben_2.php befinden sich die Anweisungen, die erforderlich sind,
				// um die �bertragung der Daten in die DB timeline vorzunehmen.
				
				require_once './php/gestorben_2.php';
				gestorben_2();
			}
		}
	}
	// }
	// }
	
	else {
		echo "Error: ".mysqli_error($db);
	}
	
	mysqli_close($db);
?>