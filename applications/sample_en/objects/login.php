<?php

/*
With this class we want to view the classic fields
"username" and "passoword" and to manage a very basic
authentication.
*/
class Login extends P4A_Mask
{
	function Login()
	{
		/*
		Let's call the parent constructor, this line
		is a must!
		*/
		parent::P4A_Mask();

		/*
		With this instruction we can set a title for the mask,
		if you don't want to set it manually, it will be generated
		using the class name.
		*/
		$this->setTitle("An example of authentication");

		/*
		Let's build a "message" widget, this element can render a message
		with an icon. After the rendering the message is cleared.
		This is very useful for system messages (required fields and so on).
		*/
		$this->build("p4a_message", "message");

		/*
		New let's instance a "field", the classic input field to receive
		data from the user.
		Fields can be of varius types (text, textarea, rich_textarea,
		password, image, file), to change the type you've to call the
		setType method. This philosiphy is very useful when you need
		to change the field type during the running time.
		*/
		$this->build("p4a_field", "username");

		/*
		We'd like to have the possibility to execute a method when
		the "return" key is pressed. To do that let's add the action
		"onReturnPress" to the field and declare the event
		interception.
		*/
		$this->username->addAction("onReturnPress");
		$this->intercept($this->username, "onReturnPress", "check");

		/*
		Let's create the field that will be used for the password and
		set it of password type. Doing this the input will be
		crypted with the md5 algorithm (you can also set plain text)
		and the field will be obfuscated with the "*" char.
		*/
		$this->build("p4a_field", "password");
		$this->password->setType("password");
		$this->password->addAction("onReturnPress");
		$this->intercept($this->password, "onReturnPress", "check");

		/*
		Now we need a "login" button to click when you want to authenticate.
		*/
		$this->build("p4a_button", "login");
		$this->intercept($this->login, "onClick", "check");

		/*
		The next step is tho anchor all the objects (in the desidered
		positions) in a visualization container.
		P4A has many containers (sheet for grid anchoration, canvas for
		absolute position anchoration, frame for the modern tableless
		layout, fieldset as a frame extension).
		*/
		$this->build("p4a_frame", "frame");
		$this->frame->anchorCenter($this->message);
		$this->frame->anchor($this->username);
		$this->frame->anchor($this->password);
		$this->frame->newRow();
		$this->frame->anchorCenter($this->login);
		$this->frame->setWidth(300);

		/*
		New we've to tell to the system how we want to view the interface
		elements. The default mask template contains different zones:
		- menu
		- top
		- main
		We'd like to view the main frame in the main zone:
		*/
		$this->display("main", $this->frame);

		/*
		Let's manage the focus for the entrance using the setFocus
		method.
		*/
		$this->setFocus($this->username);
	}

	function check()
	{
		$p4a = p4a::singleton();

		/*
		Let's read the values with the getNewValue method.
		This method is different from the getValue because the field
		manage two values, the one that was set at the creation time and
		the one entered by the user. Eg: The getValue is used when you
		want to cancel an operation.
		*/
		$username = $this->username->getNewValue();
		$password = $this->password->getNewValue();

		/*
		Let's simply verify the data (pay attention to the encrypted password)
		and than open the last mask on success or print an error message.
		*/
		if ($username == "root" and $password == md5("test")) {
			$p4a->openMask("finished");
		} else {
			$this->message->setValue("Wrong username or password (try username \"root\", password \"test\")");
		}
	}
}