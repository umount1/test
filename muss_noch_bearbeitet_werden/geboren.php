<?php

	$db = mysqli_connect("localhost","root","","source");
	
	// category.categorylinks bleibt stets unverändert. 
	// source.categorylinks hingegen wird später angepasst.
	$step0="insert into source.categorylinks select * from category.categorylinks where cl_from between (select min(page_id) from page) and (select max(page_id) from page)";
	$step0a="CREATE INDEX cl_to on categorylinks(cl_to)";
	mysqli_query($db,$step0);
	mysqli_query($db,$step0a);
	
	$step1 = "CREATE TABLE act (
	id INT AUTO_INCREMENT PRIMARY KEY,
	cl_from INT UNSIGNED)";
	echo "Create table act...";	
	$step1a="CREATE INDEX cl_from on act(cl_from)";	
	if(mysqli_query($db,$step1)){
		mysqli_query($db,$step1a);
		// Hilfstabelle füllen
		$step2 = "INSERT INTO act (cl_from) SELECT categorylinks.cl_from FROM categorylinks WHERE categorylinks.cl_to LIKE 'Geboren%' ORDER BY cl_from ASC";
		echo "<br>Insert into act...<br>";	
		if(mysqli_query($db, $step2)) {
			$step2a="delete from act where act.cl_from in (select cl_from from categorylinks where categorylinks.cl_to like 'Gestorben%')";
			mysqli_query($db, $step2a);
			// Weitere Hilfstabelle set1 erzeugen und mit Daten füllen
			// Datensätze, die später erfolgreich in die DB timeline 
			// übertragen werden konnten, werden hier gelöscht.
			// Es verbleiben nur Datensätze in der Tabelle, die zwar zur 
			// gewünschten Kategorie gehören, bei denen aber kein eindeutiges
			// Ende-Datum erkannt werden konnte. Diese Datensätze müssen später
			// von Hand übertragen werden.
			$step3a = "create table set1 ( id bigint(20) NOT NULL PRIMARY KEY, page_id bigint(20) NOT NULL, page_title varchar(255) NOT NULL, page_latest bigint(20) NOT NULL)";
			$step3b = "insert into set1 select act.id, page.page_id, page.page_title, page.page_latest from page, act where page.page_id = act.cl_from order by act.id asc";
			echo "Create table set1...<br>";	
			mysqli_query($db,$step3a);
			if(mysqli_query($db,$step3b)) {
				// In geboren_2.php befinden sich die Anweisungen, die erforderlich sind,
				// um die Übertragung der Daten in die DB timeline vorzunehmen.
				
				require_once './php/geboren_2.php';
				geboren_2();
			}
		}
	}
	else {
		echo "Error: ".mysqli_error($db);
	}
	
	mysqli_close($db);
?>