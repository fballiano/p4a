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
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Line extends Widget
{
    /**
     * @param string $name Mnemonic identifier for the object
     * @param string $id Object ID, if not specified will be generated
     */
    public function __construct($name, $id = null)
    {
        $prefix = 'line';

        if ($id === null) {
            parent::__construct($name, $prefix);
        } else {
            parent::__construct($name, $prefix, $id);
        }
    }

    /**
     * HTML rendered link
     * @return string
     */
    public function getAsString()
    {
        $id = $this->getId();
        if (!$this->isVisible()) {
            return "<div id='$id' class='hidden'></div>";
        }

        $class = $this->composeStringClass();
        $properties = $this->composeStringProperties();
        $actions = $this->composeStringActions();
        return "<hr id='$id' $class $properties $actions />";
    }
}