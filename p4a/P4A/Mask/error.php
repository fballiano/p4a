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

namespace P4A\Mask;

use P4A\P4A;

/**
 * General errors mask.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
final class Error extends Mask
{
    /**
     * @var P4A_Box
     */
    protected $box = null;

    /**
     * @var P4A_Frame
     */
    public $frame = null;

    /**
     * @var P4A_Button
     */
    public $restart_button = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTitle("Error");

        $this->build("P4A\Widget\Frame", "frame");

        $this->build("P4A\Widget\Box", "box")
            ->setStyleProperty("border", "1px solid #c6d3de")
            ->setStyleProperty("padding", "10px");

        $this->build("P4A\Widget\Button", "restart_button")
            ->setLabel("restart application")
            ->addAction("onclick");

        $this->frame
            ->anchor($this->box)
            ->newRow()
            ->anchorCenter($this->restart_button);

        $this->display("main", $this->frame);
    }

    public function main()
    {
        parent::main();
        P4A::singleton()->close();
    }

    /**
     * @param string $html
     * @return P4A_Error_Mask
     */
    public function setMessage($html)
    {
        $this->box->setHTML($html);
        return $this;
    }
}