<?php
    define("CHAR_COMMA", ','); /** \def Mainly used in low-level functions for building queries */
    define("CHAR_FIELD", "'");/** \def Mainly used in low-level functions for building queries */
    define("CHAR_APPOSTROPHE", "`"); /** \def Mainly used in low-level functions for building queries */
    require_once "wrapper.php";
    
    class THIS_IS_MORE_THAN_USELESS
    {
        static function real_escape_string($str)
        {
            return addslashes($str);
        }
    }
  
    class Queries extends THIS_IS_MORE_THAN_USELESS
    {
		
		       /*
    **************************************************************************** 
    * function : createProcessedArray 
    * scope    : private    
    * usage    : return an array ready to be used by insert_into
    * IN      : assosicative array indexed by fields name, and the id use in the order 
    * OUT     : $array ready to be inserted
	* Note    : if idOrHostname is not specified, it will  skip the hostname 
	*			This is used, because all components are based on their hostname (that must be unique inside
	*			the category and when we build this array, hostname is the key of the row not the column.
	*			Therefore to be more generalist, we can build array for order and normal arrays.
    **************************************************************************** */   
	static function createProcessedArray($itemValues,$salesOrder,$indexName, $indexValue)
	{
		
		
		
		$keyVal = explode("_", $indexName);
		if ( count($keyVal) == 2 )
			$keyVal =  $keyVal[1];
		else
			$keyVal = $indexName;
		
 		$returnedArray = array($keyVal => isset($indexValue)?$indexValue : NULL ) ;
		
		if ( ! is_array($itemValues) ) return $returnedArray;
		
		foreach ( $itemValues as $field_name=>$field_value)
		{
			if ( $field_name != "" )
			{
				
				$key = explode("_",$field_name);
				
				if ( count($key) == 2 )
				{
					
					$returnedArray[$key[1]] = $field_value;
				}
				else
					$returnedArray[$field_name] = $field_value;
			}
		}
		
		
		$returnedArray["salesOrder"] = $salesOrder;
		
		
		return $returnedArray;
	}    
        
	static function e_select($table, $ValIndexedByFName ,$useLike=True, $fieldsToShow="*", $strict=True, $likeExtended=false)
	{
		
		if ( ! is_array( $ValIndexedByFName ) ) throw new MYSQL_WRAPPER_EX("INVALID_FLAG_1");
		if ( ! is_bool($useLike) ) throw new MYSQL_WRAPPER_EX("INVALID_FLAG_2");
		if ( count($ValIndexedByFName) == 0 ) throw new MYSQL_WRAPPER_EX("INVALID_FLAG_3");	
		
		
		$lastElement = count($ValIndexedByFName) - 1;
		$curPos = 0;
		$operande = "";
		$BOOLCOMP = "";
		
		$useLike ? $operande = "LIKE" : $operande = '=';
		$strict  ? $BOOLCOMP = 'AND'  : $BOOLCOMP = 'OR';
		$cdt = "";
		
		
		foreach ( $ValIndexedByFName as $colName=>$value)
		{
			
			if ( ! empty($value) )
			{
				if ($operande == "LIKE" )
					if ( $likeExtended )
						$value = "%".parent::real_escape_string($value)."%" ; 
					else
						$value .= '%' ; 
				
				$cdt .= " `$colName` $operande '$value' ";
				if ( $curPos != $lastElement )
				{
					$cdt .= " $BOOLCOMP ";
				}
				
				
			}
			$curPos++;
			
		}		
		
		
		
		return $this::basic_select($table, $cdt ,$fieldsToShow);;
	
	}
	
	
	   /***************************************************************************** 
		* function : insert_into 
		* scope    : private    
		* usage    : insert values in a given table
		* IN       : ValuesIndexByField , $table, $REMOVE_TYPE_FLAG
		* OUT      : result of query
		* Note     : REMOVE_TYPE_FLAG is used since the convention used is <type>_<varname>.
		*		     Basicaly this enforce the control on what is submitted to the database.
		*			 When this parameters is set to True, the function will remove the <type>
		*			 in the key.
		**************************************************************************** */	
        static function insert_into($ValuesIndexByField, $table, $REMOVE_TYPE_FLAG=False) 
        {

  
          $table = parent::real_escape_string($table);
          $sql_query = "INSERT INTO ".$table." (";

          if (is_array($ValuesIndexByField))
          {
                $vals = "";
                foreach ( $ValuesIndexByField as $key=>$value) 
                {
                        if ($REMOVE_TYPE_FLAG === True )
                        {

                                $tempArray = explode("_",$key);
                                if ( count($tempArray) === 2 ) //We have <type>|<field nam>
                                        $key = $tempArray[1];

                        }


  

                        if (gettype($value) == 'integer')
                        {
                            $vals       .=  $value; 
                        }
                        elseif ($value === NULL)
                        {
                            $vals .= 'NULL';
                        }
                        else
                        {
                            $vals       .=  CHAR_FIELD.parent::real_escape_string($value).CHAR_FIELD; 
                        }
                        
                        $sql_query  .=  CHAR_APPOSTROPHE.parent::real_escape_string($key).CHAR_APPOSTROPHE;          


                        $vals       .=  CHAR_COMMA;
                        $sql_query  .=  CHAR_COMMA;


                }

                $sql_query = rtrim($sql_query, ",");
                $vals      = rtrim($vals, ",");

                $sql_query  .= ") VALUES (".$vals.")";




                return $sql_query;



          }
          return False; 
        }
        /***************************************************************************** 
        * function : sql_count 
        * scope    : private    
        * usage    : wrapper for the count function
        * IN      : tablename, condition (NOTE : in sql) $columnName to count 
        * OUT     : result of query
        **************************************************************************** */	
        static function sql_count($tableName, $condition="", $columnName='*')
        {
                        $tableName = parent::real_escape_string($tableName);
                        if (  $columnName  == "" )
                                $columnName = "*";

                        if ($tableName == "" ) throw new MYSQL_WRAPPER_EX("TABLENAME_IS_MANDATORY"); 

                        $tableName = parent::real_escape_string($tableName);
                        //$condition = $condition;
                        $columnName = parent::real_escape_string($columnName);

                        $sql_query = "SELECT COUNT(".$columnName.") FROM ".$tableName;

                        if ($condition != "")
                                $sql_query .= " WHERE ".$condition;


                        return $sql_query;
        }
		
		/***************************************************************************** 
		* function : basic_select 
		* scope    : private    
		* usage    : wrapper for select
		* IN      : tablename, condition (NOTE : in sql) $field to show 
		* OUT     : result of query
		*		  : Note added array support for $fields
		**************************************************************************** */
		static function basic_select($tableName, 
                                             $condition="", 
                                             $fields='*', 
                                             $orderBy=Null, $asc=True, $limityFrom=0,$limite_size=0,$Count=False, $dumpTo=NULL)
		{
		  
		  
		  
		  
		 if ( is_array($tableName) ) $tableName = implode(', ',$tableName);
			
			
		 
		  
		 $tableName = parent::real_escape_string($tableName);
		  
		  
		  if (  $fields  == "" ) $fields = "*";
		  
		  $stFlName = "";
		 
		  if ( is_array($fields) )
		  {
			
			foreach ($fields as $intIndice=>$Column)
			{
				if ( strpos( $Column,'.' ) !== False)
				{
					$rCol = explode('.',$Column);
					if ( count($rCol) != 2 ) throw new MYSQL_WRAPPER_EX("INVALID_COL");
					$stFlName .= CHAR_APPOSTROPHE.$rCol[0].CHAR_APPOSTROPHE.".".CHAR_APPOSTROPHE.$rCol[1].CHAR_APPOSTROPHE.CHAR_COMMA;
				}
				else
					$stFlName .= CHAR_APPOSTROPHE.$Column.CHAR_APPOSTROPHE.CHAR_COMMA;
			}
			$stFlName = rtrim($stFlName, CHAR_COMMA);
			$stFlName = trim($stFlName, CHAR_COMMA);
		  }
		  else
			$stFlName = parent::real_escape_string($fields);
		  
			
		  if ( $orderBy === Null ) 
			$orderBy = "";
		  else
		  {
			$orderBy = "ORDER BY ".$orderBy;
			if ( $asc === True )
				$orderBy .= " ASC";
			else 
				$orderBy .= " DESC";
		  }
		  
		  $cdt = "";
		  if  ($condition != "")		  
			$cdt = " WHERE $condition";
		  
		  $limit = "";
		  
		  if ( $limite_size > 0 )
		  {
			$limit = "LIMIT $limityFrom,$limite_size"; 
		  }
		  if ( $Count )
			$sql_query = "SELECT COUNT($stFlName) FROM $tableName $cdt $orderBy $limit";
		  else
		  {
			if ( ! is_null($dumpTo) )
				$dumpArgs = "INTO DUMPFILE '$dumpTo'";
			else
				$dumpArgs = "";
				
			
			$sql_query = "SELECT $stFlName $dumpArgs FROM $tableName $cdt $orderBy $limit";
		  }
		  
		  
		  return $sql_query;
		  
		}
		/***************************************************************************** 
		* function : delete_field 
		* scope    : private    
		* usage    : Set to null a selected field
		* IN      : $table to update, $colName column and $condition in sql
		* OUT     : None
		**************************************************************************** */  
		static function delete_field($tableName, $colName, $condition)
		{
#if ( $this->isReadOnlyAccess() ) throw new MYSQL_WRAPPER_EX("RO_ACCESS");
		  $this::update(array($colName=>NULL), $tableName, $condition);
		}
		/***************************************************************************** 
		* function : delete_row 
		* scope    : private    
		* usage    : delete  row(s) under certain conditions
		* IN      : $table to update, $condition (in sql), note conditon is mandatory 
		*			since we don't want to clean the entire table
		*           NOTE : We accept to delete only if a crioteria is set (avoid  
							error that waste an entire table
		* OUT     : None
		**************************************************************************** */  	
		static function delete_row($tableName, $condition)
		{
		  $ALLOWED_OPERATORS = array('>','<','=','!=','<=','>=');
		  

		  if ( $tableName == "" or $condition == "" ) throw new MYSQL_WRAPPER_EX("ERROR : Empty table name or condition use truncate instead!");
#if ( $this->isReadOnlyAccess() ) throw new MYSQL_WRAPPER_EX("RO_ACCESS");
		  $tableName = parent::real_escape_string($tableName);
		  $operator = "";
		  if ( $condition !== "" )
		  {
			//$condition = str_replace(' ', '', $condition);
			$lastIndex = strlen($condition) - 1;
			$cpt = 0;
			
			for (; $cpt <= $lastIndex ; $cpt++)
			{
				
				if ( array_search($condition[$cpt], $ALLOWED_OPERATORS) !== FALSE and $cpt < $lastIndex)
				{
					
					$operator = $condition[$cpt];
					break;
				}
				
				
			}
			if ( $operator == "" ) throw new MYSQL_WRAPPER_EX("No comparaison operator found, please use truncate instead");
			
			$condition = explode($operator, $condition);
			$condition[0] = CHAR_APPOSTROPHE.trim($condition[0]).CHAR_APPOSTROPHE;
			$condition = implode($operator, $condition);
			$condition = "WHERE $condition";
		  }
		  else
			throw new MYSQL_WRAPPER_EX("Please use truncate to empty tables");
		  
		  $sql_query = "DELETE FROM ".$tableName." $condition";
		 
		  return $sql_query;
		}
		/***************************************************************************** 
		* function : update 
		* scope    : private    
		* usage    : delete  row(s) under certain conditions
		* IN      : $table to update, $conditon (in sql), note conditon is mandatory 
		*			since we don't want to clean the entire table
		* OUT     : None
		**************************************************************************** */ 
		static function update($ValuesIndexByField, $table, $condition)
		{
#if ( $this->isReadOnlyAccess() ) throw new MYSQL_WRAPPER_EX("RO_ACCESS");
		  $table = parent::real_escape_string($table);
		 // $condition = parent::real_escape_string($condition);
		  
		  $sql_query = "UPDATE ".$table. " SET ";
                  
                  
                  $values = [];
		  foreach ( $ValuesIndexByField as $key=>$value) 
		  {
                      if ( $value === NULL )
                      {
                          $value = 'NULL';
                      }
                      elseif (gettype($value) == "integer" )
                      {
                          $values[]= CHAR_APPOSTROPHE.parent::real_escape_string($key).CHAR_APPOSTROPHE."=".$value;	  
                      }
                      else
			$values[]= CHAR_APPOSTROPHE.parent::real_escape_string($key).CHAR_APPOSTROPHE."=".CHAR_FIELD.parent::real_escape_string($value).CHAR_FIELD;	  
		  }
                  
                 $sql_query .= implode(",", $values);
		  
		  
		  $sql_query.=" WHERE ".$condition;
		  
		  return $sql_query;
	   
		}
                
                static function value_exist($table, $field, $value)
                {
                    return "SELECT $field FROM $table WHERE EXISTS (SELECT $field FROM $table WHERE $field='$value')";
                }
                
                static function SQLStmt($stmt)
                {
                    return $stmt;
                }
                
    }
?>
