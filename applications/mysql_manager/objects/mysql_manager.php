<?php

class mysql_manager extends p4a
{
    function &mysql_manager()
    {
        $this->p4a();
        $this->openMask("db_configuration");
    }

    function &main()
    {
        if (isset($this->dsn)){
            define("P4A_DSN", $this->dsn);
        }
        parent::main();
    }

    function maskOpenClick(&$object)
    {
        $name = $object->getName();
        if (!isset($this->masks->$name)) {
			$this->masks->build('p4a_auto_mask', $name);
        }
        $this->openMask($name);
    }
	
	function tableOpenClick(&$object)
    {
        $name = $object->getName();
        if (!isset($this->masks->$name)) {
			$this->masks->build('P4A_Table_Mask', $name);
        }
        $this->openMask($name);
    }

	function editMask()
	{
		$mask =& $this->active_mask;
		$table =& $mask->table;
		$table->setStyleProperty("border", "1px dashed red");
		$table->setVisibleCols();
		while ($col =& $table->cols->nextItem()) {
			$col->setStyleProperty("border", "1px dashed red");
			$col->setStyleProperty("vertical-align", "bottom");
			$col->setVisible();
			$col->addAction("onClick", "void");
 			$mask->intercept($col, "void", "editWidget");
		}


		$fields =& $this->active_mask->fields;
		while($field =& $fields->nextItem()){
			$field->setStyleProperty("border", "1px dashed red");
			$field->setStyleProperty("cursor", "pointer");
			$field->setVisible();
			$field->setNewValue("");
			$field->addAction("onClick");
			$mask->intercept($field, "onClick", "editWidget");
			$field->enable();
		}
		$mask->info->setValue("Click on the red areas to edit widgets");
	}
	
	function createTable()
	{
		$this->openMask("My_Create_Table");
	}
}
?>