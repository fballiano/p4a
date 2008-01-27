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
 * The base class extended by every p4a object.
 * Keeps object identifiers (id, name) and any method
 * for event triggering.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Object
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
	 * Keeps the association between an action and its listener.
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
	 * Instance a new $class ojbect and sets its name to $name
	 * @param string $class
	 * @param string $name
	 * @return P4A_Object
	 */
	function &build($class, $name)
	{
		$p4a =& P4A::singleton();
		$args = func_get_args();
		$str_args = '$this->$name =& new $class(';

		for ($i=1; $i<sizeof($args); $i++) {
			$str_args .= '$args[' . $i . '], ';
		}

		$str_args = substr($str_args, 0, -2);
		$str_args .= ');';

		eval($str_args);

		$p4a->store($this->$name);
		$this->_objects[] = $this->$name->getID();
		$this->$name->setParentID($this->getID());
		return $this->$name;
	}

	/**
	 * @param string $object_id
	 */
	protected function setParentID($object_id)
	{
		$this->_parent_id = $object_id;
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
	public function destroy()
	{
		$p4a =& P4A::singleton();
		$parent =& $p4a->getObject($this->getParentID());

		$this_id = $this->getID();
		$this_name = $this->getName();

		foreach($this->_objects as $key=>$object_id){
			if (array_key_exists($object_id, $p4a->objects)) {
				$p4a->objects[$object_id]->destroy();
			}
		}

		unset($parent->$this_name);
		unset($p4a->objects[$this_id]);
	}

	/**
	 * @param string $name
	 */
	protected function setName($name)
	{
		$this->_name = $name;
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
			$params = '';
			foreach ($arguments as $k=>$v) {
				$params .= "\$arguments[$k],";
			}
			$params = substr($params, 0, -1);
			eval("\$return = \$interceptor->$method($params);");
			return $return;
		} else {
			return null;
		}
	}

	/**
	 * Tells an object to execute a method when an action is called
	 * @param object $object
	 * @param string $action The action triggered by an event
	 * @param string $method The method that will be executed
	 */
	public function intercept(&$object, $action, $method = null)
	{
		$action = strtolower($action);
		if ($method === null) $method = $action;
		$object->_map_actions[$action] = array();
		$object->_map_actions[$action]['object'] =& $this;
		$object->_map_actions[$action]['method'] = $method;
	}

	/**
	 * @param string $action
	 */
	public function dropIntercept($action)
	{
		$object->_map_actions[$action] = array();
	}

	/**
	 * Wrapper for setting an intercepted event on an object
	 *
	 * @param string $action The action's name
	 * @param object $object The object that will intercept the action
	 * @param string $method The method that will be called
	 */
	public function implementMethod($action, &$object, $method)
	{
		$object->intercept($this, $action, $method);
	}

	/**
	 * @param string $action
	 * @return boolean
	 */
	public function isActionTriggered($action)
	{
		if (array_key_exists($action, $this->_map_actions)) {
			return true;
		} else {
			return false;
		}
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
		$p4a =& P4A::singleton();
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
				eval('$return = $interceptor->' . $method . '($param);');
			} else {
				eval('$return = $interceptor->' . $method . '();');
			}
			return $return;
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

		if (!$func) {
			die("Method $name not found");
		} else {
			$this->_helpers[$name] = array($file, $func);
		}

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