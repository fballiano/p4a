<?php
class P4A_Array_Source extends P4A_Data_Source 
{
	var $_array = array();
	
	function P4A_Array_Source($name){
		P4A_Data_Source::P4A_Data_Source($name);
	}
	
	function load($array) 
	{
		$this->build("P4A_Collection", "fields");
		$this->_array = $array;
		$first_row = $array[0];
		foreach ($first_row as $field_name=>$value) {
			$this->fields->build("p4a_data_field",$field_name);	
		}
	}
		
	function row($num_row = NULL, $move_pointer = TRUE)
	{
		$row = $this->_array[$num_row-1];
		
		if ($move_pointer) {
			if (!empty($row)) {
				$this->_pointer = $num_row;
	
				foreach($row as $field=>$value){
					$this->fields->$field->setValue($value);
				}
			} elseif ($this->getNumRows() == 0) {
				$this->newRow();
			}
		}
	
		return $row;
	}
	
	function getAll($from = 0, $count = 0) 
	{
		if ($from == 0 and $count == 0) {
			return $this->_array;	
		} else {
			return array_slice($this->_array,$from,$count);
		}
	}
	
	function getNumRows()
	{
		return count($this->_array);
	}
	
	function getPkRow($pk) 
	{
		foreach ($this->_array as $row) {
			if ($row[$this->_pk] == $pk) {
				return $row;
			}
		}
		return FALSE;
	}
}
?>