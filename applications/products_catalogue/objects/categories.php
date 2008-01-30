<?php
/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
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

	public function saveRow()
	{
		if (!$this->checkMandatoryFields()) {
			$this->warning("Please fill all required fields");
		} else {
			parent::saveRow();
		}
	}
}