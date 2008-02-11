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
class Categories extends P4A_Base_Mask
{
	public function __construct()
	{
		parent::__construct();
		$p4a = p4a::singleton();

		$this->setSource($p4a->categories);
		$this->firstRow();

		$this->setRequiredField("description");
		$this->fields->category_id->disable();

		$this->build("p4a_full_toolbar", "toolbar");
		$this->toolbar->setMask($this);

		$this->build("p4a_table", "table");
		$this->table->setSource($p4a->categories);
		$this->table->showNavigationBar();
		$this->table->setWidth(500);
		$this->frame->anchor($this->table);

		$this->fields->category_id->setLabel("Category ID");
		$this->table->cols->category_id->setLabel("Category ID");
		$this->table->showNavigationBar();

		$this->build("p4a_fieldset", "fs_details");
		$this->fs_details->setLabel("Category detail");
		$this->fs_details->anchor($this->fields->brand_id);
		$this->fs_details->anchor($this->fields->description);
		$this->fs_details->anchor($this->fields->visible);
 		$this->frame->anchor($this->fs_details);

		$this->display("menu", $p4a->menu);
		$this->display("top", $this->toolbar);

		$this->setFocus($this->fields->description);
	}
}