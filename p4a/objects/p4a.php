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
	 * THE APPLICATION.
	 * Stands for the currently running istance of the application.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A extends P4A_Object
	{
		/**
		 * All P4A objects are stored here.
		 * @var array
		 * @access public
		 */
		var $objects = array();

		/**
		 * The currently active object.
		 * This means that here is the pointer to
		 * the last object that has triggered an event/action.
		 * @var object object
		 * @access public
		 */
		var $active_object = NULL;

		/**
		 * Pointer to the currently active mask.
		 * @var object object
		 * @access public
		 */
		var $active_mask = NULL;

		/**
		 * History of opened masks
		 * @var object object
		 * @access public
		 */
		var $masks_history = array();

		/**
		 * Opened masks are stored here.
		 * @var object object
		 * @access public
		 */
		var $masks = null;

		/**
		 * I18n objects and methods.
		 * @var i18n
		 * @access public
		 * @see P4A_I18N
		 */
		var $i18n = array();

       	/**
		 * Application's title.
		 * @var string
		 * @access private
		 */
		var $title = NULL;

       	/**
		 * Loaded libraries registry.
		 * A library is a file in P4A_APPLICATION_LIBRARIES_DIR wich is included every time.
		 * @var array
		 * @access private
		 */
		var $libraries = array();

		/**
		 * Timers container.
		 * @var array
		 * @access private
		 */
		var $timer = array();

		/**
		 * CSS container.
		 * @var array
		 * @access private
		 */
		var $_css = array();

		/**
		 * javascript container.
		 * @var array
		 * @access private
		 */
		var $_javascript = array();

		/**
		 * Is the browser a handheld?
		 * @var boolean
		 * @access private
		 */
		var $handheld = false;

		/**
		 * Is the browser Internet Explorer?
		 * @var boolean
		 * @access private
		 */
		var $internet_explorer = false;

		/**
		 * Class constructor.
		 * @access private
		 */
		function p4a()
		{
			//do not call parent constructor
			$_SESSION["p4a"] =& $this;

			$this->addJavascript(P4A_THEME_PATH . "/p4a.js");

			require_once dirname(dirname(__FILE__)) . '/libraries/phpsniff/phpSniff.class.php';
			$client =& new phpSniff();

			$this->addCss(P4A_THEME_PATH . "/screen.css", "all");
			$this->addCss(P4A_THEME_PATH . "/screen.css", "print");
			$this->addCss(P4A_THEME_PATH . "/print.css", "print");
			$this->addCss(P4A_THEME_PATH . "/handheld.css", "handheld");

			if ($client->browser_is('ie')) {
				$this->internet_explorer = true;
				$this->addCss(P4A_THEME_PATH . "/iehacks.css");
			}

			if (!$client->has_feature('css2') or P4A_FORCE_HANDHELD_RENDERING) {
				$this->handheld = true;
				$this->css = array();
				$this->addCss(P4A_THEME_PATH . "/handheld.css");
			}

			if ($this->isInternetExplorer() and !$this->isHandheld()) {
				$this->addJavascript(P4A_THEME_PATH . "/ie7/ie7-standard-p.js");
			}

			$this->init();
		}

		function isInternetExplorer()
		{
			return $this->internet_explorer;
		}

		function isHandheld()
		{
			if (P4A_FORCE_HANDHELD_RENDERING) {
				return true;
			}

			return $this->handheld;
		}

		function &singleton($class_name = "p4a")
		{
			if (!isset($_SESSION["p4a"])) {
				//$_SESSION["p4a"] =& new $class_name(); //this line crashes apache!!!
				return new $class_name();
			} else {
				return $_SESSION["p4a"];
			}
		}

		/**
		 * Destroys P4A data.
		 * @access public
		 */
		function close()
		{
			$id = session_id();
			session_destroy();
			session_id(substr($id, 0, -3));
			session_start();
			session_destroy();
		}

		/**
		 * Calls close() and then restart the application.
		 */
		function restart()
		{
			$this->close();
			header('Location: ' . P4A_APPLICATION_PATH );
		}

		/**
		 * Inits the timer.
		 * @access public
		 */
		function initTimer()
		{
			$this->timer = array();
			$this->timer[0]['description'] = 'START';
			$this->timer[0]['value'] = P4A_Get_Microtime();
			$this->timer[0]['diff'] = 0;
		}

		/**
		 * Takes a time snapshot with a given description.
		 * @access public
		 * @param string		The description
		 */
		function timer($description = 'TIMER')
		{
			$num_record = count($this->timer);
			$this->timer[$num_record]['description'] = $description;
			$this->timer[$num_record]['value'] = P4A_Get_Microtime();
			$this->timer[$num_record]['diff'] = $this->timer[$num_record - 1]['diff'] + (P4A_Get_Microtime() - $this->timer[$num_record - 1]['value']);
		}

		/**
		 * Prints out all timer values.
		 * @access public
		 */
		function dumpTimer()
		{
			foreach($this->timer as $time){
				print $time['diff'] .':' . $time['description'] . "\n";
			}
		}

		/**
		 * Executes the initialization method of the object.
		 * @access private
		 */
		function init()
		{
			$this->i18n =& new p4a_i18n(P4A_LOCALE);
			$this->i18n->setSystemLocale();

			$this->build("P4A_Collection", "masks");
			$this->build("P4A_Collection", "listeners");

			$this->actionHandler('init');
		}


		/**
		 * Executes the main cicle.
		 * @access public
		 */
		function main()
		{
			$this->i18n->setSystemLocale();
			P4A_DB::connect();

			$this->actionHandler('main');

			// Processing get and post.
			if (array_key_exists('_object', $_REQUEST) &&
				array_key_exists('_action', $_REQUEST) &&
				$_REQUEST['_action'] &&
				$_REQUEST['_object'] &&
				isset($this->objects[$_REQUEST['_object']]))
			{
				$object = $_REQUEST['_object'];
				$action = $_REQUEST['_action'];

				$aParams = array();
				// Removing files from request...
				// workaround for windows servers
				foreach ($_FILES as $key=>$value) {
					unset($_REQUEST[$key]);
				}

				foreach ($_REQUEST as $key=>$value) {
					if (substr($key, 0, 3) == 'fld') {
						if (gettype($value) == 'string') {
							$this->objects[$key]->setNewValue(stripslashes($value));
						} else {
							$this->objects[$key]->setNewValue($value);
						}
					} elseif (substr($key, 0, 5) == 'param' and strlen($value) > 0) {
						$aParams[] = $value;
					}
				}

				foreach ($_FILES as $key=>$value) {
					$value['name'] = str_replace( ',', ';', $value['name'] );
					$value['name'] = P4A_Get_Unique_File_Name($value['name'], P4A_UPLOADS_TMP_DIR);
					move_uploaded_file($value['tmp_name'], P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
					$value['tmp_name'] = '/' . P4A_UPLOADS_TMP_NAME . '/' . $value['name'];

					if ((substr($key, 0, 3) == 'fld') and ($value['error'] == 0)) {
						$new_value = $value['name'] . ',' . $value['tmp_name'] . ',' . $value['size'] . ',' . $value['type'] . ',' ;

						if (substr($value['type'], 0, 5) == 'image') {
							$image_data = getimagesize(P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
							$new_value .= $image_data[0] . ',' . $image_data[1];
						} else {
							$new_value .= ',';
						}

						$this->objects[$key]->setNewValue('{' . $new_value . '}');
						if ($this->objects[$key]->actionHandler('afterUpload') == ABORT) return ABORT;
					}
				}

				$this->setActiveObject($this->objects[$object]);
				$action_return = $this->objects[$object]->$action($aParams);
			}

			if (is_object($this->active_mask)) {
				$this->active_mask->main();
			}

			P4A_DB::close();

			session_write_close();
			session_id(substr(session_id(), 0, -6));
			flush();
		}

		/**
		 * Sets the desidered mask as active.
		 * @param string		The name of the mask.
		 * @access private
		 */
		function setActiveMask($mask_name)
		{
			$mask =& P4A_Mask::singleton($mask_name);
			$this->active_mask =& $mask;
		}

		/**
		 * Sets the desidered object as active.
		 * @param object object		The object
		 * @access private
		 * @see $active_object
		 */
		function setActiveObject(&$object)
		{
			unset($this->active_object);
			$this->active_object =& $object;
		}

		 /**
		 * Opens a mask ed sets it active.
		 * @access public
		 */
		function &openMask($mask_name)
		{
			if ($this->actionHandler('beforeOpenMask') == ABORT) return ABORT;

			if ($this->isActionTriggered('onOpenMask')) {
				if ($this->actionHandler('onOpenMask') == ABORT) return ABORT;
			} else {
				P4A_Mask::singleton($mask_name);

				//Update masks history
				if (is_object($this->active_mask) and $this->active_mask->getName() != $mask_name) {
					array_push($this->masks_history, $this->active_mask->getName());
					//50 max history
					$this->masks_history = array_slice($this->masks_history, -50);
				}

				$this->setActiveMask($mask_name);
			}
			$this->actionHandler( 'afterOpenMask' );
			return $this->active_mask;
		}

		 /**
		 * Sets the previous mask the active mask
		 * @access public
		 */
	     function showPrevMask()
	     {
	     	if (sizeof($this->masks_history) > 0){
				$mask_name = array_pop($this->masks_history);
				$this->setActiveMask($mask_name);
	     	}
	     }

		 /**
		 * Gets an instance of the previous mask
		 * @access public
		 */
	     function &getPrevMask()
	     {
		 	$num_masks = sizeof($this->masks_history);
	     	if ($num_masks > 0){
				$mask_name = $this->masks_history[$num_masks-1];
				return $this->masks->$mask_name;
	     	}
	     }

		/**
		 * Checks if the desidered mask is in the masks collection.
		 * @param string		The mask's name.
		 * @access private
		 */
		 //todo
		function maskExists($mask_name)
		{
			if (array_key_exists($mask_name, $this->masks)){
				return TRUE;
			}else{
				return FALSE;
			}
		}

		/**
		 * Adds an object to the objects collection.
		 * @param object object		The object.
		 * @access private
		 */
		function store(&$object)
		{
			$object_id = $object->getId();
			if (array_key_exists($object_id, $this->objects)){
				ERROR('DUPLICATE OBJECT');
			}else{
				$this->objects[$object_id] = &$object;
			}
		}

		//todo
		function &getObject($object_id)
		{
			if (array_key_exists($object_id, $this->objects)){
				return $this->objects[$object_id];
			}else{
				return null;
			}
		}

		/**
		 * Sets the title for the application.
		 * @param string	Mask title.
		 * @access public
		 */
		function setTitle($title)
		{
			$this->title = $title ;
		}

		/**
		 * Returns the title for the application.
		 * @return string
		 * @access public
		 */
		function getTitle()
		{
			return $this->title ;
		}

		/**
		 * Include CSS
		 * @param string		The URI of CSS.
		 * @param string		The CSS media.
		 * @access public
		 */
		function addCss($uri, $media = "screen")
		{
			if (!isset($this->_css[$uri])) {
				$this->_css[$uri] = array();
			}
			$this->_css[$uri][$media] = null;
		}

		/**
		 * Drop inclusion of CSS file
		 * @param string		The URI of CSS.
		 * @param string		The CSS media.
		 * @access public
		 */

		function dropCss($uri, $media = "screen")
		{
			if(isset($this->_css[$uri]) and isset($this->_css[$uri][$media])){
				unset($this->_css[$uri][$media]);
				if (empty($this->_css[$uri])) {
					unset($this->_css);
				}
			}
		}

		/**
		 * Include a javascript file
		 * @param string		The URI of file.
		 * @access public
		 */
		function addJavascript($uri)
		{
			$this->_javascript[$uri] = null;
		}

		/**
		 * Drop inclusion of javascript file
		 * @param string		The URI of CSS.
		 * @access public
		 */

		function dropJavascript($uri)
		{
			if(isset($this->_javascript[$uri])){
				unset($this->_javascript[$uri]);
			}
		}
	}
?>
