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

class Categories extends P4A_Mask
{
	function Categories()
	{
		$this->p4a_mask();
		$p4a =& p4a::singleton();

		$this->build("p4a_message", "message");
		$this->message->setWidth("300");

		$p4a->categories->firstRow();
		$this->setSource($p4a->categories);

		$this->fields->category_id->disable();

		$this->build("p4a_standard_toolbar", "toolbar");
		$this->toolbar->setMask($this);

		$this->build("p4a_table", "table");
		$this->table->setSource($p4a->categories);
		$this->table->showNavigationBar();
		$this->table->setWidth(500);

		$this->build("p4a_frame", "sheet");
		$this->sheet->setWidth(700);
		$this->sheet->anchorCenter($this->message);
		$this->sheet->anchor($this->table);

		$this->fields->category_id->setLabel("Category ID");
		$this->table->cols->category_id->setLabel("Category ID");
		$this->table->showNavigationBar();

		$this->build("p4a_fieldset", "fields_sheet");
		$this->fields_sheet->setTitle("Category detail");
		$this->fields_sheet->anchor($this->fields->category_id);
		$this->fields_sheet->anchor($this->fields->description);
		$this->fields_sheet->anchor($this->fields->visible);

 		$this->sheet->anchor($this->fields_sheet);

		//Mandatory Fields
	    $this->mf = array("description");
		foreach($this->mf as $mf){
			$this->fields->$mf->label->setFontWeight("bold");
		}

		$this->display("menu", $p4a->menu);
		$this->display("top", $this->toolbar);
		$this->display("main", $this->sheet);

		$this->setFocus($this->fields->description);
	}

	function saveRow()
	{
		$errors = array();

		foreach ($this->mf as $field) {
			if (strlen($this->fields->$field->getNewValue()) == 0) {
				$errors[] = $field;
			}
		}

		if (sizeof($errors) > 0) {
			$this->message->setValue("Please fill all required fields");

			foreach ($errors as $field) {
				$this->fields->$field->setStyleProperty("border", "1px solid red");
			}
		} else {
			parent::saveRow();
		}
	}

	function main()
	{
		parent::main();

		foreach ($this->mf as $field) {
			$this->fields->$field->unsetStyleProperty("border");
		}
	}
}