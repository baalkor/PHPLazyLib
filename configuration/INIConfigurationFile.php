<?php
define("PROCESS_SECTIONS", true);
class INIConfigurationFile extends ConfigurationFile {

    function open($file) {
        parent::open($file);
        $this->configObject = parse_ini_string(
                file_get_contents($this->fileHandle),
                PROCESS_SECTIONS
        );
    }
    function __construct($file) {
        parent::__construct($file);
        
    }
    
    public function addSection(\Section $section) {
        parent::addSection($section);
    }

 

    public function get(Section $section, Parameter $param) {
        parent::get($section, $param);
    }

    public function getSection(\Section $section) {
        parent::getSection($section);
    }

    protected function read() {
        parent::read();
    }

    public function removeSection(\Section $section) {
        parent::removeSection($section);
    }

    public function set(\Section $section, \Parameter $param) {
        parent::set($section, $param);
    }

    public function writeConfig() {
        parent::writeConfig();
    }


    
    

}
?>
