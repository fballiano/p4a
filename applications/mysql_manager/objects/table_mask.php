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
            
			$create_table_info = $db->getRow("show create table $name");
			$create_table_info = $create_table_info["Create Table"];
			$match = '/FOREIGN KEY \(`(\w*)`\) REFERENCES `(\w*)\` \(`(\w*)`\)/';
			preg_match_all($match, $create_table_info, $results);
			$fks = array();
			for($i=0;$i<count($results[1]);$i++){
				$fks[$results[1][$i]]['table'] = $results[2][$i];
				$fks[$results[1][$i]]['column'] = $results[3][$i];
			}
			
            $pks = array();
			$table_info = $db->tableInfo($name);
			$max_label_width = 0;
            foreach($table_info as $pos=>$field_info){
                if (strpos($field_info['flags'], 'primary_key') !== FALSE){
                    $pks[] = $field_info['name'];
					$pos_pk = $pos;   
                }
				$info[$field_info['name']] = $field_info;
				if (strlen($field_info['name'])>$max_label_width){
					$max_label_width = strlen($field_info['name']);
				}
            }
			$indexs = $db->getAll("show index from");
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
				$field_name = $field->getName();
                if (in_array($field_name, $pks)){
                    $field->disable();
                }
				
				if(array_key_exists($field_name, $fks)){
					$fk_table = $fks[$field_name]['table'];
					$fk_column = $fks[$field_name]['column'];
 					$fk_source =& $this->build("p4a_db_source", $fk_table);
					$fk_source->setTable($fk_table);
 					$fk_source->setPk($fk_column);
 					$fk_source->load();
 					$field->setType("select");
 					$field->setSource($fk_source);
					$fk_table_info = $db->tableInfo($fk_table);
					foreach($fk_table_info as $fk_field_info){
						if($fk_field_info['type'] == "string" or $fk_field_info['type'] == "blob" ){
							$field->setSourceDescriptionField($fk_field_info["name"]);
							break;								
						}
					}
					
					if (! preg_match('/not_null/',$info[$field_name]['flags'])){
						$field->allowNull("");
					}
				}
				
				if (preg_match('/not_null/', $info[$field_name]['flags']) and $field->getType() != "checkbox"){
					$field->label->setFontWeight("bold");
				}
				
				$field->label->setWidth($max_label_width, "em");
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
		
		function checkMandatory()
		{
			
		}
    }   
?>