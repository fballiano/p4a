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
	 * All P4A objects are stored here
	 * @var array
	 */
	public $objects = array();

	/**
	 * The currently active object.
	 * This means that here is the pointer to
	 * the last object that has triggered an event/action.
	 * @var P4A_Object
	 */
	public $active_object = null;

	/**
	 * Pointer to the currently active mask
	 * @var P4A_Mask
	 */
	public $active_mask = null;

	/**
	 * History of opened masks
	 * @var array
	 */
	private $masks_history = array();

	/**
	 * Opened masks are stored here
	 * @var P4A_Collection
	 */
	public $masks = null;

	/**
	 * @var P4A_I18N
	 */
	public $i18n = null;

	/**
	 * @var string
	 */
	private $title = null;

	/**
	 * Timers container
	 * @var array
	 */
	private $timer = array();

	/**
	 * CSS container
	 * @var array
	 */
	private $_css = array();

	/**
	 * javascript container
	 * @var array
	 */
	private $_javascript = array();

	/**
	 * Is the browser a handheld?
	 * @var boolean
	 */
	private $handheld = false;

	/**
	 * Is the browser Internet Explorer?
	 * @var boolean
	 */
	private $internet_explorer = false;

	/**
	 * Find wich browser is the user using
	 * @var array
	 */
	private $browser_identification = array();

	/**
	 * Counter to avoid browser's back/forward
	 * @var integer
	 */
	private $_action_history_id = 0;

	/**
	 * @var array
	 */
	private $_to_redesign = array();
	
	/**
	 * @var boolean
	 */
	private $_redesign_popup = false;
	
	/**
	 * @var boolean
	 */
	private $_ajax_enabled = P4A_AJAX_ENABLED;
	
	/**
	 * @var boolean
	 */
	private $_in_ajax_call = false;

	/**
	 * The name of the popup mask
	 * @var string
	 */
	private $_popup = null;

	/**
	 * forces an HTTP refresh event
	 * @var boolean
	 */
	private $_do_refresh = false;

	/**
	 * @var array
	 */
	private $messages = array();

	public function __construct()
	{
		//do not call parent constructor
		$_SESSION["p4a"] =& $this;
		$this->i18n =& new p4a_i18n(P4A_LOCALE);

		$this->build("P4A_Collection", "masks");
		$this->browser_identification = $this->detectClient();

		$this->addJavascript(P4A_THEME_PATH . "/jquery/jquery.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/form.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/dimensions.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/jmedia.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/autocomplete.js");
		if (!$this->isHandheld()) {
			$this->addJavascript(P4A_THEME_PATH . "/jquery/farbtastic.js");
			$this->addJavascript(P4A_THEME_PATH . "/jquery/jqmodal.js");
			$this->addJavascript(P4A_THEME_PATH . "/jquery/interface.js");
			$this->addJavascript(P4A_THEME_PATH . "/jquery/ui.datepicker.js");
			$this->addJavascript(P4A_THEME_PATH . "/widgets/rich_textarea/fckeditor.js");
			$this->addCss(P4A_THEME_PATH . "/jquery/ui.datepicker.css", "screen");
			$this->addCSS(P4A_THEME_PATH . '/widgets/tab_pane/screen.css', 'screen');
			$this->addCSS(P4A_THEME_PATH . '/widgets/tab_pane/screen.css', 'print');
			$this->addCSS(P4A_THEME_PATH . '/widgets/tab_pane/print.css', 'print');
		}
		$this->addJavascript(P4A_THEME_PATH . "/p4a.js");

		$this->addCss(P4A_THEME_PATH . "/screen.css", "all");
		$this->addCss(P4A_THEME_PATH . "/screen.css", "print");
		$this->addCss(P4A_THEME_PATH . "/print.css", "print");
		$this->addCss(P4A_THEME_PATH . "/handheld.css", "handheld");

		if ($this->isInternetExplorer()) {
			$this->addCss(P4A_THEME_PATH . "/iehacks.css");
		}

		if ($this->isHandheld()) {
			$this->css = array();
			$this->addCss(P4A_THEME_PATH . "/handheld.css");
		}

		if ($this->isInternetExplorer() and !$this->browser_identification['ie7up'] and !$this->isHandheld()) {
			$this->addJavascript(P4A_THEME_PATH . "/jquery/ifixpng.js");
			$this->addJavascript(P4A_THEME_PATH . "/iefixes.js");
		}
	}

	/**
	 * @return array
	 */
	public function detectClient()
	{
		require_once dirname(dirname(__FILE__)) . '/libraries/pear_net_useragent_detect.php';
		Net_UserAgent_Detect::detect();

		$this->internet_explorer = Net_UserAgent_Detect::isIE();
		$this->_ajax_enabled = (Net_UserAgent_Detect::hasFeature('ajax') and P4A_AJAX_ENABLED);

		if (!Net_UserAgent_Detect::hasFeature('ajax') or P4A_FORCE_HANDHELD_RENDERING) {
			$this->handheld = true;
		}

		return Net_UserAgent_Detect::_getStaticProperty('browser');
	}

	/**
	 * @return boolean
	 */
	public function isInternetExplorer()
	{
		return $this->internet_explorer;
	}

	/**
	 * @return boolean
	 */
	public function isHandheld()
	{
		if (P4A_FORCE_HANDHELD_RENDERING) return true;
		return $this->handheld;
	}

	/**
	 * @return boolean
	 */
	public function isAjaxEnabled()
	{
		return $this->_ajax_enabled;
	}

	/**
	 * @return boolean
	 */
	public function isPopupOpened()
	{
		return ($this->_popup !== null);
	}

	/**
	 * @return boolean
	 */
	public function inAjaxCall()
	{
		return $this->_in_ajax_call;
	}

	public static function singleton($class_name = "p4a")
	{
		if (!isset($_SESSION)) {
			session_name(preg_replace('~\W~', '_', P4A_APPLICATION_NAME));
			session_start();
		}

		if (isset($_SESSION["p4a"])) {
			return $_SESSION["p4a"];
		}
		return new $class_name();
	}

	/**
	 * Destroys P4A data
	 */
	public function close()
	{
		session_destroy();
	}

	/**
	 * Calls close() and then restart the application
	 * @see close()
	 */
	public function restart()
	{
		$this->close();
		header('Location: ' . P4A_APPLICATION_PATH );
	}

	public function initTimer()
	{
		$this->timer = array();
		$this->timer[0]['description'] = 'START';
		$this->timer[0]['value'] = P4A_Get_Microtime();
		$this->timer[0]['diff'] = 0;
	}

	/**
	 * Takes a time snapshot with a given description
	 * @param string $description
	 */
	public function timer($description = 'TIMER')
	{
		$num_record = count($this->timer);
		$this->timer[$num_record]['description'] = $description;
		$this->timer[$num_record]['value'] = P4A_Get_Microtime();
		$this->timer[$num_record]['diff'] = $this->timer[$num_record - 1]['diff'] + (P4A_Get_Microtime() - $this->timer[$num_record - 1]['value']);
	}

	/**
	 * Prints out all timer values
	 */
	public function dumpTimer()
	{
		foreach($this->timer as $time){
			print $time['diff'] .':' . $time['description'] . "\n";
		}
	}

	public function main()
	{
		$this->_in_ajax_call = (isset($_REQUEST['_ajax']) and $_REQUEST['_ajax']);
		$this->actionHandler('main');

		// Processing get and post.
		if (array_key_exists('_object', $_REQUEST) and
			array_key_exists('_action', $_REQUEST) and
			array_key_exists('_action_id', $_REQUEST) and
			$_REQUEST['_object'] and
			$_REQUEST['_action'] and
			$_REQUEST['_action_id'] and
			$_REQUEST['_action_id'] == $this->getActionHistoryId() and
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
					if (in_array($this->objects[$key]->getType(), array('file','image')) && strlen($value) == 0) {
						$this->objects[$key]->setNewValue(null);
						continue;
					}
					
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
				$extension = P4A_Get_File_Extension($value['name']);
				if (P4A_Is_Extension_Allowed($extension)) {
					$value['name'] = str_replace( ',', ';', $value['name'] );
					$value['name'] = P4A_Get_Unique_File_Name($value['name'], P4A_UPLOADS_TMP_DIR);
					move_uploaded_file($value['tmp_name'], P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
					$value['tmp_name'] = '/' . P4A_UPLOADS_TMP_NAME . '/' . $value['name'];

					if ((substr($key, 0, 3) == 'fld') and ($value['error'] == 0)) {
						$width = $height = null;
						require_once P4A_ROOT_DIR . "/p4a/libraries/getid3/getid3/getid3.php";
						$old_error_reporting = error_reporting(P4A_DEFAULT_MINIMAL_REPORTING);
						$getid3 = new getID3();
						$data = $getid3->analyze(P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
						error_reporting($old_error_reporting);
						if (isset($data['video']) and isset($data['video']['resolution_x']) and isset($data['video']['resolution_y'])) {
							$width = $data['video']['resolution_x'];
							$height = $data['video']['resolution_y'];
						}
						$new_value = "{$value['name']},{$value['tmp_name']},{$value['size']},{$value['type']},$width,$height" ;
						$this->objects[$key]->setNewValue('{' . $new_value . '}');
						if ($this->objects[$key]->actionHandler('afterUpload') == ABORT) return ABORT;
					} elseif ($value['error'] != 4) {
						$e = new P4A_Error("Error uploading files", $this);
						if ($this->errorHandler('onUploadDeniedExtension', $e) !== PROCEED) {
							die();
						}
					}
				} else {
					$e = new P4A_Error("Uploading $extension files is denied", $this);
					if ($this->errorHandler('onUploadDeniedExtension', $e) !== PROCEED) {
						die();
					}
				}
			}

			$this->setActiveObject($this->objects[$object]);
			$action_return = $this->objects[$object]->$action($aParams);
		}

		if ($this->_in_ajax_call) {
			$this->_action_history_id++;
			$this->raiseXMLResponse();
		} elseif (isset($_REQUEST['_p4a_session_browser'])) {
			if (!empty($_REQUEST['_p4a_session_browser']) and isset($this->objects[$_REQUEST['_p4a_session_browser']])) {
				$obj =& $this->objects[$_REQUEST['_p4a_session_browser']];
			} else {
				$obj =& $this;
			}

			$vars = get_object_vars($obj);
			ksort($vars);
			$name = $obj->getName();
			if (empty($name)) $name = "P4A main object";
			$name .= ' (' . get_class($obj) .  ')';

			echo "<h1>$name</h1>";
			echo "<table border='1'>";
			echo "<tr><th>key</th><th>value</th></tr>";
			foreach ($vars as $k=>$v) {
				$v = _P4A_Debug_Print_Variable($v);
				echo "<tr><td valign='top'>$k</td><td>$v</td></tr>";
			}
			echo "</table>";
		} elseif (isset($_REQUEST['_rte_file_manager']) and isset($_REQUEST['_object_id']) and isset($this->objects[$_REQUEST['_object_id']])) {
			require P4A_THEME_DIR . '/widgets/rich_textarea/editor/filemanager/connectors/php/connector.php';
		} elseif (isset($_REQUEST['_upload_path'])) {
			$path = P4A_UPLOADS_PATH;
			if (isset($_REQUEST['_object_id']) and isset($this->objects[$_REQUEST['_object_id']])) {
				$object =& $this->objects[$_REQUEST['_object_id']];
				if (is_object($object) and method_exists($object, 'getUploadSubpath')) {
					$path .= '/' . $object->getUploadSubpath();
				}
			}
			print preg_replace(array("~/+~", "~/$~"), array('/', ''), $path);
		} elseif (isset($_REQUEST['_p4a_autocomplete'])) {
			if (isset($_REQUEST['_object']) and
				isset($_REQUEST['q']) and
				isset($this->objects[$_REQUEST['_object']])) {
				$object =& $this->objects[$_REQUEST['_object']];
				$db = p4a_db::singleton($object->data_field->getDSN());
				$data =& $object->data;
				$description_field = $object->getSourceDescriptionField();
				$q = addslashes($_REQUEST['q']);					
				$where = $db->getCaseInsensitiveLikeSQL($description_field, "%$q%");
				$old_where = $data->getWhere();
				if ($old_where) {
					$where = "({$old_where}) AND ($where)";
				}
				$data->setWhere($where);
				$all = $data->getAll();
				$data->setWhere($old_where);
				foreach ($all as $row) {
					print "{$row[$description_field]}\n";
				}
			}
		} elseif (isset($_REQUEST['_p4a_date_format'])) {
			print $this->i18n->format($_REQUEST['_p4a_date_format'], 'date');
		} elseif (P4A_ENABLE_RENDERING and is_object($this->active_mask)) {
			$this->_action_history_id++;
			$this->active_mask->main();
		}

		$this->_to_redesign = array();
		$this->_redesign_popup = false;

		session_write_close();
		session_id(substr(session_id(), 0, -6));
		flush();
	}

	public function raiseXMLResponse()
	{
		ob_start();
		$script_detector = '<script.*?>(.*?)<\/script>';

		header('Content-Type: text/xml');
		print '<?xml version="1.0" encoding="utf-8" ?>';
		print '<ajax-response action_id="' . $this->getActionHistoryId() . '" focus_id="' . $this->getFocusedObjectId() . '">';
		if ($this->_do_refresh) {
			$this->_do_refresh = false;
			$javascript = 'document.location="' . P4A_APPLICATION_PATH . '";';
			print "<widget id='p4a_popup' display='inherit'>\n";
			print "<html><![CDATA[]]></html>\n";
			print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
			print "</widget>";
		} else {
			foreach ($this->getRenderedMessages() as $message) {
				print "\n<message><![CDATA[$message]]></message>";
			}
			while (list( ,$id) = each($this->_to_redesign)) {
				$object =& $this->getObject($id);
				$display = $object->isVisible() ? 'block' : 'none';
				$as_string = $object->getAsString();
				$javascript_codes = array();
				$javascript = '';
				$html = preg_replace("/{$script_detector}/si", '', $as_string);
				preg_match_all("/{$script_detector}/si", $as_string, $javascript_codes);
				$javascript_codes = $javascript_codes[1];
				foreach ($javascript_codes as $code) {
					$javascript .= "$code\n\n";
				}

				print "\n<widget id='$id' display='$display'>\n";
				print "<html><![CDATA[{$html}]]></html>\n";
				print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
				print "</widget>\n";

			}
			if ($this->_redesign_popup) {
				if ($this->_popup) {
					$as_string = p4a_mask::singleton($this->_popup)->getAsString();
					$javascript_codes = array();
					$javascript = "p4a_popup_show();\n\n";
					$html = preg_replace("/{$script_detector}/si", '', $as_string);
					preg_match_all("/{$script_detector}/si", $as_string, $javascript_codes);
					$javascript_codes = $javascript_codes[1];
					foreach ($javascript_codes as $code) {
						$javascript .= "$code\n\n";
					}
				} else {
					$html = '';
					$javascript = 'p4a_popup_hide();';
				}
				print "<widget id='p4a_popup' display='inherit'>\n";
				print "<html><![CDATA[<div id='p4a_popup' style='display:none'>{$html}</div>]]></html>\n";
				print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
				print "</widget>";
			}
		}
		print "</ajax-response>";

		if (P4A_AJAX_DEBUG) {
			if (($fp = @fopen(P4A_AJAX_DEBUG, 'w')) !== false) {
				@fwrite($fp, ob_get_contents());
				@fclose($fp);
			}
		}

		ob_end_flush();
	}

	/**
	 * Sets the desidered mask as active.
	 * @param string $mask_name
	 */
	private function setActiveMask($mask_name)
	{
		$this->active_mask =& P4A_Mask::singleton($mask_name);
	}

	/**
	 * Sets the desidered object as active.
	 * @param P4A_Object
	 */
	private function setActiveObject(&$object)
	{
		unset($this->active_object);
		$this->active_object =& $object;
	}

	public function openMask($mask_name)
	{
		if ($this->actionHandler('beforeOpenMask') == ABORT) return ABORT;

		if ($this->isActionTriggered('onOpenMask')) {
			if ($this->actionHandler('onOpenMask') == ABORT) return ABORT;
		} else {
			if ($this->isPopupOpened()) {
				$this->closePopup();
			}

			if ($this->inAjaxCall()) {
				$this->_do_refresh = true;
			}

			P4A_Mask::singleton($mask_name);

			//Update masks history
			if (is_object($this->active_mask) and $this->active_mask->getName() != $mask_name) {
				array_push($this->masks_history, $this->active_mask->getName());
				//50 max history
				$this->masks_history = array_slice($this->masks_history, -50);
			}

			$this->setActiveMask($mask_name);
		}
		$this->actionHandler('afterOpenMask');
		return $this->active_mask;
	}

	public function openPopup($mask_name)
	{
		//Close opened popup
		if ($this->_popup) {
			$this->closePopup();
		}

		$this->_popup = $mask_name;
		$mask =& p4a_mask::singleton($mask_name);
		$mask->isPopup(true);

		$this->_redesign_popup = true;
		return $mask;
	}

	public function closePopup($destroy = false)
	{
		if ($destroy) {
			$mask =& p4a_mask::singleton($this->_popup);
			$mask->destroy();
		} else {
			$mask =& p4a_mask::singleton($this->_popup);
			$mask->isPopup(false);
		}
		$this->_popup = null;
		$this->_redesign_popup = true;
	}

     public function showPrevMask()
     {
		//Close opened popup
		if ($this->_popup) {
			$this->closePopup();
			return;
		}

     	if (sizeof($this->masks_history) > 0){
			$mask_name = array_pop($this->masks_history);
			$this->setActiveMask($mask_name);
     	}
     }

     /**
      * Gets an instance of the previous mask
      * @return P4A_Mask
      */
     public function getPrevMask()
     {
	 	$num_masks = sizeof($this->masks_history);
     	if ($num_masks > 0){
			$mask_name = $this->masks_history[$num_masks-1];
			return $this->masks->$mask_name;
     	}
     }
     
     /**
      * @return string
      */
     public function getPopupMaskName()
     {
     	return $this->_popup;
     }
     
     /**
      * @return P4A_Mask
      */
     public function getPopupMask()
     {
     	return p4a_mask::singleton($this->_popup);
     }
     
	/**
	 * @param string $mask_name
	 * @return boolean
	 */
	public function maskExists($mask_name)
	{
		if (array_key_exists($mask_name, $this->masks)) {
			return true;
		}
		return false;
	}

	/**
	 * Adds an object to the objects collection
	 * @param P4A_Object
	 */
	public function store(&$object)
	{
		$object_id = $object->getId();
		if (array_key_exists($object_id, $this->objects)){
			ERROR('DUPLICATE OBJECT');
		} else {
			$this->objects[$object_id] = &$object;
		}
	}

	/**
	 * @param string $object_id
	 * @return P4A_Object
	 */
	public function getObject($object_id)
	{
		if (array_key_exists($object_id, $this->objects)){
			return $this->objects[$object_id];
		}
		return null;
	}

	/**
	 * Sets the title for the application
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Returns the title for the application
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Include a CSS file
	 * @param string $uri
	 * @param string $media
	 */
	public function addCss($uri, $media = "screen")
	{
		if (!isset($this->_css[$uri])) {
			$this->_css[$uri] = array();
		}
		$this->_css[$uri][$media] = null;
	}
	

	/**
	 * Drops inclusion of a CSS file
	 * @param string $uri
	 * @param string $media
	 */
	public function dropCss($uri, $media = "screen")
	{
		if(isset($this->_css[$uri]) and isset($this->_css[$uri][$media])){
			unset($this->_css[$uri][$media]);
			if (empty($this->_css[$uri])) {
				unset($this->_css);
			}
		}
	}
	
	public function getCss()
	{
		return $this->_css;
	}

	/**
	 * Includes a javascript file
	 * @param string $uri
	 */
	public function addJavascript($uri)
	{
		$this->_javascript[$uri] = null;
	}

	/**
	 * Drops inclusion of a javascript file
	 * @param string $uri
	 */
	public function dropJavascript($uri)
	{
		if(isset($this->_javascript[$uri])){
			unset($this->_javascript[$uri]);
		}
	}
	
	/**
	 * @return array
	 */
	public function getJavascript()
	{
		return $this->_javascript;
	}

	/**
	 * Action history ID is used to avoid browser's back/forward
	 * @access public
	 * @return integer
	 */
	public function getActionHistoryId()
	{
		return $this->_action_history_id;
	}

	/**
	 * @param string $id the id of the object to be redesigned
	 */
	public function redesign($id)
	{
		$this->_to_redesign[] = $id;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return P4A_VERSION;
	}

	/**
	 * @return string
	 */
	public function getFocusedObjectId()
	{
		if ($this->_popup) {
			return p4a_mask::singleton($this->_popup)->getFocusedObjectId();
		}
		return $this->active_mask->getFocusedObjectId();
	}
	
	/**
	 * Outputs a system message to user
	 * @param string $text
	 * @param string $icon
	 * @param integer $icon_size
	 */
	public function message($text, $icon = null, $icon_size = 32)
	{
		$this->messages[] = array($text, $icon, $icon_size);
	}
	
	/**
	 * Returns all the messages and clean the queue
	 * @return array
	 */
	public function getMessages()
	{
		$messages = $this->messages;
		$this->messages = array();
		return $messages;
	}
	
	/**
	 * Returns HTML rendered system messages and clean the queue
	 * @return array
	 */
	public function getRenderedMessages()
	{
		$messages = $this->getMessages();
		foreach ($messages as &$message) {
			$text = $message[0];
			$icon = $message[1];
			$icon_size = $message[2];
			if (strlen($icon)) {
				if (strpos($icon, '.') === false) {
					$icon = P4A_ICONS_PATH . "/$icon_size/$icon." . P4A_ICONS_EXTENSION;
				}
				$icon = "<img src='$icon' alt='' />";
			}
			$message = P4A_Generate_Widget_Layout_Table($icon, $text, 'p4a_message');
		}
		return $messages;
	}
}