<?php

class mask1 extends p4a_mask
{
	function mask1()
	{
		parent::p4a_mask();
		
		$eventi = array();
		$eventi[] = array("date"=>"2007-10-01", "time"=>"14:15", "description"=>"evento 1");
		$eventi[] = array("date"=>"2007-10-01", "time"=>"14:30", "description"=>"evento 2");
		$eventi[] = array("date"=>"2007-10-03", "time"=>"16:20", "description"=>"evento 3");
		
		$this->build("p4a_array_source", "as");
		$this->as->load($eventi);
		
		$this->build("p4a_calendar", "calw");
		$this->calw->setSource($this->as);
		//$this->calw->setType("week");
		
		$this->display("main", $this->calw);
	}
}

?>