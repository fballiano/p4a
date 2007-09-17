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
 * @package p4a
 */

/**
 * This widget allows a tree navigation within a P4A_DB_Source.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_DB_Navigator extends P4A_Widget
{
	/**
	 * The P4A_DB_Source used for navigation
	 * @var P4A_DB_Source
	 * @access private
	 */
	var $source = null;

	/**
	 * The recursion field name
	 * @var string
	 * @access private
	 */
	var $recursor = "__recursor";

	/**
	 * The description field name
	 * @var string
	 * @access private
	 */
	var $description = "";

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
	 * When moving a record, this field is updated
	 * @var string
	 * @access private
	 */
	var $field_to_update_on_movement = null;

	/**
	 * Allows user to move also the root elements
	 * @var boolean
	 * @access private
	 */
	var $allow_roots_movement = false;

	/**
	 * Allows user to create new root element (with parent_id = null)
	 * @var boolean
	 * @access private
	 */
	var $allow_movement_to_root = false;

	/**
	 * Is selected element clickable?
	 * @var boolean
	 * @access private
	 */
	var $enable_selected_element = false;

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
	 * Enable/disable movement of setions (only if AJAX is enabled)
	 * @access public
	 * @param mixed (false|parent_id field on your mask)
	 */
	function allowMovement(&$field)
	{
		$this->field_to_update_on_movement = $field->getId();
		$this->intercept($field, 'onChange', 'onMovement');
	}

	/**
	 * Enable/disable movement of root sections (parent_id = null)
	 * @access public
	 * @param boolean
	 */
	function allowRootsMovement($allow = true)
	{
		$this->allow_roots_movement = $allow;
	}

	/**
	 * Enable/disable movement of sections to root (parent_id = null)
	 * @access public
	 * @param boolean
	 */
	function allowMovementToRoot($allow = true)
	{
		$this->allow_movement_to_root = $allow;
	}

	/**
	 * Is selected element clickable?
	 * @access public
	 * @param boolean
	 */
	function enableSelectedElement($enable = true)
	{
		$this->enable_selected_element = $enable;
	}

	function getAsString($id = null)
	{
		if (!$this->isVisible()) {
			return "";
		}

		$p4a =& p4a::singleton();
		$db =& p4a_db::singleton();

		$obj_id = $this->getId();
		$table = $this->source->getTable();
		$pk = $this->source->getPk();
		$order = $this->source->_composeOrderPart();
		$current = $this->source->fields->{$pk}->getValue();
		$rows = $this->source->getAll();
		if (isset($this->source->fields->{$this->recursor})) {
			$recursor = $this->source->fields->{$this->recursor}->getValue();
		}
		if ($current === null) {
			$current = $recursor;
		}

		$js = "";
		$i = 0;
		foreach ($rows as $row) {
			if (!isset($row[$this->recursor])) {
				$row[$this->recursor] = null;
			}
			$id = $row[$this->recursor];
			if (empty($id)) {
				$id = 0;
			}
			$row['__position'] = ++$i;
			$all[$id][] = $row;
		}
		$return = $this->_getAsString(0, $all, $obj_id, $table, $pk, $order, $current);

		// movements are allowed ONLY IF AJAX IS ACTIVE!!
		// that's because we use too complex javascript for old handhelds
		if (P4A_AJAX_ENABLED and $this->field_to_update_on_movement) {
			$js .= "<script type='text/javascript'>\n";
			$js .= "\$('#{$obj_id}_{$current}').Draggable({revert:true,fx:200,ghosting:true});\n";
			$js .= "\$('#{$obj_id} li a').Droppable({accept:'active_node',hoverclass:'hoverclass',ondrop:function(){\$('#{$this->field_to_update_on_movement}input').val(\$(this).parent().attr('id').split('_')[1]); executeAjaxEvent('{$this->field_to_update_on_movement}', 'onChange');}});\n";
			if ($this->allow_movement_to_root) {
				$js .= "\$('#{$obj_id}').Droppable({accept:'active_node',hoverclass:'hoverclass',ondrop:function(){\$('#{$this->field_to_update_on_movement}input').val(''); executeAjaxEvent('{$this->field_to_update_on_movement}', 'onChange');}});\n";
			}
			$js .= "</script>\n";
		}

		if (strlen($js) and $this->allow_movement_to_root) {
			$return = "<ul id='{$obj_id}' class='p4a_db_navigator' style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/folder_home." . P4A_ICONS_EXTENSION . "')\"><li>{$return}</li></ul>";
		} else {
			$return = "<div id='{$obj_id}'>{$return}</div>";
		}

		return $return . $js;
	}

	function _getAsString($id, &$all, $obj_id, $table, $pk, $order, $current, $recurse = true)
	{
		$p4a =& p4a::singleton();
		$db =& p4a_db::singleton();
		$return = "";

		if ($id == 0) {
			$html_id = "id='$obj_id'";
		} else {
			$html_id = "";
		}

		if (!isset($all[$id])) {
			return "";
		}

		$return .= "<ul class='p4a_db_navigator' style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/folder." . P4A_ICONS_EXTENSION . "')\">";
		$roots = $all[$id];
		foreach ($roots as $section) {
			if ($this->actionHandler('beforeRenderElement', $section) == ABORT) {
				continue;
			}

			$position = $section['__position'];
			$actions = $this->composeStringActions($position);
			$description = $this->_trim($section[$this->description]);

			if ($section[$pk] == $current) {
				$selected = "class='active_node' style='list-style-image:url(" . P4A_ICONS_PATH . "/16/folder_open." . P4A_ICONS_EXTENSION . ")'";
				if ($this->enable_selected_element) {
					$link_prefix = "<a href='#' {$actions}>";
					$link_suffix = "</a>";
				} else {
					$link_prefix = "";
					$link_suffix = "";
				}
			} else {
				$selected = "";
				$link_prefix = "<a href='#' {$actions}>";
				$link_suffix = "</a>";
			}

			$return .= "<li {$selected} id='{$obj_id}_{$section[$pk]}'>{$link_prefix}{$description}{$link_suffix}\n";

			if ($recurse) {
				if ($this->expand_all) {
					$return .= $this->_getAsString($section[$pk], $all, $obj_id, $table, $pk, $order, $current);
				} else {
					$path = $this->getPath($current, $table, $pk);
					for ($i=0; $i<sizeof($path); $i++) {
						if ($section[$pk] == $path[$i][$pk]) {
							$return .= $this->_getAsString($path[$i][$pk], $all, $obj_id, $table, $pk, $order, $current);
							break;
						}
					}
				}
			}
			$return .= "</li>\n";
		}
		$return .= "</ul>";
		return $return;
	}

	function getPath($id, $table, $pk)
	{
		$db =& p4a_db::singleton();

		$section = $db->queryRow("SELECT * FROM $table WHERE $pk='$id'");
		$return = array();
		$return[] = $section;

		if (empty($section[$this->recursor])) {
			return $return;
		} else {
			return array_merge($this->getPath($section[$this->recursor], $table, $pk), $return);
		}
	}

	/**
	 * OnClick event interceptor.
	 * @param array		All the parameters passed by the HTML form
	 * @access private
	 */
	function onClick($params)
	{
		$this->redesign();
		$position = $params[0];
		$row = $this->source->row($position);
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

	/**
	 * Event interceptor when user moves an element to another subtree
	 * @access private
	 */
	function onMovement()
	{
		$p4a =& p4a::singleton();
		$db =& p4a_db::singleton();

		$field =& $p4a->getObject($this->field_to_update_on_movement);

		$table = $this->source->getTable();
		$pk = $this->source->getPk();
		$current = $this->source->fields->{$pk}->getValue();
		$new_value = $field->getUnformattedNewValue();

		$receiver_path = $this->getPath($new_value, $table, $pk);
		foreach ($receiver_path as $record) {
			if ($current == $record[$pk]) {
				return;
			}
		}

		if ($this->actionHandler('beforeMovement') == ABORT) {
			return;
		}

		if ($new_value != $current) {
			if (strlen($new_value)) {
				$db->query("UPDATE $table SET {$this->recursor}='$new_value' WHERE $pk='$current'");
			} else {
				$db->query("UPDATE $table SET {$this->recursor}=NULL WHERE $pk='$current'");
			}
			$this->redesign();
		}

		return $this->actionHandler('afterMovement');
	}
}