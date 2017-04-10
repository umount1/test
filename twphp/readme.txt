* coding.php
Funktionen um die Kodierung in UTF-8 zu ändern und die Behandlung von Sonderzeichen
wie " ' \ für das Eintragen in die DB bzw. eine JSON-Datei.

* adm_update_geokoordinaten.php
Bestimmt die Geokoordinaten für 6500 DB-Einträge. Es nutzt dabei die Server von 
Google (2x2500 Datensätze) und OSM (1500 Datensätze). Die Begrenzung erfolgt durch 
die Server-Betreiber.

* adm_auslesen_excel.php
Diese Datei wird im Browser aufgerufen, um eine Excel-Datei in die DB zu importieren.
Sie bietet in der Oberfläche eine Auswahlbox für die DB und eine Auswahlschaltfläche 
für die Excel-Datei an. Nach dem Import werden die Daten am Bildschirm angezeigt. 
Alle Meldungen befinden sich in der Datei ../log/error_msg.txt und alle DB-Operationen
werden in einer SQL-Datei (im Ordner ../log/) protokolliert.

* auslesenExcel.php
Dieses Skript ist auf PHPExcel-1.8 im selben Ordner angewiesen. Außerdem werden 
weitere php Dateien im selben Ordner benötigt (s. require_once Anweisungen in der Datei).
In der Datei muss die Datenbank und die Excel-Datei angegeben werden. Anschließend
werden die Excel-Daten importiert. Sollte ein Eintrag mit gleichlautendem Titel bereits
existieren, wird er mit den neuen Daten überschrieben. Klickzahlen und Bewertungen
bleiben aber ggf. erhalten.

* icon_list.php
Die Datei enthält die Klasse iconList. Mit ihr können alle Dateinamen eines 
Verzeichnises in einem Array gespeichert und als JSON-Datei gespeichert werden.

* update_geokoordinaten.php
Das Programm durchsucht die DB nach Einträgen, bei denen die Geokoordinaten 
noch 0 und der Eintrag place_checked ebenfalls 0 ist. Für diese Datensätze, bzw. 
deren place werden die Geokoordinaten bei Google und bei Openstreetmap ermittelt
und in die DB eingetragen.

* check_date.php
is_dateCorrect($year, $month, $day) überprüft, ob das Datum zulässig ist.

* correct_date.php
correctDate($db) wird von timeline_v2_correct_date.php verwendet.

* db_operation.php
dbSelect($db, $str)
dbInsert($db, $str)
dbUpdate($db, $str)
dbOpen($db)
implementieren die entsprechenden DB-Operationen, wobei eine 
Fehlerbehandlung durchgeführt wird.

* gregorian2num.php
gregorian2Num($year, $month, $day) berechnet aus einem Datum eine 
fortlaufende Zahl. Für Daten vor dem -4714/11/24 sind die Zahlen 
negativ. 

* msg_function.php
report($file, $msg) speichert die Nachricht in der Datei (anhängen).
error_function() wird über die Anweisung trigger_error($msg, E_USER_WARNING)
aufgerufen und erzeugt einen Eintrag in der Datei error_msg.txt

* timeline_v2_correct_date.php
Sucht in der DB timeline_v2 alle Einträge mit sortstart
oder sortend = 0 und versucht sie zu korrigieren. Einträge mit
unzulässigem Datum werden in error_msg.txt protokolliert.

* twdate.php
Klasse twdate zur Datumsspeicherung mit Überprüfung