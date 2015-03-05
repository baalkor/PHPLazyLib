<?php
    require_once "static.php";
    require_once "HTML/Table.php";
    require_once "html-tag-generators.php";
    
    define("MYSQLI_BLOB_FLAG_", 144); //144 seems more accurate..
    /**
     * Class ResultsSet
     * @author david clignez
     * @version 1.0
     * 
     */
    class ResultsSet
    {
        private $cursor = 0;
        private $_data = array();
        private $_columns = array();
        private $_columnsMetadata = array();
        
        public function __toString() {
            return implode("\n", $this->_data);
        }
        
        public function buildForm($formParameters)
        {
            $form = form();
            
        }
        
        public function toJSON2()
        {
         
            return json_encode(json_encode($this->values(),JSON_FORCE_OBJECT  ) );           
        }
         public function toJSON()
        {
            $json = "[";
            foreach($this->assoc_values() as $value)
            {
              // Added a condition to filter results
                 
              $json .= json_encode($value, JSON_FORCE_OBJECT).",";
            }
            $json = rtrim($json, ",");
            $json .= "]";
            
            if (json_decode($json) === false)
                throw new Exception("Error while encoding data", __LINE__);
            
            
            return   $json;
        }
        /**
         * 
         * @param mysqli_result $result
         */
        public function set_columns(mysqli_result $result)
        {
            $data = $result->fetch_fields();
            unset ( $this->_columns);
            foreach ( $data as $field=>$dataF)
            {
                $this->_columns[] = $dataF->name;
            }
                    
        }
        /**
         * 
         * @return string HTML Select with resuint in it
         */
        public function toHtmlOptions($value_field=null,$preselid=NULL)
        {
            if ( $preselid !== NULL )
                $line = "<option value='{value}' {selected}>{text}</option>";
            else
                $line = "<option value='{value}'>{text}</option>";
            $j = "";
            foreach ( $this->_data as $index=>$value)
            {
                
                if (is_array($value))
                {
                    $index = $value[0];
                }
                if ( $value_field === NULL )
                    $out = str_replace("{text}", $value, str_replace("{value}", $value, $line));
                else
                {
                    if ( ($position = array_search($value_field, $this->_columns)) === NULL)
                    {
                        
                        throw new InvalidArgumentException;
                    }
                    else
                    {
                        $val = $value;
                        unset($val[$position]);
                        $val = implode(" ", $val);
                         
                        
                        if ( $preselid == $value[$position] )
                        {
                            $out = str_replace("{selected}", "selected",$line);
                        }
                        else
                            $out = str_replace("{selected}", "",$line);
                        
                        $out = str_replace("{text}", $val, str_replace("{value}", $value[$position], $out));
                    }
                }
                
                $j .= $out;
            }
            
           
            
            return $j;
        }
        
        /**
         * Formtat array( "col" => "item({<field>}))
         * @param type $final_fiels
         * @return type
         * @throws InvalidArgumentException
         */
        public function toHtmlTable($final_fiels=NULL)
        {
            $extraFields = false;
            $results_data = $this->_data;
            $id = uniqid();
            $results_col  = $this->columns();
            $skel = '<table id="'.$id.'" class="dataTable display compact cell-border" cellspacing="0">
                        <thead>
                            <tr>
                                {header}
                            </tr>
                        </thead>
                        <tbody>
                            {data}
                        </tbody>
                    </table>
                    ';
            
            if ( !is_null($final_fiels))
            {
                if ( !is_assoc($final_fiels))
                {
                    qlog("ERROR, extra fiels is not an correct array!");
                    throw new InvalidArgumentException();
                    
                }
                else
                {
                    $results_col = array_merge($results_col,  array_keys($final_fiels)); 
                    $extraFields = true;
                }
            }
             
            $str = str_replace("{header}", "<th>".implode('</th><th class="ui-state-default" >',$results_col)."</th>", $skel);
            
            $data = "";
            foreach (  $results_data as $rowIndex=>$rowValues )
            {
                    
                    if ( $extraFields )
                    {
                        
                        $current_fields =  $final_fiels;
                        $data_pattern   = '/{\w+}/';
                        
                        
                        foreach ($current_fields as $index=>$value)
                        {
                            $fields = array();
                            $occurenceFound = preg_match_all($data_pattern, $value ,$fields);
                           
                            if ($occurenceFound)
                            {
                                
                                $pattern_field = '({|})';
                                $fields = preg_replace($pattern_field,"", $fields[0]);
                                
                                
                                
                                $in_data = $value;
                                foreach ( $fields as $field)
                                {
                                    
                                    $position_col = array_search($field, $results_col);                                    
                                    
                                    if ( $position_col !== FALSE)    
                                    {   
                                        $in_data = preg_replace('/{'.$field.'}/', $rowValues[$position_col],$in_data);
                                        
                                    }
                                    else
                                    {
                                        qlog("Error while replacing $value by ".$rowValues[$rowIndex][$position_col]);
                                        throw new InvalidArgumentException("Error, field $field does not exist, cannot substitute!");
                                    }
                                    
                                    
                                    
                                }
                                $final_fiedls[$index] = $in_data;
                            }
                        
                        }
                       
                    }
                /*   
                */    
                    
                    
                    if (is_array($rowValues))
                    {
                        foreach ($rowValues as $index=>$cell)
                        {
                            $flag = $this->_columnsMetadata[$index]->flags;
                            if ( $flag === MYSQLI_BLOB_FLAG_ )
                            {
                                $rowValues[$index] = "Blob";
                            }
                            else
                                $rowValues[$index] = $cell;
                            
                        }
                          if ( $final_fiels !== NULL && count($final_fiels)>0)
                            $rowValues = array_merge ($rowValues,$final_fiedls);
                          
                          /** if iobjkect doesn't have __toString ... eg datetime.."*/
                         foreach ( $rowValues as $index=>$row )
                         {
                          
                             if ( ! in_array(gettype($row), array("string", "integer", "float", "boolean", "double")))
                             {
                                if (  $row instanceof DateTime )
                                    $tdContent = $row->format (DEFAULT_DATE_FMT);
                                else
                                {
                                    
                                    if ( ! method_exists ($row,"__toString")  )
                                            $tdContent  = "Not display-able";
                                    else
                                       $tdContent = strval($tdContent);
                                }
                             }
                             else
                             {
                                 
                                 $fileinfo = new finfo(FILEINFO_MIME_TYPE); 
                                 $mime = $fileinfo->buffer(base64_decode($row,true));
                                 
                                 
                                 if ( strpos($mime,"image") !== FALSE )
                                 {
                                     
                                      $image = "<img src='data:{MIME};base64,{DATA}'>";
                                      $tdContent = str_replace(array("{MIME}","{DATA}"),array($mime,$row),$image);
                                      
                                 }
                                 else
                                 {
                                     if ( urldecode($row)== $row )
                                        $tdContent = strval($row);
                                     else
                                         $tdContent = urldecode($row);
                                 }
                             }
                             if ( $final_fiels === NULL) 
                                 $final_fiels = array();
                             
                             if ( !  in_array($index,array_keys($final_fiels)))
                                $rowValues[$index] = "<td class='".$results_col[$index]."' id='$index'>$tdContent</td>";
                             else
                                $rowValues[$index] = "<td>$tdContent</td>";
                             
                         }
                         
                        
                         
                         $rowValues =  implode("",$rowValues);
                     }
                   
                    
                
                    
                    $data .= str_replace("{row}",$rowValues, "<tr>{row}</tr>");
                
                
            }
            
            return str_replace("{data}", $data, $str);
        }
        
        public function columns($var=NULL)
        {
            if ( $var === NULL )
                return $this->_columns;
            else
                $this->_columns = $var;
        }
        
   
        

        public function smartyTable($dataField="data", $columnField="columns")
        {
            return array ( $dataField=>$this->values() , $columnField=>$this->columns());
        }
        
        public function add($row,  mysqli_result $mysql_ressource)
        {
            
            if ( count($row) > 1 )
            $line = array();
            
            
            
            foreach ( $row as $index=>$key )
            {
                 
                 
                 if ( !is_string( $index ) )
                 {     
                    $field_info = $mysql_ressource->fetch_field_direct($index);
                    
                    $line[] = mysqlType($key,$field_info->type);          
                    
                    $this->_columnsMetadata[$index] = $field_info;
                
                    if ( ! isset($this->_columns[$index]) )
                    {
                        $this->_columns[$index] = $field_info->name;
                    }
                    
                 } 
                 else {
                     $assoc_line[$index] = mysqlType($key,$field_info->type);            
                     
                 }
                
            }
            
            
           
            if ( count($line) > 1 )
            {
                $this->_assoc[] = $assoc_line;
                $this->_data[]  = $line;
            }
            else
            {
                $this->_assoc = $assoc_line;
                $this->_data[]  = $line[0];
            }
            
            
     
        }
        
        
        public function get()
        {
            if ( $this->cursor<count($this->_data))
            {
                return  $this->_data[$this->cursor++];
            }
        }
        
        
 
        
        public function assoc_values()
        {
            return $this->_assoc;
        }
        
        public function values()
        {
            return $this->_data;
        }
        
        public function objects()
        {
            $array = array();
            
            foreach ( $this->assoc_values() as $key=>$values)
            {
                $obj = new stdClass;
                foreach ( $values as $fieldName=>$fieldVal)
                    $obj->$fieldName = $fieldVal;
                $array[] = $obj;
            }
           
            return $array;
        }
        
        
        public function begin()
        {
            $this->cursor = 0;
        }
        
        public function isatend()
        {
            return $this->cursor == $this->count();
        }
        
        public function count()
        {
            return count($this->_data);
        }
        
        
    }
?>
