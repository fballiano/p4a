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
 * @package P4A_Base_Application
 */

class P4A_Menu_Mask extends P4A_Mask
{
	function P4A_Menu_Mask()
	{
		$p4a =& P4A::singleton();
		
		$this->p4a_mask();
		$this->setTitle("Menu Configuration");

		$positions =& $this->build("p4a_array_source","positions");
		$a = array();
		for($i=1;$i<=50;$i++) {
			$a[]["value"] = $i;
		}
		$positions->load($a);
		$positions->setPk("value");

		$source =& $this->build("p4a_db_source","source");
		$source->setTable("menu");
		$source->addJoin("menu AS b","menu.parent_id=b.id","left");
 		$source->setFields(array("menu.*"=>"*","b.name"=>"parent_name","b.position"=>"parent_position","concat_ws('->',b.label,menu.label)"=>"menu"));
		$source->addOrder("parent_position");
		$source->addOrder("position");
		$source->addOrder("parent_name");
		$source->addOrder("name");
		$source->setPk("id");
		$source->load();
		$source->fields->id->setSequence("p4a_menu_id");
		$source->fields->access_level->setDefaultValue(1);
		$source->fields->visible->setDefaultValue(1);
		$source->fields->action->setDefaultValue("openMask");
 		$source->firstRow();

 		$parents =& $this->build("p4a_db_source","parents");
		$parents->setTable("menu");
		$parents->setWhere("parent_id IS NULL");
		$parents->addOrder("position");
		$parents->addOrder("label");
		$parents->setPk("id");
		$parents->addFilter("id != ?",$source->fields->id);
		$parents->load();

		$this->setSource($source);

		$f =& $this->fields;

		$f->parent_id->setLabel("Parent");
		$f->parent_id->setType("select");
		$f->parent_id->allowNull("");
		$f->parent_id->setSource($parents);
		$f->parent_id->setSourceDescriptionField("label");
		$f->position->setType("select");
		$f->position->setSource($positions);
		$f->visible->setType("checkbox");
		$f->access_level->setType("select");
		$f->access_level->setSource($p4a->access_levels);


		$table =& $this->build("p4a_table","table");
		$table->setSource($source);
		$table->setVisibleCols(array("menu","position", "access_level"));
		$table->setWidth(500);

		$frm =& $this->build("p4a_frame","frm");
		$frm->setWidth(600);

		$fls =& $this->build("p4a_fieldset","fls");
		$fls->setTitle("Detail");
		$fls->setWidth(300);

		$fls->anchor($f->parent_id);
		$fls->anchor($f->name);
		$fls->anchor($f->label);
		$fls->anchor($f->position);
		$fls->anchor($f->visible);
		$fls->anchor($f->access_level);
		$fls->anchor($f->action);

		$frm->anchor($table);
		$frm->anchor($fls);

		$toolbar =& $this->build("p4a_simple_toolbar","toolbar");
		$toolbar->setMask($this);
		
		$this->display("menu",$p4a->menu);
		$this->display("top",$toolbar);
		$this->display("main",$frm);

		
		// SELECT a.*, b.name AS parent_name, b.position AS parent_position, concat_ws('->',parent_name,name) as complete_name FROM menu AS a LEFT JOIN menu AS b on (a.parent_id=b.id) ORDER BY parent_position,position,parent_name,name);
	}

	function saveRow()
	{
		$f =& $this->fields;
		$label = trim($f->label->getNewValue());
		if ($label == "") {
			$label = $f->name->getNewValue();
			$label = $this->menuLabel($label);
			$f->label->setValue($label);
		}

		parent::saveRow();
	}

	function menuLabel($value)
	{
		$value = str_replace("_"," ",$value);
		$value = ucwords($value);
		return $value;
	}
}
?>