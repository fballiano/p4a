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

/**
 * This mask is useful when you've to deal with authentication.
 * Note that all actions set here are AJAX actions, this was decided
 * because we don't want the mask to be redesigned when the user hits
 * return or the GO button (focus loss is a problem we want to avoid).
 * Due to this choice you've to remember that when you're opening a new
 * mask after upon the completition of the login, you'll have to call
 * the mask's main() method.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
final class Login extends Mask
{
    /**
     * P4A_Frame
     */
    public $frame = null;

    /**
     * @var P4A_Field
     */
    public $username = null;

    /**
     * @var P4A_Field
     */
    public $password = null;

    /**
     * @var P4A_Button
     */
    public $go = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTitle('Login');

        $this->build('P4A\Widget\Field', 'username')
            ->addAction('onreturnpress')
            ->addAjaxAction('onreturnpress')
            ->implement('onreturnpress', $this, 'login');

        $this->build('P4A\Widget\Field', 'password')
            ->setType('password')
            ->addAjaxAction('onreturnpress')
            ->implement('onreturnpress', $this, 'login');

        $this->build('P4A\Widget\Button', 'go')
            ->addAjaxAction('onclick')
            ->implement('onclick', $this, 'login');

        $this->build('P4A\Widget\Frame', 'frame')
            ->setStyleProperty('margin-top', '50px')
            ->setStyleProperty('margin-bottom', '50px')
            ->anchor($this->username)
            ->anchor($this->password)
            ->anchorCenter($this->go);

        $this
            ->display('main', $this->frame)
            ->setFocus($this->username);
    }

    public function login()
    {
        $this->actionHandler('onLogin');
    }
}