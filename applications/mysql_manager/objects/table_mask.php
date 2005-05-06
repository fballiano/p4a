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

class P4A_Table_Mask extends P4A_Mask
{
	var $_table_name = "";

	function &P4A_Table_Mask($name)
	{
		$this->_table_name = $name;
		parent::P4A_Mask();
		$p4a =& P4A::singleton();

		$toolbar =& $this->build("P4A_Standard_Toolbar","toolbar");
		$toolbar->setMask($this);

		$source =& $this->build("My_Table_Source","source");
		$source->load($name);
		$source->setPageLimit(100);
		$this->setSource($source);
 		$source->firstRow();

//  	$table =& new P4A_Table();
		$table =& $this->build("p4a_table","table");
 		$table->setSource($source);

		$this->fields->Null->setType("checkbox");

// 		$frame =& new P4A_Frame();
		$frame =& $this->build("P4A_Frame", "frame");
		$frame->setWidth(700);
		$frame->anchor($table);
		while ($field =& $this->fields->nextItem()) {
			$frame->anchor($field);
		}

		$this->display("menu",$p4a->menu);
		$this->display("top",$toolbar);
		$this->display("main",$frame);
	}

	function getName()
	{
		return $this->_table_name;
	}

	function saveRow()
	{
		$db =& P4A_DB::singleton();
		$table =& $this->_table_name;
		$old_field_name = $this->fields->Field->getValue();
		$new_field_name = $this->fields->Field->getNewValue();
		$type =& $this->fields->Type->getNewValue();

		if ($this->data->isNew()) {
			$query = "ALTER TABLE $table ADD $new_field_name $type";
			$result = $db->query($query);
			$this->source->reload();
			$this->lastRow();
		} else {
			$query = "ALTER TABLE $table CHANGE $old_field_name $new_field_name $type";
			$this->source->reload();
			$result = $db->query($query);
		}
		$this->source->reload();
	}

	function deleteRow()
	{
		$db =& P4A_DB::singleton();
		$table =& $this->_table_name;
		$field_name = $this->fields->Field->getValue();
		$query = "ALTER TABLE $table DROP $field_name";
		$db->query($query);
		$this->source->reload();

	}
}
?>