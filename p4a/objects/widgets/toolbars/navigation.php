<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * Navigation toolbar for data source operations.
 * This toolbar has "first", "prev", "next", "last", "exit" buttons.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Navigation_Toolbar extends P4A_Toolbar
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
		$first =& $this->addButton('first', 'first');
		$first->setLabel("Go to the first element");
		$first->setAccessKey(8);

		$prev =& $this->addButton('prev', 'prev');
		$prev->setLabel("Go to the previous element");
		$prev->setAccessKey(4);

		$next =& $this->addButton('next', 'next');
		$next->setLabel("Go to the next element");
		$next->setAccessKey(6);

		$last =& $this->addButton('last', 'last');
		$last->setLabel("Go to the last element");
		$last->setAccessKey(2);

		$this->addSeparator();

		$print =& $this->addButton('print', 'print');
		$print->dropAction('onclick');
		$print->setProperty('onclick', 'window.print(); return false;');
		$print->setAccessKey("P");

		$exit =& $this->addButton('exit', 'exit', 'right');
		$exit->setLabel("Go back to the previous mask");
		$exit->setAccessKey("X");
	}

	/**
	 * @param P4A_Mask $mask
	 */
	public function setMask(P4A_Mask $mask)
	{
		$this->_mask_name = $mask->getName();

		$this->buttons->first->implement('onClick', $mask, 'firstRow');
		$this->buttons->prev->implement('onClick', $mask, 'prevRow');
		$this->buttons->next->implement('onClick', $mask, 'nextRow');
		$this->buttons->last->implement('onClick', $mask, 'lastRow');
		$this->buttons->exit->implement('onClick', $mask, 'showPrevMask');
	}
}