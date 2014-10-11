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
 * A canvas is a panel where you anchor widgets with absolute positions
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_Canvas extends P4A_Widget
{
    /**
     * @var array
     */
    protected $objects = array();

    /**
     * @var integer
     */
    protected $top = 10;

    /**
     * @var integer
     */
    protected $left = 10;

    /**
     * @var string
     */
    protected $unit = "px";

    /**
     * @var integer
     */
    protected $offset_top = 0;

    /**
     * @var integer
     */
    protected $offset_left = 0;

    /**
     * @param P4A_Object $object
     * @param integer $top
     * @param integer $left
     * @return P4A_Canvas
     */
    public function anchor(&$object, $top, $left = 0)
    {
        $this->objects[] = array($object, $top, $left);
        return $this;
    }

    /**
     * @param integer $top
     * @param integer $left
     * @return P4A_Canvas
     */
    public function setOffset($top, $left)
    {
        $this->offset_top += $top;
        $this->offset_left += $left;
        return $this;
    }

    /**
     * @param integer $top
     * @param integer $left
     * @param string $unit
     * @return P4A_Canvas
     */
    public function defineGrid($top = 10, $left = 10, $unit = 'px')
    {
        $this->top = $top;
        $this->left = $top;
        $this->unit = $unit;
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

        $class = $this->composeStringClass();
        $actions = $this->composeStringActions();
        $properties = $this->composeStringProperties();
        $string = "<div id='$id' $properties $actions $class>";
        foreach (array_keys($this->objects) as $key) {
            if (is_object($this->objects[$key][0])) {
                $top = ($this->objects[$key][1] * $this->top) + $this->offset_top;
                $left = ($this->objects[$key][2] * $this->left) + $this->offset_left;
                $unit = $this->unit;

                $string .= "<div id='$id' style='position:absolute;top:{$top}{$unit};left:{$left}{$unit};'>\n";
                $string .= $this->objects[$key][0]->getAsString() . "\n";
                $string .= "</div>\n\n";
                unset($object);
            }
        }
        return "$string</div>";
    }
}