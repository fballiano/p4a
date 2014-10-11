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
 * A toolbar is a buttons/images set.
 * Every button/image can have an action handler.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Toolbar extends Widget
{
    /**
     * Counts the number of separators/spacers in the toolbar
     * @var integer
     */
    protected $separators_counter = 0;

    /**
     * @var integer
     */
    protected $_size = 32;

    /**
     * Buttons collection
     * @var array
     */
    public $buttons = null;

    /**
     * @param string $name Mnemonic identifier for the object
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->build("P4A\Collection", "buttons");
    }

    /**
     * Istances a new p4a_button object and than adds it to the toolbar
     * @param string $button_name Mnemonic identifier for the object
     * @param string $icon The icon taken from icon set (file name without extension)
     * @param string $position (left|right)
     * @see P4A_Button
     */
    public function addButton($button_name, $icon = null, $position = "left")
    {
        $this->buttons->build("P4A\Widget\Button", $button_name);
        $this->buttons->$button_name->setIcon($icon);
        $this->buttons->$button_name->setStyleProperty("float", $position);
        if ($this->_size) {
            $this->buttons->$button_name->setSize($this->_size);
        }
        return $this->buttons->$button_name;
    }

    /**
     * Adds a separator image
     * @param string $position (left|right)
     * @return P4A_Image
     */
    public function addSeparator($position = "left")
    {
        $name = 's' . $this->separators_counter++;
        return $this->addBox($name, '', $position)
            ->addCSSClass("p4a_toolbar_separator")
            ->setHeight($this->getSize());
    }

    /**
     * Adds a P4A_Box object
     * @see P4A_Box
     * @param string $name
     * @param string $text
     * @param string $position (left|right)
     * @return P4A_Box
     */
    public function addBox($name, $text, $position = "left")
    {
        $this->buttons->build("P4A\Widget\Box", $name);
        $this->buttons->$name->setHTML($text);
        $this->buttons->$name->setStyleProperty("float", $position);
        return $this->buttons->$name;
    }

    /**
     * Adds a spacer image of the desidered width
     * @param integer $width
     * @param string $position (left|right)
     * @return P4A_Image
     */
    public function addSpacer($width = 10, $position = "left")
    {
        $name = 's' . $this->separators_counter++;
        $this->buttons->build("p4a_image", $name);
        $this->buttons->$name->setStyleProperty("float", $position);
        $this->buttons->$name->setIcon(P4A_ICONS_PATH . "/spacer." . P4A_ICONS_EXTENSION);
        $this->buttons->$name->setWidth($width);
        $this->buttons->$name->setHeight(1);
        return $this->buttons->$name;
    }

    /**
     * Disables all buttons
     * @return P4A_Toolbar
     */
    public function disable()
    {
        while ($button = $this->buttons->nextItem()) {
            $button->disable();
        }
        return $this;
    }

    /**
     * Enable/disable all buttons
     * @param boolean $enabled
     * @return P4A_Toolbar
     */
    public function enable($enabled = true)
    {
        while ($button = $this->buttons->nextItem()) {
            $button->enable($enabled);
        }
        return $this;
    }

    /**
     * @param integer $size
     * @return P4A_Toolbar
     */
    public function setSize($size)
    {
        $this->_size = $size;

        while ($button = $this->buttons->nextItem()) {
            if (method_exists($button, "setSize")) {
                $button->setSize($size);
            } elseif (method_exists($button, "setHeight")) {
                $button->setHeight($size);
            } else {
                trigger_error(
                    get_class($button) . " object in P4A_Toolbar does not have setSize or setHeight methods",
                    E_USER_WARNING
                );
            }
        }
        return $this;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->_size;
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

        $size = $this->getSize();
        $properties = $this->composeStringProperties();
        $class = $this->composeStringClass(array("btn-toolbar"));
        $return = "<div id='$id' $class $properties role='toolbar'><div class='btn-group'>";
        while ($button = $this->buttons->nextItem()) {
            if (in_array("p4a_toolbar_separator", $button->getCSSClasses())) {
                $return .= "</div><div class='btn-group'>";
                continue;
            }

            $return .= "{$button->getAsString()}";
        }
        $return .= "</div></div>\n\n";
        return $return;
    }
}