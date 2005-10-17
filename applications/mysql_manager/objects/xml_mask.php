<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

class P4A_XML_Mask extends P4A_Mask
{
	var $_xml = array();
	var $_mandatory_fields = array();

	function P4A_XML_Mask($mask_name = NULL)
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

			if ($tag["tag"] == "MESSAGE") {
				$message =& $this->build("P4A_Message", $attr["NAME"]);
				if (!isset($attr["POSITION"])) {
					$attr["POSITION"] = "left";
				}

				switch ($attr["POSITION"]) {
					case "left":
						$frame->anchor($message);
						break;
					case "center":
						$frame->anchorCenter($message);
						break;
					case "right":
						$frame->anchorRight($message);
						break;
					default:
						$frame->anchor($message);
				}

				unset($attr["POSITION"]);
				$this->setAttr($message, $attr);
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
				case "NAME":
					break;
				case "MANDATORY":
					$obj->label->setFontWeight('bold');
					$this->_mandatory_fields[] = $obj->getName();
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
					$this->setPk($obj, $attr);
					break;
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
		if (array_key_exists("PK", $attr) and $attr["PK"] != "") {
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
				$source->load();
				$source->fields->$pk->setSequence($pk_sequence);
			}
		}
	}

	function saveRow()
	{
		foreach ($this->_mandatory_fields as $fieldname) {
			$value = trim($this->fields->$fieldname->getNewValue());
			if (!strlen($value) and $this->fields->$fieldname->getType() != "checkbox") {
				return FALSE;
			}
		}
		parent::saveRow();
		return TRUE;
	}
}
?>