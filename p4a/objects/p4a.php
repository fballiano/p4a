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
 * THE APPLICATION.
 * Stands for the currently running istance of the application.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
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
	private $_redesign_whole_mask = false;
	
	/**
	 * @var array
	 */
	private $messages = array();
	
	/**
	 * @var string
	 */
	private $browser = null;
	
	/**
	 * @var string
	 */
	private $browser_os = self::BROWSER_LINUX;
	
	const BROWSER_GECKO = 'gecko';
	const BROWSER_HANDHELD = 'handheld';
	const BROWSER_IE = 'ie';
	const BROWSER_OPERA = 'opera';
	const BROWSER_SAFARI = 'safari';
	
	const BROWSER_LINUX = 'linux';
	const BROWSER_MAC = 'mac';
	const BROWSER_WINDOWS = 'windows';

	public function __construct()
	{
		//do not call parent constructor
		$_SESSION["p4a"] =& $this;
		$this->i18n = new p4a_i18n(P4A_LOCALE);

		$this->build("P4A_Collection", "masks");
		$browser_identification = $this->detectClient();

		$this->addJavascript(P4A_THEME_PATH . "/jquery/jquery.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/form.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/dimensions.js");
		$this->addJavascript(P4A_THEME_PATH . "/jquery/autocomplete.js");
		if (!$this->isHandheld()) {
			$this->addJavascript(P4A_THEME_PATH . "/jquery/jmedia.js");
			$this->addJavascript(P4A_THEME_PATH . "/jquery/ui.datepicker.js");
			$this->addJavascript(P4A_THEME_PATH . "/widgets/rich_textarea/fckeditor.js");
			if ($this->isInternetExplorer() and !$browser_identification['ie7up']) {
				$this->addJavascript(P4A_THEME_PATH . "/jquery/bgiframe.js");
				$this->addJavascript(P4A_THEME_PATH . "/jquery/ifixpng.js");
				$this->addJavascript(P4A_THEME_PATH . "/ie6fixes.js");
			}
		}
		$this->addJavascript(P4A_THEME_PATH . "/p4a.js");

		$this->addCSS(P4A_THEME_PATH . "/reset-fonts.css", "all");
		$this->addCSS(P4A_THEME_PATH . "/jquery/ui.datepicker.css", "screen");
		$this->addCSS(P4A_THEME_PATH . "/screen.css.php?p4a_icons_path=" . urlencode(P4A_ICONS_PATH) . "&p4a_theme_path=" . urlencode(P4A_THEME_PATH), "all");
		$this->addCSS(P4A_THEME_PATH . "/screen.css.php?p4a_icons_path=" . urlencode(P4A_ICONS_PATH) . "&p4a_theme_path=" . urlencode(P4A_THEME_PATH), "print");
		$this->addCSS(P4A_THEME_PATH . "/print.css", "print");
		$this->addCSS(P4A_THEME_PATH . "/handheld.css", $this->isHandheld() ? "all" : "handheld" );
	}

	/**
	 * @return array
	 */
	private function detectClient()
	{
		require_once dirname(dirname(__FILE__)) . '/libraries/pear_net_useragent_detect.php';
		Net_UserAgent_Detect::detect();
		
		if (P4A_FORCE_HANDHELD_RENDERING) {
			$this->browser = self::BROWSER_HANDHELD;
		} elseif (Net_UserAgent_Detect::isIE()) {
			$this->browser = self::BROWSER_IE;
		} elseif (Net_UserAgent_Detect::isBrowser('gecko')) {
			$this->browser = self::BROWSER_GECKO;
		} elseif (Net_UserAgent_Detect::isBrowser('safari')) {
			$this->browser = self::BROWSER_SAFARI;
		} elseif (Net_UserAgent_Detect::isBrowser('opera')) {
			$this->browser = self::BROWSER_OPERA;
		} else {
			$this->browser = self::BROWSER_HANDHELD;
		}
		
		foreach (Net_UserAgent_Detect::_getStaticProperty('os') as $os=>$detected) {
			if ($detected) {
				if (preg_match("/^win.*$/", $os)) {
					$this->browser_os = self::BROWSER_WINDOWS;
				} elseif (preg_match("/^mac.*$/", $os)) {
					$this->browser_os = self::BROWSER_MAC;
				}
				break;
			}
		}

		return Net_UserAgent_Detect::_getStaticProperty('browser');
	}

	/**
	 * @return boolean
	 */
	public function isInternetExplorer()
	{
		return ($this->browser == self::BROWSER_IE);
	}
	
	/**
	 * @return boolean
	 */
	public function isGecko()
	{
		return ($this->browser == self::BROWSER_GECKO);
	}
	
	/**
	 * @return boolean
	 */
	public function isHandheld()
	{
		return ($this->browser == self::BROWSER_HANDHELD);
	}
	
	/**
	 * @return boolean
	 */
	public function isOpera()
	{
		return ($this->browser == self::BROWSER_OPERA);
	}
	
	/**
	 * @return boolean
	 */
	public function isSafari()
	{
		return ($this->browser == self::BROWSER_SAFARI);
	}
	
	/**
	 * @return string
	 */
	public function getBrowser()
	{
		return $this->browser;
	}
	
	/**
	 * @return string
	 */
	public function getBrowserOS()
	{
		return $this->browser_os;
	}

	/**
	 * @return boolean
	 * @deprecated 
	 */
	public function isPopupOpened()
	{
		return $this->active_mask->isPopup();
	}

	/**
	 * @return boolean
	 */
	public function inAjaxCall()
	{
		return (isset($_REQUEST['_ajax']) and $_REQUEST['_ajax']);
	}

	public static function singleton($class_name = "p4a")
	{
		if (!isset($_SESSION)) {
			session_name(preg_replace('~\W~', '_', P4A_APPLICATION_NAME));
			session_start();
			if (isset($_SESSION['p4a'])) {
				error_reporting(0);
				$_SESSION['p4a']->executeExternalCommands();
			}
			
			set_exception_handler('P4A_Exception_Handler');
			if (P4A_EXTENDED_ERRORS) {
				error_reporting(P4A_EXTENDED_ERROR_REPORTING);
				set_error_handler('P4A_Error_Handler', P4A_EXTENDED_ERROR_REPORTING);
			} else {
				error_reporting(P4A_DEFAULT_ERROR_REPORTING);
				set_error_handler('P4A_Error_Handler', P4A_DEFAULT_ERROR_REPORTING);
			}
		}
		
		if (isset($_SESSION["p4a"])) {
			return $_SESSION["p4a"];
		}
		return new $class_name();
	}

	/**
	 * Destroys P4A
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
	
	/**
	 * Never call this method if you don't know what you're doing
	 */
	public function executeExternalCommands()
	{
		if (isset($_REQUEST['_p4a_application_download_missing_link'])) {
			$text  = "You've the right to receive the source code of this application.<br />";
			$text .= "Please contact your software vendor, he has to give it to you.<br />";
			$text .= "If you get a negative answer drop an e-mail to <a href='mailto:info@crealabs.it'>CreaLabs</a>, ";
			$text .= "they'll help you getting your rights honored.";
			P4A_Mask::singleton("P4A_Error_Mask")
				->setTitle(__("Possible license violation"))
				->setMessage(__($text))
				->main();
			die();
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
			die();
		} elseif (isset($_REQUEST['_rte_file_manager']) and isset($_REQUEST['_object_id']) and isset($this->objects[$_REQUEST['_object_id']])) {
			require P4A_THEME_DIR . '/widgets/rich_textarea/editor/filemanager/connectors/php/connector.php';
			die();
		} elseif (isset($_REQUEST['_upload_path'])) {
			$path = P4A_UPLOADS_PATH;
			if (isset($_REQUEST['_object_id']) and isset($this->objects[$_REQUEST['_object_id']])) {
				$object =& $this->objects[$_REQUEST['_object_id']];
				if ($object instanceof P4A_Field) {
					$path .= '/' . $object->getUploadSubpath();
				}
			}
			echo preg_replace(array("~/+~", "~/$~"), array('/', ''), $path);
			die();
		} elseif (isset($_REQUEST['_p4a_autocomplete'])) {
			if (isset($_REQUEST['_object']) and
				isset($_REQUEST['q']) and
				isset($this->objects[$_REQUEST['_object']])) {
				$object =& $this->objects[$_REQUEST['_object']];
				$db = p4a_db::singleton($object->data_field->getDSN());
				$data =& $object->data;
				$description_field = $object->getSourceDescriptionField();
				$q = P4A_Quote_SQL_Value($_REQUEST['q']);					
				$where = $db->getCaseInsensitiveLikeSQL($description_field, "%$q%");
				$old_where = $data->getWhere();
				if ($old_where) {
					$where = "({$old_where}) AND ($where)";
				}
				$data->setWhere($where);
				$all = $data->getAll();
				$data->setWhere($old_where);
				foreach ($all as $row) {
					echo "{$row[$description_field]}\n";
				}
			}
			die();
		} elseif (isset($_REQUEST['_p4a_date_format'])) {
			echo $this->i18n->format($_REQUEST['_p4a_date_format'], 'date');
			die();
		} elseif (isset($_REQUEST['_p4a_image_thumbnail'])) {
			$image_data = explode('&', $_REQUEST['_p4a_image_thumbnail']);
			$thumb = new P4A_Thumbnail_Generator();
			$thumb->setCacheDir(P4A_UPLOADS_TMP_DIR)
				->setFilename(P4A_Strip_Double_Slashes(P4A_UPLOADS_DIR . $image_data[0]))
				->setMaxWidth($image_data[1])
				->setMaxHeight($image_data[1])
				->processFile()
				->cacheThumbnail();
			header('Location: ' . P4A_UPLOADS_TMP_PATH . '/' . $thumb->getCachedFilename());
			die();
		} elseif (isset($_REQUEST['_p4a_download_file'])) {
			$file = realpath(P4A_UPLOADS_DIR . '/' . $_REQUEST['_p4a_download_file']);
			if ($file !== false and strpos($file, P4A_UPLOADS_DIR) === 0 and file_exists($file)) {
				$name = preg_replace("~^.*/~", '', $file);
				header("Pragma: public");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private", false);
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"$name\"");
				header("Content-Length: " . filesize($file));
				$fp = fopen($file, "rb");
				fpassthru($fp);
				fclose($fp);
				
				if (strpos($file, P4A_UPLOADS_TMP_DIR . '/_p4a_') === 0) {
					unlink($file);
				}
			}
			die();
		}
	}

	public function main()
	{
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
					if ($this->objects[$key]->getType() == 'file' and strlen($value) == 0) {
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
				if (P4A_Is_Extension_Allowed($extension) and in_array($value['error'], array(UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE))) {
					if ($value['error'] == UPLOAD_ERR_NO_FILE) continue;
					$value['name'] = str_replace( ',', ';', $value['name'] );
					$value['name'] = P4A_Get_Unique_File_Name($value['name'], P4A_UPLOADS_TMP_DIR);
					move_uploaded_file($value['tmp_name'], P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
					$value['tmp_name'] = P4A_Strip_Double_Slashes('/' . P4A_UPLOADS_TMP_NAME . '/' . $value['name']);

					if ((substr($key, 0, 3) == 'fld')) {
						list($width, $height) = @getimagesize(P4A_UPLOADS_TMP_DIR . '/' . $value['name']);
						$new_value = "{$value['name']},{$value['tmp_name']},{$value['size']},{$value['type']},$width,$height" ;
						$this->objects[$key]->setNewValue('{' . $new_value . '}');
						if ($this->objects[$key]->actionHandler('afterUpload') == ABORT) return ABORT;
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

		if ($this->inAjaxCall()) {
			$this->_action_history_id++;
			if ($_REQUEST['_ajax'] == 2) $this->active_mask->main();
			$this->raiseXMLResponse();
		} elseif (P4A_ENABLE_RENDERING and is_object($this->active_mask)) {
			$this->_action_history_id++;
			$this->active_mask->main();
		}

		$this->_to_redesign = array();
		$this->_redesign_whole_mask = false;

		session_write_close();
		session_id(substr(session_id(), 0, -6));
		flush();
	}

	/**
	 * Never call this method if you don't know what you're doing
	 */
	public function raiseXMLResponse()
	{
		ob_start();
		$script_detector = '<script.*?>(.*?)<\/script>';

		header('Content-Type: text/xml');
		print '<?xml version="1.0" encoding="utf-8" ?>';
		print '<ajax-response action_id="' . $this->getActionHistoryId() . '" focus_id="' . $this->getFocusedObjectId() . '">';
		foreach ($this->getRenderedMessages() as $message) {
			print "\n<message><![CDATA[$message]]></message>";
		}
		
		if ($this->_redesign_whole_mask or $_REQUEST['_ajax'] == 2) {
			$as_string = $this->active_mask->getAsString(false);
			$javascript_codes = array();
			$javascript = '';
			$html = preg_replace("/{$script_detector}/si", '', $as_string);
			preg_match_all("/{$script_detector}/si", $as_string, $javascript_codes);
			$javascript_codes = $javascript_codes[1];
			foreach ($javascript_codes as $code) {
				$javascript .= "$code\n\n";
			}
			print "<widget id='p4a_inner_body'>\n";
			print "<html><![CDATA[{$html}]]></html>\n";
			print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
			print "</widget>";
		} else {
			while (list( ,$id) = each($this->_to_redesign)) {
				$object =& $this->getObject($id);
				$as_string = $object->getAsString();
				$javascript_codes = array();
				$javascript = '';
				$html = preg_replace("/{$script_detector}/si", '', $as_string);
				preg_match_all("/{$script_detector}/si", $as_string, $javascript_codes);
				$javascript_codes = $javascript_codes[1];
				foreach ($javascript_codes as $code) {
					$javascript .= "$code\n\n";
				}

				print "\n<widget id='$id'>\n";
				print "<html><![CDATA[{$html}]]></html>\n";
				print "<javascript><![CDATA[{$javascript}]]></javascript>\n";
				print "</widget>\n";
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
	private function setActiveObject($object)
	{
		unset($this->active_object);
		$this->active_object =& $object;
	}

	public function openMask($mask_name)
	{
		if ($this->actionHandler('beforeopenmask') == ABORT) return ABORT;

		if ($this->isActionTriggered('onopenmask')) {
			if ($this->actionHandler('onopenmask') == ABORT) return ABORT;
		} else {
			if ($this->active_mask and $this->active_mask->isPopup()) {
				$this->closePopup();
			}

			if ($this->inAjaxCall()) {
				$this->_redesign_whole_mask = true;
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
		$this->actionHandler('afteropenmask');
		return $this->active_mask;
	}

	public function openPopup($mask_name)
	{
		if ($this->active_mask->isPopup()) {
			$this->closePopup();
		}
		
		$mask = $this->openMask($mask_name);
		$mask->isPopup(true);
		$this->_redesign_whole_mask = true;
		
		return $mask;
	}

	/**
	 * Alias for showPrevMask()
	 *
	 * @param boolean $destroy completely destroy the mask object?
	 */
	public function closePopup($destroy = false)
	{
		$this->showPrevMask($destroy);
	}

	/**
	 * @param unknown_type $destroy completely destroy the current mask object?
	 */
	public function showPrevMask($destroy = false)
	{
		if ($destroy === true) {
			$this->active_mask->destroy();
	 	} elseif ($this->active_mask->isPopup()) {
			$this->active_mask->isPopup(false);
			$this->_redesign_whole_mask = true;
		}

	 	if (sizeof($this->masks_history) > 0) {
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
	 * @return P4A
	 */
	public function store(&$object)
	{
		$object_id = $object->getId();
		if (array_key_exists($object_id, $this->objects)){
			ERROR('DUPLICATE OBJECT');
		} else {
			$this->objects[$object_id] = &$object;
		}
		return $this;
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
	 * @return P4A
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
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
	 * @return P4A
	 */
	public function addCss($uri, $media = "screen")
	{
		if (!isset($this->_css[$uri])) {
			$this->_css[$uri] = array();
		}
		$this->_css[$uri][$media] = null;
		return $this;
	}

	/**
	 * Drops inclusion of a CSS file
	 * @param string $uri
	 * @param string $media
	 * @return P4A
	 */
	public function dropCss($uri, $media = "screen")
	{
		if(isset($this->_css[$uri]) and isset($this->_css[$uri][$media])){
			unset($this->_css[$uri][$media]);
			if (empty($this->_css[$uri])) {
				unset($this->_css);
			}
		}
		return $this;
	}
	
	public function getCss()
	{
		return $this->_css;
	}

	/**
	 * Includes a javascript file
	 * @param string $uri
	 * @return P4A
	 */
	public function addJavascript($uri)
	{
		$this->_javascript[$uri] = null;
		return $this;
	}

	/**
	 * Drops inclusion of a javascript file
	 * @param string $uri
	 * @return P4A
	 */
	public function dropJavascript($uri)
	{
		if(isset($this->_javascript[$uri])){
			unset($this->_javascript[$uri]);
		}
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getJavascript()
	{
		return $this->_javascript;
	}
	
	/**
	 * Returns the Javascript code neede for P4A initialization
	 * @return string
	 */
	public function getJavascriptInitializations()
	{
		$locale_engine = $this->i18n->getLocaleEngine();
		$ajax_enabled = P4A_AJAX_ENABLED ? 'true' : 'false';
		
		$days = $locale_engine->getTranslationList('days');
		$days = $days['format']['abbreviated'];
		
		return '<script type="text/javascript">' . "\n" .
		'p4a_theme_path = "' . P4A_THEME_PATH . '";' . "\n" .
		'p4a_ajax_enabled = ' . $ajax_enabled . ';' . "\n" .
		'$(function() {' . "\n" .
		'$.datepicker._defaults["dateFormat"] = "yy-mm-dd";' . "\n" .
		'$.datepicker._defaults["dayNamesMin"] = ["'. join('","', $days) . '"];' . "\n" .
		'$.datepicker._defaults["monthNames"] = ["'. join('","', $locale_engine->getTranslationList('month')) . '"];' . "\n" .
		'$.datepicker._defaults["firstDay"] = ' . $this->i18n->getFirstDayOfTheWeek() . ";\n" .
		'p4a_focus_set("' . $this->getFocusedObjectId() . '");' . "\n" .
		'});' . "\n" .
		'</script>';
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
	 * @return P4A
	 */
	public function redesign($id)
	{
		$this->_to_redesign[] = $id;
		return $this;
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
	 * @deprecated 
	 */
	public function getFocusedObjectId()
	{
		if (is_object($this->active_mask)) {
			return $this->active_mask->getFocusedObjectId();
		}
		return null;
	}
	
	/**
	 * Outputs a system message to user
	 * @param string $text
	 * @param string $icon
	 * @param integer $icon_size
	 * @return P4A
	 */
	public function message($text, $icon = null, $icon_size = 32)
	{
		$this->messages[] = array($text, $icon, $icon_size);
		return $this;
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
	
	public function __wakeup()
	{
		$this->messages = array();
	}
}