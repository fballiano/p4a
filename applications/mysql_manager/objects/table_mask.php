<?php

    class table_mask extends p4a_mask
    {
        function table_mask($name)
        {
            $this->p4a_mask();

            $p4a =& p4a::singleton();
            $db =& p4a_db::singleton();         

            $title = ucwords(str_replace('_', ' ', $name));
            $this->setTitle($title);
            
            $table_info = $db->tableInfo($name);
            $pks = array();
            foreach($table_info as $pos=>$field_info){
                if (strpos($field_info['flags'], 'primary_key') !== FALSE){
                    $pks[] = $field_info['name'];
					$pos_pk = $pos;   
                }
            }
            $source =& $this->build("p4a_db_source", "source");
            $source->setTable($name);
            $source->setPk($pks);
            $source->load();
			if (count($pks) == 1 and $table_info[$pos_pk]["type"] == "int"){
				$pk = $table_info[$pos_pk]["name"];
				$source->fields->$pk->setSequence($name);
			}			
            $source->firstRow();
            $this->setSource($source);
            
            $table =& $this->build('p4a_table', 'table');
            $table->setSource($source);

            $sheet =& $this->build("p4a_sheet", "sheet");
            $sheet->anchor($table);
            $sheet->anchor($line);
            while($field =& $this->fields->nextItem()){
                if (in_array($field->getName(), $pks)){
                    $field->disable();
                }
                $sheet->anchor($field);
            }
            
            $this->build("p4a_standard_toolbar", "toolbar");
            $this->toolbar->setMask($this);

            $this->display("menu", $p4a->menu);
            $this->display("top", $this->toolbar);
            $this->display("main", $this->sheet);
        }
    }   
?>