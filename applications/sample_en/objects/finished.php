<?php

/*
With this mask we only tell the user that the login
has been successful.
This is only an example ;-)
*/
class Finished extends P4A_Mask
{
	function &Finished()
	{
		parent::P4A_Mask();

		$this->setTitle("Authentication successful");

		$this->build("p4a_message", "message");

		$this->build("p4a_button", "restart");
		$this->restart->setLabel("Restart");
		$this->intercept($this->restart, "onClick", "restart");

		$this->build("p4a_frame", "frame");
		$this->frame->setWidth(300);

		$this->frame->anchorCenter($this->message);
		$this->frame->anchorCenter($this->restart);

		$this->display("main", $this->frame);
	}

	/*
	This method is called on every access to the mask.
	Here we set the test of the "message" object that,
	as we see before, is deleted immediately after its
	rendering. In this case would be better a "label"
	object, that only prints the message (as the "message
	does) but does not delete the value after the
	rendering.
	In this example we used the "message" because it supports
	a nice icon while the "label" does not.
	*/
	function main()
	{
		$this->message->setValue("Complimenti, ti sei appena autenticato!");
		$this->message->setIcon("info");

		/*
		Remember to call the main method of the parent class or
		we won't see nothing on the screen.
		*/
		parent::main();
	}

	function restart()
	{
		$p4a =& p4a::singleton();

		/*
		This method destroies the session and allows the restart of the
		application.
		*/
		$p4a->restart();
	}
}

?>