<?php
class P4A_Icon extends P4A_Widget
{
	var $_icon = NULL;
	var $_size = 32;
	
	function &P4A_Icon($name)
	{
		parent::P4A_Widget($name);
	}
	
	function setSize($size)
	{
		$this->_size = $size;		
	}
		
	function getSize()
	{
		return $this->_size;
	}
	
	function setIcon($icon)
	{
		$this->_icon = $icon;		
	}
	
	function getIcon()
	{
		return $this->_icon;
	}
	
	function getAsString()
	{
		if ($this->isVisible()) {
			$actions = $this->composeStringActions();
			$properties = $this->composeStringProperties();
			$src = P4A_ICONS_PATH . '/' . $this->_size .  '/' . $this->_icon; 
			if(!$this->isEnabled()){
				$src .= "_disabled";		
			}
			$src .= '.' . P4A_ICONS_EXTENSION ;
			return "<img $properties $actions src=\"$src\" />\n";
		} else {
			return "";
		}
	}
}
?>