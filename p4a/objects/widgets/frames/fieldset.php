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
			$row_html = "\n<div class='row' style='border:1px solid white'>";
			$one_visible = false;

			foreach ($row as $obj) {
				$object =& $p4a->getObject($obj["id"]);
				$as_string = $object->getAsString();

				if (strlen($as_string)>0) {
					$one_visible = true;
					$float = $obj["float"];
					if ($obj["float"] != "none") {
						$margin = "margin-" . $obj["float"];
					} else {					
						$margin = "margin";	
					}
					$margin_value = $obj["margin"];
					$row_html .= "\n\t<div style='padding:2px 0px; float:$float;$margin:$margin_value'>";
					$row_html .= "\n\t\t$as_string";
					$row_html .= "\n\t</div>";
				}
			}

// 			$row_html .= "\n\n\t<div class='br'></div>\n";
			$row_html .= "\n</div>\n";

			if ($one_visible) {
				$string .= $row_html;
			}
		}
		$string .= "</fieldset>\n\n";
		return $string;
	}

}
?>
