<?php

class P4A_Table_Mask extends P4A_Mask
{
	var $_table_name = "";

	function &P4A_Table_Mask($name)
	{
		$this->_table_name = $name;
		parent::P4A_Mask();
		$p4a =& P4A::singleton();
		
		$toolbar =& $this->build("P4A_Standard_Toolbar","toolbar");
		$toolbar->setMask($this);
				
		$source =& $this->build("My_Table_Source","source");
		$source->load($name);
		$source->setPageLimit(100);
		$this->setSource($source);
 		$source->firstRow();

//  	$table =& new P4A_Table();
		$table =& $this->build("p4a_table","table");
 		$table->setSource($source);
		
		$this->fields->Null->setType("checkbox");
		
// 		$frame =& new P4A_Frame();
		$frame =& $this->build("P4A_Frame", "frame");
		$frame->setWidth(700);
		$frame->anchor($table);
		while ($field =& $this->fields->nextItem()) {
			$frame->anchor($field);
		}

		$this->display("menu",$p4a->menu);
		$this->display("top",$toolbar);
		$this->display("main",$frame);
	}
	
	function getName()
	{
		return $this->_table_name;
	}
	
	function saveRow()
	{
		$db =& P4A_DB::singleton();
		$table =& $this->_table_name;
		$old_field_name = $this->fields->Field->getValue();
		$new_field_name = $this->fields->Field->getNewValue();
		$type =& $this->fields->Type->getNewValue();
		
		if ($this->data->isNew()) {
			$query = "ALTER TABLE $table ADD $new_field_name $type";				
			$result = $db->query($query);
			$this->source->reload();
			$this->lastRow();
		} else {
			$query = "ALTER TABLE $table CHANGE $old_field_name $new_field_name $type";		
			$this->source->reload();
			$result = $db->query($query);
		}
		$this->source->reload();
	}
	
	function deleteRow()
	{
		$db =& P4A_DB::singleton();
		$table =& $this->_table_name;
		$field_name = $this->fields->Field->getValue();
		$query = "ALTER TABLE $table DROP $field_name";
		$db->query($query);
		$this->source->reload();
		
	}
}
?>