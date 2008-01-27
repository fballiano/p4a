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

class Products extends P4A_Base_Mask
{
	public function __construct()
	{
		parent::__construct();

		// DB Source
		$this->build("p4a_db_source", "source");
		$this->source->setTable("products");
		$this->source->addJoin("categories",
							   "products.category_id = categories.category_id",
							   array('description'=>'category'));
		$this->source->addJoin("brands", "products.brand_id = brands.brand_id",
							   array('description'=>'brand'));
		$this->source->addOrder("product_id");
		$this->source->setPageLimit(10);
		$this->source->load();

		$this->setSource($this->source);
		$this->firstRow();

		// Customizing fields properties
		$this->setFieldsProperties();

		// Search Fieldset
		$fs_search = $this->build("p4a_fieldset", "fs_search");
		$fs_search->setLabel("Search");
		$txt_search = $this->build("p4a_field", "txt_search");
		$this->intercept($txt_search, "onreturnpress", "search");
		$txt_search->setLabel("Model");
		$cmd_search = $this->build("p4a_button", "cmd_search");
		$cmd_search->setLabel("Go");
		$this->intercept($cmd_search, "onclick", "search");
		$fs_search->anchor($txt_search);
		$fs_search->anchorLeft($cmd_search);

		// Toolbar
		$this->build("p4a_full_toolbar", "toolbar");
		$this->toolbar->setMask($this);

		// Table
		$table = $this->build("p4a_table", "table");
 		$table->setWidth(700);
		$table->setSource($this->source);
		$table->setVisibleCols(array("product_id","model","category",
									 "brand"));
		$table->cols->product_id->setLabel("Cod. Product");
		while ($col = $table->cols->nextItem()) {
			$col->setWidth(150);
		}
		$table->showNavigationBar();

		$this->build("p4a_fieldset", "fs_details");
		$this->fs_details->setLabel("Product details");
 		$this->fs_details->anchor($this->fields->product_id);
		$this->fs_details->anchor($this->fields->category_id);
		$this->fs_details->anchorLeft($this->fields->brand_id);
		$this->fs_details->anchor($this->fields->model);
		$this->fs_details->anchor($this->fields->purchasing_price);
 		$this->fs_details->anchor($this->fields->selling_price);
		$this->fs_details->anchorLeft($this->fields->discount);
 		$this->fs_details->anchor($this->fields->little_photo);
 		$this->fs_details->anchorLeft($this->fields->big_photo);
		$this->fs_details->anchor($this->fields->is_new);
		$this->fs_details->anchorLeft($this->fields->visible);
		$this->fs_details->anchor($this->fields->description);

		$this->frame->anchor($fs_search);
		$this->frame->newRow();
		$this->frame->anchor($table);
  		$this->frame->anchor($this->fs_details);
  		
  		$this->addMandatoryField("product_id");
  		$this->addMandatoryField("category_id");
  		$this->addMandatoryField("brand_id");
  		$this->addMandatoryField("model");
  		$this->addMandatoryField("purchasing_price");
  		$this->addMandatoryField("selling_price");
  		$this->addMandatoryField("description");
  		$this->addMandatoryField("discount");

		// Display
		$this->display("menu", P4A::singleton()->menu);
		$this->display("top", $this->toolbar);
	}

	private function setFieldsProperties()
	{
		$p4a = p4a::singleton();
		$fields = $this->fields;

		$fields->product_id->setLabel("Product ID");
		$fields->product_id->setWidth(200);
		$fields->product_id->enable(false);

		$fields->category_id->setType("select");
		$fields->category_id->setSource($p4a->categories);
		$fields->category_id->setSourceDescriptionField("description");

		$fields->category_id->setLabel("Category");
		$fields->category_id->setWidth(200);

		$fields->brand_id->setLabel("Brand");
		$fields->brand_id->setWidth(200);
		$fields->brand_id->setType("select");
		$fields->brand_id->setSource($p4a->brands);
		$fields->brand_id->setSourceDescriptionField("description");

		$fields->model->setWidth(200);

		$fields->purchasing_price->setLabel("Purchasing price $");
		$fields->purchasing_price->setWidth("40");

		$fields->discount->setLabel("Discount %");
		$fields->discount->setWidth("40");

		$fields->selling_price->setLabel("Price $");
		$fields->selling_price->setWidth("40");

		$fields->little_photo->setType("file");
		$fields->big_photo->setType("file");

		$fields->description->setType("rich_textarea");
		$fields->description->enableUpload();
	}

	public function saveRow()
	{
		if (!$this->checkMandatoryFields()) {
			$this->warning("Please fill all required fields");
		} else {
			parent::saveRow();
		}
	}

	public function search()
	{
		$value = $this->txt_search->getSQLNewValue();
		$this->source->setWhere(P4A_DB::singleton()->getCaseInsensitiveLikeSQL('model', "%{$value}%"));
		$this->source->firstRow();
		$num_rows = $this->source->getNumRows();

		if (!$num_rows) {
			$this->warning("No results were found");
			$this->source->setWhere(null);
			$this->source->firstRow();
		}
	}
}