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
 * The mask is the basic interface object wich contains all widgets and generically every displayed object.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
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
	 * Temporary variables container.
	 * These vars are usally in the templates, removed after main.
	 * @var array
	 */
	private $_temp_vars = array();

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
		$this->close_popup_button->implement('onclick', P4A::singleton(), 'showPrevMask');

		$this->title = ucwords(str_replace('_', ' ', $this->getName())) ;
		$this->useTemplate('default');
	}

	/**
	 * @param string $name
	 * @return P4A_Mask
	 */
	public function singleton($name)
	{
		$name = strtolower($name);
		$p4a = P4A::singleton();
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
	 * @return P4A_Mask
	 */
	public function setFocus($object = null)
	{
		if (is_object($object)) {
			$this->focus_object_id = $object->getId();
		} else {
			$this->focus_object_id = null;
		}
		return $this;
	}

	/**
	 * Removes focus property
	 * @return P4A_Mask
	 */
	public function unsetFocus()
	{
		$this->focus_object_id = null;
		return $this;
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
	public function getPrevMask()
	{
		return P4A::singleton()->getPrevMask();
	}

	/**
	 * Tells the mask that we're going to use a template
	 * @param string|false $template_name "template name" stands for "template name.tpl" in the "CURRENT THEME\masks\" directory. If false removes template.
	 * @return P4A_Mask
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
	 * Returns the currently used template name
	 * @return string
	 */
	public function getTemplateName()
	{
		if ($this->isPopup()) {
			return 'popup';
		}
		return $this->template_name;
	}

	/**
	 * Tells the template engine to show an object as a variable.
	 * $object will be shown in the $variable template zone.
	 * @param string $variable Variable name, stands for a template zone
	 * @param mixed $object Widget or string, the value of the assignment
	 * @return P4A_Mask
	 */
	public function display($variable, &$object)
	{
		$this->_tpl_vars[$variable] =& $object;
		return $this;
	}

	 /**
	 * Tells the template engine to show a strng as a variable
	 * @param string $variable Variable name, stands for a template variable
	 * @param string $text String, the value of the assignment
	 * @return P4A_Mask
	 */
	public function displayText($variable, $text)
	{
		$this->_tpl_vars[$variable] = $text;
		return $this;
	}

	/**
	 * Sets the title for the mask
	 * @param string $title
	 * @return P4A_Mask
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
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
		if (!P4A::singleton()->inAjaxCall()) {
			header("Content-Type: text/html; charset=UTF-8");
			echo $this->getAsString();
		}
	}

	/**
	 * Renders the mask as HTML code and returns it
	 * @param string $_template
	 * @return string
	 */
	public function getAsString($_template = false)
	{
		$p4a = P4A::singleton();
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

		ob_start();
		$_xml_header = '<?xml version="1.0" encoding="UTF-8"?>';
		if (!$p4a->inAjaxCall()) require P4A_THEME_DIR . "/p4a_header.php";
		require P4A_THEME_DIR . "/masks/{$_template}/{$_template}.php";
		if (!$p4a->inAjaxCall()) require P4A_THEME_DIR . "/p4a_footer.php";
		$output = ob_get_contents();
		ob_end_clean();

		$this->clearTempVars();
		return $output;
	}

	/**
	 * Removes every template variable assigned
	 * @return P4A_Mask
	 */
	public function clearTemplateVars()
	{
		$this->_tpl_vars = array();
		return $this;
	}

	/**
	 * Associates a data source with the mask.
	 * Also set the data structure to allow correct widget rendering.
	 * Also moves to the first row of the data source.
	 * @param P4A_Data_Source $data_source
	 * @return P4A_Collection the fields collection
	 */
	public function setSource($data_source)
	{
		$this->data = $data_source;

		while($field = $this->data->fields->nextItem()) {
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
		while ($field = $this->fields->nextItem()) {
			$field_type = $field->getType();
			if ($field_type=='file' or $field_type=='image') {
				$new_value  = $field->getNewValue();
				$old_value  = $field->getValue();
				$target_dir = P4A_UPLOADS_DIR . '/' . $field->getUploadSubpath();

				if (!is_dir($target_dir)) {
					if (!P4A_Mkdir_Recursive($target_dir)) {
						$e = new P4A_ERROR("Cannot create directory \"$target_dir\"", $this);
						if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
							die();
						}
					}
				}

				$a_new_value = explode(',', substr($new_value, 1, -1 ));
				$a_old_value = explode(',', substr($old_value, 1, -1 ));

				if ($old_value === null) {
					if ($new_value !== null) {
						$a_new_value[0] = P4A_Get_Unique_File_Name($a_new_value[0], $target_dir);
						$new_path = $target_dir . '/' . $a_new_value[0];
						$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
						if (!rename($old_path, $new_path)) {
							$e = new P4A_ERROR("Cannot rename file \"$old_path\" to \"$new_path\"", $this);
							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
								die();
							}
						}
						$a_new_value[1] = P4A_Strip_Double_Slashes(str_replace(P4A_UPLOADS_DIR , '', $new_path));
						$field->setNewValue('{' . join($a_new_value, ',') . '}');
					} else {
						$field->setNewValue(null);
					}
				} else {
					if ($new_value === null) {
						$path = $target_dir . '/' . $a_old_value[0];
						if (!@unlink($path) and @file_exists($path)) {
							$e = new P4A_ERROR("Cannot delete file \"$path\"", $this);
							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
								die();
							}
						}
						$field->setNewValue(null);
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
	 * Validate all fields and saves row to the data source
	 * @return boolean
	 */
	public function saveRow()
	{
		if ($this->validateFields()) {
			$this->saveUploads();
			$this->data->saveRow();
			return true;
		}
		return false;
	}

	/**
	 * Goes in "new row" modality.
	 * This means that we prepare p4a for adding a new record
	 * to the data source wich is associated to the mask.
	 * @return P4A_Mask
	 */
	public function newRow()
	{
		$this->data->newRow();
		return $this;
	}

	/**
	 * Deletes the currently pointed record
	 * @return P4A_Mask
	 */
	public function deleteRow()
	{
		$this->data->deleteRow();
		return $this;
	}

	/**
	 * Moves to the next row
	 * @return P4A_Mask
	 */
	public function nextRow()
	{
		$this->data->nextRow();
		return $this;
	}

	/**
	 * Moves to the previous row
	 * @return P4A_Mask
	 */
	public function prevRow()
	{
		$this->data->prevRow();
		return $this;
	}

	/**
	 * Moves to the last row
	 * @return P4A_Mask
	 */
	public function lastRow()
	{
		$this->data->lastRow();
		return $this;
	}

	/**
	 * Moves to the first row
	 * @return P4A_Mask
	 */
	public function firstRow()
	{
		$this->data->firstRow();
		return $this;
	}

	/**
	 * Returns the opening code for the mask
	 * @return string
	 */
	protected function maskOpen()
	{
		$return = "<form method='post' enctype='multipart/form-data' id='p4a' onsubmit='return false' action='index.php'>\n";
		$return .= "<input type='hidden' name='_object' value='" . $this->getId() . "' />\n";
		$return .= "<input type='hidden' name='_action' value='none' />\n";
		$return .= "<input type='hidden' name='_ajax' value='0' />\n";
		$return .= "<input type='hidden' name='_action_id' value='" . p4a::singleton()->getActionHistoryId() . "' />\n";
		$return .= "<input type='hidden' name='param1' />\n";
		$return .= "<input type='hidden' name='param2' />\n";
		$return .= "<input type='hidden' name='param3' />\n";
		$return .= "<input type='hidden' name='param4' />\n";
		$return .= "<div id='p4a_inner_body'>\n";
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
	 * Add a temporary variable
	 * @param string $name
	 * @param string $value
	 * @return P4A_Mask
	 */
	public function addTempVar($name, $value)
	{
		$this->_temp_vars[$name] = $value;
		return $this;
	}

	/**
	 * Drop a temporary variable
	 * @param string $name
	 * @return P4A_Mask
	 */
	public function dropTempVar($name)
	{
		if(isset($this->_temp_vars[$name])){
			unset($this->_temp_vars[$name]);
		}
		return $this;
	}

	/**
	 * Clear temporary vars list
	 * @return P4A_Mask
	 */
	public function clearTempVars()
	{
		$this->_temp_vars = array();
		return $this;
	}

	/**
	 * @param string $icon
	 * @return P4A_Mask
	 */
	public function setIcon($icon)
	{
		$this->_icon = $icon;
		return $this;
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
	 * @return P4A_Mask
	 */
	public function setIconSize($size)
	{
		$this->_icon_size = strtolower($size);
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getIconSize()
	{
		return $this->_icon_size;
	}
	
	/**
	 * @return string
	 */
	public function getFocusedObjectId()
	{
		return $this->focus_object_id;
	}
	
	/**
	 * Adds the "not empty" validator to the passed field
	 *
	 * @param string|P4A_Field $field_name
	 * @return P4A_Mask
	 */
	public function setRequiredField($field)
	{
		if (is_string($field)) {
			$field =& $this->fields->$field;
		}
		$field->addValidator(new P4A_Validate_NotEmpty, true);
		$field->label->addCSSClass('p4a_label_required');
		return $this;
	}

	/**
	 * Calls the isValid() method for every field.
	 * If a field does not pass validation sets its error message.
	 * @return boolean
	 */
	public function validateFields()
	{
		$return = true;
		while ($field = $this->fields->nextItem()) {
			$validation_results = $field->isValid();
			if ($validation_results !== true) {
				foreach ($validation_results as &$message) {
					$message = $message;
				}
				$field->setError(join('. ', $validation_results) . '.');
				$return = false;
			}
		}
		return $return;
	}
	
	/**
	 * Prints out a warning message (with a warning icon).
	 * It's a wrapper for P4A::message()
	 * @param string $message
	 * @return P4A_Mask
	 */
	public function warning($message)
	{
		P4A::singleton()->message($message, 'warning');
		return $this;
	}
	
	/**
	 * Prints out an error message (with an error icon).
	 * It's a wrapper for P4A::message()
	 * @param string $message
	 * @return P4A_Mask
	 */
	public function error($message)
	{
		P4A::singleton()->message($message, 'error');
		return $this;
	}
	
	/**
	 * Prints out an info message (with an info icon).
	 * It's a wrapper for P4A::message()
	 * @param string $message
	 * @return P4A_Mask
	 */
	public function info($message)
	{
		P4A::singleton()->message($message, 'info');
		return $this;
	}
}