<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

	class P4A_Login extends P4A_Mask
	{
		function P4A_Login()
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