<?php
//todo
class P4A_Canvas extends P4A_Widget
{
	var $objects = array();
	var $top = 10;
	var $left = 10;
	var $unit = "px";
	var $offset_top = 0;
	var $offset_left = 0;

	function P4A_Canvas($name)
	{
		parent::P4A_Widget($name);
	}

	function anchor(&$object, $top, $left=0)
	{
		$this->objects[] = array($object, $top, $left);
	}

	function setOffset($top, $left)
	{
		$this->offset_top += $top;
		$this->offset_left += $left;
	}

	function defineGrid($top = 10, $left = 10, $unit = 'px')
	{
		$this->top = $top;
		$this->left = $top;
		$this->unit = $unit;
	}

	function getAsString()
	{
		$this->debug = true;
		$string  = "";

		foreach(array_keys($this->objects) as $key){
			$object =& $this->objects[$key][0];
			$top = ($this->objects[$key][1] * $this->top) + $this->offset_top;
			$left = ($this->objects[$key][2] * $this->left) + $this->offset_left;
			$unit = $this->unit;

			$string .= "<div style='position:absolute;top:{$top}{$unit};left:{$left}{$unit};'>\n";
			$string .= $object->getAsString() . "\n";
			$string .= "</div>\n\n";
			unset($object);
		}
		return $string;
	}
}
?>