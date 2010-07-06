<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * To contact the authors write to:                                     <br />
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

/**
 * Standard toolbar for data source operations.
 * This toolbar has "confirm", "cancel", "exit" buttons.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_Actions_Toolbar extends P4A_Toolbar
{
	/**
	 * @param string $name Mnemonic identifier for the object
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->addDefaultButtons();
	}
	
	private function addDefaultButtons()
	{
		$this->addButton('save', 'actions/document-save')
			->setLabel("Confirm and save")
			->setAccessKey("S");

		$this->addButton('cancel', 'actions/edit-undo')
			->setLabel("Cancel current operation")
			->setAccessKey("Z");

		$this->addSeparator();

		$this->addButton('print', 'actions/document-print')
			->dropAction('onclick')
			->setProperty('onclick', 'window.print(); return false;')
			->setAccessKey("P");

		$this->addButton('exit', 'actions/window-close', 'right')
			->setLabel("Go back to the previous mask")
			->setAccessKey("X");
	}

	/**
	 * @param P4A_Mask $mask
	 * @return P4A_Actions_Toolbar
	 */
	public function setMask(P4A_Mask $mask)
	{
		$this->buttons->save->implement('onClick', $mask, 'saveRow');
		$this->buttons->cancel->implement('onClick', $mask, 'reloadRow');
		$this->buttons->exit->implement('onClick', $mask, 'showPrevMask');
		return $this;
	}
}