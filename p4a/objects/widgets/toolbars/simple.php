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

/**
 * Simple toolbar.
 * This toolbar has "confirm", "cancel", "print", "exit" buttons.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 * @see P4A_Toolbar
 */
class P4A_Simple_Toolbar extends P4A_Toolbar
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
		$this->addButton('new', 'new');
		$this->buttons->new->setLabel("Insert a new element");
		$this->buttons->new->setProperty("accesskey", "N");
		
		$this->addButton('save', 'save');
		$this->buttons->save->setLabel("Confirm and save");
		$this->buttons->save->setAccessKey("S");

		$this->addButton('cancel', 'cancel');
		$this->buttons->cancel->setLabel("Cancel current operation");
		$this->buttons->cancel->setAccessKey("Z");

		$this->addSeparator();

		$this->addButton('delete', 'delete');
		$this->buttons->delete->setLabel("Delete current element");
		$this->buttons->delete->requireConfirmation();

		$this->addSeparator();

		$this->addButton('print', 'print');
		$this->buttons->print->dropAction('onclick');
		$this->buttons->print->setProperty('onclick', 'window.print(); return false;');
		$this->buttons->print->setAccessKey("P");

		$this->addButton('exit', 'exit', 'right');
		$this->buttons->exit->setLabel("Go back to the previous mask");
		$this->buttons->exit->setAccessKey("X");
	}

	/**
	 * @param P4A_Mask $mask
	 */
	public function setMask(P4A_Mask $mask)
	{
		$this->_mask_name = $mask->getName();

		$this->buttons->save->implementMethod('onClick', $mask, 'saveRow');
		$this->buttons->cancel->implementMethod('onClick', $mask, 'reloadRow');
		$this->buttons->new->implementMethod('onClick', $mask, 'newRow');
		$this->buttons->delete->implementMethod('onClick', $mask, 'deleteRow');
		$this->buttons->exit->implementMethod('onClick', $mask, 'showPrevMask');
	}
}