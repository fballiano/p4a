<?php

class mask1 extends p4a_mask
{
	public function __construct()
	{
		parent::__construct();

		// let's build a message
		$this->build("p4a_message", "message");
		$this->message->autoClear(false);
		$this->message->setIcon("info");

		// just set the tex using the __ function, it will translate
		// the text using P4A_LOCALE
		$this->message->setValue(__("hello_world"));

		// the label of this field will be automatically translated
		$this->build("p4a_field", "field_1");

		// anchoring and displaying widgets
		$this->build("p4a_frame", "frame");
		$this->frame->setWidth(700);
		$this->frame->anchor($this->message);
		$this->frame->anchor($this->field_1);

		$this->display("main", $this->frame);
	}
}