<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 *
 * To contact the authors write to:                                     <br />
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

namespace P4A\Widget;

use P4A\Object;

/**
 * Base class for objects that permit user interation with the application.
 * Every P4A objects thats can be rendered should use WIDGET as base class.
 * This class have all the basic methods to build complex widgets that must
 * be P4A compatible.
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
abstract class Widget extends Object
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
     * @var array
     */
    protected $_css_classes = array();

    /**
     * @var string
     */
    protected $_tooltip = null;

    /**
     * @param string Object identifier, when you add an object to another object (such as $p4a) you can access to it by $p4a->object_name
     * @param string Prefix string for ID generation
     * @param string Object ID identifies an object in the $p4a's object collection. You can set a static ID if you want that all clients uses the same ID (tipically for web sites).
     */
    public function __construct($name = null, $prefix = 'obj', $id = null)
    {
        parent::__construct($name, $prefix, $id);
        $this->addCSSClass(strtolower(get_class($this)));
    }

    /**
     * @param boolean $enabled
     * @see $enabled
     * @return P4A_Widget
     */
    public function enable($enabled = true)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @see $enabled
     * @return P4A_Widget
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
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
     * @return P4A_Widget
     */
    public function setVisible($visible = true)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Sets the widget invisible
     * @return P4A_Widget
     */
    public function setInvisible()
    {
        $this->visible = false;
        return $this;
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
     * @return P4A_Widget
     */
    public function setLabel($label)
    {
        // Used for sheets group->sheets labels
        $this->actionHandler('set_label', $label);
        $this->label = $label;
        return $this;
    }

    /**
     * Create from name a default label for the widget
     * In rendering phase it will be added with ':  '.
     * @see $label
     * @return P4A_Widget
     */
    public function setDefaultLabel()
    {
        $this->setLabel(P4A_Generate_Default_Label($this->getName()));
        return $this;
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
     * @return P4A_Widget
     */
    public function setProperty($property, $value)
    {
        $this->properties[strtolower($property)] = $value;
        return $this;
    }

    /**
     * Unsets an HTML property for the widget
     * @param string $property
     * @return P4A_Widget
     */
    public function unsetProperty($property)
    {
        unset($this->properties[strtolower($property)]);
        return $this;
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
        }
        return null;
    }

    /**
     * Sets a CSS property for the widget
     * @param string $property
     * @param string $value
     * @return P4A_Widget
     */
    public function setStyleProperty($property, $value)
    {
        $this->style[$property] = $value;
        return $this;
    }

    /**
     * Unset a CSS property for the widget
     * @param string $property
     * @return P4A_Widget
     */
    public function unsetStyleProperty($property)
    {
        unset($this->style[$property]);
        return $this;
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
        }
        return null;
    }

    /**
     * @param string $key
     * @return P4A_Widget
     */
    public function setAccessKey($key)
    {
        $this->setProperty("accesskey", $key);
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->getProperty("accesskey");
    }

    /**
     * Sets the width for the widget.
     * It's a wrapper for setStyleProperty().
     * @param integer $value The value to be used as width
     * @param string $unit The measure unit (px|pt|%) etc...
     * @see setStyleProperty()
     * @return P4A_Widget
     */
    public function setWidth($value = null, $unit = 'px')
    {
        if (is_numeric($value)) {
            $value = $value . $unit;
        }
        if ($value === null) {
            return $this->unsetStyleProperty('width');
        }
        return $this->setStyleProperty('width', $value);
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
     * @return P4A_Widget
     */
    public function setHeight($value = null, $unit = 'px')
    {
        if (is_numeric($value)) {
            $value = $value . $unit;
        }
        if ($value === null) {
            return $this->unsetStyleProperty('height');
        }
        return $this->setStyleProperty('height', $value);
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
     * @return P4A_Widget
     */
    public function setBgcolor($value)
    {
        $this->setStyleProperty('background-color', $value);
        return $this;
    }

    /**
     * Sets the background image for the widget.
     * It's a wrapper for setStyleProperty().
     * @param string $value The url of the image
     * @see setStyleProperty()
     * @return P4A_Widget
     */
    public function setBgimage($value)
    {
        $this->setStyleProperty('background-image', "url('" . $value . "')");
        return $this;
    }

    /**
     * Sets the font weight for the widget.
     * It's a wrapper for setStyleProperty().
     * @param string $value The url of the image
     * @see setStyleProperty()
     * @return P4A_Widget
     */
    public function setFontWeight($value)
    {
        $this->setStyleProperty('font-weight', $value);
        return $this;
    }

    /**
     * Sets the font color for the widget
     * It's a wrapper for setStyleProperty().
     * @param string $value The url of the image
     * @see setStyleProperty()
     * @return P4A_Widget
     */
    public function setFontColor($value)
    {
        $this->setStyleProperty('color', $value);
        return $this;
    }

    /**
     * Adds an action to the implemented actions stack for the widget
     * @param string $action
     * @param string $event The JavaScript event that triggers
     * @param string|boolean $confirmation_text If the action requires user confirmation, type here the confirmation message (use boolean true for a general message)
     * @param boolean $ajax is an ajax action?
     * @return P4A_Widget
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

        return $this;
    }

    /**
     * Adds an ajax action to the implemented actions stack for the widget
     * @param string $action
     * @param string $event The JavaScript event that triggers
     * @param string|boolean $confirmation_text If the action requires user confirmation, type here the confirmation message (use boolean true for a general message)
     * @return P4A_Widget
     */
    public function addAjaxAction($action, $event = null, $confirmation_text = null)
    {
        return $this->addAction($action, $event, $confirmation_text, P4A_AJAX_ENABLED);
    }

    /**
     * Requires confirmation for an action
     * @param string $action
     * @param string|boolean $confirmation_text The confirmation message (default is boolean true for a general message)
     * @return P4A_Widget
     */
    public function requireConfirmation($action = 'onclick', $confirmation_text = true)
    {
        $action = strtolower($action);
        if (!$this->actions[$action]) {
            trigger_error("you've to call addaction before requireConfirmation", E_USER_ERROR);
        }
        if ($confirmation_text === true) {
            $confirmation_text = 'Are you sure?';
        }
        $this->actions[$action]['confirm'] = $confirmation_text;
        return $this;
    }

    /**
     * Removes confirmation for an action
     * @param string $action
     * @return P4A_Widget
     */
    public function unrequireConfirmation($action)
    {
        $action = strtolower($action);
        $this->actions[$action]['confirm'] = null;
        return $this;
    }

    /**
     * Changes the event associated to an action.
     * If no event is given, here we set event=action.
     *
     * @param string $action
     * @param string $event The JavaScript event that triggers
     * @return P4A_Widget
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
        return $this;
    }

    /**
     * Removes an action from the implemented actions stack for the widget
     * @param string $action
     * @return P4A_Widget
     */
    public function dropAction($action)
    {
        $action = strtolower($action);
        unset($this->actions[$action]);
        return $this;
    }

    /**
     * Composes a string containing all the HTML properties of the widget.
     * Note: it will also contain the name and the value.
     * @return string
     */
    protected function composeStringProperties()
    {
        $sReturn = '';
        foreach ($this->properties as $property_name => $property_value) {
            $sReturn .= $property_name . '="' . $property_value . '" ';
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
    public function composeStringActions($params = null, $check_enabled_state = true)
    {
        if ($check_enabled_state and !$this->isEnabled()) {
            return '';
        }

        $sParams = '';
        $sActions = '';
        if (is_string($params) or is_numeric($params)) {
            $params = P4A_Quote_Javascript_String($params);
            $params = str_replace('\\', '\\\\', $params);
            $sParams .= ", '{$params}'";
        } elseif (is_array($params) and count($params)) {
            $sParams = ', ';
            foreach ($params as $param) {
                $param = P4A_Quote_Javascript_String($param);
                $params = str_replace('\\', '\\\\', $param);
                $sParams .= "'{$param}', ";
            }
            $sParams = substr($sParams, 0, -2);
        }

        foreach ($this->actions as $action => $action_data) {
            $browser_action = $action;
            $return = 'false';
            $prefix = '';
            $suffix = '';

            if ($action == 'onreturnpress') {
                $browser_action = 'onkeypress';
                $return = 'true';
                $prefix .= 'if(p4a_keypressed_is_return(event)){';
                $suffix .= 'return false;}';
            } elseif ($action == 'onkeypress'
                or $action == 'onkeydown'
                or $action == 'onkeyup'
            ) {
                $sParams .= ", p4a_keypressed_get(event)";
            }

            if ($action_data['confirm'] !== null) {
                $prefix .= 'if(confirm(\'' . P4A_Quote_Javascript_String(__($action_data['confirm'])) . '\')){';
                $suffix .= '}';
            }

            if (isset($action_data['ajax']) and $action_data['ajax'] == 1) {
                $execute = 'p4a_event_execute_ajax';
            } else {
                $execute = 'p4a_event_execute';
            }

            if (isset($action_data['event'])) {
                $action_data_event = $action_data['event'];
            } else {
                $action_data_event = '';
            }

            $sActions .= $browser_action . '="' . $prefix . "{$execute}('" . $this->getId(
                ) . '\', \'' . $action_data_event . '\'' . $sParams . ');' . $suffix . ' return ' . $return . ';" ';
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
        foreach ($this->style as $property => $property_value) {
            $sStyle .= "$property:$property_value;";
        }

        if ($sStyle) {
            $sStyle = 'style="' . substr($sStyle, 0, -1) . '" ';
        }
        return $sStyle;
    }

    /**
     * Composes a string contaning the class property for the widget
     * @param array $additional_classes
     * @return string
     */
    protected function composeStringClass($additional_classes = array())
    {
        $classes = array_merge($this->getCSSClasses(), $additional_classes);
        if (empty($classes)) {
            return '';
        }
        $classes = join(' ', $classes);
        return "class='$classes'";
    }

    /**
     * Defines the template used by the widget
     * @param string $template_name
     * @return P4A_Widget
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
        return $this;
    }

    /**
     * Adds this variable (name and value) to the template engine variables' stack.
     * @param string $var_name
     * @param string $var_value
     * @return P4A_Widget
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
            trigger_error("P4A_Widget::display(): Unable to fetch template, call useTemplate() before display()");
        }
        return $this;
    }

    /**
     * Empties the template engine variables' stack
     * @return P4A_Widget
     */
    public function clearTemplateVars()
    {
        $this->_tpl_vars = array();
        return $this;
    }

    /**
     * Returns the HTML rendered template
     * @return string
     */
    public function fetchTemplate()
    {
        if ($this->use_template) {
            if (strpos($this->template_name, '/') !== false) {
                list($_template_dir, $_template_file) = explode('/', $this->template_name);
            } else {
                $_template_dir = $this->template_name;
                $_template_file = $this->template_name;
            }

            foreach ($this->_tpl_vars as $k => $v) {
                if (is_object($v)) {
                    $$k = $v->getAsString();
                } else {
                    $$k = $v;
                }
            }

            foreach ($this->_temp_vars as $k => $v) {
                $$k = $v;
            }

            ob_start();
            require P4A_THEME_DIR . "/widgets/{$_template_dir}/{$_template_file}.php";
            $output = ob_get_contents();
            ob_end_clean();

            $this->clearTempVars();

            return $output;
        } else {
            trigger_error("P4A_Widget::display(): Unable to fetch template, call useTemplate() before display()");
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
     * Wrapper used to add the handling of onBlur action
     * @return unknown
     * @see actionHandler()
     */
    public function onBlur($params = null)
    {
        return $this->actionHandler('onBlur', $params);
    }

    /**
     * Wrapper used to add the handling of onClick action
     * @return unknown
     * @see actionHandler()
     */
    public function onClick($params = null)
    {
        return $this->actionHandler('onClick', $params);
    }

    /**
     * Wrapper used to add the handling of onChange action
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
     * Wrapper used to add the handling of onKeyPress action
     * @return unknown
     * @see actionHandler()
     */
    public function onKeyPress($params = null)
    {
        return $this->actionHandler('onKeyPress', $params);
    }

    /**
     * Wrapper used to add the handling of onKeyUp action
     * @return unknown
     * @see actionHandler()
     */
    public function onKeyUp($params = null)
    {
        return $this->actionHandler('onKeyUp', $params);
    }

    /**
     * Wrapper used to add the handling of onKeyDown action
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
     * @return P4A_Widget
     */
    public function addTempVar($name, $value)
    {
        $this->_temp_vars[$name] = $value;
        return $this;
    }

    /**
     * Drop a temporary variable
     * @param string $name
     * @return P4A_Widget
     */
    public function dropTempVar($name)
    {
        if (isset($this->_temp_vars[$name])) {
            unset($this->_temp_vars[$name]);
        }
        return $this;
    }

    /**
     * Clear temporary vars list
     * @return P4A_Widget
     */
    public function clearTempVars()
    {
        $this->_temp_vars = array();
        return $this;
    }

    /**
     * @param string $class
     * @return P4A_Widget
     */
    public function addCSSClass($class)
    {
        $this->_css_classes[] = $class;
        return $this;
    }

    /**
     * @param string $class
     * @return P4A_Widget
     */
    public function removeCSSClass($class)
    {
        $key = array_search($class, $this->_css_classes);
        if ($key !== false) {
            unset($this->_css_classes[$key]);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getCSSClasses()
    {
        return $this->_css_classes;
    }

    /**
     * @param string $text
     * @return P4A_Label
     */
    public function setTooltip($text)
    {
        $this->_tooltip = $text;
        return $this;
    }

    /**
     * @return string
     */
    function getTooltip()
    {
        return $this->_tooltip;
    }

    /**
     * @return P4A_Widget
     */
    public function redesign()
    {
        P4A::singleton()->redesign($this->getId());
        return $this;
    }
}