<?php

abstract class Model extends DatabaseWrapper 
{
    private $table;
    
    protected $current_key = 0;
    protected $fields_info;
    protected $data;
    
    

    protected function getTable() {
        return $this->table;
    }
    public function setTable($table) {
        $this->table = $table;
    }


 
    
    public function __construct( $where=null, $join=null) {
        parent::__construct(new DBConnectionData());
        $this->fields_info = new FieldsInformations();
        $this->map();
     
                
    }    
    
    public function add($record)
    {
        $this->insert($this->getTable(), $record);
    }
    
    public function remove($record)
    {
        $this->remove($record);
    }
    
  

    private function map()
    {
       $this->setTable(get_class($this));
       foreach ( $this->describe($this->getTable()) as $row)
       {
           $this->fields_info->addFieldInfo(new TableField(
                   $row["Field"],
                   $row["Type"],
                   $row["Null"] == "YES",
                   $row["Key"] == "PRI",
                   $row["Default"],
                   $row["Extra"]
                   )
                );
        }
       
    }
    
  
}
?>
