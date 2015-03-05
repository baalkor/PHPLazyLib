<?php
define("PROCESS_SECTIONS", true);

require_once 'ConfigurationFile.php';

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
        foreach ( $this->getConfiguration() as $section => $sectionValues)
        {
           
        }
    }
}
?>
