<?php

    class db_configuration extends p4a_mask
    {
        function &db_configuration($name)
        {
            $this->p4a_mask();
			$this->setTitle("DB Connection");
            $fields = array("user","pass","port","server","database");
            $sheet =& $this->build("p4a_sheet","sheet");
            foreach($fields as $field_name){
                $field =& $this->fields->build("p4a_field",$field_name);
                $sheet->anchor($field);
            }
        
            $this->fields->user->setValue("root");
            $this->fields->server->setValue("localhost");
			$this->fields->pass->setType("password");
			$this->fields->pass->setEncryptionType("none");
        
            $button =& $this->build("p4a_button","button");
            $button->setLabel("Enter");
            $this->intercept($button,'onClick', 'enter');
            $sheet->anchor($button);        
            
            $this->display("main", $this->sheet);
        }
        
        function enter()
        {
            $p4a =& p4a::singleton();
            $user = $this->fields->user->getNewValue();
            $pass = $this->fields->pass->getNewValue();
            $port = $this->fields->port->getNewValue();
            $server = $this->fields->server->getNewValue();
            $database = $this->fields->database->getNewValue();
            
            $p4a->dsn = "mysql://$user:$pass@$server/$database";
            define("P4A_DSN", $p4a->dsn);
            $db =& p4a_db::singleton();         
            
            $tables =& $db->getCol("show tables");
            $menu =& $p4a->build("p4a_menu", "menu");
            $menu->addItem("tables", "Tables");
            
            foreach($tables as $table){
				if (! preg_match("/_seq$/", $table)){
					$valid_table = $table;
                	$label =  ucwords(str_replace('_', ' ', $table));
                	$menu->items->tables->addItem($table,$label);
                	$p4a->intercept($menu->items->tables->items->$table, 'onClick', 'menuClick');
				}
            }
			
			$edit =& $menu->addItem("edit", "Edit");
			$p4a->intercept($edit, "onClick", "editMask");
            $menu->items->tables->items->$valid_table->onClick();
// 			$p4a->openMask("p4a_auto_mask");
        }
    }
?>