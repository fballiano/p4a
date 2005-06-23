<?php
class P4A_Base_Application extends p4a
{
	var $user_data = array();

	function &P4A_Base_Application()
	{
		$this->p4a();
		$this->openMask("p4a_login");

		$access_levels =& $this->build("p4a_array_source","access_levels");
		$a = array();
		for($i=1;$i<=10;$i++) {
			$a[]["value"] = $i;
		}
		$access_levels->load($a);
		$access_levels->setPk("value");
	}

	function createMenu()
	{
		if ( isset($this->menu) AND is_object($this->menu)) {
			$this->menu->destroy();
		}
		$menu =& $this->build("p4a_menu","menu");

		$db =& P4A_DB::singleton();
		$items = $db->getAll("SELECT a.*, b.name AS parent_name, b.position AS parent_position
							  FROM menu AS a
						      LEFT JOIN menu AS b on (a.parent_id=b.id)
							  ORDER BY parent_position,position,parent_name,name");

		foreach ($items as $item) {
			$parent_name = $item["parent_name"];
			$name  = $item["name"];
			$label = $item["label"];

			if (strlen($parent_name)) {
				$item_obj =& $menu->items->$parent_name->addItem($name);
			} else {
				$item_obj =& $menu->addItem($name);
			}
			$item_obj->setLabel($label);
			if (	$this->user_data['level'] < $item['access_level']
				or $item['visible'] == FALSE) {
				$item_obj->setVisible(FALSE);	
			}

			if (strlen($item['action'])) {
				$this->intercept($item_obj,'onClick',$item['action']);
			}
		}
		$menu->addItem("logout");
		$this->intercept($menu->items->logout,"onClick","restart");
	}

	function openMask($name)
	{
		if (is_object($name)) {
			parent::openMask($name->getName());
		} else {
			parent::openMask($name);
		}
	}
}
?>