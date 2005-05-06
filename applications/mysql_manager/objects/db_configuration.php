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

class db_configuration extends p4a_mask
{
	function &db_configuration($name)
	{
		$this->p4a_mask();
		$this->setTitle("DB Connection");

		$fields = array("host","port","user","password","database");
		$frame =& $this->build("p4a_frame","frame");
		$frame->setWidth(300);

		$message =& $this->build("p4a_message", "message");
		$message->setValue("MySQL Manager edit mode is available only for Mozilla browsers.");
		$message->setIcon("info");
		$frame->anchorCenter($message);

		foreach($fields as $field_name){
			$field =& $this->fields->build("p4a_field",$field_name);
			$frame->anchor($field);
		}

		$this->fields->user->setValue("root");
		$this->fields->host->setValue("localhost");
		$this->fields->password->setType("password");
		$this->fields->password->setEncryptionType("none");

		while ($field =& $this->fields->nextItem()) {
			$field->addAction("onReturnPress");
			$this->intercept($field, "onReturnPress", "enter");
		}

		$button =& $this->build("p4a_button","button");
		$button->setLabel("Enter");
		$this->intercept($button,'onClick', 'enter');
		$frame->newRow();
		$frame->anchorCenter($button);

		$this->setFocus($this->fields->host);

		$this->display("main", $frame);
	}

	function main()
	{
		parent::main();
		$this->message->setIcon("warning");
	}

	function enter()
	{
		$p4a =& p4a::singleton();
		$host = $this->fields->host->getNewValue();
		$port = $this->fields->port->getNewValue();
		$user = $this->fields->user->getNewValue();
		$password = $this->fields->password->getNewValue();
		$database = $this->fields->database->getNewValue();

		if (empty($database)) {
			$this->message->setValue("Please type the database name");
			return ABORT;
		}

		$p4a->dsn = "mysql://$user:$password@$host/$database";
		define("P4A_DSN", $p4a->dsn);
		$db =& p4a_db::singleton();

		$tables =& $db->getCol("show tables");
		$menu =& $p4a->build("p4a_menu", "menu");
		$menu->addItem("masks", "&Masks");
		$menu->addItem("tables", "&Tables");

		foreach($tables as $table){
			if (! preg_match("/_seq$/", $table)){
				$valid_table = $table;
				$label =  ucwords(str_replace('_', ' ', $table));
				$menu->items->masks->addItem($table,$label);
				$menu->items->tables->addItem($table,$label);
				$p4a->intercept($menu->items->masks->items->$table, 'onClick', 'maskOpenClick');
				$p4a->intercept($menu->items->tables->items->$table, 'onClick', 'tableOpenClick');
			}
		}
		$create_table =& $menu->addItem("create_table", "New Table");

		$edit =& $menu->addItem("edit", "Edit");
		$p4a->intercept($edit, "onClick", "editMask");
		$p4a->intercept($create_table, "onClick", "createTable");
		$menu->items->masks->items->$valid_table->onClick();
	}
}

?>