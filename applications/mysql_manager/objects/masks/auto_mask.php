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
		foreach ($info as $col_name=>$array) {
			$xml .= "\t\t\t<col name='$col_name' />\n";
		}
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

	function editWidget(&$wdg)
	{
		$p4a =& p4a::singleton();
		if (isset($this->wdg_id)) {
			$last_wdg =& $p4a->getObject($this->wdg_id);
			$last_wdg->setStyleProperty("border", "1px dashed red");
		}
		$this->wdg_id = $wdg->getId();

		$wdg->setStyleProperty("border", "1px solid black");

		if(isset($this->_sidebar)){
			$this->_sidebar->destroy();
		}

		$s =& $this->build("p4a_frame", "_sidebar");
		$s->build("P4A_Collection", "fields");

		$label =& $s->fields->build("p4a_field", "label");
		$label->setValue($wdg->getLabel());

		$width =& $s->fields->build("p4a_field", "width");
		$width->setValue($wdg->getWidth());

		$height =& $s->fields->build("p4a_field", "height");
		$height->setValue($wdg->getHeight());

		$aTypes = array();
		$aTypes[] = array("pk"=>"text","label"=>"Text");
		$aTypes[] = array("pk"=>"password","label"=>"Password");
		$aTypes[] = array("pk"=>"checkbox","label"=>"Checkbox");
		$aTypes[] = array("pk"=>"date","label"=>"Date");
		$aTypes[] = array("pk"=>"textarea","label"=>"Textarea");
		$aTypes[] = array("pk"=>"rich_textarea","label"=>"Rich text editor");
		$aTypes[] = array("pk"=>"file","label"=>"File");
		$aTypes[] = array("pk"=>"image","label"=>"Image");
		$data_type = $s->build("p4a_array_source","data_type");
		$data_type->setPk("pk");
		$data_type->load($aTypes);
		$type =& $s->fields->build("p4a_field", "type");
		$type->setType("select");
		$type->setSource($data_type);
		$type->allowNull("");
		if (get_class($wdg) == "p4a_field") {
			$type->setValue($wdg->getType());
		}
		$type->addAction("onChange");
		$this->intercept($type, "onChange", "customizeSidebar");

		$visible =& $s->fields->build("p4a_field", "visible");
		$visible->setType("checkbox");
		$visible->setValue(1);

		$enable =& $s->fields->build("p4a_field", "enable");
		$enable->setType("checkbox");
		$enable->setValue(1);

		$ok =& $s->build("p4a_button", "ok");
		$ok->setLabel("OK");
		$ok->setWidth(100);
		$cancel =& $s->build("p4a_button", "cancel");
		$cancel->setWidth(100);
		$save =& $s->build("p4a_button", "save");
		$save->setLabel("Save &amp; Close");
		$save->setWidth(215);

		$this->intercept($save,'onClick', "save_edit");
		$this->intercept($cancel,'onClick', "cancel_edit");
		$this->intercept($ok,'onClick', "ok_edit");

		while ($fld =& $s->fields->nextItem()) {
			$s->anchor($fld);
		}
		$s->anchor($ok);
		$s->anchorLeft($cancel);
		$s->anchor($save);

		$this->customizeSidebar($wdg);

		$this->display("sidebar", $s);
	}

	function customizeSidebar(&$obj)
	{
		$s =& $this->_sidebar;

		$a["p4a_table_col"] = array("label","width","visible","enable");
		$class = get_class($obj);

		if ($class == "p4a_field") {
			$type = $s->fields->type->getNewValue();
			if ($type == 'textarea' or $type == 'rich_textarea') {
				$a["p4a_field"] = array("label","width","height","type","visible","enable");
			} else {
				$a["p4a_field"] = array("label","width","type","visible","enable");
			}
		}


		while ($fld =& $s->fields->nextItem()) {
			$fld->setVisible(FALSE);
		}

		foreach ($a[$class] as $fieldname) {
			$s->fields->$fieldname->setVisible();
		}

	}

	function ok_edit()
	{
		$id = $this->wdg_id;

		$p4a =& p4a::singleton();

		$wdg =& $p4a->getObject($id);
		$wdg->setStyleProperty("background", "#F0F0F0");

		$table_name= $this->table_name;


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
	}

	function cancel_edit()
	{
		$this->_sidebar->destroy();
	}

	function save_edit()
	{
		$p4a =& p4a::singleton();

		$this->ok_edit();
		$table_name = $this->table_name;

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