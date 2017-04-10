<?php
/*!
 \file      icon_list.php
 \author    J. Schönbohm
 \par Erstellt am:
            2017-02-06

 \version   1.0.0 &nbsp;&nbsp; <b>Datum:</b> &nbsp; 2017-02-06
 \par Versionshistorie:
             1.0.0 &nbsp; Datum: &nbsp; 2017-02-06 <br>
                <em>Erstes Release (J. Schönbohm)</em><br>

 \brief	Klasse uzm Erstellen einer icon-Liste. 
 \details Anwendungsbeispiel
			// Erstellen der Datei
			$path = "\\Software\\xampp\\htdocs\\TimeWiki\\timeglider\\icons";
			$content = json_encode(new iconList($path), JSON_PRETTY_PRINT);
			$datei = fopen("icons.json","w");
			fwrite($datei, $content);
			fclose($datei);
			// Einlesen
			$handle = fopen("icons.json", "r");
			$content = fread($handle, filesize("icons.json"));
			fclose($handle);

$var = json_decode($content);
print_r($var->path);

 \remark Abhängigkeiten<br>
 \li \link msg_function.php\endlink<br>
	
*/
	require_once("msg_function.php"); // report($file, $msg);
	
/*!
	\class	iconList
	\brief	Klasse zum Speichern einer Liste von Dateien.
	\detail	Die Klasse speichert alle Dateinamen innerhalb eines Verzeichnisses
			und ermöglich die Ausgabe im JSON-Format.
*/
	class iconList implements JsonSerializable {
  
		// Wandelt das Objekt in das JSON-Format um und
		// speichert es in der Datei icons.json
		public function jsonSerialize() {
			if(FALSE !== $this->scanDirectory())
				return $this;
			else{
				report("error.txt", "Datei konnte nicht erstellt werden!\n");
				return FALSE;
			}
		}
  
		/*!
			\fn	__construct($path="")
			\brief	Konstruktor
			\param	path	Verzeichnispfad zu den icon-Dateien
		*/		
		function __construct($path="")
		{
			$this->path = $path;
			$this->icons = array();
		}
		
		// Sucht alle Dateinamen im $path
		// und filtert sie.
		protected function scanDirectory()
		{
			$icons = scandir($this->path);			
			if(FALSE === $icons) return FALSE;
			else
			{
				$this->filter($icons);
			}
		}
		
		// nur zum Debuggen
		protected function printArray($z)
		{
			foreach($z as $a) echo $a."<br>";
		}
		
		// Sucht im Array $z alle Einträge, die mit '.' oder
		// '_' beginnen und übernimmt sie NICHT in die Dateiliste.
		protected function filter($z){
			foreach($z as $a)
			{
				if(strpos($a,".") !== 0 && strpos($a,"_") !== 0 )
					array_push($this->icons, $a);
			}	
		
		}
		public $path;	//!< Pfad zu den Dateien
		public $icons;	//!< Array mit den Dateinamen

	}
?>