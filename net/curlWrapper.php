<?php

require_once "init.inc.php";
if ( ! defined("MAX_MEMORY_ALLOWED"))     define("MAX_MEMORY_ALLOWED"  , "512M"  );
if ( ! defined("PHP_MAX_EXEC_TIME"))   define("PHP_MAX_EXEC_TIME", 60*10 );

class curlWrapper
{
    private $old_memlimit;
    private $oldtimeout;
    
    private $_chOpts = array (
        
	CURLOPT_VERBOSE        => False,
	CURLOPT_HTTPAUTH       => CURLAUTH_NTLM,
	CURLOPT_RETURNTRANSFER => TRUE,
	CURLOPT_CUSTOMREQUEST  => "GET",
        CURLOPT_FOLLOWLOCATION => TRUE	

    );
    public function __construct($options=NULL) {
        $this->_ch = curl_init();
        if ( $options !== NULL)
            $this->_chOpts = $options;
        else
            $this->set_options($this->_chOpts);
        
        
        
        $this->old_memlimit = ini_get("memory_limit");
        $this->oldtimeout   = ini_get("max_execution_time");
        ini_set("memory_limit",MAX_MEMORY_ALLOWED  );
        
        
        

 
        
        
    }
    
    public function set_option($option, $value)
    {
        $this->_chOpts[$option] = $value;
        curl_setopt($this->_ch, $option, $value);         
    }


    public function set_options($options) 
    { 
        $this->_chOpts = $options;
        curl_setopt_array($this->_ch, $this->_chOpts );     
    }
    
    
     public function getinfo($centry)
     {
         return  curl_getinfo($this->_ch, $centry);
     }

     public function get_options($key)
     {
         return $this->__chOpts[$key];
     }
     

    public function exec($url="") { 

        if ( $url != "")
            $this->set_option(CURLOPT_URL, $url);                 
        ini_set("max_execution_time",PHP_MAX_EXEC_TIME  );
        $data = curl_exec($this->_ch);
        
        if ( $data === false)
        {
            qlog("curl error", curl_error($this->_ch));
        }
     
        return $data;
    
                   
    } 
    


    public function __destruct() {
        if ( gettype($this->_ch) == 'resource')
        {
            curl_close($this->_ch);
        }
        
          ini_set("memory_limit",$this->old_memlimit  );
          ini_set("max_execution_time",$this->oldtimeout  );

    }
}


?>
