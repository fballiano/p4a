<?php

class P4A_XML_Mask extends P4A_Mask
{
	var $_xml = array();

	function &P4A_XML_Mask($mask_name = NULL)
	{
		P4A_Mask::P4A_Mask();
		if ($mask_name) {
			$this->loadFromXML($mask_name);
		}
	}

	function loadFromXML($mask_name)
	{
		$p4a =& p4a::singleton();

		$host = $p4a->masks->db_configuration->fields->host->getNewValue();
		$database = $p4a->masks->db_configuration->fields->database->getNewValue();

		$file_name = P4A_APPLICATION_DIR . "/xml/$host/$database/$mask_name.xml";

		$xml = file_get_contents($file_name);

		$p = xml_parser_create();
		xml_parse_into_struct($p, $xml, $vals);
		xml_parser_free($p);

		$this->_xml = $vals;

		foreach ($vals as $key=>$tag) {

			if ($tag["type"] == "cdata") {
				continue;
			}

			$name = "";
			if (array_key_exists("attributes", $tag)) {
				$attr = $tag["attributes"];
				if (array_key_exists("NAME", $attr)) {
					$name = $attr["NAME"];
				}
			}else {
				$attr = array();
			}

			$level = $tag["level"];


			if ($tag["tag"] == "MASK") {
 				$this->setLevel($this, $level);
			}

			if ($tag["tag"] == "SOURCE") {
				if (array_key_exists("TABLE", $attr)) {
					$source =& $this->build("P4A_DB_Source", $name);
					$source->setTable($attr["TABLE"]);
					$this->setAttr($source, $attr);
					$source->load();
					$source->firstRow();				
					$this->setPK($source, $attr);
				} else {
					$source =& $this->{$name};
				}

				$new_level = $level-1;
 				$this->levels{$new_level}->setSource($source);
				$this->setId($source, $tag, $key);
			}

			if ($tag["tag"] == "FRAME" and $tag["type"] == "open") {
				$frame =& $this->build("P4A_Frame", "frame");
				$this->setAttr($frame,$attr);
				$this->setId($frame, $tag, $key);
			}

			if ($tag["tag"] == "FIELD"  and $tag["type"] != "close") {
				$field =& $this->fields->{$attr["NAME"]};
				$field->setUploadSubpath("$host/$database");

				$this->setAttr($field,$attr);
				$this->setLevel($field, $level);
				$frame->anchor($field);
				$this->setId($field, $tag, $key);
			}

			if ($tag["tag"] == "TABLE" and $tag["type"] == "open") {
				$cols = array();
				$table =& $this->build("P4A_Table", $name);
				$this->setLevel($table, $level);
				$frame->anchor($table);
				$this->setId($table, $tag, $key);
			}

			if ($tag["tag"] == "TABLE" and $tag["type"] == "close") {
 				$this->levels{$level}->setVisibleCols($cols);
			}

			if ($tag["tag"] == "COL") {
				$new_level = $level-1;
				$this->setLevel($this->levels{$new_level}->cols->$name, $level);
 				$this->setAttr($this->levels{$new_level}->cols->$name, $attr);
				$this->setId($this->levels{$new_level}->cols->$name, $tag, $key);
				if ($this->levels{$new_level}->cols->$name->isVisible()){
					$cols[] = $name;
				}
			}

			if ($tag["tag"] == "MENU") {
				$this->display("menu", $p4a->menu);
			}

			if ($tag["tag"] == "TOOLBAR") {
				$type = $attr["TYPE"];
				$toolbar =& $this->build("P4A_{$type}_TOOLBAR", "toolbar");
				$toolbar->setMask($this);
				$this->display("top", $toolbar);
			}
		}
		$this->display("main",$frame);
	}

	function getXML() {
		$xml = "";
		foreach($this->_xml as $tag) {
			$type = $tag["type"];
			if ($type == "cdata") {
				continue;
			}
			$prefix = str_repeat("\t",($tag["level"] - 1)) ;
			$tagname = strtolower($tag["tag"]);
			$attributes = "";
			if (array_key_exists("attributes", $tag)) {
				foreach($tag["attributes"] as $attribute=>$value) {
					$attribute = strtolower($attribute);
					if ($attribute!="id") {
						$attributes .= "$attribute='$value' ";
					}
				}
			}

			if ($type == "close") {
				$prefix .= 	"</";
			} else {
				$prefix .= 	"<";
			}

			if ($type == "complete") {
				$postfix = "/>\n";
			} else {
				$postfix = ">\n";
			}

			$xml .= "{$prefix}{$tagname} $attributes $postfix";
		}
		return $xml;
	}

	function setAttr(&$obj, $attr)
	{
		foreach ($attr as $att=>$value) {

			if ($value == 'false' or $value == '0') {
				$value = FALSE;
			} elseif ($value == 'true' or $value == '1') {
				$value = TRUE;
			}

			switch ($att) {
				case "MANDATORY":
					$obj->label->setFontWeight('bold');
					break;
				case "SOURCE_DESCRIPTION_FIELD":
					$obj->setSourceDescriptionField($value);
					break;
				case "UPLOAD_SUBPATH":
					$obj->setUploadSubpath($value);
					break;
				case "ENABLE":
					$obj->enable($value);
					break;
				case "PK":
				case "AUTOINCREMENT":
					break;
				default:
					eval('$obj->set' . $att . "('$value');");

			}
		}
	}

	function setId(&$obj, $tag, $key)
	{
		$id = $obj->getId();
		$tag["id"] = $id;
		$this->_xml[$key] = $tag;
	}

	function setLevel(&$obj, $level)
	{
		$this->levels{$level} =& $obj;
	}
	
	function setPK(&$source, $attr)
	{
		if (array_key_exists("PK", $attr)) {
			$pk = $attr["PK"];
			$pks = split(",",$pk);

			if (count($pks)==1) {
				$source->setPK($pk);
			}else{
				$source->setPK($pks);
			}
			
			if (array_key_exists("AUTOINCREMENT",$attr) and count($pks) == 1) {
				$table = $source->getTable();
				$pk_sequence = "{$table}_{$pk}";
				$source->fields->$pk->setSequence($pk_sequence);
			}
		} 
	}
}
?>