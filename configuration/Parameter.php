<?php
    class Parameter {
        private $name;
        private $val;
        private $type;
        
        function __construct() {
        
        }
        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getVal() {
            return $this->val;
        }

        public function setVal($val) {
            $this->val = $val;
        }

        public function getType() {
            return $this->type;
        }

        public function setType($type) {
            $this->type = $type;
        }

        

}
?>
