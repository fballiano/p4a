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
 * This widget allows a tree navigation within 2 nested P4A_DB_Source.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_2_Levels_DB_Navigator
 */
class P4A_2_Levels_DB_Navigator extends P4A_Widget
{
	/**
	 * The root P4A_DB_Source
	 * @var P4A_DB_Source
	 * @access private
	 */
	var $root_source = null;
	
	/**
	 * The description field name for the root P4A_DB_Source
	 * @var string
	 * @access private
	 */
	var $root_description = "";
	
	/**
	 * The nested P4A_DB_Source
	 * @var P4A_DB_Source
	 * @access private
	 */
	var $nested_source = null;
	
	/**
	 * The description field name for the nested P4A_DB_Source
	 * @var string
	 * @access private
	 */
	var $nested_description = "";
	
	/**
	 * Expand whole tree or collapse?
	 * @var boolean
	 * @access private
	 */
	var $expand_all = true;
	
	/**
	 * Trim after this number of characters
	 * @var integer
	 * @access private
	 */
	var $trim = 0;

	/**
	 * The constructor
	 * @param string		The name of the widget
	 * @access public
	 */
	function P4A_2_Levels_DB_Navigator($name)
	{
		parent::P4A_Widget($name);
		$this->addAction("onClick");
		$this->intercept($this, "onClick", "onClick");
	}
	
	/**
	 * Sets the root source of the tree, it must be a P4A_DB_Source.
	 * @param P4A_DB_Source	The DB source to navigate.
	 * @access public
	 */
	function setRootSource(&$source)
	{
		$this->root_source =& $source;
	}
	
	/**
	 * Sets the field name used to print out the description in the tree (for the root P4A_DB_Source).
	 * @param string		The field name
	 * @access public
	 */
	function setRootDescription($field_name)
	{
		$this->root_description = $field_name;
	}
	
	/**
	 * Sets the nested source of the tree, it must be a P4A_DB_Source.
	 * @param P4A_DB_Source	The DB source to navigate.
	 * @access public
	 */
	function setNestedSource(&$source)
	{
		$this->nested_source =& $source;
	}
	
	/**
	 * Sets the field name used to print out the description in the tree (for the nested P4A_DB_Source).
	 * @param string		The field name
	 * @access public
	 */
	function setNestedDescription($field_name)
	{
		$this->nested_description = $field_name;
	}
	
	/**
	 * Trims description after x chars (0 = disabled).
	 * @param integer		Num of chars
	 * @access public
	 */
	function setTrim($chars)
	{
		$this->trim = $chars;
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
	
	/**
	 * Renders the widget
	 * @access public
	 * @return string
	 */
	function getAsString()
	{
		if (!$this->isVisible()) {
			return "";
		}
		
		$p4a =& p4a::singleton();
		$db =& p4a_db::singleton();
		
		$p4a->active_mask->addTempCSS(P4A_APPLICATION_PATH . "/p4a_2_levels_db_navigator.css");
		$obj_id = $this->getId();
		
		// copying data sources because we need to move them without changing the original ones
		
		$root_source = clone($this->root_source);
		$root_row = $root_source->row();
		$root_pk = $root_source->getPk();
		$nested_source = clone($this->nested_source);
		$nested_row = $nested_source->row();
		$nested_pk = $nested_source->getPk();
		
		$return = "";
		$root_source->firstRow();
		$num_rows = $root_source->getNumRows();
		if ($num_rows > 0) {
			$return .= "<ul class='p4a_2_levels_db_navigator' style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/folder." . P4A_ICONS_EXTENSION . "')\">";
			for ($i=0; $i<$num_rows; $i++) {
				$row = $root_source->row();
				$active = '';
				if ($row[$root_pk] == $root_row[$root_pk]) {
					$active = "class='active_node' style='list-style-image:url(" . P4A_ICONS_PATH . "/16/folder_open." . P4A_ICONS_EXTENSION . ")'";
				}
				$root_description = $this->_trim($root_source->fields->{$this->root_description}->getValue());
				$actions = $this->composeStringActions($i);
				$return .= "<li $active><a href='#' $actions>$root_description</a>";
				
				if ($this->expand_all or !empty($active)) {
					$children = $nested_source->getAll();
					if (sizeof($children)) {
						$return .= "<ul>";
						foreach ($children as $key=>$child) {
							$active = '';
							if ($child[$nested_pk] == $nested_row[$nested_pk]) {
								$active = "class='active_node' style='font-weight:bold; list-style-image:url(" . P4A_ICONS_PATH . "/16/folder_open." . P4A_ICONS_EXTENSION . ")'";
							} else {
								$active = "style='list-style-image:url(" . P4A_ICONS_PATH . "/16/folder." . P4A_ICONS_EXTENSION . ")'";
							}
							$nested_description = $this->_trim($child[$this->nested_description]);
							$actions = $this->composeStringActions("{$i}_{$key}");
							$return .= "<li $active><a href='#' $actions>{$nested_description}</a></li>\n";
						}
						$return .= "</ul>";
					}
				}
				
				$return .= "</li>\n";
				$root_source->nextRow();
			}
			$return .= "</ul>";
		}
		
		return $return;
	}

	/**
	 * OnClick event interceptor.
	 * @param array		All the parameters passed by the HTML form
	 * @access private
	 */
	function onClick($params)
	{
		$parts = explode('_', $params[0]);
		$this->root_source->row($parts[0]+1);
		
		if (isset($parts[1])) {
			$row = $this->nested_source->row($parts[1]+1);
		} else {
			$row = $this->nested_source->firstRow();
		}
		
		return $this->actionHandler('afterClick', $row);
	}
	
	/**
	 * Trims a text after a fixed number of characters.
	 * @param string		The text to be trimmed
	 * @access private
	 */
	function _trim($text)
	{
		if ($this->trim > 0) {
			$len = strlen($text);
			$text = substr($text, 0, $this->trim);
			if ($len>$this->trim) {
				$text .= "...";
			}
		}
		return $text;
	}
}

?>