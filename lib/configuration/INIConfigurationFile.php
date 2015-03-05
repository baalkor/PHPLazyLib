<?php
define("PROCESS_SECTIONS", true);

require_once 'ConfigurationFile.php';
/*
* ConfigurationFile 
*
*/
class INIConfigurationFile extends ConfigurationFile {

    function open($file) {
        parent::open($file);
        
        $this->parseData( parse_ini_string( $this->readContent(), PROCESS_SECTIONS ));
        
   
    }
	
    function __construct($file) {
        parent::__construct($file);
        
    }
    
    function writeConfig($overwrite=true) {

        $file = parent::writeConfig($overwrite);
        $lhdl = fopen($file,"w");
        foreach ( $this->getConfiguration() as $section => $sectionValues)
        {
            fwrite($lhdl,"[$section]\n");
            foreach ( $sectionValues as $key=>$name)
                fwrite($lhdl,"$key=\"$name\"\n");
            
        }
        fclose($lhdl);
    }
}
?>
