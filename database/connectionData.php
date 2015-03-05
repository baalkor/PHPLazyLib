<?php
    
    define("DSN_FMT", "{driver}://{username}:{password}@{hosts}:{port}/{database}");
    
    /**
     * DBConnectionData allow to store connection data to open connection to 
     * various DB.
     * 
     */
    class DBConnectionData
    {
        private $supportedDrivers = array( "mysql", "mariadb" );
        private $host     ;
        private $username ;
        private $password ;
        private $database ;
        private $port     ;
        private $driver   ;
        
        private $charset = 'utff-8';
        private $pdoOptions;
        public function getCharset() {
            return $this->charset;
        }

        public function setCharset($charset) {
            $this->charset = $charset;
        }

        public function getPdoOptions() {
            return $this->pdoOptions;
        }

        public function setPdoOptions($pdoOptions) {
            $this->pdoOptions = $pdoOptions;
        }

                
        public function __construct($host="",$username="",$password="",$database="",$port=3306, $dbtype="mysql")
        {
            
            if ( $host == ""  )
            {
                $this->setHost(StaticConfigParser::sget("DATABASE", "host") );
                $this->setUsername(  StaticConfigParser::sget("DATABASE", "user") );
                $this->setPassword(StaticConfigParser::sget("DATABASE", "password") );
                $this->setDatabase( StaticConfigParser::sget("DATABASE", "dbname") );
                $this->setPort(StaticConfigParser::sget("DATABASE", "port") ) ;          
                $this->setDriver( StaticConfigParser::sget("DATABASE", "type") );
                
            }
            else
            {
                $this->setHost($host);
                $this->setUsername(  $username );
                $this->setPassword(  $password );
                $this->setDatabase( $database ); 
                $this->setPort( $port );
                $this->setDriver( $dbtype );
                
            }
            
            
        }
        
       public function getHost()     { return $this->host;     }
       public function getUsername() { return $this->username; }
       public function getPassword() { return $this->password; }
       public function getDatabase() { return $this->database; }
       public function getPort()     { return $this->port;     }
       public function getDriver()     { return $this->driver;     }
       
       public function setDriver($val)
       {
           if ( in_array(strtolower($val), $this->supportedDrivers) )
           {
               $this->driver = strtolower($val);
           }
           else
           {
               throw new InvalidArgumentException("$val is not supported, please select : ".implode(",", $this->supportedDrivers));
           }
       }       
       public function setPort($val)
       {
           if ( is_int($val) )
           {
               $this->port = $val;
           }
           else
           {
               throw new InvalidArgumentException();
           }
       }         
       public function setPassword($val)
       {
           if ( $val !== "" )
           {
               $this->password = $val;
           }
           else
           {
               throw new InvalidArgumentException();
           }
       }       
       public function setDatabase($val)
       {
           if ( $val !== "" )
           {
               $this->database = $val;
           }
           else
           {
               throw new InvalidArgumentException();
           }
       }        
       public function setHost($val)
       {
           if ( $val !== "" )
           {
               $this->host = $val;
           }
           else
           {
               throw new InvalidArgumentException();
           }
       }       
       public function setUsername($val)
       {
           if ( $val !== "" )
           {
               $this->username = $val;
           }
           else
           {
               throw new InvalidArgumentException();
           }
       } 
       public function getDSN() { 
           $searches = array (
               "{driver}",
               "{username}",
               "{password}",
               "{hosts}",
               "{port}",
               "{database}"
           );
           
           $replacement = array ( 
                $this->driver,
                $this->username,
                $this->password,
                $this->host,
                $this->port,
                $this->database
           );
           return str_replace($searches, $replacement, DSN_FMT);
       } 
       
        
    }
?>
