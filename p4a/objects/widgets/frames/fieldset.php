<?php
class P4A_Fieldset extends P4A_Frame
{
	var $_title = "";

	//constructor
	function P4A_Fieldset($name)
	{
		parent::P4A_Frame($name);
	}

	function setTitle($title)
	{
		$this->_title = $title;
	}

	function getTitle()
	{
		return $this->_title;
	}

	function getAsString()
	{
		if (!$this->isVisible()) {
			return "";
		}

		$p4a = P4A::singleton();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();

		$string  = "<fieldset class='frame' $properties $actions >";
		if ($this->getTitle()) {
			$string  .= "<legend>" . $this->getTitle() . "</legend>";
		}
		foreach($this->_map as $i=>$row){

			$string .= "\n<div class='row' style='border:1px solid white'>";

			foreach ($row as $obj) {
				$object = $p4a->getObject($obj["id"]);
				$float = $obj["float"];
				$margin = "margin-" . $obj["float"];
				$margin_value = $obj["margin"];
				$string .= "\n\t<div style='padding:2px; float:$float;$margin:$margin_value'>";
				$string .= "\n\t\t" . $object->getAsString() ;
				$string .= "\n\t</div>";
			}

			$string .= "\n\n\t<div class='br'></div>\n";
			$string .= "\n</div>\n";
		}
		$string .= "</fieldset>\n\n";
		return $string;
	}

}
?>