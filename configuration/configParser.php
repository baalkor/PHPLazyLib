<?

   
   define("INI_EXCEPTION_FILE_ERROR", "+FILE_ACCESS_ERROR");    //Error message raised when the class can't access the file sysproddb.cfg
   define("INI_EXCEPTION_FILE_ERROR_EC", 10);                       //Error code assosicated 
   define("INI_EXCEPTION_FILE_CONTENT_ERROR", "+FILE_ERROR_UNREADABLE");  //The file is malformated
   define("INI_EXCEPTION_FILE_CONTENT_ERROR_EC", 20);                          //Er code
   define("INI_EXCEPTION_KEY_NOT_FOUND", "+KEY_NOT_FOUND");         //Error message raised when the key don't exist in the file
   define("INI_EXCEPTION_KEY_NOT_FOUND_EC", 15);                    //Error code assisciated
   
   require_once "init.inc.php";
   require_once "debug/debug.php"; 
  /**
   *ConfigParser:
   *-------------
   *
   *Parse the configuration file and store it as memory hash table
   *The class does not allow the write since all changes should be 
   *made by the local administrator
   ********************************************************************
   *Exceptions definition
   * version 1.2
   * @todo Need to troubleshoot the redeclaration
   */   
   class CONFIGPARSER
   {
      
      
      
      
      private $debug = false; 
      private $filePath = "";   
      private $config;  //The configutation file's hanle
      

      /**
       * Set the ini ffile parsed to config handle
       * @throws Exception
       */
      private function setConfigFile()
      {
      if ( ($this->config = parse_ini_file($this->filePath, True) ) === FALSE)
	  {
		  
          throw new Exception(INI_EXCEPTION_FILE_CONTENT_ERROR, __LINE__) ;      // Read the file and store the result in config      
      }
	  }
      /**
       * 
       * @param string $config Path to an file or NULL for CFG_FILE
       * @throws Exception
       */
      public function __construct($config=NULL)
      { 
        if ( $config !== NULL)
        {
            $pathToConfig = $config;
        }
	else
            $pathToConfig = CFG_FILE;

        if ( $pathToConfig == "" or  ! is_readable( $pathToConfig ))            ///Test if the file is correct
          throw new Exception(INI_EXCEPTION_FILE_ERROR."[".$pathToConfig."]",$code=INI_EXCEPTION_FILE_ERROR_EC);
         
        $this->filePath = $pathToConfig;
        $this->setConfigFile();      //Parse the file and raise or raise an exception
                                     // if the parse_ini_file return False (malformated)
        $this->debug =  False;
        
        
        
        
        
        
      }  
      
	  /**
           * Backup the current ini file and use a new one (use setConfigFile before)
           * @return boolean
           * @throws Exception
           */
	  public function replace()
	  {
		
		if ( ! copy($this->filePath, $this->filePath.'-'.date("dd-mm-YY") )) 
		{
			
			
			throw new Exception("ERROR_SAVING_FILE");
		}

		
		$content = parse_ini_file($this->filePath, true);

		
		unlink($this->filePath);
		$file = fopen($this->filePath, 'w');
		fwrite($file, "; Updated on ".date("dd-mm-YY")."\n");
		foreach ( array_keys($content) as $index=>$category )
		{
			
			fwrite($file, "[$category]\n");
			foreach ( $content[$category] as $param=>$paramVal )
				fwrite($file, "$param='".$this->config[$param]."'\n");
		
		}
		fclose($file);

		return true;
		
		
		
	  }
	  
      /**
       * modify an existing key to a new value
       * @param string $category
       * @param string $key
       * @param mixed $newVal
       */
      public function set($category, $key, $newVal)
      {
          if (  isset($this->config[$category]))
              if (isset ($this->config[$category][$key]))
              {
                return $this->config[$category][$key] = $newVal;           
              }
         return false;
      }
      /**
       * Get a value form the selected section
       * @params   string/key stored in the ini file
       * @return boolean
       */
      public function get($section, $key=null)
      {
          if ( $key === null )
          {
              $key = $section;
              $section = "ENV"; //! Legagy 
          }
        $section = strtoupper($section);
        if ( isset($this->config[$section][$key]) )
        {
          if ( $this->config[$section][$key] == "true" || $this->config[$section][$key] == "false"  )
              return  $this->config[$section][$key] == "true" ? True : False;
          else
            return $this->config[$section][$key];
        }
        else
         throw new Exception ("Configuration $key not found", __LINE__);
          
      }
      /**
       * Get a value from a file
       * (can be called staticly)
       * @param type $section
       * @param type $key
       * @return boolean
       */
      public static function  sget($section, $key)
      {
          
         $config = parse_ini_file(CFG_FILE, true);
          

         if ( isset($config[$section][$key]) )
         {
            
           if ( strtolower($config[$section][$key]) == "true" || strtolower($config[$section][$key]) == "false"  )
               return  $config[$section][$key] == "true" ? True : False;
           else
             return $config[$section][$key];
         }
         else
           return false;          
          
      }

      /**
       * Get all key in a section
       * This should be removed 
       * @param string $section
       * @return array
       */
      public function getSectionKeys($section)
      {
          return $this->config[$section];
      }
      /**
       * Get all sections present in memory 
       * This should be removed 
       * @param string $section
       * @return array
       */      
      public function getSections($section)
      {
          $out = array();
          foreach ( $this->config as $key=>$value)
          {
              $sec = explode("#",$key);
              if (count($sec)==2)
              {
                $id      = $sec[1];
                $secini = $sec[0];
                if ( $secini == $section )
                    $out[$id] = $this->getSectionKeys($key);
               }
               else
               {
                   if ( $section == $key)
                       $out[$section] = $this->getSectionKeys($key);
               }
               
          }
          return $out;
      }
      
  /**
   * reload_config re-read the configuration file
   */
      public function reload_config()
      {
        unset($this->config);
        $this->setConfigFile();  
      }

      /**
       * Return the config file into an array
       * @return array
       */
      public function getConfig()
	  {
		return parse_ini_file($this->filePath, true);
	  }
      /**
       * Return a string copy of the configuration file
       * @return type
       */
      public function dumpConfig()
      {
        
	return file_get_contents($this->filePath);
      }

   }   
   
   
   class StaticConfigParser 
   {
      public static function  sget($section, $key, $filename=CFG_FILE)
      {
          
         $config = parse_ini_file($filename, TRUE);
          
         if ( ! $config )
         {
             throw new InvalidArgumentException($filename." cannot be parsed"); 
         }
         if ( isset($config[$section][$key]) )
         {
           $value = $config[$section][$key];
           
           
           if (($var = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)))
           {
               return $var;
           }
           if (($var = filter_var($value, FILTER_VALIDATE_INT)))
           {
               return $var;
           }           
           if (($var = filter_var($value, FILTER_VALIDATE_FLOAT)))
           {
               return $var;
           }
           

           if ( $config[$section][$key] == "" )
            return "ini_undefined";
           else
             return $config[$section][$key];           
  
         }
         else
           throw new InvalidArgumentException("parameter [$section : $key] doesn't exist"); 
      }
           
   }
  
?>