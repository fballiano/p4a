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
 * This widget allows a tree navigation within a P4A_DB_Source.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_DB_Navigator extends P4A_Widget
{
	/**
	 * The P4A_DB_Source used for navigation
	 * @var P4A_DB_Source
	 */
	protected $source = null;

	/**
	 * The recursion field name
	 * @var string
	 */
	protected $recursor = "__recursor";

	/**
	 * The description field name
	 * @var string
	 */
	protected $description = "";

	/**
	 * Expand whole tree or collapse?
	 * @var boolean
	 */
	protected $expand_all = true;

	/**
	 * Trim after this number of characters
	 * @var integer
	 */
	protected $trim = 0;

	/**
	 * When moving a record, this field is updated
	 * @var string
	 */
	protected $field_to_update_on_movement = null;

	/**
	 * Allows user to move also the root elements
	 * @var boolean
	 */
	protected $allow_roots_movement = false;

	/**
	 * Allows user to create new root element (with parent_id = null)
	 * @var boolean
	 */
	protected $allow_movement_to_root = false;

	/**
	 * Is selected element clickable?
	 * @var boolean
	 */
	protected $enable_selected_element = false;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->addAction('onclick');
		$this->intercept($this, 'onclick', 'onClick');
	}

	/**
	 * Sets the source of the tree, it must be a P4A_DB_Source
	 * @param P4A_DB_Source $source	The DB source to navigate
	 * @return P4A_DB_Navigator
	 */
	public function setSource(P4A_DB_Source $source)
	{
		$this->source =& $source;
		return $this;
	}

	/**
	 * Sets the field name used to recursively navigate the P4A_DB_Source
	 * @param string $field_name
	 * @return P4A_DB_Navigator
	 */
	public function setRecursor($field_name)
	{
		$this->recursor = $field_name;
		return $this;
	}

	/**
	 * Sets the field name used to print out the description in the tree
	 * @param string $field_name
	 * @return P4A_DB_Navigator
	 */
	public function setDescription($field_name)
	{
		$this->description = $field_name;
		return $this;
	}

	/**
	 * Trims description after x chars (0 = disabled)
	 * @param integer $chars Num of chars
	 * @return P4A_DB_Navigator
	 */
	public function setTrim($chars)
	{
		$this->trim = $chars;
		return $this;
	}

	/**
	 * Sets if the tree is expanded or not
	 * @param boolean $value
	 * @return P4A_DB_Navigator
	 */
	public function expandAll($value = true)
	{
		$this->expand_all = $value;
		return $this;
	}

	/**
	 * Sets if the tree is collapsed or not
	 * @param boolean $value
	 * @return P4A_DB_Navigator
	 */
	public function collapse($value = true)
	{
		$this->expand_all = !$value;
		return $this;
	}

	/**
	 * Enable/disable movement of setions.
	 * 
	 * Note: Movements will work only if P4A_AJAX_ENABLED is true.
	 * 
	 * @param mixed (false|parent_id field on your mask)
	 * @return P4A_DB_Navigator
	 */
	public function allowMovement($field)
	{
		$this->field_to_update_on_movement = $field->getId();
		$field->implement('onchange', $this, 'onMovement');
		return $this;
	}

	/**
	 * Enable/disable movement of root sections (parent_id = null).
	 * 
	 * Note: Movements will work only if P4A_AJAX_ENABLED is true.
	 * 
	 * @param boolean
	 * @return P4A_DB_Navigator
	 */
	public function allowRootsMovement($allow = true)
	{
		$this->allow_roots_movement = $allow;
		return $this;
	}

	/**
	 * Enable/disable movement of sections to root (parent_id = null)
	 * 
	 * Note: Movements will work only if P4A_AJAX_ENABLED is true.
	 * 
	 * @param boolean
	 * @return P4A_DB_Navigator
	 */
	public function allowMovementToRoot($allow = true)
	{
		$this->allow_movement_to_root = $allow;
		return $this;
	}

	/**
	 * Is selected element clickable?
	 * @param boolean
	 * @return P4A_DB_Navigator
	 */
	public function enableSelectedElement($enable = true)
	{
		$this->enable_selected_element = $enable;
		return $this;
	}

	/**
	 * @param unknown_type $id
	 * @return string
	 */
	public function getAsString()
	{
		$obj_id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$obj_id' class='hidden'></div>";
		}

		$table = $this->source->getTable();
		$pk = $this->source->getPk();
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
		$return = $this->_getAsString(0, $all, $obj_id, $table, $pk, $current);

		if (P4A_AJAX_ENABLED and $this->field_to_update_on_movement) {
			$allow_movement_to_root = $this->allow_movement_to_root ? 'true' : 'false';
			$js .= "<script type='text/javascript'>p4a_db_navigator_load(";
			$js .= "'{$obj_id}','{$current}','{$this->field_to_update_on_movement}',{$allow_movement_to_root}";
			$js .= ")</script>\n";
		}

		$class = $this->composeStringClass();
		if (strlen($js) and $this->allow_movement_to_root) {
			$return = "<ul id='{$obj_id}' $class style=\"list-style-position:inside;list-style-image:url('" . P4A_ICONS_PATH . "/16/actions/go-home." . P4A_ICONS_EXTENSION . "')\"><li>&nbsp;{$return}</li></ul>";
		} else {
			$return = "<div id='{$obj_id}' $class>{$return}</div>";
		}

		return $return . $js;
	}

	private function _getAsString($id, $all, $obj_id, $table, $pk, $current)
	{
		if (!isset($all[$id])) {
			return '';
		}
		
		$html_id = '';
		if ($id == 0) {
			$html_id = "id='$obj_id'";
		}

		$return = "<ul class='p4a_db_navigator' style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/places/folder." . P4A_ICONS_EXTENSION . "')\">";
		$roots = $all[$id];
		foreach ($roots as $section) {
			if ($this->actionHandler('beforeRenderElement', $section) == ABORT) continue;

			$position = $section['__position'];
			$actions = $this->composeStringActions($position);
			$description = $this->_trim($section[$this->description]);

			if ($section[$pk] == $current) {
				$selected = "class='active_node' style='list-style-image:url(" . P4A_ICONS_PATH . "/16/status/folder-open." . P4A_ICONS_EXTENSION . ")'";
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

			if ($this->expand_all) {
				$return .= $this->_getAsString($section[$pk], $all, $obj_id, $table, $pk, $current);
			} else {
				$path = $this->getPath($current, $table, $pk);
				for ($i=0; $i<sizeof($path); $i++) {
					if ($section[$pk] == $path[$i][$pk]) {
						$return .= $this->_getAsString($path[$i][$pk], $all, $obj_id, $table, $pk, $current);
						break;
					}
				}
			}
			$return .= "</li>\n";
		}
		$return .= "</ul>";
		return $return;
	}

	public function getPath($id, $table, $pk)
	{
		$section = P4A_DB::singleton()->adapter->fetchRow("SELECT * FROM $table WHERE $pk=?", array($id));
		$return = array();
		$return[] = $section;

		if (empty($section[$this->recursor])) {
			return $return;
		}
		return array_merge($this->getPath($section[$this->recursor], $table, $pk), $return);
	}

	/**
	 * OnClick event interceptor
	 * @param array $params
	 */
	public function onClick($params)
	{
		$this->redesign();
		$position = $params[0];
		$row = $this->source->row($position);
		return $this->actionHandler('afterClick', $row);
	}

	/**
	 * Trims a text after a fixed number of characters
	 * @param string $text
	 */
	protected function _trim($text)
	{
		if ($this->trim > 0) {
			$len = strlen($text);
			$text = substr($text, 0, $this->trim);
			if ($len > $this->trim) {
				$text .= "...";
			}
		}
		return $text;
	}

	/**
	 * Event interceptor when user moves an element to another subtree
	 */
	public function onMovement()
	{
		$this->redesign();
		$table = $this->source->getTable();
		$pk = $this->source->getPk();
		$current = $this->source->fields->{$pk}->getValue();
		$field = P4A::singleton()->getObject($this->field_to_update_on_movement);
		$new_value = $field->getNormalizedNewValue();

		$receiver_path = $this->getPath($new_value, $table, $pk);
		foreach ($receiver_path as $record) {
			if ($current == $record[$pk]) {
				return;
			}
		}

		if ($this->actionHandler('beforeMovement') == ABORT) return ABORT;

		if ($new_value != $current) {
			if (strlen($new_value)) {
				P4A_DB::singleton()->adapter->query("UPDATE $table SET {$this->recursor} = ? WHERE $pk = ?", array($new_value, $current));
			} else {
				P4A_DB::singleton()->adapter->query("UPDATE $table SET {$this->recursor} = NULL WHERE $pk = ?", array($current));
			}
		}

		return $this->actionHandler('afterMovement');
	}
}