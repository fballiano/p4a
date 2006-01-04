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
 * @package MySQL_Manager
 */

class My_Create_Table extends P4A_Mask
{
	function My_Create_Table()
	{
		parent::P4A_Mask("My_Create_Table");

		$p4a =& P4A::singleton();

		$table_name =& $this->build("p4a_field","table_name");
		$create_table =& $this->build("p4a_button","create_table");
		$this->intercept($create_table,"onClick","createTable");

		$frame =& $this->build("p4a_frame","frame");
		$frame->anchor($table_name);
		$frame->anchor($create_table);
		$frame->setWidth(700);

		$this->display("menu",$p4a->menu);
		$this->display("main",$frame);
	}

	function createTable()
	{
		$p4a =& P4A::singleton();
		$db =& P4A_DB::singleton();

		$table_name =& $this->table_name->getNewValue();
		$result = $db->query("CREATE TABLE $table_name (id int)");
		if (!DB::isError($result)) {
			$menu_item =& $p4a->menu->items->tables->addItem($table_name);
			$p4a->intercept($menu_item,"onClick","tableOpenClick");
			$p4a->menu->items->tables->items->$table_name->onClick();
			$this->table_name->setNewValue("");
		}
	}
}
?>