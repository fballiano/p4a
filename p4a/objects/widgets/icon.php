<?php
class p4a_icon extends p4a_widget
{
	var $_icon = NULL;
	var $_size = 32;
	
	function &p4a_icon($name)
	{
		parent::p4a_widget($name);
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