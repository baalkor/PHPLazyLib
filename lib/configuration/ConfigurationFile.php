<?php
    /*
     * ConfigurationParser is a small class that allow the read/write of
     * parameters like old ini file
     */
     require_once 'IConfigurationFile.php';
     
     define("BOOLEAN_VALUES_TRUE", "yes on enabled true active");
     define("BOOLEAN_VALUES_FALSE", "no off disabled false inactive");
     
     abstract class ConfigurationFile implements IConfigurationFile {
         

         private $fileHandle;
         private $readOnly;
         private $fileName;
         private $filePath;
         
         protected $configuration;
         
         
         protected function parseData($data)
         {
            foreach ( $data as $section=>$sectionRecords)
            {
                foreach ($sectionRecords as $key=>$value)
                {
                    if (strpos(BOOLEAN_VALUES_FALSE,strtolower($value)) !== FALSE ) {
                        $typedValue = false;
                    }
                    elseif (strpos(BOOLEAN_VALUES_TRUE,strtolower($value)) !== FALSE) {
                        $typedValue = true;
                    }
                    elseif (is_null($value)) {
                        $typedValue = "";
                    }
                    elseif (is_numeric($value)) {
                        if (! ctype_digit($value))
                            $typedValue = floatval ($value);
                        else
                            $typedValue = intval ($value);
                    }
                    else
                        $typedValue = $value;
                    
                    if ( isset($this->configuration[$section][$key]))
                    {
                        throw new RuntimeException("Duplicate key in the same metasection");
                    }
                    else
                    {
                        $this->configuration[$section][$key] = $typedValue;
                    }
                    
                    
                }
            }
            
         }

         protected function readContent()
         {
             return file_get_contents($this->filePath);
         }
         public function open($file) {
             
             if (file_exists($file))
             {
                 $this->readOnly = ! is_writable($file);
                 $this->fileName = pathinfo ($file, PATHINFO_FILENAME);
                 $this->filePath = $file;
                 $this->readOnly ?  $this->file_handle  = fopen ($file, 'r') :  $this->file_handle  = fopen ($file, 'rw');
                 
             }
             else
             {
                 throw new Exception("$file is not accessible");
             }
         }
    
         public function set($section, $key, $param) { 
             
              if ( ! $this->sectionExist($section))
              {
                  $this->addSection($section);
              }
              
              $this->configuration[$section][$key]= strval($param);
             
             
                 
         }
         
         private function sectionExist($section) { return isset($this->configuration[$section]); }
         private function keyExist($section,$key) { 
             if ($this->sectionExist($section))
                return isset($this->configuration[$section][$key]); 
             else
                 return FALSE;
         }        
   
         public function get( $section,  $key ) { 
             if ( $this->keyExist($section, $key))
                return $this->configuration[$section][$key]; 
             else
                 throw new RuntimeException("$section:$key does not exist");
             
         }
         public function addSection( $section) { 
             if ( isset($this->configuration[$section]))
             {
                 throw new RuntimeException("Section $section already exist");
             }
             else
             {
                 $this->configuration[$section] = array();
             }
         } 
         public function removeSection( $section){ 
              if (  $this->sectionExist($section) ) {
                  unset($this->configuration[$section]);
              }
         } 
         function writeConfig($overwrite=true) {
             if ( ! $this->readOnly )
             {
                if ( ! $overwrite )
                    return $this->fileName."-".time();
                else
                    return $this->fileName; 
             }
             else
                 throw new RuntimeException("File is read only");
         }
         public function close() { 
                if (is_resource($this->fileHandle))
                    fclose ($this->fileHandle);
         }
         
         public function getSection($section) {
             return $this->configuration[$section];
         }
         
         public function getConfiguration() { return $this->configuration; }
         

         public function __construct($filePath) {
             $this->open($filePath);
         }


    }
?>
