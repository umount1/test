* coding.php
Funktionen um die Kodierung in UTF-8 zu �ndern und die Behandlung von Sonderzeichen
wie " ' \ f�r das Eintragen in die DB bzw. eine JSON-Datei.

* adm_update_geokoordinaten.php
Bestimmt die Geokoordinaten f�r 6500 DB-Eintr�ge. Es nutzt dabei die Server von 
Google (2x2500 Datens�tze) und OSM (1500 Datens�tze). Die Begrenzung erfolgt durch 
die Server-Betreiber.

* adm_auslesen_excel.php
Diese Datei wird im Browser aufgerufen, um eine Excel-Datei in die DB zu importieren.
Sie bietet in der Oberfl�che eine Auswahlbox f�r die DB und eine Auswahlschaltfl�che 
f�r die Excel-Datei an. Nach dem Import werden die Daten am Bildschirm angezeigt. 
Alle Meldungen befinden sich in der Datei ../log/error_msg.txt und alle DB-Operationen
werden in einer SQL-Datei (im Ordner ../log/) protokolliert.

* auslesenExcel.php
Dieses Skript ist auf PHPExcel-1.8 im selben Ordner angewiesen. Au�erdem werden 
weitere php Dateien im selben Ordner ben�tigt (s. require_once Anweisungen in der Datei).
In der Datei muss die Datenbank und die Excel-Datei angegeben werden. Anschlie�end
werden die Excel-Daten importiert. Sollte ein Eintrag mit gleichlautendem Titel bereits
existieren, wird er mit den neuen Daten �berschrieben. Klickzahlen und Bewertungen
bleiben aber ggf. erhalten.

* icon_list.php
Die Datei enth�lt die Klasse iconList. Mit ihr k�nnen alle Dateinamen eines 
Verzeichnises in einem Array gespeichert und als JSON-Datei gespeichert werden.

* update_geokoordinaten.php
Das Programm durchsucht die DB nach Eintr�gen, bei denen die Geokoordinaten 
noch 0 und der Eintrag place_checked ebenfalls 0 ist. F�r diese Datens�tze, bzw. 
deren place werden die Geokoordinaten bei Google und bei Openstreetmap ermittelt
und in die DB eingetragen.

* check_date.php
is_dateCorrect($year, $month, $day) �berpr�ft, ob das Datum zul�ssig ist.

* correct_date.php
correctDate($db) wird von timeline_v2_correct_date.php verwendet.

* db_operation.php
dbSelect($db, $str)
dbInsert($db, $str)
dbUpdate($db, $str)
dbOpen($db)
implementieren die entsprechenden DB-Operationen, wobei eine 
Fehlerbehandlung durchgef�hrt wird.

* gregorian2num.php
gregorian2Num($year, $month, $day) berechnet aus einem Datum eine 
fortlaufende Zahl. F�r Daten vor dem -4714/11/24 sind die Zahlen 
negativ. 

* msg_function.php
report($file, $msg) speichert die Nachricht in der Datei (anh�ngen).
error_function() wird �ber die Anweisung trigger_error($msg, E_USER_WARNING)
aufgerufen und erzeugt einen Eintrag in der Datei error_msg.txt

* timeline_v2_correct_date.php
Sucht in der DB timeline_v2 alle Eintr�ge mit sortstart
oder sortend = 0 und versucht sie zu korrigieren. Eintr�ge mit
unzul�ssigem Datum werden in error_msg.txt protokolliert.

* twdate.php
Klasse twdate zur Datumsspeicherung mit �berpr�fung