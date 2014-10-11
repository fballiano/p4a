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
 * A frame is a panel where you anchor widgets.
 * It generates tableless HTML and is used for relative positioning, this means
 * that every anchored widget is floating next to the previous one.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Frame extends Widget
{
    /**
     * @var array
     */
    protected $_map = array();

    /**
     * @var integer
     */
    protected $_row = 1;

    /**
     * @param P4A_Widget $object
     * @param integer $cols How many colums should the widget occupy?
     * @param  integer $offset How many columns left empty from the previous widget (or the left border)?
     * @return P4A_Frame
     */
    protected function _anchor($object, $cols, $offset)
    {
        if (is_object($object)) {
            $to_add = array("id" => $object->getId(), "cols" => $cols, "offset" => $offset);
            $this->_map[$this->_row][] = $to_add;
        }
        return $this;
    }

    /**
     * @param P4A_Widget $object
     * @param integer $cols How many colums should the widget occupy?
     * @param integer $offset How many columns left empty from the previous widget (or the left border)?
     * @return P4A_Frame
     */
    public function anchor($object, $cols = 1, $offset = 0)
    {
        $this->newRow();
        return $this->_anchor($object, $cols, $offset);
    }

    /**
     * @param P4A_Widget $object
     * @param string $margin
     * @return P4A_Frame
     */
    public function anchorRight($object, $cols = 1, $offset = 0)
    {
        return $this->anchorLeft($object, $cols, $offset);
    }

    /**
     * @param P4A_Widget $object
     * @param string $margin
     * @return P4A_Frame
     */
    public function anchorLeft($object, $cols = 1, $offset = 0)
    {
        return $this->_anchor($object, $cols, $offset);
    }

    public function anchorCenter($object, $cols = 1, $offset = 0)
    {
        $this->newRow();
        return $this->_anchor($object, $cols, $offset);
    }

    /**
     * @return P4A_Frame
     */
    public function clean()
    {
        $this->_map = array();
        return $this;
    }

    /**
     * @return P4A_Frame
     */
    public function newRow()
    {
        $this->_row++;
        return $this;
    }

    /**
     * @return string
     */
    public function getAsString()
    {
        $id = $this->getId();
        if (!$this->isVisible()) {
            return "<div id='$id' class='hidden'></div>";
        }

        $properties = $this->composeStringProperties();
        $actions = $this->composeStringActions();
        $class = $this->composeStringClass();

        $string = "<div id='{$id}' $class $properties $actions>";
        $string .= $this->getChildrenAsString();
        $string .= "</div>\n\n";
        return $string;
    }

    protected function getChildrenAsString()
    {
        $string = "";
        $p4a = P4A::singleton();
        $handheld = P4A::singleton()->isHandheld();
        foreach ($this->_map as $objs) {
            $one_visible = false;
            $row = array();

            foreach ($objs as $obj) {
                $classes = array();
                $object = $p4a->getObject($obj["id"]);
                if (is_object($object)) {
                    $as_string = $object->getAsString();
                } else {
                    unset($p4a->objects[$obj["id"]]);
                    unset($this->_map[$i][$j]);
                    if (empty($this->_map[$i])) {
                        unset($this->_map[$i]);
                    }
                    $as_string = '';
                }
                if (strlen($as_string) > 0) {
                    $one_visible = true;
                    $classes[] = "col-md-" . $obj["cols"];
                    if ($obj["offset"]) $classes[] = "col-md-offset-" . $obj["offset"];
                    /*
                    $float = $obj["float"];
                    $margin = "margin-" . $obj["float"];
                    $margin_value = $obj["margin"];
                    */
                    $as_string = "\n\t\t$as_string";
                    $class = empty($classes) ? '' : 'class="' . implode(' ', $classes) . '"';
                    $row .= "<div $class>$as_string</div>";
                }
            }

            $row = "\n<div class='row'>";
            $row .= "\n</div>\n";

            if ($one_visible) {
                $string .= $row;
            }
        }
        return $string;
    }
}