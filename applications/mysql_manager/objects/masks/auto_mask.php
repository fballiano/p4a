<?php
class P4A_Auto_Mask extends P4A_XML_Mask 
{
	function &P4A_Auto_Mask($name)
	{
		$filename = P4A_APPLICATION_DIR . "/xml/{$name}.xml";
		$this->table_name = $name;
		if (!file_exists($filename)) {
			$this->createXML($name);
		}
		
		P4A_XML_Mask::P4A_XML_Mask($name);
	}
	
	function createXML($name) 
	{
        $p4a =& p4a::singleton();
        $db =& p4a_db::singleton();         

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
		$pk = join($pks, ",");
		
		$xml  = "<mask>\n";
		$xml .= "\t<source name='source' table='$name' pk='$pk' />\n";
		$xml .= "\t<menu />\n";
		$xml .= "\t<toolbar type='standard' />\n";
		
		$xml .= "\n\t<frame width='700'>\n";
				
		
		/*if (count($pks) == 1 and $table_info[$pos_pk]["type"] == "int"){
			$pk = $table_info[$pos_pk]["name"];
			$source->fields->$pk->setSequence($name);
		}			
        $source->firstRow();
        $this->setSource($source);*/
        $xml .= "\t\t<table name='table'>\n";
		$xml .= "\t\t\t<source name='source' />\n";
		$xml .= "\t\t</table>\n\n";    

		foreach ($info as $field_name=>$array) {
		
			$xml .= "\t\t<field name='$field_name' ";
			if (preg_match('/not_null/',$info[$field_name]['flags'])){
				$xml .= "mandatory='true' ";
			}				
			
			if (in_array($field_name, $pks)){
            	$xml .= "enable='false' ";
            }
			
			if(array_key_exists($field_name, $fks)){
				$xml .= "type='select' ";	
				
				$fk_table = $fks[$field_name]['table'];
				$fk_column = $fks[$field_name]['column'];				
				
				$fk_table_info = $db->tableInfo($fk_table);
				foreach ($fk_table_info as $fk_field_info) {
					if ($fk_field_info['type'] == "string" 
					or $fk_field_info['type'] == "blob") {
						$fk_description_field = $fk_field_info["name"];
						$xml .= "source_description_field='$fk_description_field' ";
						break;								
					}
				}
					
				$xml .= "> \n";
				$xml .= "\t\t\t<source ";
				$xml .= "name='$fk_table' table='$fk_table' ";
				$xml .= "pk='$fk_column' />\n";
				$xml .= "\t\t</field>\n";					   
			} else {
				$xml .= " />\n";
			}
// 			$field->label->setWidth($max_label_width, "em");
//          $sheet->anchor($field);
		}
		$xml .= "\t</frame>\n";
		$xml .= "</mask>\n";
		
		$this->writeXML($name,$xml);

	}
	
	function editField(&$field)
	{
		if (isset($this->field_name)){
			$this->fields->{$this->field_name}->setStyleProperty("background", "white");
			$this->fields->{$this->field_name}->setStyleProperty("border", "1px dashed red");
		}
		$this->field_name = $field->getName();
		$this->fields->{$this->field_name}->setStyleProperty("background", "#F0F0F0");
		$this->fields->{$this->field_name}->setStyleProperty("border", "1px solid red");
		
		if(isset($this->_sidebar)){
			$this->_sidebar->destroy();
		}
		
		$s =& $this->build("p4a_frame", "_sidebar");
		$s->build("P4A_Collection", "fields");
		
		$label =& $s->fields->build("p4a_field", "label");
		$label->setValue($field->getLabel());
		
		$width =& $s->fields->build("p4a_field", "width");
		$width->setValue($field->getWidth());
		
		$height =& $s->fields->build("p4a_field", "height");
		
		$visible =& $s->fields->build("p4a_field", "visible");
		$visible->setType("checkbox");
		$visible->setValue(1);
		
		$enable =& $s->fields->build("p4a_field", "enable");
		$enable->setType("checkbox");
		$enable->setValue(1);
		
		$save =& $s->build("p4a_button", "save");
		$this->intercept($save,'onClick', "save");
		
		while ($fld =& $s->fields->nextItem()) {
			$s->anchor($fld);
		}
		$s->anchor($save);
		$this->display("sidebar", $s);
	}
	
	function editCol()
	{
	}
	
	function save()
	{
		$p4a =& p4a::singleton();		
		$table_name= $this->table_name;
		$field_name = $this->field_name;
		$id = $this->fields->$field_name->getId();
		
		$key = 0;
		for ($i=0;$i<count($this->_xml);$i++) {
			if (array_key_exists("id", $this->_xml[$i]) and 
			$this->_xml[$i]["id"] == $id) {
				$key = $i;
				break;
			}
		}
		
		while ($field =& $this->_sidebar->fields->nextItem()) {
			$property = strtoupper($field->getName());
			$value = $field->getNewValue();
			if (strlen($value)) { 
				$this->_xml[$key]["attributes"][$property] = $value;
			}	
		}
		
		$xml =  $this->getXML();
		$this->writeXML($table_name,$xml);
		
		$this->destroy();
		$p4a->masks->build('p4a_auto_mask', $table_name);
	    $mask =& $p4a->openMask($table_name);
	}	

	function getName()
	{
		return $this->table_name;
	}		
	
	function writeXML($name, $xml)
	{
		$filename = P4A_APPLICATION_DIR . "/xml/{$name}.xml";
		$handle = fopen($filename, "w");
		fwrite($handle,$xml);
		fclose($handle);
	}
}
?>