<?php

class mask1 extends p4a_mask
{
	function mask1()
	{
		parent::p4a_mask();
		
		$eventi = array();
		$eventi[] = array("date"=>"2006-05-01", "time"=>"14:15", "description"=>"dentista");
		$eventi[] = array("date"=>"2006-05-01", "time"=>"14:30", "description"=>"altro");
		$eventi[] = array("date"=>"2006-05-03", "time"=>"16:20", "description"=>"heredium");
		
		$this->build("p4a_array_source", "as");
		$this->as->load($eventi);
		
		$this->build("p4a_calendar", "calw");
		$this->calw->setSource($this->as);
		
		$this->display("main", $this->calw);
	}
}

?>