In dieser Datei werden die �nderungen an den Datenbaken und den Skriptdateien f�r
die Erstellung der Datenbanken dokumentiert. Dabei stehen die letzten �nderungen
immer am Anfang der Datei!
--------------------------------------------------------------------------------
21.11.2016	J. Sch�nbohm
DB timeline:	
	Excel-Tabelle vom 07.11.2016 eingelesen. 
	Sterbedatum aktualisiert (durchgelaufen bis 23.11.2016).
--------------------------------------------------------------------------------
--------------------------------------------------------------------------------
20.10.2016	J. Sch�nbohm
DB timeline:	
	DB timeline und Skripte gesichert. 
--------------------------------------------------------------------------------
--------------------------------------------------------------------------------
12.10.2016	J. Sch�nbohm
DB timeline:	
	Splate id wurde umbenannt in wiki_id und die Eigenschaften 
	PRIMARY KEY, Autoinkrement und NOT NULL wurden entfernt. Daf�r wurde 
	ein Index wiki_id erzeugt.
	Eine neue Spalte id wurde eingef�gt mit den Eigenschaften PRIMARY KEY,
	Autoinkrement und NOT NULL
Skripte:
	geboren2.php (Z. 241), gestorben2.php (Z. 366), klick2dbTimeline.php (Z. 20, 37)
	und timeline.sql mussten entsprechend angepasst werden.
--------------------------------------------------------------------------------

Beinhaltet die Dateien, die erforderlich sind, um die DB zu erzeugen und die Skripte,
um die Inhalte einzuf�gen. 
Unterordner
auslesenExcel:	Skripte und eine Bibliothek, um die Daten einer Excel-Datei in die
				DB timeline einzuf�gen.
DB anlegen:		timeline.sql und wiki_leer.sql zum Erzeugen der DB
klick_xx:		Skripte zum Auswerten der Klickzahlen bei Wikipedia und zum 
				aktualisieren der entsprechenden DB-Eintr�ge (DB timeline)
php:			Skripte zum einlesen der Wikipedia-Dumps und Skripte mit 
				Hilfsfunktionen.

Dateien:
geboren.php		Ermittelt alle Personen, die geboren wurden und tr�gt sie in die
				DB timeline ein.
gestorben.php	Ermittelt alle Personen, die gestorben sind und tr�gt sie in die
				DB timeline ein (muss vor geboren.php ausgef�hrt werden).
update_geokoordinaten.php
				Ermittelt die Geokoordinaten zu den Ortsangaben (bei Google max. 2500/Tag)
update_geokoordinaten_osm.php
				Ermittelt die Geokoordinaten zu den Ortsangaben (bei OSM max. 1500/Tag)
update_personen.sql
				Tr�gt in die Spalte event_type einen Begriff ein, der die jeweilige
				Person zugeordnet wird. Diese Begriffe werden sp�ter f�r eine
				Legende im TimeGlider verwendet.