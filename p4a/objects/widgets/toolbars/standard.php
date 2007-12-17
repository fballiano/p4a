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
	 * Standard toolbar for data source operations.
	 * This toolbar has "confirm", "cancel", "first", "prev", "next", "last", "new", "delete", "exit" buttons.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 * @see P4A_Toolbar
	 */
	class P4A_Standard_Toolbar extends P4A_Toolbar
	{
		/**
		 * Class costructor.
		 * @param string				Mnemonic identifier for the object.
		 * @param mask					The mask on wich the toolbar will operate.
		 * @access private
		 */
		function P4A_Standard_Toolbar($name)
		{
			parent::P4A_Toolbar($name);
			$this->addDefaultButtons();
		}
		
		function addDefaultButtons()
		{
			$new =& $this->addButton('new', 'new');
			$new->setValue("Insert a new element");
			$new->setProperty("accesskey", "N");
			
			$save =& $this->addButton('save', 'save');
			$save->setValue("Confirm and save");
			$save->setAccessKey("S");

			$cancel =& $this->addButton('cancel', 'cancel');
			$cancel->setValue("Cancel current operation");
			$cancel->setAccessKey("Z");
			
			$this->addSeparator();
			
			$this->addButton('delete', 'delete');
			$this->buttons->delete->setValue("Delete current element");
			$this->buttons->delete->requireConfirmation();
			
			$this->addSeparator();

			$first =& $this->addButton('first', 'first');
			$first->setValue("Go to the first element");
			$first->setAccessKey(8);

			$prev =& $this->addButton('prev', 'prev');
			$prev->setValue("Go to the previous element");
			$prev->setAccessKey(4);

			$next =& $this->addButton('next', 'next');
			$next->setValue("Go to the next element");
			$next->setAccessKey(6);

			$last =& $this->addButton('last', 'last');
			$last->setValue("Go to the last element");
			$last->setAccessKey(2);

			$this->addSeparator();

			$print =& $this->addButton('print', 'print');
			$print->dropAction('onclick');
			$print->setProperty('onclick', 'window.print(); return false;');
			$print->setAccessKey("P");

			$exit =& $this->addButton('exit', 'exit', 'right');
			$exit->setValue("Close mask");
			$exit->setAccessKey("X");
		}

		function setMask(&$mask)
		{
			$this->_mask_name = $mask->getName();

			$this->buttons->save->implementMethod('onClick', $mask, 'saveRow');
			$this->buttons->cancel->implementMethod('onClick', $mask, 'reloadRow');
			$this->buttons->first->implementMethod('onClick', $mask, 'firstRow');
			$this->buttons->prev->implementMethod('onClick', $mask, 'prevRow');
			$this->buttons->next->implementMethod('onClick', $mask, 'nextRow');
			$this->buttons->last->implementMethod('onClick', $mask, 'lastRow');
			$this->buttons->new->implementMethod('onClick', $mask, 'newRow');
			$this->buttons->delete->implementMethod('onClick', $mask, 'deleteRow');
			$this->buttons->exit->implementMethod('onClick', $mask, 'showPrevMask');
		}

		function getAsString()
		{
			$mask =& p4a_mask::singleton($this->_mask_name);

			if ($mask->data->isNew() and $mask->data->getNumRows() > 0) {
				$this->buttons->first->enable(FALSE);
				$this->buttons->prev->enable(FALSE);
				$this->buttons->next->enable(FALSE);
				$this->buttons->last->enable(FALSE);
				$this->buttons->new->enable(FALSE);
				$this->buttons->delete->enable(FALSE);
			} else {
				$this->buttons->first->enable(TRUE);
				$this->buttons->prev->enable(TRUE);
				$this->buttons->next->enable(TRUE);
				$this->buttons->last->enable(TRUE);
				$this->buttons->new->enable(TRUE);
				$this->buttons->delete->enable(TRUE);
			}

			return parent::getAsString();
		}
	}