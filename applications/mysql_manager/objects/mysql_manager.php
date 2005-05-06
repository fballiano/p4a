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

class mysql_manager extends p4a
{
    function &mysql_manager()
    {
        $this->p4a();
        $this->openMask("db_configuration");
    }

    function &main()
    {
        if (isset($this->dsn)){
            define("P4A_DSN", $this->dsn);
        }
        parent::main();
    }

    function maskOpenClick(&$object)
    {
        $name = $object->getName();
        if (!isset($this->masks->$name)) {
			$this->masks->build('p4a_auto_mask', $name);
        }
        $this->openMask($name);
    }

	function tableOpenClick(&$object)
    {
        $name = $object->getName();
        if (!isset($this->masks->$name)) {
			$this->masks->build('P4A_Table_Mask', $name);
        }
        $this->openMask($name);
    }

	function editMask()
	{
		$mask =& $this->active_mask;
		$table =& $mask->table;
		$table->setStyleProperty("border", "1px dashed red");
		$table->setVisibleCols();
		while ($col =& $table->cols->nextItem()) {
			$col->setStyleProperty("border", "1px dashed red");
			$col->setStyleProperty("vertical-align", "bottom");
			$col->setVisible();
			$col->addAction("onClick", "void");
 			$mask->intercept($col, "void", "editWidget");
		}


		$fields =& $this->active_mask->fields;
		while($field =& $fields->nextItem()){
			$field->setStyleProperty("border", "1px dashed red");
			$field->setStyleProperty("cursor", "pointer");
			$field->setVisible();
			$field->setNewValue("");
			$field->addAction("onClick");
			$mask->intercept($field, "onClick", "editWidget");
			$field->enable();
		}
		$mask->info->setValue("Click on the red areas to edit widgets");
	}

	function createTable()
	{
		$this->openMask("My_Create_Table");
	}
}
?>