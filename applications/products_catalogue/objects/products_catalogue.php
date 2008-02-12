<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class Products_Catalogue extends P4A
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
		$this->build("p4a_menu", "menu");
		$this->menu->addItem("products");
		$this->menu->items->products->setAccessKey("r");
		$this->menu->items->products->implementMethod("onclick", $this, "menuClick");

		$this->menu->addItem("support_tables", "Support Tables");

		$this->menu->items->support_tables->addItem("categories");
		$this->menu->items->support_tables->items->categories->implementMethod("onclick", $this, "menuClick");

		$this->menu->items->support_tables->addItem("brands");
		$this->menu->items->support_tables->items->brands->implementMethod("onclick", $this, "menuClick");

		// Data sources
		$this->build("p4a_db_source", "brands");
		$this->brands->setTable("brands");
		$this->brands->addOrder("description");
		$this->brands->load();

		$this->build("p4a_db_source", "categories");
		$this->categories->setTable("categories");
		$this->categories->addOrder("description");
		$this->categories->load();

		// Primary action
		$this->openMask("P4A_Login_Mask");
		$this->active_mask->implementMethod('onLogin', $this, 'login');
		$this->loginInfo();
	}

	public function menuClick()
	{
		$this->openMask($this->active_object->getName());
	}
	
	public function login()
	{
		$username = $this->active_mask->username->getNewValue();
		$password = $this->active_mask->password->getNewValue();
		
		if ($username == "p4a" and $password == md5("p4a")) {
			$this->openMask("products");
		} else {
			$this->message("Login failed", "warning");
			$this->loginInfo();
		}
	}
	
	protected function loginInfo()
	{
		$this->message('To login type:<br />username: p4a<br />password: p4a', 'info');
	}
}