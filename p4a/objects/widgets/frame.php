<?php

class P4A_FRAME extends P4A_WIDGET 
{
    
	var $_map = array();
	var $_row = 1;	
	function &P4A_FRAME($name)
	{
		P4A_WIDGET::P4A_WIDGET($name);
	}
	
	function _anchor(&$object, $margin = "20px", $float="left")
	{
		if (is_object($object)) {
			$to_add = array("id"=>$object->getId(), "margin" => $margin, "float" => $float);
			$this->_map[$this->_row][]  = $to_add;
		}
	}
	
	function anchor(&$object, $margin = "0px", $float="left")
	{
		$this->newRow();
		$this->_anchor($object, $margin, $float);
	}
	
	function anchorRight(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "right");
	}
	
	function anchorLeft(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "left");
	}
	
	function anchorCenter(&$object, $margin = "10px")
	{
		$this->_anchor(&$object, $margin, "none");
	}
	
	function newRow()
	{
		$this->_row++;
	}
	
	function getAsString()
	{
		$p4a =& P4A::singleton();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		
		$string  = "<div class='frame' $properties $actions >";
		foreach($this->_map as $i=>$row){
				
			$string .= "\n<div class='row' style='border:1px solid white'>";
			
			foreach ($row as $obj) {
				$object =& $p4a->getObject($obj["id"]);
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
		$string .= "</div>\n\n";
		return $string;
	}
}

?>
