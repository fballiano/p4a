<?php
//todo
class P4A_COLLECTION extends P4A_Object
{
	var $_pointer = 0;

	function &p4a_collection($name = null)
	{
		parent::p4a_object($name);
	}

	//todo da modificare, sbagliata in caso di destroy di un figlio
	function &nextItem()
	{
		$p4a =& P4A::singleton();
		if ($this->_pointer < $this->getNumItems()){
			$id = $this->_objects[$this->_pointer];
			$this->_pointer++;
			return $p4a->objects[$id];
		}else{
			$this->_pointer = 0;
		}
	}

	/*
	function &item()
	{
		$p4a =& P4A::singleton();

		if ($this->getNumItems() > 0)
			$id = $this->_objects[$this->_pointer];
			return $p4a->objects[$id];
		} else {
			return null;
		}
	}
	*/

	//todo
	function getNumItems()
	{
 		return count($this->_objects);
	}

	function reset()
	{
		$this->_pointer = 0;
	}

	function getNames()
	{
		$names = array();

		while ($item =& $this->nextItem()) {
			$names[] = $item->getName();
		}

		return $names;
	}
}
?>