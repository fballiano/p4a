<?php

class Products_Catalogue extends P4A
{
	function &Products_Catalogue()
	{
		parent::p4a();

		// Menu
		$this->build("p4a_menu", "menu");
		$this->menu->addItem("products", "P&roducts");
		$this->intercept($this->menu->items->products, "onClick", "menuClick");

		$this->menu->addItem("support_tables", "S&upport Tables");

		$this->menu->items->support_tables->addItem("categories");
		$this->intercept($this->menu->items->support_tables->items->categories,
						"onClick", "menuClick");

		$this->menu->items->support_tables->addItem("brands");
		$this->intercept($this->menu->items->support_tables->items->brands,
						"onClick", "menuClick");

		// Data sources
		$this->build("p4a_db_source", "brands");
		$this->brands->setTable("brands");
		$this->brands->setPk("brand_id");
		$this->brands->addOrder("description");
		$this->brands->load();
		$this->brands->fields->brand_id->setSequence("brands");

		$this->build("p4a_db_source", "categories");
		$this->categories->setTable("categories");
		$this->categories->setPk("category_id");
		$this->categories->addOrder("description");
		$this->categories->load();
		$this->categories->fields->category_id->setSequence("categories");

		// Primary action
		$this->openMask("products");
	}

	function menuClick()
	{
		$this->openMask($this->active_object->getName());
	}
}
?>