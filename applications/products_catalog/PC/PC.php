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

use P4A\P4A;

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class PC extends P4A
{
    /**
     * @var P4A_Menu
     */
    public $menu = null;

    /**
     * @var P4A_DB_Source
     */
    public $brands = null;

    /**
     * @var P4A_DB_Source
     */
    public $categories = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTitle("Products Catalogue");

        // Menu
        $this->build("P4A\Widget\Menu", "menu");

        $this->menu->addItem("products")
            ->setAccessKey("r")
            ->implement("onclick", $this, "menuClick");

        $this->menu->addItem("support_tables", "Support Tables");

        $this->menu->items->support_tables->addItem("categories")
            ->implement("onclick", $this, "menuClick");

        $this->menu->items->support_tables->addItem("brands")
            ->implement("onclick", $this, "menuClick");

        $this->menu->addItem("change_language")
            ->implement("onclick", $this, "openLanguagesPopup");

        // Data sources
        $this->build("P4A\DataSource\DbSource", "brands")
            ->setTable("brands")
            ->addOrder("description")
            ->load();

        $this->build("P4A\DataSource\DbSource", "categories")
            ->setTable("categories")
            ->addOrder("description")
            ->load();

        // Primary action
        $this->openMask("P4A\Mask\Login");
        $this->active_mask->implement('onLogin', $this, 'login');
        $this->active_mask->username->setTooltip("Simply type p4a");
        $this->active_mask->password->setTooltip("Type p4a here too");
        $this->loginInfo();
    }

    public function menuClick()
    {
        $this->openMask("PC\\" . $this->active_object->getName());
    }

    public function openLanguagesPopup()
    {
        $this->openPopup('PC\ChangeLanguage');
    }

    public function login()
    {
        $username = $this->active_mask->username->getNewValue();
        $password = $this->active_mask->password->getNewValue();

        if ($username == "p4a" and $password == md5("p4a")) {
            $this->messageInfo("Login successful");
            $this->openMask("PC\Brands");
        } else {
            $this->messageError("Login failed");
            $this->loginInfo();
        }
    }

    protected function loginInfo()
    {
        $this->messageInfo('To login type:<br />username: p4a<br />password: p4a', 'info');
    }
}