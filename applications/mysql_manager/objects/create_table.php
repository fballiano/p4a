<?php
class My_Create_Table extends P4A_Mask
{
	function &My_Create_Table()
	{
		parent::P4A_Mask("My_Create_Table");
		
		$p4a =& P4A::singleton();
		
		$table_name =& $this->build("p4a_field","table_name");
		$create_table =& $this->build("p4a_button","create_table");
		$this->intercept($create_table,"onClick","createTable");
		
		$frame =& $this->build("p4a_frame","frame");
		$frame->anchor($table_name);
		$frame->anchor($create_table);
		$frame->setWidth(700);
				
		$this->display("menu",$p4a->menu);
		$this->display("main",$frame);
	}
	
	function createTable()
	{
		$p4a =& P4A::singleton();
		$db =& P4A_DB::singleton();
		
		$table_name =& $this->table_name->getNewValue();
		$result = $db->query("CREATE TABLE $table_name (id int)");
		if (!DB::isError($result)) {
			$menu_item =& $p4a->menu->items->tables->addItem($table_name);
			$p4a->intercept($menu_item,"onClick","tableOpenClick");
			$p4a->menu->items->tables->items->$table_name->onClick();
			$this->table_name->setNewValue("");
		}
	}
}
?>