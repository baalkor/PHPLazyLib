<?php

abstract class DatabaseWrapper extends medoo
{
    public function __construct(DBConnectionData $connectInfo) {
        parent::__construct([
                    'database_type' => $connectInfo->getDriver(),
                    'database_name' => $connectInfo->getDatabase(),
                    'server'        => $connectInfo->getHost(),
                    'username'      => $connectInfo->getUsername(),
                    'password'      => $connectInfo->getPassword(),
                    'charset'       => $connectInfo->getCharset()
                ]);
    }
    
    protected function describe($table)
    {
       return  $this->query("DESCRIBE ".$table);
    }
    
    
   
}

?>
