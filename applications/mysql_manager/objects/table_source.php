<?php
class My_Table_Source extends P4A_Array_Source
{
	var $_table_name;
	
	function &load($table_name)
	{
		$db =& P4A_DB::singleton();
		$this->_table_name = $table_name;
		$table_array = $this->getTableArray();	
		return parent::load($table_array);		
	}	
	
	function getTableArray()
	{
		$db =& P4A_DB::singleton();
		$table_name = $this->_table_name;
		return $db->getAll("desc $table_name");
	}
	
	function reload()
	{
 		$this->_array = $this->getTableArray();
	}
	
}
?>