<?php
    class TableField
    {
        private $name;
        private $type;
        private $isNull;
        private $isKey;
        private $default;
        private $extra;
        
        
        
        function __construct($name, $type, $isNull, $isKey, $default, $extra) {
            $this->name = $name;
            $this->type = $type;
            $this->isNull = $isNull;
            $this->isKey = $isKey;
            $this->default = $default;
            $this->extra = $extra;
        }

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getType() {
            return $this->type;
        }

        public function setType($type) {
            $this->type = $type;
        }

        public function getIsNull() {
            return $this->isNull;
        }

        public function setIsNull($isNull) {
            $this->isNull = $isNull;
        }

        public function getIsKey() {
            return $this->isKey;
        }

        public function setIsKey($isKey) {
            $this->isKey = $isKey;
        }

        public function getDefault() {
            return $this->default;
        }

        public function setDefault($default) {
            $this->default = $default;
        }

        public function getExtra() {
            return $this->extra;
        }

        public function setExtra($extra) {
            $this->extra = $extra;
        }


      
            
    }
    class FieldsInformations
    {
       private $fields = [];
       
       
       
       public function addFieldInfo(TableField $field) { $this->fields[] = $field; }
       public function getFieldsInfo() { return $this->fields; }
       public function getColumns() { 
           $columns = array();
           foreach ( $this->fields as $field)
               $columns[] = $field->getName();
           return $columns;
       }
       public function getPrimaryKeys()
       {
           
           foreach ( $this->fields as $field)
           {
               if ( $field->getIsKey() )
                yield ($field->getName());
           
           }
           
       }
       
       public function __construct() {
           
       }
    }
?>
