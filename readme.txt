In dieser Datei wird der Arbeitsfortschritt an der Version 2.0 von TimeWiki
dokumentiert.

2017-03-28
Excel-Import abgeschlossen. Anleitung s. twphp/readme_excel.txt
Erfolg hängt von der php-Version ab. Version 5.x ist ungeeignet, es muss 7.x sein.

2017-01-24
Test mit der Zeichenkonvertierung durchgeführt.
Ziel ist der Einbau in auslesenExcel.php

2017-01-23
Einige Verwaltungsskripte erstellt und im Ordner dbwiki_v2/php abgelegt. Die 
Skripte wurden weitestgehend getestet. Die enthaltenen Funktionen erzeugen
Status- und Fehlermeldungen in der Datei error_msg.txt.
Mit diesen Dateien behobene Probleme:
Datumsangaben werden auf Korrektheit geprüft.
Die Umrechnung eines Datums in eine fortlaufende Zahl erfolgt nun auch für Angaben,
	die vor -4714/11/24 liegen
Excel-Dateien werden eingelesen und evtl. vorhandene Datensätze mit gleichem Titel
	werden überschrieben.
Die Geokoordinaten werden mit einem einzigen Programmaufruf von allen Anbietern
	ermittelt.

2017-01-16
Ordner entwicklung in dbwiki_v2 angelegt. Hier werden alle Programme
entwickelt und später für den Betrieb dann in entsprechende Ordner verteilt.

Dump der Daten (2017-01-10) mit neuer Struktur erstellt: timeline_v2_2017_01_16.sql



2017-01-10	
Dump der DB timeline vom Server geladen und als timeline_2017_01_10.sql.zip
gespeichert.

1. Schritt: Import der Daten in DB timeline_v2 -ok
2. Schritt: Anpassen der DB-Struktur -ok
3. Schritt: Dump der neuen Struktur samt Daten -ok
4. Probleme in den Daten ermitteln -ok
