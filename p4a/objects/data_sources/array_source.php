<?php
class P4A_Array_Source extends P4A_Data_Source
{
	var $_array = array();

	function &P4A_Array_Source($name){
		P4A_Data_Source::P4A_Data_Source($name);
	}

	function load($array)
	{
		$this->build("P4A_Collection", "fields");
		$this->_array = array();
		$this->_array[-1] = array();

		foreach($array as $value) {
			$this->_array[] = $value;
		}

		$first_row = $array[0];
		foreach ($first_row as $field_name=>$value) {
			$this->fields->build("p4a_data_field",$field_name);
			$this->_array[-1][$field_name] = "";
		}
	}

	function row($num_row = NULL, $move_pointer = TRUE)
	{
		if ($num_row !== NULL) {
			$row = $this->_array[$num_row-1];
		} else {
			$num_row = $this->_pointer;
			$row = $this->_array[$num_row-1];
		}

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
		if ($this->getNumRows()==0) {
			return array();
		}

		if ($from == 0 and $count == 0) {
			$array = $this->_array;
			array_shift($array);
			return $array;
		} else {
			$array = $this->_array;
			array_shift($array);
			return array_slice($array, $from, $count);
		}
	}

	function getNumRows()
	{
		return count($this->_array) - 1;
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

	function deleteRow()
	{
		$pointer = $this->getRowNumber();
		unset($this->_array[$pointer-1]);
		parent::deleteRow();
	}
}
?>