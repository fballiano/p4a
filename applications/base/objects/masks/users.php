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

class P4A_Users extends P4A_Mask
{
	function P4A_Users()
	{
		$this->P4A_Mask();
		$p4a =& P4A::singleton();
		
		//Build
 		$users =& $this->build("p4a_db_source","users");
		$table =& $this->build("p4a_table",'table');
		$frm =& $this->build("p4a_frame","frm");
		$fls =& $this->build("p4a_fieldset","fls");
		$flslogin =& $this->build("p4a_fieldset","flslogin");
		$flsana =& $this->build("p4a_fieldset","flsana");
		$toolbar =& $this->build("p4a_simple_toolbar","toolbar");

		//Source
		$users->setTable("users");
		$users->addOrder("user");
		$users->setPk("id");
		$users->load();
		$users->fields->id->setSequence("users_id");
		$users->firstRow();
		$this->setSource($users);
		$f =& $this->fields;

		//Properties
		$f->pass->setType("password");

		$f->level->setType("select");
		$f->level->setSource($p4a->access_levels);
		
		$table->setSource($users);
		$table->setVisibleCols(array("id","user","level","name","surname","tel1","email"));
		$table->setWidth(700);

		$fls->setTitle("User Detail");
		$flslogin->setTitle("Login Data");
		$flsana->setTitle("Personal Data");

		$frm->setWidth(700);

		$toolbar->setMask($this);

		//Display
		$flslogin->anchor($f->user);
		$flslogin->anchor($f->pass);
		$flslogin->anchor($f->level);
		$flslogin->anchor($f->default_mask);
		$flsana->anchor($f->surname);
		$flsana->anchorLeft($f->name);
		$flsana->anchor($f->address);
		$flsana->anchor($f->city);
		$flsana->anchorLeft($f->country);
		$flsana->anchor($f->tel1);
		$flsana->anchorLeft($f->tel2);
		$flsana->anchor($f->mobile);
		$flsana->anchorLeft($f->fax);
		$fls->anchor($flslogin);
		$fls->anchor($flsana);

		$frm->anchor($table);
		$frm->anchor($fls);

		$this->display("menu",$p4a->menu);
		$this->display("top",$toolbar);
		$this->display("main",$frm);
	}
}
?>