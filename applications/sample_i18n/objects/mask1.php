<?php

class mask1 extends p4a_mask
{
	function mask1()
	{
		parent::p4a_mask();

		$this->build("p4a_message", "message");
		$this->message->autoClear(false);
		$this->message->setIcon("info");
		$this->message->setValue(__("hello_world"));

		$this->build("p4a_field", "field_1");

		$this->build("p4a_frame", "frame");
		$this->frame->setWidth(700);
		$this->frame->anchor($this->message);
		$this->frame->anchor($this->field_1);

		$this->display("main", $this->frame);
	}
}