<?php

class db_configuration extends p4a_mask
{
	function &db_configuration($name)
	{
		$this->p4a_mask();
		$this->setTitle("DB Connection");

		$fields = array("host","port","user","password","database");
		$frame =& $this->build("p4a_frame","frame");
		$frame->setWidth(300);

		$message =& $this->build("p4a_message", "message");
		$frame->anchorCenter($message);

		foreach($fields as $field_name){
			$field =& $this->fields->build("p4a_field",$field_name);
			$frame->anchor($field);
		}

		$this->fields->user->setValue("root");
		$this->fields->host->setValue("localhost");
		$this->fields->password->setType("password");
		$this->fields->password->setEncryptionType("none");

		while ($field =& $this->fields->nextItem()) {
			$field->addAction("onReturnPress");
			$this->intercept($field, "onReturnPress", "enter");
		}

		$button =& $this->build("p4a_button","button");
		$button->setLabel("Enter");
		$this->intercept($button,'onClick', 'enter');
		$frame->anchorCenter($button);

		$this->setFocus($this->fields->host);

		$this->display("main", $frame);
	}

	function enter()
	{
		$p4a =& p4a::singleton();
		$host = $this->fields->host->getNewValue();
		$port = $this->fields->port->getNewValue();
		$user = $this->fields->user->getNewValue();
		$password = $this->fields->password->getNewValue();
		$database = $this->fields->database->getNewValue();

		if (empty($database)) {
			$this->message->setValue("Please type the database name");
			return ABORT;
		}

		$p4a->dsn = "mysql://$user:$password@$host/$database";
		define("P4A_DSN", $p4a->dsn);
		$db =& p4a_db::singleton();

		$tables =& $db->getCol("show tables");
		$menu =& $p4a->build("p4a_menu", "menu");
		$menu->addItem("masks", "&Masks");
		$menu->addItem("tables", "&Tables");

		foreach($tables as $table){
			if (! preg_match("/_seq$/", $table)){
				$valid_table = $table;
				$label =  ucwords(str_replace('_', ' ', $table));
				$menu->items->masks->addItem($table,$label);
				$menu->items->tables->addItem($table,$label);
				$p4a->intercept($menu->items->masks->items->$table, 'onClick', 'maskOpenClick');
				$p4a->intercept($menu->items->tables->items->$table, 'onClick', 'tableOpenClick');
			}
		}
		$create_table =& $menu->addItem("create_table", "New Table");

		$edit =& $menu->addItem("edit", "Edit");
		$p4a->intercept($edit, "onClick", "editMask");
		$p4a->intercept($create_table, "onClick", "createTable");
		$menu->items->masks->items->$valid_table->onClick();
	}
}

?>