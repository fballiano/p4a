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
 * The mask is the basic interface object wich contains all widgets and generically every displayed object.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Mask extends P4A_Object
{
   	/**
	 * The mask's data source
	 * @var P4A_Data_Source
	 */
	private $data = null;

   	/**
	 * The fields collection
	 * @var P4A_Collection
	 */
	public $fields = null;

	/**
	 * Store the external fields' object_id
	 * @var array
	 */
	private $external_fields = array();

   	/**
	 * Keeps the association between actions events and actions.
	 * @var array
	 */
	private $map_actions = array();

   	/**
	 * @var string
	 */
	private $title = null;

	/**
	 * The id of the object with active focus
	 * @var string
	 */
	private $focus_object_id = null;

	/**
	 * Currently used template name
	 * @var string
	 */
	private $template_name = null;

	/**
	 * CSS files to be added to the mask's HTML
	 * @var array
	 */
	private $_css = array();

	/**
	 * Temporary javascript container.
	 * These javascripts are rendered and removed.
	 * @var array
	 */
	private $_temp_javascript = array();

	/**
	 * Temporary CSS container.
	 * These CSS are rendered and removed.
	 * @var array
	 */
	private $_temp_css = array();

	/**
	 * Temporary variables container.
	 * These vars are usally in the templates, removed after main.
	 * @var array
	 */
	private $_temp_vars = array();

	/**
	 * javascript container
	 * @var array
	 */
	private $_javascript = array();

	/**
	 * variables used for templates
	 * @var array
	 */
	private $_tpl_vars = array();

	/**
	 * @var boolean
	 */
	private $is_popup = false;

	/**
	 * @var string
	 */
	private $_icon = null;
	
	/**
	 * @var integer
	 */
	private $_icon_size = 48;

	/**
	 * @var P4A_Button
	 */
	public $close_popup_button = null;

	/**
	 * @param string $name Object name (identifier)
	 */
	public function __construct($name = null)
	{
		if ($name == null) {
			$name = get_class($this);
		}

		$name = strtolower($name);
		parent::__construct($name, 'ma');

		$this->build("P4A_Collection", "fields");
		$this->build("P4A_Button", "close_popup_button");
		$this->close_popup_button->addAjaxAction("onclick");
		$this->close_popup_button->setIcon("exit");
		P4A::singleton()->intercept($this->close_popup_button, "onclick", "closePopup");

		$this->title = ucwords(str_replace('_', ' ', $this->getName())) ;
		$this->useTemplate('default');
	}

	/**
	 * @param string $name
	 * @return P4A_Mask
	 */
	public function &singleton($name)
	{
		$name = strtolower($name);
		$p4a =& P4A::singleton();

		if (!isset($p4a->masks->$name)) {
			$p4a->masks->build($name, $name);
		}
		return $p4a->masks->$name;
	}

	/**
	 * gets/sets popup state
	 *
	 * @param boolean|null $is_popup
	 * @return boolean
	 */
	public function isPopup($is_popup = null)
	{
		if ($is_popup !== null) {
			$this->is_popup = $is_popup;
		}
		return $this->is_popup;
	}

	/**
	 * Sets the focus on object
	 * @param object $object
	 */
	public function setFocus($object = null)
	{
		if (is_object($object)) {
			$this->focus_object_id = $object->getId();
		} else {
			$this->focus_object_id = null;
		}
	}

	/**
	 * Removes focus property
	 */
	public function unsetFocus()
	{
		$this->focus_object_id = null;
	}

	/**
	 * Shows the previous mask
	 */
	public function showPrevMask()
	{
		P4A::singleton()->showPrevMask();
	}

	/**
	 * Get the previous mask
	 * @return P4A_Mask
	 */
	public function &getPrevMask()
	{
		return P4A::singleton()->getPrevMask();
	}

	/**
	 * Tells the mask that we're going to use a template
	 * @param string|false $template_name "template name" stands for "template name.tpl" in the "CURRENT THEME\masks\" directory. If false removes template.
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
	 * Returns the currently used template name
	 * @return string
	 */
	public function getTemplateName()
	{
		if ($this->isPopup()) {
			return 'popup';
		} else {
			return $this->template_name;
		}
	}

	/**
	 * Tells the template engine to show an object as a variable.
	 * $object will be shown in the $variable template zone.
	 * @param string $variable Variable name, stands for a template zone
	 * @param mixed $object Widget or string, the value of the assignment
	 */
	public function display($variable, &$object)
	{
		$this->_tpl_vars[$variable] =& $object;
	}

	 /**
	 * Tells the template engine to show a strng as a variable
	 * @param string $variable Variable name, stands for a template variable
	 * @param string $text String, the value of the assignment
	 * @access public
	 */
	function displayText($variable, $text)
	{
		$this->_tpl_vars[$variable] = $text;
	}

	/**
	 * Sets the title for the mask
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title ;
	}

	/**
	 * Returns the title for the mask
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Prints out the mask
	 */
	public function main()
	{
		header("Content-Type: text/html; charset=UTF-8");
		echo $this->getAsString();
	}

	/**
	 * Renders the mask as HTML code and returns it
	 * @param string $_template
	 * @return string
	 */
	public function getAsString($_template = false)
	{
		$p4a =& P4A::singleton();
		if (!$_template) {
			$_template = $this->getTemplateName();
		}

		foreach ($this->_tpl_vars as $k=>$v) {
			if (is_object($v)) {
				$$k = $v->getAsString();
			} else {
				$$k = $v;
			}
		}

		$_xml_header = '<?xml version="1.0" encoding="UTF-8"?>';
		$_javascript = array_merge($p4a->getJavascript(), $this->_javascript, $this->_temp_javascript);
		$_css = array_merge_recursive($p4a->getCSS(), $this->_css, $this->_temp_css);

		$_icon = '';
		$_title = $this->getTitle();
		if ($this->getTitle() and $this->getIcon() and !$p4a->isHandheld()) {
			$_icon = $this->getIcon();
			if (strpos($_icon, '.') !== false) {
				$_icon = $_icon;
			} else {
				$_icon_size = $this->getIconSize();
				$_icon = P4A_ICONS_PATH . "/{$_icon_size}/{$_icon}." . P4A_ICONS_EXTENSION;
			}
			$_icon = "<img src='$_icon' alt='' />";
		}

		extract($this->_temp_vars);

		$_popup = '';
		if ($p4a->isPopupOpened() and !$this->isPopup()) {
			$_popup = $p4a->getPopupMask()->getAsString();
		}

		ob_start();
		require P4A_THEME_DIR . "/masks/{$_template}/{$_template}.tpl";
		$output = ob_get_contents();
		ob_end_clean();

		$this->clearTempCSS();
		$this->clearTempJavascript();
		$this->clearTempVars();

		return $output;
	}

	/**
	 * Removes every template variable assigned
	 */
	public function clearTemplateVars()
	{
		$this->_tpl_vars = array();
	}

	/**
	 * Associates a data source with the mask.
	 * Also set the data structure to allow correct widget rendering.
	 * Also moves to the first row of the data source.
	 * @param P4A_Data_Source $data_source
	 * @return P4A_Collection the fields collection
	 */
	public function &setSource(&$data_source)
	{
		$this->data =& $data_source;

		while($field =& $this->data->fields->nextItem()) {
			$field_name = $field->getName();
			$this->fields->build(P4A_FIELD_CLASS, $field_name, false);
			$this->fields->$field_name->setDataField($field);
		}

		return $this->fields;
	}

	/**
	 * Loads the current record data
	 * @param integer $num_row The wanted row number
	 */
	public function loadRow($num_row = null)
	{
		$this->data->row($num_row);
	}

	/**
	 * Reloads data for the current record
	 */
	public function reloadRow()
	{
		if ($this->data->isNew()) {
			$this->firstRow();
		} else {
			$this->data->row();
		}
	}

	/**
	 * Manages file uploads when arriving from HTTP POST
	 * @throws onFileSystemError
	 */
	protected function saveUploads()
	{
		while ($field =& $this->fields->nextItem()) {
			$field_type = $field->getType();
			if ($field_type=='file' or $field_type=='image') {
				$new_value  = $field->getNewValue();
				$old_value  = $field->getValue();
				$target_dir = P4A_UPLOADS_DIR . '/' . $field->getUploadSubpath();

				if (!is_dir($target_dir)) {
					if (!@System::mkDir("-p $target_dir")) {
						$e = new P4A_ERROR("Cannot create directory \"$target_dir\"", $this);
						if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
							die();
						}
					}
				}

				$a_new_value = explode(',', substr($new_value, 1, -1 ));
				$a_old_value = explode(',', substr($old_value, 1, -1 ));

				if ($old_value === NULL) {
					if ($new_value !== NULL) {
						$a_new_value[0] = P4A_Get_Unique_File_Name( $a_new_value[0], $target_dir );
						$new_path = $target_dir . '/' . $a_new_value[0];
						$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
						if (!rename($old_path, $new_path)) {
							$e = new P4A_ERROR("Cannot rename file \"$old_path\" to \"$new_path\"", $this);
							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
								die();
							}
						}
						$a_new_value[1] = str_replace(P4A_UPLOADS_DIR , '', $new_path);
						$field->setNewValue('{' . join($a_new_value, ',') . '}');
					} else {
						$field->setNewValue(NULL);
					}
				} else {
					if ($new_value === NULL) {
						$path = $target_dir . '/' . $a_old_value[0];
						if (!@unlink($path) and @file_exists($path)) {
							$e = new P4A_ERROR("Cannot delete file \"$path\"", $this);
							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
								die();
							}
						}
						$field->setNewValue(NULL);
					} elseif ($new_value!=$old_value) {
						$path = $target_dir . '/' . $a_old_value[0];
						if (!@unlink($path) and @file_exists($path)) {
							$e = new P4A_ERROR("Cannot delete file \"$path\"", $this);
							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
								die();
							}
						}
						$a_new_value[0] = P4A_Get_Unique_File_Name($a_new_value[0], $target_dir);
						$new_path = $target_dir . '/' . $a_new_value[0];
						$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
						if (!@rename($old_path, $new_path)) {
							$e = new P4A_ERROR("Cannot rename file \"$old_path\" to \"$new_path\"", $this);
							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
								die();
							}
						}
						$a_new_value[1] = str_replace(P4A_UPLOADS_DIR , '', $new_path);
						$field->setNewValue('{' . join($a_new_value, ',') . '}');
					}
				}
			}
		}
	}

	/**
	 * Overwrites internal data with the data arriving from the submitted mask
	 */
	public function saveRow()
	{
		$this->saveUploads();
		$this->data->saveRow();
	}

	/**
	 * Goes in "new row" modality.
	 * This means that we prepare p4a for adding a new record
	 * to the data source wich is associated to the mask.
	 */
	public function newRow()
	{
		$this->data->newRow();
	}

	/**
	 * Deletes the currently pointed record
	 */
	public function deleteRow()
	{
		$this->data->deleteRow();
	}

	/**
	 * Moves to the next row
	 */
	public function nextRow()
	{
		$this->data->nextRow();
	}

	/**
	 * Moves to the previous row
	 */
	public function prevRow()
	{
		$this->data->prevRow();
	}

	/**
	 * Moves to the last row
	 */
	public function lastRow()
	{
		$this->data->lastRow();
	}

	/**
	 * Moves to the first row
	 */
	public function firstRow()
	{
		$this->data->firstRow();
	}

	/**
	 * Returns the opening code for the mask
	 * @return string
	 */
	protected function maskOpen()
	{
		$return = "<form method='post' enctype='multipart/form-data' id='p4a' onsubmit='return false' action='index.php'>\n";
		$return .= "<div>\n";
		$return .= "<input type='hidden' name='_object' value='" . $this->getId() . "' />\n";
		$return .= "<input type='hidden' name='_action' value='none' />\n";
		$return .= "<input type='hidden' name='_ajax' value='0' />\n";
		$return .= "<input type='hidden' name='_action_id' value='" . p4a::singleton()->getActionHistoryId() . "' />\n";
		$return .= "<input type='hidden' name='param1' />\n";
		$return .= "<input type='hidden' name='param2' />\n";
		$return .= "<input type='hidden' name='param3' />\n";
		$return .= "<input type='hidden' name='param4' />\n";

		return $return;
	}

	/**
	 * Returns the closing code for the mask
	 * @return string
	 */
	protected function maskClose()
	{
		return "</div>\n</form>";
	}

	/**
	 * Does nothing
	 */
	public function none()
	{
	}

	/**
	 * Include a CSS file in the mask
	 * @param string $uri The URI of CSS
	 * @param string $media The CSS media
	 */
	public function addCss($uri, $media = "screen")
	{
		if (!isset($this->_css[$uri])) {
			$this->_css[$uri] = array();
		}
		$this->_css[$uri][$media] = null;
	}

	/**
	 * Drop inclusion of CSS file
	 * @param string $uri The URI of CSS
	 * @param string $media The CSS media
	 */
	public function dropCss($uri, $media = "screen")
	{
		if(isset($this->_css[$uri]) and isset($this->_css[$uri][$media])) {
			unset($this->_css[$uri][$media]);
			if (empty($this->_css[$uri])) {
				unset($this->_css);
			}
		}
	}

	/**
	 * Include a CSS file that will be removed after rendering
	 * @param string $uri The URI of CSS
	 * @param string $media The CSS media
	 */
	public function addTempCss($uri, $media = "screen")
	{
		if (!isset($this->_temp_css[$uri])) {
			$this->_temp_css[$uri] = array();
		}
		$this->_temp_css[$uri][$media] = null;
	}

	/**
	 * Drop inclusion of temp CSS file
	 * @param string $uri The URI of CSS
	 * @param string $media The CSS media
	 */
	public function dropTempCss($uri, $media = "screen")
	{
		if(isset($this->_temp_css[$uri]) and isset($this->_temp_css[$uri][$media])) {
			unset($this->_temp_css[$uri][$media]);
			if (empty($this->_temp_css[$uri])) {
				unset($this->_temp_css);
			}
		}
	}

	/**
	 * Clear temporary CSS list
	 */
	public function clearTempCss()
	{
		$this->_temp_css = array();
	}

	/**
	 * Include a javascript file
	 * @param string $uri
	 */
	public function addJavascript($uri)
	{
		$this->_javascript[$uri] = null;
	}

	/**
	 * Drop inclusion of javascript file
	 * @param string $uri
	 */
	public function dropJavascript($uri)
	{
		if(isset($this->_javascript[$uri])){
			unset($this->_javascript[$uri]);
		}
	}

	/**
	 * Include a javascript file
	 * These javascripts are removed after rendering
	 * @param string $uri
	 */
	public function addTempJavascript($uri)
	{
		$this->_temp_javascript[$uri] = null;
	}

	/**
	 * Drop inclusion of javascript file
	 * These javascripts are removed after rendering
	 * @param string $uri
	 */
	public function dropTempJavascript($uri)
	{
		if(isset($this->_temp_javascript[$uri])){
			unset($this->_temp_javascript[$uri]);
		}
	}

	/**
	 * Clear temporary javascript list
	 */
	public function clearTempJavascript()
	{
		$this->_temp_javascript = array();
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
		if(isset($this->_temp_vars[$name])){
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

	public function setIcon($icon)
	{
		$this->_icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->_icon;
	}

	/**
	 * @param integer $size
	 */
	public function setIconSize($size)
	{
		$this->_icon_size = strtolower($size);
	}

	/**
	 * @return integer
	 */
	public function getIconSize()
	{
		return $this->_icon_size;
	}
	
	/**
	 * Returns the Javascript code neede for P4A initialization
	 * @return string
	 */
	protected function getP4AJavascript()
	{
		$p4a_i18n =& p4a::singleton()->i18n;
		$locale_engine = $p4a_i18n->getLocaleEngine();
		
		return '<script type="text/javascript">' . "\n" .
		'p4a_theme_path = "' . P4A_THEME_PATH . '";' . "\n" .
		'$(function() {' . "\n" .
		'$.datepicker._defaults["dateFormat"] = "yy-mm-dd";' . "\n" .
		'$.datepicker._defaults["dayNamesMin"] = ["'. join('","', $locale_engine->getTranslationList('day_short')) . '"];' . "\n" .
		'$.datepicker._defaults["monthNames"] = ["'. join('","', $locale_engine->getTranslationList('month')) . '"];' . "\n" .
		'$.datepicker._defaults["firstDay"] = ' . $p4a_i18n->getFirstDayOfTheWeek() . ";\n" .
		'p4a_set_focus("' . P4A::singleton()->getFocusedObjectId() . '");' . "\n" .
		'});' . "\n" .
		'</script>';
	}
}