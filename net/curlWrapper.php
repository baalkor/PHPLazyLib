<?php
class curlWrapper
{

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
        
        $data = curl_exec($this->_ch);
        
        if ( $data === false)
        {
            throw new RuntimeException("curl error". curl_error($this->_ch));
        }
     
        return $data;
    
                   
    } 
    


    public function __destruct() {
        if ( gettype($this->_ch) == 'resource')
        {
            curl_close($this->_ch);
        }

    }
}


?>
