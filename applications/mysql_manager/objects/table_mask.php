<?php

    class table_mask extends p4a_mask
    {
        function table_mask($name)
        {
			$this->p4a_mask();
			$this->table_name = $name;

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
		
		function editField(&$field)
		{
			if (isset($this->field_name)){
				$this->fields->{$this->field_name}->setStyleProperty("background", "white");
				$this->fields->{$this->field_name}->setStyleProperty("border", "1px dashed #ccc");
			}
			$this->field_name = $field->getName();
			$this->fields->{$this->field_name}->setStyleProperty("background", "#F0F0F0");
			$this->fields->{$this->field_name}->setStyleProperty("border", "1px solid red");
			
			if(isset($this->_sidebar)){
				$this->_sidebar->destroy();
			}
			
			$s =& $this->build("p4a_sheet", "_sidebar");
			
			$label =& $s->build("p4a_field", "label");
			$label->setValue($field->getLabel());
			$width =& $s->build("p4a_field", "width");
			$width->setValue($field->getWidth());
			$height =& $s->build("p4a_field", "height");
			$is_visible =& $s->build("p4a_field", "is_visible");
			$is_visible->setType("checkbox");
			$save =& $s->build("p4a_button", "save");
			
			
			$s->blankRow();
			$s->anchor($label);
			$s->anchor($width);
			$s->anchor($height);
			$s->anchor($is_visible);
			$s->anchor($save);
			$this->intercept($save,'onClick', "apply");
					
			$this->display("sidebar", $s);
		}
		
		function apply()
		{
			$p4a =& p4a::singleton();		
			$table_name= $this->table_name;
			$field_name = $this->field_name;
			$label = $this->_sidebar->label->getNewValue();
			
			$this->destroy();
			$p4a->masks->build('table_mask', $table_name);
	        $mask =& $p4a->openMask($table_name);
			$mask->fields->$field_name->setLabel($label);
		}
    }   
?>