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

namespace P4A\Widget;

/**
 * p4a menu system
 * As in every big IDE such as Sun ONE or Microsoft Visual Studio
 * you have the possibility to add the top menu for simple
 * organization of masks.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Menu extends Widget
{
    /**
     * Menu elements
     * @var P4A_Collection
     */
    public $items = null;

    /**
     * The element/subelement currently active
     * @var P4A_Menu_Item
     */
    protected $_active_item = null;

    /**
     * @param string $name Mnemonic identifier for the object
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->build("P4A\Collection", "items");
    }

    /**
     * Adds an element to the menu
     * @param string $name Mnemonic identifier for the element
     * @param string $label
     * @return P4A_Menu_Item
     */
    public function addItem($name, $label = null)
    {
        $item = $this->items->build("P4A\Widget\MenuItem", $name);
        if ($label !== null) {
            $item->setLabel($label);
        }
        return $item;
    }

    /**
     * Removes an element from the menu
     * @param string $name
     * @return P4A_Menu
     */
    public function dropItem($name)
    {
        if (isset($name, $this->items->$name)) {
            $this->items->$name->destroy();
            unset($this->items->$name);
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasItems()
    {
        if ($this->items->getNumItems()) {
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @return P4A_Menu_Item
     */
    protected function setActiveItem($name)
    {
        $this->_active_item = $name;
        return $this;
    }

    /**
     * Returns the active item name
     * @return string
     */
    public function getActiveItem()
    {
        return $this->_active_item;
    }

    /**
     * Returns the HTML rendered widget
     * @return string
     */
    public function getAsString()
    {
        $id = $this->getId();
        if (!$this->isVisible()) {
            return "<div id='$id' class='hidden'></div>";
        }

        $sReturn = "";
        if ($this->hasItems()) {
            $sReturn .= "<ul class='sidebar-menu'>";
            while ($item = $this->items->nextItem()) {
                $sReturn .= $item->getAsString();
            }
            $sReturn .= "</ul>";
        }
        return $sReturn;
    }
}