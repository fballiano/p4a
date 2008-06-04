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
class Products extends P4A_Base_Mask
{
	public $fs_search = null;
	public $txt_search = null;
	public $cmd_search = null;
	
	public $toolbar = null;
	public $table = null;
	public $fs_details = null;
	
	public function __construct()
	{
		parent::__construct();

		// DB Source
		$this->build("p4a_db_source", "source")
			->setTable("products")
			->addJoin("categories",
					  "products.category_id = categories.category_id",
					  array('description'=>'category'))
			->addJoin("brands", "products.brand_id = brands.brand_id",
					  array('description'=>'brand'))
			->addOrder("product_id")
			->setPageLimit(10)
			->load();

		$this->setSource($this->source);
		$this->firstRow();

		// Customizing fields properties
		$this->setFieldsProperties();

		// Search Fieldset
		$this->build("p4a_field", "txt_search")
			->setLabel("Model")
			->implement("onreturnpress", $this, "search");
		$this->build("p4a_button", "cmd_search")
			->setLabel("Go")
			->implement("onclick", $this, "search");
		$this->build("p4a_fieldset", "fs_search")
			->setLabel("Search")
			->anchor($this->txt_search)
			->anchorLeft($this->cmd_search);

		// Toolbar
		$this->build("p4a_full_toolbar", "toolbar")
			->setMask($this);

		// Table
		$this->build("p4a_table", "table")
			->setWidth(600)
			->setSource($this->source)
			->setVisibleCols(array("product_id", "model", "date_arrival", "category", "brand"))
			->showNavigationBar();
		$this->table->cols->product_id->setLabel("Product ID");

		$this->build("p4a_fieldset", "fs_details")
			->setLabel("Product detail")
 			->anchor($this->fields->product_id)
 			->anchorLeft($this->fields->model)
			->anchor($this->fields->category_id)
			->anchorLeft($this->fields->brand_id)
			->anchor($this->fields->date_arrival)
 			->anchorLeft($this->fields->price)
			->anchorLeft($this->fields->discount)
 			->anchor($this->fields->picture)
			->anchor($this->fields->is_new)
			->anchorLeft($this->fields->visible)
			->anchor($this->fields->description);

		$this->frame
			->anchor($this->fs_search)
			->anchor($this->table)
  			->anchor($this->fs_details);
  		
  		$this
  			->setRequiredField("product_id")
  			->setRequiredField("category_id")
  			->setRequiredField("brand_id")
  			->setRequiredField("model")
  			->setRequiredField("price")
  			->setRequiredField("description")
  			->setRequiredField("discount");

		// Display
		$this
			->display("menu", P4A::singleton()->menu)
			->display("top", $this->toolbar)
			->setFocus($this->fields->model);
	}

	private function setFieldsProperties()
	{
		$this->fields->product_id
			->setLabel("Product ID")
			->setWidth(198)
			->setTooltip("This ID is automatically generated and you cannot modify it")
			->enable(false);

		$this->fields->category_id
			->setType("select")
			->setSource(P4A::singleton()->categories)
			->setSourceDescriptionField("description")
			->setLabel("Category")
			->setTooltip("Choose a category from the list")
			->setWidth(200);

		$this->fields->brand_id
			->setLabel("Brand")
			->setWidth(200)
			->setTooltip("Choose a brand from the list")
			->setType("select")
			->setSource(P4A::singleton()->brands)
			->setSourceDescriptionField("description");

		$this->fields->model->setWidth(198);
		$this->fields->date_arrival->setWidth(178);

		$this->fields->discount
			->setLabel("Discount %")
			->setWidth(40);

		$this->fields->price
			->setLabel("Price $")
			->setWidth(40);

		$this->fields->picture->setType("file");

		$this->fields->description
			->setType("rich_textarea")
			->enableUpload();
	}

	public function search()
	{
		$value = $this->txt_search->getSQLNewValue();
		$this->source
			->setWhere(P4A_DB::singleton()->getCaseInsensitiveLikeSQL('model', "%{$value}%"))
			->firstRow();

		if (!$this->source->getNumRows()) {
			$this->warning("No results were found");
			$this->source->setWhere(null);
			$this->source->firstRow();
		}
	}
}