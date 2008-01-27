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

/**
 * Base class for objects that permit user interation with the application.
 * Every P4A objects thats can be rendered should use WIDGET as base class.
 * This class have all the basic methods to build complex widgets that must
 * be P4A compatible.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */
class P4A_Widget extends P4A_Object
{
	/**
	 * Object's enabled status. If the widget is visible but not enable it won't be clickable.
	 * @var boolean
	 */
	protected $enabled = true;

	/**
	 * @var boolean
	 */
	protected $visible = true;

	/**
	 * Keeps the association between an action and its listener
	 * @var array
	 */
	protected $map_actions = array();

	/**
	 * Keeps all the actions implemented by the widget
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Keeps the label associated with the widget
	 * The label will be displayed on the left of the widget
	 * @var P4A_Label
	 */
	public $label = null;

	/**
	 * Keeps all the HTML properties for the widget
	 * @var array
	 */
	protected $properties = array();

	/**
	 * Keeps all the CSS properties for the widget
	 * @var array
	 */
	protected $style = array();

	/**
	 * Defines if we are going to use a template for the widget
	 * @var boolean
	 */
	protected $use_template = false;

	/**
	 * Defines the name of the widget
	 * if you set it to 'menu' P4A will search for "menu/menu.tpl"
	 * in the "themes/CURRENT_THEME/widgets/" directory.
	 * @var string
	 */
	protected $template_name = null;

	/**
	 * variables used for templates
	 * @var array
	 */
	protected $_tpl_vars = array();

	/**
	 * Temporary variables (destroyed after rendering)
	 * @var array
	 */
	protected $_temp_vars = array();

	/**
	 * @param boolean $enabled
	 * @see $enabled
	 */
	public function enable($enabled = true)
	{
		$this->enabled = $enabled;
	}

	/**
	 * @see $enabled
	 */
	public function disable()
	{
		$this->enabled = false;
	}

	/**
	 * Returns true if the widget is enabled
	 * @see $enable
	 */
	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * @param boolean $visible
	 */
	public function setVisible($visible = true)
	{
		$this->visible = $visible;
	}

	/**
	 * Sets the widget invisible
	 */
	public function setInvisible()
	{
		$this->visible = false;
	}

	/**
	 * Returns true if the widget is visible
	 * @return boolean
	 */
	public function isVisible()
	{
		return $this->visible;
	}

	/**
	 * Sets the label for the widget.
	 * In rendering phase it will be added with ':  '.
	 * @param string $label
	 * @see $label
	 */
	public function setLabel($label)
	{
		// Used for sheets group->sheets labels
		$this->actionHandler('set_label', $label);
		$this->label = $label;
	}

	/**
	 * Create from name a default label for the widget
	 * In rendering phase it will be added with ':  '.
	 * @see $label
	 */
	public function setDefaultLabel()
	{
		$this->setLabel(P4A_Generate_Default_Label($this->getName()));
	}

	/**
	 * Returns the label for the widget
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * Sets an HTML property for the widget
	 * @param string $property
	 * @param string $value
	 */
	public function setProperty($property, $value)
	{
		$this->properties[strtolower($property)] = $value;
	}

	/**
	 * Unsets an HTML property for the widget
	 * @param string $property
	 */
	public function unsetProperty($property)
	{
		unset($this->properties[strtolower($property)]);
	}

	/**
	 * Returns the value of a property
	 * @param string $property
	 * @return string
	 */
	public function getProperty($property)
	{
		$property = strtolower($property);
		if (array_key_exists($property, $this->properties)) {
			return $this->properties[$property];
		} else {
			return null;
		}
	}

	/**
	 * Sets a CSS property for the widget
	 * @param string $property
	 * @param string $value
	 */
	public function setStyleProperty($property, $value)
	{
		$this->style[$property] = $value;
	}

	/**
	 * Unset a CSS property for the widget
	 * @param string $property
	 */
	public function unsetStyleProperty($property)
	{
		unset($this->style[$property]);
	}
	
	/**
	 * Returns the value of a CSS property
	 * @param string $property
	 * @return string
	 */
	public function getStyleProperty($property)
	{
		if (array_key_exists($property, $this->style)) {
			return $this->style[$property];
		} else {
			return null;
		}
	}

	/**
	 * @param string $key
	 */
	function setAccessKey($key) {
		$this->setProperty("accesskey", $key);
	}

	/**
	 * @return string
	 */
	function getAccessKey() {
		return $this->getProperty("accesskey");
	}

	/**
	 * Sets the width for the widget.
	 * It's a wrapper for setStyleProperty().
	 * @param integer $value The value to be used as width
	 * @param string $unit The measure unit (px|pt|%) etc...
	 * @see setStyleProperty()
	 */
	public function setWidth($value = null, $unit = 'px')
	{
		if (is_numeric($value)) {
			$value = $value . $unit;
		}
		if ($value === null) {
			$this->unsetStyleProperty('width');
		} else {
			$this->setStyleProperty('width', $value);
		}
	}

	/**
	 * Returns the width for the widget.
	 * It's a wrapper for getStyleProperty().
	 * @return string
	 * @see getStyleProperty()
	 */
	public function getWidth()
	{
		return $this->getStyleProperty('width');
	}

	/**
	 * Sets the height for the widget.
	 * It's a wrapper for setStyleProperty().
	 * @param integer $value The value to be used as height.
	 * @param string $unit The measure unit (px|pt|%) etc...
	 * @see setStyleProperty()
	 */
	public function setHeight($value = null, $unit = 'px')
	{
		if (is_numeric($value)) {
			$value = $value . $unit;
		}
		if ($value === null) {
			$this->unsetStyleProperty('height');
		} else {
			$this->setStyleProperty('height', $value);
		}
	}

	/**
	 * Returns the height for the widget.
	 * It's a wrapper for getStyleProperty().
	 * @see getStyleProperty()
	 */
	public function getHeight()
	{
		return $this->getStyleProperty('height');
	}

	/**
	 * Sets the background color for the widget.
	 * It's a wrapper for setStyleProperty().
	 * @param string $value The value to be used as color
	 * @see setStyleProperty()
	 */
	public function setBgcolor($value)
	{
		$this->setStyleProperty('background-color', $value) ;
	}

	/**
	 * Sets the background image for the widget.
	 * It's a wrapper for setStyleProperty().
	 * @param string $value The url of the image
	 * @see setStyleProperty()
	 */
	public function setBgimage($value)
	{
		$this->setStyleProperty('background-image', "url('" . $value . "')");
	}

	/**
	 * Sets the font weight for the widget.
	 * It's a wrapper for setStyleProperty().
	 * @param string $value The url of the image
	 * @see setStyleProperty()
	 */
	public function setFontWeight($value)
	{
		$this->setStyleProperty('font-weight', $value);
	}

	/**
	 * Sets the font color for the widget
	 * It's a wrapper for setStyleProperty().
	 * @param string $value The url of the image
	 * @see setStyleProperty()
	 */
	public function setFontColor($value)
	{
		$this->setStyleProperty('color', $value);
	}

	/**
	 * Adds an action to the implemented actions stack for the widget
	 * @param string $action
	 * @param string $event The JavaScript event that triggers
	 * @param string|boolean $confirmation_text If the action requires user confirmation, type here the confirmation message (use boolean true for a general message)
	 * @param boolean $ajax is an ajax action?
	 */
	public function addAction($action, $event = null, $confirmation_text = null, $ajax = false)
	{
		$action = strtolower($action);
		$event = strtolower($event);

		// If not specified, the event has the same name of the action
		if (!$event) {
			$event = $action;
		}
		
		if ($confirmation_text === true) {
			$confirmation_text = 'Are you sure?';
		}

		$tmp_action = array();
		$tmp_action['event'] = $event;
		$this->actions[$action] = $tmp_action;
		$this->actions[$action]['confirm'] = $confirmation_text;
		$this->actions[$action]['ajax'] = $ajax;
	}

	/**
	 * Adds an ajax action to the implemented actions stack for the widget
	 * @param string $action
	 * @param string $event The JavaScript event that triggers
	 * @param string|boolean $confirmation_text If the action requires user confirmation, type here the confirmation message (use boolean true for a general message)
	 */
	public function addAjaxAction($action, $event = null, $confirmation_text = null)
	{
		$p4a = p4a::singleton();
		$this->addAction($action, $event, $confirmation_text, $p4a->isAjaxEnabled());
	}

	/**
	 * Requires confirmation for an action
	 * @param string $action
	 * @param string|boolean $confirmation_text The confirmation message (default is boolean true for a general message)
	 */
	public function requireConfirmation($action = 'onclick', $confirmation_text = true)
	{
		$action = strtolower($action);
		if ($confirmation_text === true) {
			$confirmation_text = 'Are you sure?';
		}
		$this->actions[$action]['confirm'] = $confirmation_text;
	}

	/**
	 * Removes confirmation for an action
	 * @param string $action
	 */
	public function unrequireConfirmation($action)
	{
		$action = strtolower($action);
		$this->actions[$action]['confirm'] = null;
	}

	/**
	 * Changes the event associated to an action.
	 * If no event is given, here we set event=action.
	 *
	 * @param string $action
	 * @param string $event The JavaScript event that triggers
	 */
	public function changeEvent($action, $event = null)
	{
		$action = strtolower($action);
		$event = strtolower($event);

		// If not specified, the event has the same name of the action
		if ($event === null) {
			$event = $action;
		}

		if (array_key_exists($action, $this->actions)) {
			$this->actions[$action]['event'] = $event;
		}
	}

	/**
	 * Removes an action from the implemented actions stack for the widget
	 * @param string $action
	 */
	public function dropAction($action)
	{
		$action = strtolower($action);
		unset($this->actions[$action]);
	}

	/**
	 * Composes a string containing all the HTML properties of the widget.
	 * Note: it will also contain the name and the value.
	 * @return string
	 */
	protected function composeStringProperties()
	{
		$sReturn = '';
		foreach ($this->properties as $property_name=>$property_value) {
			$sReturn .= $property_name . '="' . $property_value . '" ' ;
		}

		$sReturn .= $this->composeStringStyle();
		return $sReturn;
	}

	/**
	 * Composes a string containing all the actions implemented by the widget.
	 * Note: it will also contain the name and the value.
	 * @param array $params
	 * @param boolean $check_enabled_state
	 * @return string
	 */
	protected function composeStringActions($params = null, $check_enabled_state = true)
	{
		$p4a =& P4A::singleton();

		$sParams = '';
		$sActions = '';

		if (is_string($params) or is_numeric($params)) {
			$sParams .=  ", '{$params}'";
		} elseif (is_array($params) and count($params)) {
			$sParams = ', ';
			foreach ($params as $param) {
				$sParams .= "'{$param}', ";
			}
			$sParams = substr($sParams, 0, -2);
		}

		foreach ($this->actions as $action=>$action_data) {
			if ($check_enabled_state and !$this->isEnabled()) {
				return '';
			}

			$browser_action = $action;
			$return = 'false';
			$prefix = '';
			$suffix = '';

			if ($action == 'onreturnpress') {
				$browser_action = 'onkeypress';
				$return = 'true';
				$prefix .= 'if(isReturnPressed(event)){';
				$suffix .= '}';
			} elseif ($action == 'onkeypress'
				   or $action == 'onkeydown'
				   or $action == 'onkeyup') {
				$sParams .= ", getKeyPressed(event)";
			}

			if ($action_data['confirm'] !== null) {
				$prefix .= 'if(confirm(\''. str_replace('\'', '\\\'', __($action_data['confirm'])) .'\')){';
				$suffix .= '}';
			}

			if (isset($action_data['ajax']) and $action_data['ajax'] == 1) {
				$execute = 'executeAjaxEvent';
			} else {
				$execute = 'executeEvent';
			}

			if (isset($action_data['event'])) {
				$action_data_event = $action_data['event'];
			} else {
				$action_data_event = '';
			}

			$sActions .= $browser_action . '="' . $prefix . "{$execute}('" . $this->getId() . '\', \'' . $action_data_event . '\''. $sParams .');' . $suffix . ' return ' . $return . ';" ';
		}
		return $sActions;
	}

	/**
	 * Composes a string containing the CSS properties for the widget
	 * @return string
	 */
	protected function composeStringStyle()
	{
		$sStyle = '';
		foreach($this->style as $property=>$property_value) {
			$sStyle .= "$property:$property_value;";
		}

		if ($sStyle) {
			$sStyle = 'style="' . substr($sStyle, 0, -1) . '" ';
		}
		return $sStyle;
	}

	/**
	 * Composes a string contaning the class property for the widget
	 * @param array $classes
	 * @return string
	 */
	protected function composeStringClass($classes = array())
	{
		if (empty($classes)) return '';
		return 'class="' . join(' ', $classes) . '" ';
	}

	/**
	 * Defines the template used by the widget
	 * @param string $template_name
	 */
	public function useTemplate($template_name)
	{
		if ($template_name === false) {
			$this->use_template = false;
			$this->template_name = null;
		} else {
			$this->use_template = true;
			$this->template_name = $template_name;
		}
	}

	/**
	 * Adds this variable (name and value) to the template engine variables' stack.
	 * @param string $var_name
	 * @param string $var_value
	 */
	public function display($var_name, $var_value)
	{
		$sDisplay = '';
		if (is_object($var_value)) {
			$sDisplay = $var_value->getAsString();
		} else {
			$sDisplay = $var_value;
		}

		if ($this->use_template) {
			$this->_tpl_vars[$var_name] = $sDisplay;
		} else {
			p4a_error("FETCH TEMPLATE IMPOSSIBLE. Call first use_template.");
		}
	}

	/**
	 * Empties the template engine variables' stack
	 */
	public function clearTemplateVars()
	{
		$this->_tpl_vars = array();
	}

	/**
	 * Returns the HTML rendered template
	 * @return string
	 */
	public function fetchTemplate()
	{
		if ($this->use_template) {
			$p4a =& p4a::singleton();

			if (strpos($this->template_name, '/') !== false) {
				list($_template_dir, $_template_file) = explode('/', $this->template_name);
			} else {
				$_template_dir = $this->template_name;
				$_template_file = $this->template_name;
			}

			foreach ($this->_tpl_vars as $k=>$v) {
				if (is_object($v)) {
					$$k = $v->getAsString();
				} else {
					$$k = $v;
				}
			}

			foreach ($this->_temp_vars as $k=>$v) {
				$$k = $v;
			}

			ob_start();
			require P4A_THEME_DIR . "/widgets/{$_template_dir}/{$_template_file}.tpl";
			$output = ob_get_contents();
			ob_end_clean();

			$this->clearTempVars();

			return $output;
		} else {
			p4a_error("ERROR: Unable to fetch template, first call \"use_template\".");
		}
	}

	/**
	 * Returns the HTML rendered widget.
	 * This method MUST be overridden by every
	 * widget that extends P4A_this class.
	 */
	public function getAsString()
	{
	}

	/**
	 * Wrapper used to add the handling of OnBlur action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onBlur($params = null)
	{
		return $this->actionHandler('onBlur', $params);
	}

	/**
	 * Wrapper used to add the handling of OnClick action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onClick($params = null)
	{
		return $this->actionHandler('onClick', $params);
	}

	/**
	 * Wrapper used to add the handling of OnChange action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onChange($params = null)
	{
		return $this->actionHandler('onChange', $params);
	}

	/**
	 * Wrapper used to add the handling of onDblClick action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onDblClick($params = null)
	{
		return $this->actionHandler('onDblClick', $params);
	}

	/**
	 * Wrapper used to add the handling of onFocus action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onFocus($params = null)
	{
		return $this->actionHandler('onFocus', $params);
	}

	/**
	 * Wrapper used to add the handling of onMouseDown action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onMouseDown($params = null)
	{
		return $this->actionHandler('onMouseDown', $params);
	}

	/**
	 * Wrapper used to add the handling of onMouseMove action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onMouseMove($params = null)
	{
		return $this->actionHandler('onMouseMove', $params);
	}

	/**
	 * Wrapper used to add the handling of onMouseOver action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onMouseOver($params = null)
	{
		return $this->actionHandler('onMouseOver', $params);
	}

	/**
	 * Wrapper used to add the handling of onMouseUp action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onMouseUp($params = null)
	{
		return $this->actionHandler('onMouseUp', $params);
	}

	/**
	 * Wrapper used to add the handling of OnKeyPress action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onKeyPress($params = null)
	{
		return $this->actionHandler('onKeyPress', $params);
	}

	/**
	 * Wrapper used to add the handling of OnKeyUp action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onKeyUp($params = null)
	{
		return $this->actionHandler('onKeyUp', $params);
	}

	/**
	 * Wrapper used to add the handling of OnKeyDown action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onKeyDown($params = null)
	{
		return $this->actionHandler('onKeyDown', $params);
	}

	/**
	 * Wrapper used to add the handling of onReturnPress action.
	 * The onReturnPress action is an onKeyPress with checking if
	 * the pressed key is return.
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onReturnPress($params = null)
	{
		return $this->actionHandler('onReturnPress', $params);
	}

	/**
	 * Wrapper used to add the handling of onSelect action
	 * @return unknown
	 * @see actionHandler()
	 */
	public function onSelect($params = null)
	{
		return $this->actionHandler('onSelect', $params);
	}

	/**
	 * Add a temporary variable
	 * @param string $name
	 * @param string $value
	 */
	public function addTempVar($name, $value)
	{
		$this->_temp_vars[$name] = $value;
	}

	/**
	 * Drop a temporary variable
	 * @param string $name
	 */
	public function dropTempVar($name)
	{
		if (isset($this->_temp_vars[$name])) {
			unset($this->_temp_vars[$name]);
		}
	}

	/**
	 * Clear temporary vars list
	 */
	public function clearTempVars()
	{
		$this->_temp_vars = array();
	}

	public function redesign()
	{
		$p4a =& p4a::singleton();
		$p4a->redesign($this->getId());
	}
}