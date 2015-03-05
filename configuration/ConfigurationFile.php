<?php
    /*
     * ConfigurationParser is a small class that allow the read/write of
     * parameters like old ini file
     */
     abstract class ConfigurationFile implements IConfigurationFile {
         protected $configObject;
         protected $fileHandle;
         private $readOnly;
         private $fileName;
         protected $configuration;
         
         
         protected function read() { }


         public function open($file) {
             
             if (file_exists($file))
             {
                 $this->readOnly = ! is_writable($file);
                 $this->fileName = pathinfo ($file, PATHINFO_FILENAME);
                 
                 $this->readOnly ?  $this->file_handle  = fopen ($file, 'r') :  $this->file_handle  = fopen ($file, 'rw');
                 
             }
             else
             {
                 throw new Exception("$file is not accessible");
             }
         }
    
         public function set(Section $section, Parameter $param) { }
         public function get(Section $section, Parameter $param) { }
         public function addSection(Section $section) { } 
         public function removeSection(Section $section){ } 
         public function writeConfig();
         public function close() { 
                if (is_resource($this->fileHandle))
                    fclose ($this->fileHandle);
         }
         
         public function getSection(Section $section) {}
         
         public function __construct($filePath) {
             $this->open($filePath);
         }


    }
    


?>
