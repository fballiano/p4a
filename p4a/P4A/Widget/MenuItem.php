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

use P4A\P4A;

/**
 * Rapresents every menu item
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class MenuItem extends Widget
{
    /**
     * Subelements collection
     * @var P4A_Collection
     */
    public $items = null;

    /**
     * The element/subelement currently active
     * @var P4A_Menu_Item
     */
    protected $_active_item = null;

    /**
     * Icon associated to the element
     * @var string
     * @access private
     */
    protected $_icon = null;

    /**
     * @param string        Mnemonic identifier for the object.
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setDefaultLabel();
        $this->addAction('onclick');
        $this->build("P4A\Collection", "items");
    }

    /**
     * Adds an element to the element
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
     * Removes an element from the current element
     * @param string $name Mnemonic identifier for the element
     * @return P4A_Menu_Item
     */
    public function dropItem($name)
    {
        if (isset($this->items->$name)) {
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
     * Sets the desidered subelement as active
     * @param string $name
     * @return P4A_Menu_Item
     */
    protected function setActiveItem($name)
    {
        $this->_active_item = $name;
        $this->setActive();
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
     * @param string $icon
     * @return P4A_Menu_Item
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->_icon;
    }

    /**
     * What is executed on a click on the element.
     * If the current element has subitems,
     * than we pass the action to the subitem.
     */
    public function onClick($params = null)
    {
        return $this->actionHandler('onclick');
    }

    /**
     * Renders HTML
     * @access public
     * @return string
     */
    public function getAsString()
    {
        $id = $this->getId();
        if (!$this->isVisible()) {
            return "<div id='$id' class='hidden'></div>";
        }

        $has_items = $this->hasItems() ? "class='p4a_menu_has_items'" : '';
        $properties = $this->composeStringProperties();
        if (P4A::singleton()->isHandheld()) {
            $icon = '';
        } else {
            $icon = $this->getIcon();
            if (strlen($icon)) {
                if (strpos($this->_icon, '.') === false) {
                    $icon_disabled = '';
                    if (!$this->isEnabled()) {
                        $icon_disabled = '_disabled';
                    }
                    $icon = P4A_ICONS_PATH . "/16/{$icon}{$icon_disabled}." . P4A_ICONS_EXTENSION;
                }
                $icon = "<img src='$icon' alt='' />";
            }
        }

        if (empty($this->_map_actions["onclick"]["method"]) or !$this->isEnabled()) {
            $sReturn = "<li $has_items>" . P4A_Generate_Widget_Layout_Table(
                    $icon,
                    "<div $properties>" . __($this->getLabel()) . "</div>"
                );
        } else {
            $actions = $this->composeStringActions();
            $sReturn = "<li $has_items>" . P4A_Generate_Widget_Layout_Table(
                    $icon,
                    "<a href='#' $actions $properties>" . P4A_Highlight_AccessKey(
                        __($this->getLabel()),
                        $this->getAccessKey()
                    ) . "</a>"
                );
        }

        if ($has_items) {
            $sReturn .= "<ul>";
            while ($item = $this->items->nextItem()) {
                $sReturn .= $item->getAsString();
            }
            $sReturn .= "</ul>";
        }
        $sReturn .= "</li>";
        return $sReturn;
    }
}