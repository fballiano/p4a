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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	class P4A_OBJECT
	{
		/**
		 * Object's ID
		 * @access private
		 * @var string
		 */
		var $id = NULL;
		
		/**
		 * Object's name
		 * @access public
		 * @var string
		 */
		var $name = NULL;
		
		/**
		 * Keeps the association between an action and its listener.
		 * @var array
		 * @access private
		 */
		var $map_actions = array();
		
		/**
		 * Count the references for an object
		 * @var integer
		 * @access private
		 */
		var $num_references = 0; 


		/**
		 * The children objects collection
		 * @var array
		 * @access private
		 */
		var $children = array();
		
		/**
		 * The parents objects collection
		 * @var array
		 * @access private
		 */
		var $parents = array();		
		
		/**
		 * Sets if the object it's destroyed
		 * @var boolean
		 * @access private
		 */		
		var $destroyed = FALSE;
		
				
		/**
		 * Class constructor.
		 * Sets default properties and store the object in the application object stack.
		 * @param string	Object identifier, when you add an object to another object (such as $p4a) you can access to it by $p4a->object_name.
		 * @param string	Prefix string for ID generation.
		 * @param string	Object ID identifies an object in the $p4a's object collection. You can set a static ID if you want that all clients uses the same ID (tipically for web sites).
		 * @access private 
		 */
 		function &p4aObject($prefix = 'obj', $id = NULL)
		{
			$p4a =& P4A::singleton();
		
			// Setting unique identifier ID
			if( $id === NULL )  {
				$this->id = uniqid($prefix);
			}else{
				$this->id = $prefix . $id ;	
			}
		}
		
		//todo
		function build($class, $name)
		{
			$p4a =& P4A::singleton();
			$this->$name = & new $class();
			$p4a->store($this->$name);
		}
		
		/**
		 * Destroys the object
		 * Retrieves all children objects and destroy them.
		 * It is able to check if children object are referenced elesewhere
		 * in the application, if true the children will not be destroyed.
		 * @access public
		 */	
		function destroy()
		{
			$this->destroyed = TRUE;
			foreach($this->children as $id)
			{
				if ( isset($this->p4a->objects[$id]) and (!$this->p4a->objects[$id]->destroyed))
				{
					$num_references = $this->p4a->objects[$id]->num_references;
					if ( $num_references == 1)
					{	
						$this->p4a->objects[$id]->destroy();
					}
					else
					{
						list($parents, $children) = $this->p4a->objects[$id]->getGenealogy();
						if (count(array_diff($parents, $children)) == 0)
						{
							$children = array_diff($children, array($this->id));
							foreach($children as $child){
								if (isset($this->p4a->objects[$child])){
									$this->p4a->objects[$child]->destroy();
								}								
							}
						}
						else
						{
							$this->p4a->objects[$id]->num_references--;
						}
					}
				}
			}
			unset($this->p4a->objects[$this->id]);
			$this = NULL;
		}
		
		/**
		 * Returns all parents and children of the object.
		 * Params are used for recursion
		 * @access private
		 * @param array		The parents
		 * @param array 	The children
		 * @return array	A list with 0=>parents, 1=>children
		 */
		function getGenealogy($parents = array(), $children = array())
		{
			if (!$parents){
				$parents[] = $this->id;
				$children[] = $this->id;
			}
			foreach($this->children as $child){
				if (isset($this->p4a->objects[$child])
					and !in_array($child, $children))
				{
					$children[] = $child;
					foreach($this->p4a->objects[$child]->parents as $parent)
					{
						if (!in_array($parent, $parents) and 
							isset($this->p4a->objects[$parent]))
						{
							$parents[] = $parent; 		
						}
					}
					list($parents, $children) = $this->p4a->objects[$child]->getGenealogy($parents, $children);
				}
			}
			return array($parents, $children);
		}
		
		/**
		 * Gets all children of the object
		 * Params are used for recursion.
		 * @access private
		 * @param array		The children array passed in recursion
		 * @return array	All children in array
		 */
		function getChildren($children = array())
		{
			if (!$children){
				$children[] = $this->id;
			}
			
			foreach($this->children as $id)
			{
				if (! in_array($id, $children)  and isset($this->p4a->objects[$id]))
				{
					$children[] = $id;
					$children =  $this->p4a->objects[$id]->getChildren($children);
				}
			}
			return $children; 
		}
		
		/**
		 * Adds an object to the objects collection of the current object.
		 * @param object object		The object to be added.
		 * @param string			If "test" is given, then the object will be in $this->test[$object->name]
		 * @param string			If "test" is given, then the object will be $this->test instead of $this->{$object->name}
		 * @access public
		 */
		function addObject(&$object, $collection = NULL, $name = NULL)
		{
			if (!$name){ 
				$name = $object->name;
			}
			
			//if (! in_array($this->id, $object->getChildren())) {
				$object->num_references++;
				$object->parents[] = $this->id;
				$this->children[] = $object->id; 			
			//}
			
			unset($object->parent);
			unset($object->mask);
			
			$object->parent =& $this;
			$object->mask 	=& $this;
			
			if ($collection === NULL ){
				$this->$name =& $object;
			}else{
				eval('$this->' .  $collection . '[$name] =& $object;');
			}  
		}

		/**
		 * Sets the object's name.
		 * @access public
		 */
		function setName($name)
		{
			$this->name=$name;
		}
		
		/**
		 * Returns the object name.
		 * @access public
		 * @return string
		 * @see $name
		 */
		function getName()
		{
			return $this->name;
		}
		
		/**
		 * Handle an action implemented by the object.
		 * @param string	The action to be handled.
		 * @param mixed		Parameter that will be passed to the action handler.
		 * @access private
		 */
		function actionHandler($action, $param = NULL)
		{
			if ( array_key_exists( $action, $this->map_actions ))
			{
				$interceptor =& $this->map_actions[$action]['object'];
				$method = $this->map_actions[$action]['method'];
				if ($param !== NULL){
					eval('$return = $interceptor->' . $method . '($this,$param);');
				}else{
					eval('$return = $interceptor->' . $method . '($this);');
				}
				return $return;
			}
		}
			
		/**
		 * Tells an object to execute a method when an action is called.
		 * @param object object		The object that has the method.
		 * @param string			The action triggered by an event.
		 * @param string			The method that will be executed.
		 * @access public
		 */
		function intercept(&$object, $action, $method)
		{
			$object->map_actions[$action] = array();
			$object->map_actions[$action]['object'] =& $this;
			$object->map_actions[$action]['method'] = $method;	
		}
		
		/**
		 * Wrapper for setting an intercepted event on an object.
		 * 
		 * @param string			The action's name.
		 * @param object object		The object that will intercept the action.
		 * @param string			The method that will be called.
		 * @access public
		 */
		function implementMethod($action, &$object, $method )
		{
			$object->intercept( $this, $action, $method );
		}
		
		/**
		 * Tells if an action is triggered.
		 * @param string	The action.
		 * @return boolean
		 * @access public
		 */
		function isActionTriggered($action)
		{
			if ( array_key_exists( $action, $this->map_actions ))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/**
		 * Returns the "CLASS" type of the object.
		 * @return string
		 * @access public
		 */
		function getObjectType()
		{
			return get_class($this);
		}
		
		/**
		 * Handle an error action implemented by the object.
		 * Checks if the the specified action is triggered and calls it,
		 * if the action is not triggered calls the general error handler
		 * for the object.
		 * @param string	The action to be handled.
		 * @param mixed		Parameter that will be passed to the error handler.
		 * @access private
		 */
		function errorHandler($action, $param = NULL)
		{
			$interceptor = NULL;
			$method = NULL;
			
			if (array_key_exists( $action, $this->map_actions ))
			{
				$interceptor =& $this->map_actions[$action]['object'];
				$method = $this->map_actions[$action]['method'];
			}
			elseif(array_key_exists( 'onError', $this->map_actions ))
			{
				$interceptor =& $this->map_actions['onError']['object'];
				$method = $this->map_actions['onError']['method'];
			}
			elseif(isset($this->p4a) and array_key_exists( 'onError', $this->p4a->map_actions ))
			{
				$interceptor =& $this->p4a->map_actions['onError']['object'];
				$method = $this->p4a->map_actions['onError']['method'];
			}
			
			if ($interceptor !== NULL)
			{
    			if ($param !== NULL){
    				eval('$return = $interceptor->' . $method . '($param);');
    			}else{
    				eval('$return = $interceptor->' . $method . '();');
    			}		
    			return $return;
			}
			else
			{
				if (isset($this->p4a)) {
					$p4a =& $this->p4a;
				} else {
					$p4a =& $this;
				}
				
				$p4a->close();
				$p4a->openMask('p4a_error');
				$p4a->mask_active->listener->renderError($param);
				$p4a->mask_active->raise();
			}
		}
	}
?>