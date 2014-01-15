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

namespace PC;

use P4A\Mask\Base\Base;
use P4A\P4A;

/**
 * A simple editing mask built in the "standard" way.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Brands extends Base
{
    public $toolbar = null;
    public $table = null;
    public $fs_details = null;

    public function __construct()
    {
        parent::__construct();
        $p4a = p4a::singleton();

        $this->setSource($p4a->brands);
        $this->firstRow();

        $this->build("P4A\Widget\Toolbar\Full", "toolbar")
            ->setMask($this);

        $this->build("P4A\Widget\Table", "table")
            ->setSource($p4a->brands)
            ->setWidth(500)
            ->showNavigationBar();

        $this->setRequiredField("description");
        $this->table->cols->brand_id->setLabel("Brand ID");
        $this->fields->brand_id
            ->disable()
            ->setLabel("Brand ID");

        $this->build("P4A\Widget\Fieldset", "fs_details")
            ->setLabel("Brand detail")
            ->anchor($this->fields->brand_id)
            ->anchor($this->fields->description)
            ->anchor($this->fields->visible);

        $this->frame
            ->anchor($this->table)
            ->anchor($this->fs_details);

        $this
            ->display("menu", $p4a->menu)
            ->display("top", $this->toolbar)
            ->setFocus($this->fields->description);
    }
}