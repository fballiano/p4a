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

namespace P4A\Mask\Base;

/**
 * A quick way to edit a database table.
 * This mask automatically creates a P4A_DB_Source (if you don't pass your one)
 * than creates a P4A_Table, a P4A_Full_Toolbar, a P4A_Fieldset (with all fields
 * anchored inside itself) and sets the focus on the first available field.<br />
 * The table will have a 500px default width.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class SimpleEdit extends Base
{
    /**
     * @var P4A_DB_Source
     */
    public $source = null;

    /**
     * @var P4A_Full_Toolbar
     */
    public $toolbar = null;

    /**
     * @var P4A_Table
     */
    public $table = null;

    /**
     * P4A_Fieldset
     */
    public $fieldset = null;

    /**
     * @param string|P4A_DB_Source $source Table name or P4A_DB_Source object
     */
    public function __construct($source = null)
    {
        parent::__construct();

        if ($source === null) {
            $source = get_class($this);
        }

        $this->constructSimpleEdit($source);
    }
}