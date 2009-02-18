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
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

/**
 * The base class extended by every p4a object.
 * Keeps object identifiers (id, name) and any method
 * for event triggering.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
abstract class P4A_Object
{
	/**
	 * @var string
	 */
	protected $_id = null;

	/**
	 * @var string
	 */
	protected $_parent_id = null;

	/**
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Keeps the association between an action and its listener
	 * @var array
	 */
	protected $_map_actions = array();

	/**
	 * @var array
	 */
	protected $_objects = array();

	/**
	 * helpers cache
	 * @var array
	 */
	protected $_helpers = array();

	/**
	 * @param string Object identifier, when you add an object to another object (such as $p4a) you can access to it by $p4a->object_name
	 * @param string Prefix string for ID generation
	 * @param string Object ID identifies an object in the $p4a's object collection. You can set a static ID if you want that all clients uses the same ID (tipically for web sites).
	 */
	public function __construct($name = null, $prefix = 'obj', $id = null)
	{
		$this->setName($name);

		if ($id === null) {
			$this->_id = uniqid($prefix);
		} else {
			$this->_id = $prefix . $id;
		}
	}

	/**
	 * Instance a new $class ojbect and set its name to $name
	 * @param string $class
	 * @param string $name
	 * @return P4A_Object
	 */
	function build($class, $name)
	{
		$args = func_get_args();
		$str_args = '$this->$name = new $class(';

		for ($i=1; $i<sizeof($args); $i++) {
			$str_args .= '$args[' . $i . '], ';
		}

		$str_args = substr($str_args, 0, -2);
		$str_args .= ');';

		eval($str_args);

		P4A::singleton()->store($this->$name);
		$this->_objects[] = $this->$name->getID();
		$this->$name->setParentID($this->getID());
		return $this->$name;
	}

	/**
	 * @param string $object_id
	 * @return P4A_Object
	 */
	protected function setParentID($object_id)
	{
		$this->_parent_id = $object_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParentID()
	{
		return $this->_parent_id;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Destroys the object
	 * Retrieves all children objects and destroy them.
	 */
	public function destroy($name = null)
	{
		$p4a = P4A::singleton();
		$parent = $p4a->getObject($this->getParentID());
		if ($parent === null) $parent = $p4a;

		$this_id = $this->getID();
		$this_name = $this->getName();

		foreach($this->_objects as $key=>$object_id) {
			if (isset($p4a->objects[$object_id]) and $p4a->objects[$object_id] instanceof P4A_Object) {
				$p4a->objects[$object_id]->destroy();
			}
			unset($this->_objects[$key]);
		}
		
		$parent->$this_name = null;
		unset($p4a->objects[$this_id]);
	}

	/**
	 * @param string $name
	 * @return P4A_Object
	 */
	protected function setName($name)
	{
		$this->_name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Handle an action implemented by the object
	 * @param string $action
	 */
	public function actionHandler($action)
	{
		$action = strtolower($action);
		if (array_key_exists($action, $this->_map_actions)) {
			$interceptor =& $this->_map_actions[$action]['object'];
			$method = $this->_map_actions[$action]['method'];
			$arguments = func_get_args();
			$arguments[0] =& $this;
			return call_user_func_array(array($interceptor, $method), $arguments);
		} else {
			return null;
		}
	}

	/**
	 * Tells an object to execute a method on the current object when an action is called
	 * @param object $object
	 * @param string $action The action triggered by an event
	 * @param string $method The method that will be executed
	 * @return P4A_Object
	 */
	public function intercept($object, $action, $method = null)
	{
		$object->implement($action, $this, $method);
		return $this;
	}

	/**
	 * Tells the current object to execute a method on another object when an action is called
	 * @param string $action The action's name
	 * @param object $object The object that will intercept the action
	 * @param string $method The method that will be called
	 * @return P4A_Object
	 */
	public function implement($action, $object, $method = null)
	{
		$action = strtolower($action);
		if ($method === null) $method = $action;
		if (P4A_Is_Browser_Event($action) and !isset($this->actions[$action])) {
			$this->addAction($action);
		}
		$this->_map_actions[$action] = array();
		$this->_map_actions[$action]['object'] = $object;
		$this->_map_actions[$action]['method'] = $method;
		return $this;
	}
	
	/**
	 * alias for dropImplement
	 * @param string $action
	 * @return P4A_Object
	 * @see dropImplement
	 * @deprecated 
	 */
	public function dropMethod($action)
	{
		return $this->dropImplement($action);
	}
	
	/**
	 * Removes handling an action
	 * @param string $action
	 * @return P4A_Object
	 */
	public function dropImplement($action)
	{
		if (isset($this->_map_actions[$action])) {
			unset($this->_map_actions[$action]);
		}
		return $this;
	}

	/**
	 * @param string $action
	 * @return boolean
	 */
	public function isActionTriggered($action)
	{
		if (array_key_exists(strtolower($action), $this->_map_actions)) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the "CLASS" type of the object.
	 * @return string
	 */
	public function getObjectType()
	{
		return get_class($this);
	}

	/**
	 * Handle an error action implemented by the object.
	 * Checks if the the specified action is triggered and calls it,
	 * if the action is not triggered calls the general error handler
	 * for the object.
	 * @param string $action
	 * @param mixed $param Parameter(s) that will be passed to the error handler
	 * @return unknown
	 */
	public function errorHandler($action, $param = null)
	{
		$p4a = P4A::singleton();
		$interceptor = null;
		$method = null;

		if (array_key_exists($action, $this->_map_actions)) {
			$interceptor =& $this->_map_actions[$action]['object'];
			$method = $this->_map_actions[$action]['method'];
		} elseif(array_key_exists('onError', $this->_map_actions)) {
			$interceptor =& $this->_map_actions['onError']['object'];
			$method = $this->_map_actions['onError']['method'];
		} elseif(isset($p4a) and array_key_exists('onError', $p4a->_map_actions)) {
			$interceptor =& $p4a->_map_actions['onError']['object'];
			$method = $p4a->_map_actions['onError']['method'];
		}

		if ($interceptor !== null) {
			if ($param !== null) {
				return eval('return $interceptor->' . $method . '($param);');
			} else {
				return eval('return $interceptor->' . $method . '();');
			}
		} else {
			ob_start();
			$p4a->openMask('p4a_mask_error');
			$p4a->active_mask->main($param);
			$p4a->close();
			ob_end_flush();
			return ABORT;
		}
	}

	/**
	 * just a placeholder
	 *
	 * @param mixed $params
	 * @return unknown
	 */
	public function void($params = null)
	{
		return $this->actionHandler('void', $params);
	}

	/**
	 * @param string $name
	 */
	protected function _loadHelper($name)
	{
		$a_dirs[] = P4A_APPLICATION_LIBRARIES_DIR;
		$a_dirs[] = P4A_LIBRARIES_DIR;
		$a_dirs[] = P4A_ROOT_DIR . '/p4a/helpers';

		$class_name = strtolower(get_class($this));
		$classes[] = $class_name;
		while ($class_name = strtolower(get_parent_class($class_name))) {
			$classes[] = $class_name;
		}
		$classes = array_reverse($classes);
		foreach ($a_dirs as $dir) {
			foreach ($classes as $class_name) {
				if (file_exists("{$dir}/{$class_name}_{$name}.php")) {
					$file = "{$dir}/{$class_name}_{$name}.php";
					$func = "{$class_name}_{$name}";
					break 2;
				}
			}
		}

		if (!isset($func)) {
			$backtrace = debug_backtrace();
			$backtrace = $backtrace[2];
			P4A_Error_Handler(E_USER_ERROR, "Method $name not found", $backtrace['file'], $backtrace['line']);
		}
		$this->_helpers[$name] = array($file, $func);
	}

	/**
	 * @param string $name
	 * @param mixed $args
	 * @return unknown
	 */
	private function __call($name, $args)
	{
		$name = strtolower($name);
		if (!array_key_exists($name, $this->_helpers)) {
			$this->_loadHelper($name);
		}

		list($file, $func) = $this->_helpers[$name];
		require_once $file;

	 	// call the helper method
	 	$a = array($this);
	 	array_push($a, $args);
		return call_user_func_array($func, $a);
	}
}