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
 * To contact the authors write to:								<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)											<br>
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
 * @package P4A_DB_Navigator
 */

/**
 * This widget allows a tree navigation within a P4A_DB_Source.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_DB_Navigator
 */
class P4A_DB_Navigator extends P4A_Widget
{
	var $source = null;
	var $recursor = "";
	var $description = "";
	var $expand_all = true;

	/**
	 * The constructor
	 * @param string		The name of the widget
	 * @access public
	 */
	function P4A_DB_Navigator($name)
	{
		parent::P4A_Widget($name);
		$this->addAction("onClick");
		$this->intercept($this, "onClick", "onClick");
	}
	
	/**
	 * Sets the source of the tree, it must be a P4A_DB_Source.
	 * @param P4A_DB_Source	The DB source to navigate.
	 * @access public
	 */
	function setSource(&$source)
	{
		$this->source =& $source;
	}
	
	/**
	 * Sets the field name used to recursively navigate the P4A_DB_Source.
	 * @param
	 * @access public
	 */
	function setRecursor($field_name)
	{
		$this->recursor = $field_name;
	}
	
	/**
	 * Sets the field name used to print out the description in the tree.
	 * @param string		The field name
	 * @access public
	 */
	function setDescription($field_name)
	{
		$this->description = $field_name;
	}
	
	/**
	 * Sets if the tree is expanded or not.
	 * @param boolean		The value
	 * @access public
	 */
	function expandAll($value = true)
	{
		$this->expand_all = $value;
	}
	
	/**
	 * Sets if the tree is collapsed or not.
	 * @param boolean		The value
	 * @access public
	 */
	function collapse($value = true)
	{
		$this->expand_all = !$value;
	}
	
	function getAsString($id = null)
	{
		if (!$this->isVisible()) {
			return "";
		}
		
		$p4a =& p4a::singleton();
		$db =& p4a_db::singleton();
		$return = "";
		
		$p4a->active_mask->addTempCSS(P4A_APPLICATION_PATH . "/p4a_db_navigator.css");
		
		$obj_id = $this->getId();
		$table = $this->source->getTable();
		$pk = $this->source->getPk();
		$order = $this->source->_composeOrderPart();
		$current = $this->source->fields->{$pk}->getValue();
		$all = $this->source->getAll();
		
		if (empty($id)) {
			$roots = $db->getAll("SELECT * FROM $table WHERE {$this->recursor} IS NULL $order");
		} else {
			$roots = $db->getAll("SELECT * FROM $table WHERE {$this->recursor} = '$id' $order");
		}
		
		$return .= "<ul class=\"p4a_db_navigator\" style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/folder." . P4A_ICONS_EXTENSION . "')\">";
		foreach ($roots as $section) {
			if ($section[$pk] == $current) {
				$return .= "<li class='active_node' style='list-style-image:url(" . P4A_ICONS_PATH . "/16/folder_open." . P4A_ICONS_EXTENSION . ")'>{$section[$this->description]}";
			} else {
				foreach ($all as $key=>$record) {
					if ($record[$pk] == $section[$pk]) {
						$position = $key+1;
						break;
					}
				}
			
				$actions = $this->composeStringActions($position);
				$return .= "<li><a href='#' $actions>{$section[$this->description]}</a>";
			}
			
			if ($this->expand_all) {
				$return .= $this->getAsString($section[$pk]);
			} else {
				
			}
			$return .= "</li>\n";
		}
		$return .= "</ul>";
		return $return;
	}

	/**
	 * OnClick event interceptor.
	 * @param array		All the parameters passed by the HTML form
	 * @access private
	 */
	function onClick($params)
	{
		$position = $params[0];
		$this->source->row($position);
	}
}

?>