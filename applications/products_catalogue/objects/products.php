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

class Products extends P4A_Mask
{
	function &Products()
	{
		$this->p4a_mask();
		$p4a =& p4a::singleton();

		// DB Source
		$this->build("p4a_db_source", "source");
		$this->source->setFields(array("products.*" => "*",
									   "categories.description" => "category",
									   "brands.description" => "brand"
										));
		$this->source->setTable("products");
		$this->source->addJoin("categories",
							   "products.category_id = categories.category_id");
		$this->source->addJoin("brands", "products.brand_id = brands.brand_id");
		$this->source->setPk("product_id");
		$this->source->addOrder("product_id");
		$this->source->setPageLimit(10);
		$this->source->load();
		$this->source->fields->product_id->setSequence("product_id");

		$this->setSource($this->source);
		$this->source->firstRow();

		$this->source->fields->purchasing_price->setType("decimal");
		$this->source->fields->selling_price->setType("decimal");

		// Customizing fields properties
		$this->setFieldsProperties();
		$fields =& $this->fields;

		// Search Fieldset
		$fs_search =& $this->build("p4a_fieldset","fs_search");
		$fs_search->setTitle("Search");
		$txt_search =& $this->build("p4a_field", "txt_search");
		$txt_search->addAction("onReturnPress");
		$this->intercept($txt_search, "onReturnPress","search");
		$txt_search->setLabel("Cod. Product");
		$cmd_search =& $this->build("p4a_button","cmd_search");
		$cmd_search->setValue("Go");
		$this->intercept($cmd_search, "onClick","search");
		$fs_search->anchor($txt_search);
		$fs_search->anchorLeft($cmd_search);

		// Toolbar
		$this->build("p4a_standard_toolbar", "toolbar");
		$this->toolbar->setMask($this);

		// Table
		$table =& $this->build("p4a_table", "table");
 		$table->setWidth(700);
		$table->setSource($this->source);
		$table->setVisibleCols(array("product_id","model","category",
									 "brand"));
		$table->cols->product_id->setLabel("Cod. Product");

		while ($col =& $table->cols->nextItem()) {
			$col->setWidth(150);
		}
		$table->showNavigationBar();

		// Message
		$message =& $this->build("p4a_message", "message");
		$message->setWidth("300");


		//Fieldset con l'elenco dei campi
		$fset=& $this->build("p4a_fieldset", "frame");
		$fset->setTitle("Product details");

 		$fset->anchor($this->fields->product_id);
		$fset->anchor($this->fields->category_id);
		$fset->anchorLeft($this->fields->brand_id);
		$fset->anchor($this->fields->model);
		$fset->anchor($this->fields->purchasing_price);
 		$fset->anchor($this->fields->selling_price);
		$fset->anchorLeft($this->fields->discount);
 		$fset->anchor($this->fields->little_photo);
 		$fset->anchorLeft($this->fields->big_photo);
		$fset->anchor($this->fields->is_new);
		$fset->anchorLeft($this->fields->visible);
		$fset->anchor($this->fields->description);

		// Frame
		$frm=& $this->build("p4a_frame", "frm");
		$frm->setWidth(730);
		$frm->anchor($fs_search);
		$frm->newRow();
		$frm->anchorCenter($message);
		$frm->anchor($table);
  		$frm->anchor($fset);

		// Mandatory Fields
	    $this->mf = array("product_id", "category_id", "brand_id", "model", "purchasing_price",
 					"selling_price", "description", "discount");
		foreach($this->mf as $mf){
			$fields->$mf->label->setFontWeight("bold");
		}

		// Display
		$this->display("main", $frm);
		$this->display("menu", $p4a->menu);
		$this->display("top", $this->toolbar);
	}

	function main()
	{
		parent::main();

		foreach($this->mf as $mf){
			$this->fields->$mf->unsetStyleProperty("border");
		}
	}

	function setFieldsProperties()
	{
		$p4a =& p4a::singleton();

		$fields =& $this->fields;

		$fields->product_id->setLabel("Product ID");
		$fields->product_id->setWidth(200);
		$fields->product_id->enable(false);

		$categories =& $this->build("P4A_DB_Source","categories");
		$categories->setTable("categories");
		$categories->setPK("category_id");
		$categories->load();
		$fields->category_id->setLabel("Category");
		$fields->category_id->setWidth(200);
		$fields->category_id->setType("select");
		$fields->category_id->setSource($categories);
		$fields->category_id->setSourceDescriptionField("description");

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

		$fields->little_photo->setType("image");
		$fields->big_photo->setType("image");

		$fields->description->setType("rich_textarea");
	}

	function saveRow()
	{
		$valid = true;

		foreach($this->mf as $mf){
			$value = $this->fields->$mf->getNewValue();
			if(trim($value) === ""){
				$this->fields->$mf->setStyleProperty("border", "1px solid red");
				$valid = false;
			}
		}

		if ($valid) {
			parent::saveRow();
		}else{
			$this->message->setValue("Please fill all required fields");
		}
	}

	function search()
	{
		$value = $this->txt_search->getNewValue();
		$this->data->setWhere("model LIKE '%{$value}%'");
		$this->data->firstRow();
		$num_rows = $this->data->getNumRows();

		if (!$num_rows) {
			$this->message->setValue("No results were found");
			$this->data->setWhere(null);
			$this->data->firstRow();
		}
	}
}

?>