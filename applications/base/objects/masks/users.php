<?php
class P4A_Users extends P4A_Mask
{
	function &P4A_Users()
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