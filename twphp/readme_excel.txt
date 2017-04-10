Die Ordnerstruktur muss so übernommen werden. Der Name des übergeordneten Verzeichnisses
ist egal. Alle Pfadangaben in den (.php) Dateien sind relativ zu ihrem eigenen und es existieren 
zahlreiche Abhängigkeiten der Dateien untereinander. 

Achtung: Evtl. müsen in ein paar Dateien die Namen der Datenbank angepasst werden!

Achtung: Die Excel-Tabelle muss im Format UTF-8 gespeichert werden. Dazu "Speichern unter", links neben 
"Speichern" befindet sich eine DropDown-Liste "Tools" dort die Weboptionen wählen und auf der Karteikarte
"Codierung" in "Datei speichern als:" die Einstellung "Unicode (UTF-8)" auswählen. Dann "OK" und "Speichern".

* DatenerfassungTest.xlsx
Testdaten zum Einlesen mit php/adm_einlesen_excel.php.
Alle Zellen der Tabellenblätter müssen als "Text" formatiert sein.
Sollten folgende Einträge fehlen, werden sie durch Standardwerte ergänzt:
place -> ""
enddate -> 7777-12-31
date_display -> day
weight -> 42
keywords -> ""
link -> Wikipedia-Suchseite
description -> ""

Fehlen folgende Einträge, wird der Datensatz verworfen und das Ergebnis in twlog/error_msg.txt
protokolliert:
title, startdate, category

Ist ein Datensatz bereits in der DB enthalten (gleicher Titel), dann werden die alten
Daten mit den neuen überschrieben. Weitere Spalten, die nicht in der Excel-Datei sind
werden nicht geändert (z.B. die Klickzahlen).

Alle Kategorien, die in der Spalte category in der Exceldatei stehen, müssen bereits 
in der Tabelle category der DB timeline enthalten sein. Andernfalls wird der Datensatz verworfen.

* timeline_v2_Struktur_2017_01_20.sql
Enthält nur die Strukturinformation der neuen DB (Tabelle daten und category).

* timeline_v2_2017_01_31.sql.gz
Enthält neben der Struktur auch die aktuellen Daten der DB timeline.