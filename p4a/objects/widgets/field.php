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
 * A fields is a GUI element that shows its value, and this value can be changed.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Field extends P4A_Widget
{
	/**
	 * @var P4A_Collection
	 */
	public $buttons = null;

	/**
	 * @var P4A_Data_Source
	 */
	public $data = null;

	/**
	 * @var P4A_Data_Field
	 */
	public $data_field = null;

	/**
	 * The data source member that contains the values for this field.
	 * @var string
	 */
	protected $data_value_field = null;

	/**
	 * The data source member that contains the descriptions for this field
	 * @var string
	 */
	protected $data_description_field	= null;

	/**
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * Max size in pixels for image thumbnail
	 * @var integer
	 */
	protected $max_thumbnail_size = 100;

	/**
	 * Defines if a SELECT has "none selected" record
	 * @var boolean
	 */
	protected $allow_null = false;

	/**
	 * Defines the message for "none selected" record for select
	 * @var string
	 */
	protected $null_message = null;

	/**
	 * Field alignment
	 * @var string
	 */
	protected $align = 'left';

	/**
	 * Tells if the fields content is formatted or not
	 * @var boolean
	 */
	protected $formatted = true;

	/**
	 * Path under P4A_UPLOADS_PATH where uploads will be stored
	 * @var string
	 */
	protected $upload_subpath = null;

	/**
	 * Type of encryption to use for password fields
	 * @var string
	 */
	protected $encryption_type = 'md5';

	/**
	 * Is upload enabled on rich text area?
	 * This is disabled by default for security reasons, enable it only after a well done permission check.
	 * @var boolean
	 */
	protected $upload = false;

	/**
	 * @var string
	 */
	protected $rich_textarea_theme = 'Default';

	/**
	 * The error message
	 * @var string
	 */
	protected $_error = null;
	
	/**
	 * @var string
	 */		
	protected $multivalue_separator = '';
	
	/**
	 * @var Zend_Validate
	 */
	protected $_validator_chain = null;

	/**
	 * @param string $name Mnemonic identifier for the object.
	 * @param string $add_default_data_field If it's false the widget doesn't instance a default data_field. You must to set a data_field for the widget before call get_value, get_new_value or getAsstring.
	 */
	public function __construct($name, $add_default_data_field = true)
	{
		parent::__construct($name, 'fld');
		$this->setProperty('name', $this->getId());
		$this->build('p4a_collection', 'buttons');

		if ($add_default_data_field) {
			$this->build('P4A_Data_Field', 'data_field');
		}

		$this->build('P4A_Label', 'label');
		$this->label->setProperty('for', $this->getId() . 'input');
		$this->setDefaultLabel();
	}
	
	/**
	 * Sets a data field as current data_field.
	 * This changes default text alignment for
	 * integer, decimal, float, date, time to right.
	 * 
	 * @param P4A_Data_Field $data_field
	 * @return P4A_Field
	 */
	public function setDataField($data_field)
	{
		if (!$data_field->isReadOnly()) {
			switch(strtolower($data_field->getType())) {
				case 'date':
					$this->setType('date');
					break;
				case 'boolean':
					$this->setType('checkbox');
					break;
			}
		} else {
			$this->setType('label');
		}
		
		$this->data_field =& $data_field;
		return $this;
	}

	protected function getVisualizedDataType()
	{
		$visualization_data_type = null;
		$source_description_field = $this->getSourceDescriptionField();

		if (!is_null($source_description_field) and is_object($this->data)) {
			if (!isset($this->data->fields->$source_description_field)) {
				trigger_error("P4A_Field is missing: {$source_description_field}", E_USER_ERROR);
			}
			$visualization_data_type = $this->data->fields->$source_description_field->getType();
		} elseif (!is_null($this->data_field)) {
			$visualization_data_type = $this->data_field->getType();
		}
		
		return $visualization_data_type;
	}

	/**
	 * @param mixed $value
	 * @return P4A_Field
	 */
	public function setValue($value)
	{
		$this->data_field->setValue($value);
		return $this;
	}

	/**
	 * Sets the error message
	 * @param string $error
	 * @return P4A_Field
	 */
	public function setError($error = '')
	{
		$this->_error = $error;
		$this->redesign();
		return $this;
	}

	/**
	 * Returns the error message
	 * @return string
	 */
	public function getError()
	{
		return $this->_error;
	}

	/**
	 * @param string $text
	 * @return P4A_Field
	 */
	public function setTooltip($text)
	{
		$this->label->setTooltip($text);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTooltip()
	{
		return $this->label->getTooltip();
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->data_field->getValue();
	}

	/**
	 * Returns the "value" for the field to create safe SQL query
	 * @return string
	 */
	public function getSQLValue()
	{
		return $this->data_field->getSQLValue();
	}

	/**
	 * Examines the value passed by the web form and set the new value.
	 * @param mixed $new_value
	 * @return P4A_Field
	 */
	public function setNewValue($new_value)
	{
		$set = true;

		if ($new_value === null) {
			$new_value = null;
		} elseif (($this->type == 'multicheckbox' or $this->type == 'multiselect') and $this->multivalue_separator and is_array($new_value)) {
			$new_value = implode($this->multivalue_separator,$new_value);
		} elseif ($this->isFormattable() and $this->isFormatted()) {
			$new_value = $this->normalize($new_value);
		} elseif (($this->type == 'password') and ($new_value != P4A_PASSWORD_OBFUSCATOR)) {
			switch ($this->getEncryptionType()) {
				case 'md5':
					if (!empty($new_value)) {
						$new_value = md5($new_value);
					}
					break;
				case 'none':
					break;
				default:
					trigger_error('unknown encryption type:' . $this->getEncryptionType(), E_USER_ERROR);
			}
		} elseif (($this->type == 'password') and ($new_value == P4A_PASSWORD_OBFUSCATOR)) {
			$set = false;
		}

		if ($set) {
			$this->data_field->setNewValue($new_value);
		}
		
		return $this;
	}

	/**
	 * Returns the "new_value" for the field (with locale formatting).
	 * @param integer $index If the value is an array that we can return only one element.
	 * @return string
	 */
	public function getNewValue($index = null)
	{
		$new_value = $this->data_field->getNewValue();

		if ($new_value === null) {
			// $new_value = null;
		} elseif (($this->type == 'multicheckbox' or $this->type == 'multiselect') and $this->multivalue_separator and is_string($new_value)) {
			$new_value = explode($this->multivalue_separator,$new_value);
		} elseif ($index === null) {
			if ($this->isFormattable() and $this->isFormatted()) {
				$new_value = $this->format($new_value);
			}
		} elseif (is_array($new_value)) {
			$new_value = $new_value[$index];
		} elseif (substr($new_value, 0, 1) == '{' and substr($new_value, -1) == '}') {
			$tmp_value = substr($new_value, 1, -1);
			$tmp_value = explode("," , $tmp_value);
			$new_value = $tmp_value[$index];
		} 
		return $new_value;
	}

	/**
	 * Returns the "new_value" for the field to make safe SQL query
	 * @return string
	 */
	public function getSQLNewValue()
	{
		return $this->data_field->getSQLNewValue();
	}

	/**
	 * Returns the "new_value" for the field (without locale formatting).
	 * @param integer $index If the value is an array that we can return only one element.
	 * @return string
	 */
	public function getNormalizedNewValue($index = null)
	{
		$new_value = $this->data_field->getNewValue();

		if ($new_value === null) {
			// $new_value = null;
		} elseif ($index === null) {
			// $new_value = $new_value;
		} elseif (is_array($new_value)) {
			$new_value = $new_value[$index];
		} elseif (substr($new_value, 0, 1) == '{' and substr($new_value, -1) == '}') {
			$tmp_value = substr($new_value, 1, -1);
			$tmp_value = explode("," , $tmp_value);
			$new_value = $tmp_value[$index];
		}

		return $new_value;
	}

	/**
	 * @param unknown_type $type (text|password|textarea|rich_textarea|date|hidden|label|select|radio|checkbox|file|multiselect|multicheckbox)
	 * @param unknown_type $multivalue_separator
	 * @return P4A_Field
	 */
	public function setType($type, $multivalue_separator = null)
	{
		if (P4A::singleton()->isHandheld() and $type == 'rich_textarea') {
			$type = 'textarea';
		}

		switch($type) {
		case 'label':
			$label_width = $this->label->getWidth();
			if (!empty($label_width)) {
				$this->setStyleProperty('margin-left', $label_width+20 . "px") ;
			}
			break;
		case 'textarea':
			$this->setWidth(500);
			$this->setHeight(200);
			break;
		case 'rich_textarea':
			$this->setWidth(500);
			$this->setHeight(300);
			break;
		case 'multicheckbox':
		case 'multiselect':
			if ($multivalue_separator !== null) {
				$this->setMultivalueSeparator($multivalue_separator);
			}
			break;
		case 'text':
		case 'date':
		case 'hidden':
		case 'select':
		case 'radio':
		case 'checkbox':
		case 'file':
		case 'multiselect':
		case 'multicheckbox':
		case 'password':
			break;
		default:
			trigger_error("$type is not a supported P4A_Field type", E_USER_ERROR);
		}
		$this->type = $type;
		return $this;
	}
	
	/**
	 * Set type of encryption to use for password fields
	 * @param string $type (md5|none)
	 * @return P4A_Field
	 */
	public function setEncryptionType($type) {
		switch ($type) {
			case 'md5':
			case 'none':
				$this->encryption_type = $type;
				break;
			default:
				trigger_error("unknown encryption type: $type", E_USER_ERROR);
		}
		return $this;
	}

	/**
	 * If we use fields like combo box we have to set a data source.
	 * By default we'll take the data source primary key as value field
	 * and the first fiels (not pk) as description.
	 * @param P4A_Data_Source $data_source
	 * @return P4A_Field
	 */
	public function setSource($data_source)
	{
		unset($this->data);
		$this->data =& $data_source;

		$pk = $this->data->getPk();
		if (is_string($pk)) {
			if ($this->getSourceValueField() === null) {
				$this->setSourceValueField($pk);
			}

			if ($this->getSourceDescriptionField() === null) {
				$num_fields = $this->data->fields->getNumItems();
				$fields = $this->data->fields->getNames();
				$pk = $this->getSourceValueField();

				if ($num_fields == 1) {
					$description_field = $pk;
				} else {
					foreach ($fields as $field) {
						if ($field != $pk) {
							$description_field = $field;
							break;
						}
					}
				}

				$this->setSourceDescriptionField($description_field);
			}
		} elseif (is_array($pk)) {
			trigger_error("Only single primary key is allowed", E_USER_ERROR);
		} elseif (is_null($pk)) {
			trigger_error("Please define a primary key", E_USER_ERROR);
		}
		return $this;
	}

	/**
	 * When the field has a source, this is used to know which source's field keeps the value to be used in this field
	 * @param string $name
	 * @return P4A_Field
	 */
	public function setSourceValueField($name)
	{
		$this->data_value_field = $name;
		return $this;
	}

	/**
	 * When the field has a source, this is used to know which source's field keeps the description to be displayed by this field
	 * @param string $name
	 * @return P4A_Field
	 */
	public function setSourceDescriptionField($name)
	{
		$this->data_description_field = $name;
		return $this;
	}

	/**
	 * Returns the name of the data source member that keeps the field's value.
	 * @return string
	 */
	public function getSourceValueField()
	{
		return $this->data_value_field ;
	}

	/**
	 * Returns the name of the data source member that keeps the field's description.
	 * @return string
	 */
	public function getSourceDescriptionField()
	{
		return $this->data_description_field ;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Returns the encryption type (for password fields)
	 * @return string
	 */
	public function getEncryptionType() {
		return $this->encryption_type;
	}

	/**
	 * Returns true if the field is text, textarea, label or date
	 * @return boolean
	 */
	protected function isFormattable()
	{
		switch ($this->type) {
			case 'label':
			case 'text':
			case 'textarea':
			case 'date':
				return true;
		}

		return false;
	}
	
	/**
	 * Tells you if the field is formatted or not, also sets formatting on/off
	 *
	 * @param boolean $enable_formatting
	 * @return P4A_Field
	 */
	public function isFormatted($enable_formatting = null)
	{
		if ($enable_formatting === null) return $this->formatted;
		$this->formatted = $enable_formatting;
		return $this;
	}

	/**
	 * Format the given value using the current formatting options.
	 * Empty values are not formatted.<br>
	 * If formatting is turned of it does nothing.
	 * @param mixed $value
	 * @param string $type
	 * @param integer $num_of_decimals
	 * @return mixed
	 */
	protected function format($value, $type = null, $num_of_decimals = null)
	{
		if ($type === null) $type = $this->data_field->getType();
		if ($num_of_decimals === null) $num_of_decimals = $this->data_field->getNumOfDecimals();
		
		if ($this->isActionTriggered("onformat")) {
			return $this->actionHandler("onformat", $value, $type, $num_of_decimals);
		} elseif (is_array($value) or is_object($value) or $value === null or strlen($value) == 0) {
			return $value;
		} else {
			return p4a::singleton()->i18n->format($value, $type, $num_of_decimals);
		}
	}

	/**
	 * Takes the formatted passed value and takes it back to its normalized form.
	 * @param mixed $value
	 * @param string $type
	 * @return mixed
	 */
	protected function normalize($value, $type = null)
	{
		if ($type === null) $type = $this->data_field->getType();
		
		if ($this->isActionTriggered("onformat")) {
			return $this->actionHandler("onformat", $value, $type);
		} elseif (is_array($value) or is_object($value) or $value === null or strlen($value) == 0) {
			return $value;
		} else {
			return p4a::singleton()->i18n->normalize($value, $type);
		}
	}

	/**
	 * Returns the HTML rendered field.
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<span id='{$id}' class='hidden'></span>";
		}

		$type = $this->type;
		$suffix = '';
		$css_classes = $this->getCSSClasses();
		$css_classes[] = "p4a_field_$type";

		if ($type == 'rich_textarea') {
			if (!$this->isEnabled()) {
				return $this->getAsLabel();
			}

			$type = 'textarea';
			$suffix = $this->getAsRichTextarea();
		}

		$new_method = 'getAs' . $type;
		$return = $this->$new_method() . $suffix ;
		$error = '';
		if ($this->_error !== null) {
			$css_classes[] = 'field_error';
			$error = "<div class='field_error_msg'>{$this->_error}</div><script type='text/javascript'>\$('#{$id} iframe').mouseover(function () {\$('#{$id} .field_error_msg').show()}); \$('#{$id}input').mouseover(function () {\$('#{$id} .field_error_msg').show()}).mouseout(function () {\$('#{$id} .field_error_msg').hide()})</script>";
			$this->_error = null;
		}
		
		$visualized_data_type = $this->getVisualizedDataType();
		if ($visualized_data_type) $css_classes[] = "p4a_field_data_$visualized_data_type";
		
		$css_classes = join(' ', $css_classes);
		return "<div id='{$id}' class='$css_classes'>{$return}{$error}</div>";
	}

	/**
	 * Returns the HTML rendered field as '<input type="text"'.
	 * @return string
	 */
	public function getAsText()
	{
		$id = $this->getId();
		$header 		= "<input id='{$id}input' type='text' ";
		$close_header 	= '/>';

		if (!$this->isEnabled()) {
			$header .= 'disabled="disabled" ';
		}

		$sReturn = $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
		if ($this->isEnabled() and is_object($this->data) and $this->data instanceof P4A_DB_Source) {
			$sReturn .= "<script type='text/javascript'>\$(function(){\$('#{$id}input').autocomplete('index.php?_p4a_autocomplete&_object={$id}',{delay:10,minChars:2,matchSubset:1,matchContains:1,cacheLength:10,autoFill:true});});</script>";
		}
		return $sReturn;
	}

	/**
	 * @return string
	 */
	public function getAsDate()
	{
		$id = $this->getId();
		$enabled = $this->isEnabled();
		$disabled = $enabled ? "": " disabled='disabled' ";

		$header = "<input id='{$id}input' type='text' class='p4a_date_calendar' $disabled";
		$close_header = "/>";

		if (!P4A::singleton()->isHandheld()) {
			$value = $this->data_field->getNewValue();
			if ($enabled) $close_header .= "<input type='hidden' value='$value' name='p4a_{$id}' id='p4a_{$id}' onchange=\"p4a_calendar_select('p4a_{$id}', '{$id}input')\" />";
			$close_header .= "<input type='button' value='...' id='{$id}button' $disabled onclick=\"return p4a_calendar_open('p4a_{$id}');\" />";
		}

		return $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
	}

	/**
	 * Returns the HTML rendered field as '<input type="password"'.
	 * We use P4A_PASSWORD_OBFUSCATOR for password value so the old password isn't sent over the net.
	 * @return string
	 */
	public function getAsPassword()
	{
		$id = $this->getId();
		$header = "<input id='{$id}input' type='password' ";
		$close_header = '/>';

		if (!$this->isEnabled()) {
			$header .= 'disabled="disabled" ';
		}

		if (strlen($this->getNewValue()) > 0) {
			$header .= ' value="' . P4A_PASSWORD_OBFUSCATOR . '" ';
		}

		$sReturn = $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
		return $sReturn;
	}

	/**
	 * Returns the HTML rendered field as '<input type="textarea"'.
	 * @return string
	 */
	public function getAsTextarea()
	{
		$id = $this->getId();
		$cols = floor($this->getWidth() / 6) - 4;
		$rows = floor($this->getHeight() / 13);
		$header = "<textarea id='{$id}input' cols='$cols' rows='$rows' ";
		$close_header = '>';
		$footer	= '</textarea>';

		if (!$this->isEnabled()) {
			$header .= 'disabled="disabled" ';
		}

		$sReturn  = $this->composeLabel() . "<div class='br'></div>{$header}" . $this->composeStringProperties() . $this->composeStringActions() . $close_header;
		$sReturn .= $this->composeStringValue();
		$sReturn .= $footer;
		return $sReturn;
	}

	/**
	 * Returns the HTML rendered field as '<input type="textarea"' with rich text editing features.
	 * @return string
	 */
	public function getAsRichTextarea()
	{
		$this->useTemplate('rich_textarea');
		$this->addTempVar("connector", urlencode(P4A_APPLICATION_PATH . "/index.php?_rte_file_manager=1&_object_id=" . $this->getId()));
		return $this->fetchTemplate();
	}

	/**
	 * Returns the HTML rendered field as '<input type="hidden"'.
	 * @return string
	 */
	public function getAsHidden()
	{
		$id = $this->getId();
		return "<input type='hidden' id='{$id}input' " . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . " />";
	}

	/**
	 * Returns the HTML rendered field as '<div>$value</div>'.
	 * @return string
	 */
	public function getAsLabel()
	{
		$header         = '<div ';
		$close_header   = '>';
		$footer         = '</div>';
		$value			= '';

		if ($this->data === null) {
			$value = nl2br(htmlspecialchars_decode($this->composeStringValue()));
		} else {
			$external_data		= $this->data->getAll() ;
			$value_field		= $this->getSourceValueField() ;
			$description_field	= $this->getSourceDescriptionField() ;

			foreach ($external_data as $key=>$current) {
				if ($current[$value_field] == $this->getNewValue()) {
					$value = $current[$description_field] ;
				}
			}

			if (empty($value)) {
				$value = __($this->null_message);
			}
		}
		return $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringActions() . $close_header . $value . $footer ;
	}

	/**
	 * Returns the HTML rendered field as combo box.
	 * @return string
	 */
	public function getAsSelect()
	{
		$id = $this->getId();

		$header 			= "<select id='{$id}input' ";
		$close_header 		= '>';
		$footer				= '</select>';
		$header			   .= $this->composeStringActions() . $this->composeStringProperties();

		if (!$this->isEnabled()) {
			$header .= 'disabled="disabled" ';
		}

		$header			   .= $close_header;
		$external_data		= $this->data->getAll() ;
		$value_field		= $this->getSourceValueField() ;
		$description_field	= $this->getSourceDescriptionField() ;
		$new_value			= $this->getNewValue() ;

		if ($this->isNullAllowed()) {
			if ($this->null_message === null) {
				$message = 'None Selected';
			} else {
				$message = $this->null_message;
			}

			$header .= "<option value=''>" . __($message) . "</option>";
		}

		foreach ($external_data as $key=>$current) {
			if ($current[ $value_field ] == $new_value){
				$selected = "selected='selected'";
			} else {
				$selected = "";
			}

			$sContent  = "<option $selected value=\"" . htmlspecialchars($current[$value_field]) ."\">";
			if ($this->isFormatted()) {
				$sContent .= htmlspecialchars($this->format($current[$description_field], $this->data->fields->$description_field->getType(), $this->data->fields->$description_field->getNumOfDecimals()));
			} else {
				$sContent .= htmlspecialchars($current[$description_field]);
			}
			$sContent .= "</option>";

			$header .= $sContent;
		}

		return $this->composeLabel() . $header . $footer ;
	}

	/**
	 * @return string
	 */
	public function getAsMultiselect()
	{
		$id = $this->getID();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		$external_data = $this->data->getAll();
		$value_field = $this->getSourceValueField();
		$description_field = $this->getSourceDescriptionField();
		$new_value = $this->getNewValue();

		$sReturn  = "<input type='hidden' name='{$id}' id='{$id}input' value='' />";
		$sReturn .= "<select id='{$id}input' multiple='multiple' " . $this->composeStringStyle() . " ";
		foreach($this->properties as $property_name=>$property_value){
			if ($property_name == "name") {
				$property_value .= '[]';
			}
			$sReturn .= $property_name . '="' . $property_value . '" ' ;
		}
		if (!isset($this->properties['size']) and sizeof($external_data)) {
			$sReturn .= 'size="' . sizeof($external_data) . '" ' ;
		}
		$sReturn .= "$actions>";

		foreach ($external_data as $key=>$current) {
			if (!$new_value) {
				$new_value = array();
			}
			if (in_array($current[$value_field],$new_value)) {
				$selected = "selected='selected'";
			} else {
				$selected = "";
			}

			$sReturn .= "<option $selected value='" . htmlspecialchars($current[$value_field]) ."'>";
			if ($this->isFormatted()) {
				$sReturn .= htmlspecialchars($this->format($current[ $description_field ], $this->data->fields->$description_field->getType(), $this->data->fields->$description_field->getNumOfDecimals()));
			} else {
				$sReturn .= htmlspecialchars($current[$description_field]);
			}
			$sReturn .= "</option>";

		}
		$sReturn .= "</select>";
		return $this->composeLabel() . $sReturn;
	}

	/**
	 * @return string
	 */
	public function getAsMulticheckbox()
	{
		$id = $this->getID();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();

		$sReturn  = "<div class='p4a_field_multicheckbox_values'>";
		$sReturn .= "<input type='hidden' name='$id' id='{$id}input' value='' />";

		$external_data		= $this->data->getAll();
		$value_field		= $this->getSourceValueField();
		$description_field	= $this->getSourceDescriptionField();
		$new_value			= $this->getNewValue();

		$i = 0;
		foreach ($external_data as $key=>$current) {
			if (!$new_value) {
				$new_value = array();
			}

			if (in_array($current[$value_field], $new_value)) {
				$checked = "checked='checked'";
			} else {
				$checked = "";
			}
			$sReturn .= "<div><input type='checkbox' id='{$id}_{$i}input' name='{$id}[]' value='{$current[$value_field]}' $checked /><label for='{$id}_{$i}input'>{$current[$description_field]}</label></div>\n";
			$i++;
		}

		$sReturn .= "</div>";
		return $this->composeLabel() . $sReturn;
	}

	/**
	 * Use this method when you're creating a multivalue field which rely on a single db field instead of an external table
	 * @param string $string
	 * @return P4A_Field
	 */
	function setMultivalueSeparator($string)
	{
		$this->multivalue_separator = $string;
		return $this;
	}

	/**
	 * Used ony for select, sets the select to allow a "none selected" record
	 * @param string|boolean $message If false disable the feature, otherwise enable it
	 * @return P4A_Field
	 */
	public function allowNull($message = null)
	{
		if ($message === false) {
			$this->allow_null = false;
			$this->null_message = null;
		} else {
			$this->allow_null = true;
			$this->null_message = $message;
		}
		return $this;
	}

	/**
	 * Used ony for select, returns if the select allows a "none selected" record
	 * @return boolean
	 */
	public function isNullAllowed()
	{
		return $this->allow_null;
	}

	/**
	 * Returns the HTML rendered field as radio buttons group
	 * @return string
	 */
	public function getAsRadio()
	{
		$id = $this->getId();
		$external_data		= $this->data->getAll() ;
		$value_field		= $this->getSourceValueField() ;
		$description_field	= $this->getSourceDescriptionField() ;
		$new_value			= $this->getNewValue();
		$sContent			= "";

		$enabled = '';
		if (!$this->isEnabled()) {
			$enabled = 'disabled="disabled" ';
		}

		if ($this->isNullAllowed()) {
			if ($this->null_message === null) {
				$message = 'none_selected';
			} else {
				$message = $this->null_message;
			}

			array_unshift($external_data, array($value_field=>'', $description_field=>__($message)));
		}

		foreach ($external_data as $key=>$current) {
			if ($current[$value_field] == $new_value) {
				$checked = "checked='checked'";
			} else {
				$checked = "";
			}

			$sContent .= "<div><input $enabled name='{$id}' id='{$id}_{$key}input' type='radio' " . $this->composeStringActions() . " $checked value='" . htmlspecialchars($current[$value_field]) ."'/>";
			$sContent .= "<label for='{$id}_{$key}input'>";
			if ($this->isFormatted()) {
				$sContent .= $this->format($current[$description_field], $this->data->fields->$description_field->getType(), $this->data->fields->$description_field->getNumOfDecimals());
			} else {
				$sContent .= $current[$description_field];
			}
			$sContent .= "</label>";
			$sContent .= '</div>';
		}

		$this->label->unsetProperty('for');
		$return = $this->composeLabel() . "<div>$sContent</div>";
		$this->label->setProperty('for', "{$id}input");
		return $return;
	}

	/**
	 * Returns the HTML rendered field as checkbox
	 * @return string
	 */
	public function getAsCheckbox()
	{
		$new_value = $this->getNewValue();

		// PostgreSQL uses "t" and "f" to return boolen values
		// For all the others we assume "1" or "0"
		if ($new_value == 1 or $new_value === 't') {
			$checked = "checked='checked'" ;
			$new_value = 1;
		} else {
			$checked = '' ;
			$new_value = 0;
		}

		$id = $this->getId();
		$header = "<input type='hidden' name='{$id}' value='{$new_value}' /><input type='checkbox' id='{$id}input' value='1' $checked ";
		$close_header = "/>";

		if( !$this->isEnabled() ) {
			$header .= 'disabled="disabled" ';
		}

		$header .= $this->composeStringActions() . $this->composeStringProperties() . $close_header;
		return $this->composeLabel() . $header;
	}

	/**
	 * Returns the HTML rendered field as file upload
	 * @return string
	 */
	public function getAsFile()
	{
		$p4a = P4A::singleton();
		$id = $this->getID();

		if ($this->getNewValue() === null) {
			$action = 'p4a_event_execute';
			if (P4A_AJAX_ENABLED) $action .= '_ajax';
			$sReturn = "<input type='file' id='{$id}input' onchange='$action(\"$id\", \"onchange\");' ";
			$this->intercept($this, 'onchange', 'redesign');
			if (!$this->isEnabled()) {
				$sReturn .= 'disabled="disabled" ';
			}

			$sReturn .= $this->composeStringActions() . $this->composeStringProperties() . ' />';
		} else {
			$this->buildDeletePreviewDownloadButtons();
			if ($this->isEnabled()) {
				$this->buttons->button_file_delete->enable();
			} else {
				$this->buttons->button_file_delete->disable();
			}
			
			if (P4A_Thumbnail_Generator::isMimeTypeSupported($this->getNewValue(3))) {
				return $this->getAsImage();
			}

			$src = P4A_UPLOADS_URL . $this->getNewValue(1);
			$mime_type = $this->getNewValue(3);
			$this->label->unsetProperty('for');

			$sReturn  = '<table>';
			$sReturn .= '<tr><th>' . __('Name') . ':</th><td>' . $this->getNewValue(0) . '</td></tr>';
			$sReturn .= '<tr><th>' . __('Size') . ':</th><td>' . $p4a->i18n->format($this->getNewValue(2)/1024, "decimal") . ' KB</td></tr>';
			$sReturn .= '<tr><th>' . __('Type') . ':</th><td>' . $this->getNewValue(3) . '</td></tr>';
			$this->buttons->button_file_preview->enable(P4A_Is_Mime_Type_Embeddable($mime_type));
			$sReturn .= '<tr><td colspan="2">' . $this->buttons->button_file_preview->getAsString() . ' '. $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
			$sReturn .= '</table>';
		}

		$sReturn = $this->composeLabel() . $sReturn;
		$this->label->setProperty('for', "{$id}input");
		return $sReturn;
	}

	/**
	 * Action handler for file deletetion
	 */
	public function fileDeleteOnClick()
	{
		$this->redesign();
		$this->setNewValue(null);
	}

	/**
	 * Action handler for file preview (only images)
	 */
	public function filePreviewOnClick()
	{
		$file = P4A_Strip_Double_Slashes(P4A_UPLOADS_URL . $this->getNewValue(1));
		
		if (P4A_Is_Mime_Type_Embeddable($this->getNewValue(3))) {
			$raw_html = P4A_Embedded_Player($file, $this->getNewValue(3), $this->getNewValue(4), $this->getNewValue(5));
		} else {
			$raw_html = "<img alt='' src='$file' />";
		}
		
		P4a::singleton()->openMask("P4A_Preview_Mask")
			->setTitle($this->getNewValue(0))
			->setRawHTML($raw_html);
	}

	/**
	 * Action handler for file download
	 */
	public function fileDownloadOnClick()
	{
		P4A_Redirect_To_File($this->getNewValue(1));
	}

	/**
	 * Sets the subpath of P4A_UPLOADS_PATH where the upload will happen
	 * @param string The subdir (can be "test", "test/", "test/test", "test/test/test/")
	 * @return P4A_Field
	 */
	public function setUploadSubpath($subpath = null)
	{
		$this->upload_subpath = $subpath;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUploadSubpath()
	{
		return $this->upload_subpath;
	}

	/**
	 * Returns the HTML rendered field as image upload
	 * @return string
	 */
	public function getAsImage()
	{
		if ($this->getNewValue() === null) return $this->getAsFile();

		$mime_type = explode('/', $this->getNewValue(3));
		$mime_type = $mime_type[0];
		if ($mime_type != 'image') return $this->getAsFile();

		$sReturn  = '<table>' ;
		if (P4A_GD) {
			$src = $this->getNewValue(1);
			$sReturn .= '<tr><td colspan="2"><img alt="' . __('Preview') . '" src=".?_p4a_image_thumbnail=' . urlencode("$src&{$this->max_thumbnail_size}") . '" /></td></tr>';
		}
		$this->buttons->button_file_preview->enable();
		$sReturn .= '<tr><th>' . __('Name') . ':</th><td>' . $this->getNewValue(0) . '</td></tr>';
		$sReturn .= '<tr><th>' . __('Size') . ':</th><td>' . P4A::singleton()->i18n->format($this->getNewValue(2)/1024, "decimal") . ' KB</td></tr>';
		$sReturn .= '<tr><th>' . __('Type') . ':</th><td>' . $this->getNewValue(3) . '</td></tr>';
		$sReturn .= '<tr><td colspan="2">' . $this->buttons->button_file_preview->getAsString() . ' '. $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
		$sReturn .= '</table>' ;

		return $this->composeLabel() . $sReturn;
	}

	/**
	 * Sets the maximum size for image thumbnails
	 * @param integer
	 * @return P4A_Field
	 */
	public function setMaxThumbnailSize($size = null)
	{
		$this->max_thumbnail_size = $size;
		return $this;
	}

	/**
	 * Returns the maximum size for image thumbnails.
	 * @return integer
	 */
	public function getMaxThumbnailSize()
	{
		return $this->max_thumbnail_size;
	}
	
	/**
	 * @return P4A_Field
	 */
	protected function buildDeletePreviewDownloadButtons()
	{
		if (!isset($this->buttons->button_file_delete)) {
			$this->buttons->build("p4a_button", "button_file_delete")
				->setLabel('Delete')
				->addAjaxAction('onclick')
				->implement('onclick', $this, 'fileDeleteOnClick');
		}
		if (!isset($this->buttons->button_file_preview)) {
			$this->buttons->build("p4a_button", "button_file_preview")
				->setLabel('Preview')
				->implement('onclick', $this, 'filePreviewOnClick');
		}
		if (!isset($this->buttons->button_file_download)) {
			$this->buttons->build("p4a_button", "button_file_download")
				->setLabel('Download')
				->implement('onclick', $this, 'fileDownloadOnClick');
		}
		return $this;
	}

	/**
	 * Sets the label for the field.
	 * In rendering phase it will be added with ':  '.
	 * @param string $value
	 * @return P4A_Field
	 */
	public function setLabel($value)
	{
		$this->label->setLabel($value);
		return $this;
	}

	/**
	 * Returns the label for the field
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label->getLabel();
	}

	/**
	 * Renders the label to HTML
	 * @return string
	 */
	protected function composeLabel()
	{
		return $this->label->getAsString();
	}

	/**
	 * Sets the alignment property for the field.
	 * @param string $align
	 * @return P4A_Field
	 */
	public function setAlign($align)
	{
		$this->align = $align;
		return $this;
	}

	/**
	 * Composes a string containing all the HTML properties of the widget.
	 * Note: it will also contain the name and the value.
	 * @return string
	 */
	protected function composeStringProperties()
	{
		$sReturn = "";
		foreach($this->properties as $property_name=>$property_value) {
			if (!(($this->type == 'password') and ($property_name == 'value'))) {
				$sReturn .= $property_name . "='" . htmlspecialchars($property_value) . "' " ;
			}
		}

		$sReturn .= $this->composeStringStyle();
		return $sReturn;
	}

	/**
	 * Returns the field's value differently if we are in an '<input value="' environment on in a '<tag>value</tag>' environment.
	 * @return string
	 */
	protected function composeStringValue()
	{
		$value = $this->getNewValue();
		if (is_array($value)) {
			$value = join($value, ",");
		}

		switch ($this->type) {
			case 'text':
			case 'hidden':
			case 'date':
				return 'value="' . htmlspecialchars($value) . '" ';
				break;
			case 'textarea':
			case 'rich_textarea':
				return htmlspecialchars($value);
				break;
			case 'label':
				return $value;
				break;
		}
	}

	/**
	 * Enables upload for rich_textarea fields
	 * @param boolean $enable
	 * @return P4A_Field
	 */
	public function enableUpload($enable = true)
	{
		$this->upload = $enable;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isUploadEnabled()
	{
		return $this->upload;
	}

	/**
	 * sets the rich textarea theme (Basic|Default|Full)
	 * @return P4A_Field
	 */
	public function setRichTextareaTheme($theme)
	{
		$this->rich_textarea_theme = $theme;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRichTextareaTheme()
	{
		return $this->rich_textarea_theme;
	}
	
	/**
	 * @param Zend_Validate_Abstract $validator
	 * @param boolean $break_chain_on_failure
	 * @return P4A_Field
	 */
	public function addValidator(Zend_Validate_Interface $validator, $break_chain_on_failure = false)
	{
		if ($this->_validator_chain === null) {
			$this->_validator_chain = new P4A_Validate();
		}
		
		$this->_validator_chain->addValidator($validator, $break_chain_on_failure);
		return $this;
	}
	
	public function removeValidator($validator_class)
	{
		if ($this->_validator_chain !== null) {
			$this->_validator_chain->removeValidator($validator_class);
		}
		return $this;
	}
	
	/**
	 * Validate the normalized new value.
	 * Returns true if there are no validators or if validation passes,
	 * returns the array of error messages if validators fail.
	 *
	 * @return boolean|array
	 */
	public function isValid()
	{
		if ($this->_validator_chain === null) return true;
		if ($this->_validator_chain->isValid($this->getNormalizedNewValue())) return true;
		return $this->_validator_chain->getMessages();
	}
}