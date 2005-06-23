<?php
	class P4A_Login extends P4A_Mask
	{
		function &P4A_Login()
		{
			$this->P4A_Mask();

			//Build
			$frm =& $this->build("p4a_frame","frame");
			$fls =& $this->build("p4a_fieldset","fls_login");

			$error =& $this->build("p4a_message","error");

			$user  =& $this->build("p4a_field","user");
			$pass  =& $this->build("p4a_field","pass");
			$enter =& $this->build("p4a_button","enter");

			//Properties
			$this->setTitle("Login");

			$frm->setWidth("320");
	
			$fls->setTitle("Login");
			$fls->setWidth(300);

			$user->setLabel("Username");
			$pass->setLabel("Password");
			$pass->setType("password");
			$enter->setLabel("Login");

			//Actions
			$this->intercept($enter,"onClick","enter");
		
			//Focus	
			$this->setFocus($user);

			//Display
			$frm->anchor($error);
			$frm->anchor($fls);
			
			$fls->anchor($user);
			$fls->anchor($pass);
			$fls->anchor($enter);

			$this->display("main",$frm);
		}

		function enter()
		{
			$user = $this->user->getNewValue();
			$pass = $this->pass->getNewValue();
						
			$db =& P4A_DB::singleton();
			$user_data = $db->getRow("SELECT * FROM users WHERE user = '$user' AND pass = '$pass'");
			if (!$user_data) {
				$this->error->setValue("Attenzione! Username o Password errata.");
			} else {
				$p4a =& P4A::singleton();
				$p4a->user_data = $user_data;
				$p4a->createMenu();
				$p4a->openMask($user_data['default_mask']);
			}
			
			return FALSE;
		}
	}
?>