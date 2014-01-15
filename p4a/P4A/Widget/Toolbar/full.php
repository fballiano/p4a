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

namespace P4A\Widget\Toolbar;

use P4A\Widget\Widget;

/**
 * Standard toolbar for data source operations.
 * This toolbar has "confirm", "cancel", "first", "prev", "next", "last", "new", "delete", "exit" buttons.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Full extends Toolbar
{
    /**
     * @param string $name Mnemonic identifier for the object
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->addDefaultButtons();
    }

    protected function addDefaultButtons()
    {
        $this->addButton('new', 'actions/document-new')
            ->setLabel("Insert a new element")
            ->setProperty("accesskey", "N");

        $this->addButton('save', 'actions/document-save')
            ->setLabel("Confirm and save")
            ->setAccessKey("S");

        $this->addButton('cancel', 'actions/edit-undo')
            ->setLabel("Cancel current operation")
            ->setAccessKey("Z");

        $this->addSeparator();

        $this->addButton('delete', 'actions/edit-delete')
            ->setLabel("Delete current element")
            ->addAction("onclick")
            ->requireConfirmation();

        $this->addSeparator();

        $this->addButton('first', 'actions/go-first')
            ->setLabel("Go to the first element")
            ->setAccessKey(8);

        $this->addButton('prev', 'actions/go-previous')
            ->setLabel("Go to the previous element")
            ->setAccessKey(4);

        $this->addButton('next', 'actions/go-next')
            ->setLabel("Go to the next element")
            ->setAccessKey(6);

        $this->addButton('last', 'actions/go-last')
            ->setLabel("Go to the last element")
            ->setAccessKey(2);

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
     * @return P4A_Full_Toolbar
     */
    public function setMask(\P4A\Mask\Mask $mask)
    {
        $this->buttons->save->implement('onclick', $mask, 'saveRow');
        $this->buttons->cancel->implement('onclick', $mask, 'reloadRow');
        $this->buttons->first->implement('onclick', $mask, 'firstRow');
        $this->buttons->prev->implement('onclick', $mask, 'prevRow');
        $this->buttons->next->implement('onclick', $mask, 'nextRow');
        $this->buttons->last->implement('onclick', $mask, 'lastRow');
        $this->buttons->new->implement('onclick', $mask, 'newRow');
        $this->buttons->delete->implement('onclick', $mask, 'deleteRow');
        $this->buttons->exit->implement('onclick', $mask, 'showPrevMask');
        return $this;
    }
}