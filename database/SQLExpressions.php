<?php

    abstract class  _SQLExpression { 
        protected $arrayOut;
        public function __construct()
        {
            $this->asrryOut = array();
        }
        public function toMedooArray() { return $this->arrayOut; } 
    }
    class _SQLWhere extends _SQLExpression { 
        public function add($column, $operator, $value) { $this->arrayOut[$column."[".$operator."]"] = $value; }
    }
    class _SQLColumnSet extends _SQLExpression  { 
        public function add($column) { $this->arrayOut[] = $column; }
    }
    
    class _SQLJoin extends _SQLExpression { 
        public function add($expression) { $this->arrayOut[] = $expression; }
    }
    
    class _SQLSelect extends _SQLExpression {  
        
        private $destTable;
        private $columns;
        private $join;
        private  $where;
    }
    
?>
