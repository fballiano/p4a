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
 * A fields is a GUI element that shows its value, and this value can be changed.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
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
	protected $data = null;

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
	 * The data source member that contains the descriptions for this field.
	 * @var string
	 * @access private
	 */
	protected $data_description_field	= null;

	/**
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * Max size in pixels for image thumbnail.
	 * @var integer
	 */
	protected $max_thumbnail_size = 100;

	/**
	 * Defines if a SELECT has "none selected" record.
	 * @var boolean
	 */
	protected $allow_null = false;

	/**
	 * Defines the message for "none selected" record for select.
	 * @var integer
	 * @access private
	 */
	var $null_message = null;

	/**
	 * Field align.
	 * @var string
	 * @access private
	 */
	var $align = 'left';

	/**
	 * Tells if the fields content is formatted or not.
	 * @var string
	 * @access private
	 */
	var $formatted = true;

	/**
	 * Path under P4A_UPLOADS_PATH where uploads happens.
	 * @var string
	 * @access private
	 */
	var $upload_subpath = null;

	/**
	 * Type of encryption to use for password fields
	 * @var		string
	 * @access	private
	 */
	var $encryption_type = 'md5';

	/**
	 * Is upload enabled on rich text area?
	 * This is disabled by default for security reasons, enable it only after a well done permission check.
	 * @var		boolean
	 * @access	private
	 */
	var $upload = false;

	/**
	 * rich textarea theme/toolbar (advanced|simple)
	 * @var string
	 * @access private
	 */
	var $rich_textarea_theme = 'Default';

	/**
	 * buttons for rich textarea toolbars
	 * @var array
	 * @access private
	 */
	var $rich_textarea_toolbars = array();

	/**
	 * The error message
	 * @var string
	 * @access private
	 */
	var $_error = null;
	
	/**
	 * The multivalue separator
	 * @var string
	 * @access public
	 */		
	var $multivalue_separator = '';

	/**
	 * @param string				Mnemonic identifier for the object.
	 * @param string				If it's false the widget doesn't instance a default data_field. You must to set a data_field for the widget before call get_value, get_new_value or getAsstring.
	 * @access private
	 */
	function __construct($name, $add_default_data_field = true)
	{
		parent::__construct($name, 'fld');
		$this->setProperty('name', $this->getId());

		$this->build("p4a_collection", "buttons");
		$this->setType('text');

		//Data field
		if ($add_default_data_field){
			$this->build("P4A_Data_Field", "data_field");
		}

		//Rich textarea things
		$this->setRichTextareaToolbar(0, 'Cut,Copy,Paste,PasteText,PasteWord,-,Undo,Redo,-,Find,Replace,-,SelectAll,RemoveFormat,-,SourceSimple,FitWindow,Preview,Print');
		$this->setRichTextareaToolbar(1, 'Bold,Italic,Underline,StrikeThrough,-,Subscript,Superscript,-,OrderedList,UnorderedList,-,Outdent,Indent,-,FontFormat');
		$this->setRichTextareaToolbar(2, 'Link,Unlink,Anchor,-,Image,Flash,-,Table,TableInsertRow,TableDeleteRows,TableInsertColumn,TableDeleteColumns,TableInsertCell,TableDeleteCells,TableMergeCells,TableSplitCell,-,Rule,SpecialChar');

		//Label
		$this->build("P4A_Label", "label");
		$this->label->setProperty("for", $this->getId() . 'input');
		$this->setDefaultLabel();
	}

	/**
	 * Sets a data field as current data_field.
	 * This changes default text alignment for
	 * integer, decimal, float, date, time to right.
	 * @access public
	 * @param DATA_FIELD		The new data field
	 */
	function setDataField(&$data_field)
	{
		unset($this->data_field);

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
		$this->setDefaultVisualizationProperties();
	}

	/**
	 * Sets the default visualization property for the field.
	 * @access private
	 */
	function setDefaultVisualizationProperties()
	{
		$visualization_data_type = null;
		$source_description_field = $this->getSourceDescriptionField();

		if (!is_null( $source_description_field ) and is_object($this->data)) {
			if (!isset($this->data->fields->$source_description_field)) {
				P4A_Error("P4A_Field is missing: {$source_description_field}");
			}
			$visualization_data_type = $this->data->fields->$source_description_field->getType();
		} elseif (!is_null($this->data_field)) {
			$visualization_data_type = $this->data_field->getType();
		}

		switch ($visualization_data_type) {
			case 'integer':
			case 'decimal':
			case 'float':
			case 'date':
			case 'time':
				$this->setStyleProperty('text-align', 'right');
				break;
			default:
				$this->setStyleProperty('text-align', 'left');
				break;
		}
	}

	/**
	 * Sets the value for the field.
	 * @param mixed				Value.
	 * @access public
	 */
	function setValue($value)
	{
		$this->data_field->setValue($value);
	}

	/**
	 * Sets the error message
	 * @param string $error
	 */
	public function setError($error = '')
	{
		$this->_error = $error;
	}

	/**
	 * Returns the error message.
	 */
	public function getError()
	{
		return $this->_error;
	}

	/**
	 * @param string $text
	 */
	public function setTooltip($text)
	{
		$this->label->setTooltip($text);
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
					P4A_Error('unknown encryption type:' . $this->getEncryptionType());
			}
		} elseif (($this->type == 'password') and ($new_value == P4A_PASSWORD_OBFUSCATOR)) {
			$set = false;
		}

		if ($set) {
			$this->data_field->setNewValue($new_value);
		}
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
	 * @param unknown_type $type (text|password|textarea|rich_textarea|date|hidden|label|select|radio|checkbox|file|image|multiselect|multicheckbox)
	 * @param unknown_type $multivalue_separator
	 */
	public function setType($type, $multivalue_separator = null)
	{
		$p4a =& p4a::singleton();
		if ($p4a->isHandheld() and $type == 'rich_textarea') {
			$type = 'textarea';
		}

		$this->type = $type;

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
		case 'color':
			$this->setWidth(60);
			$this->setProperty('maxlength', 7);
			break;
		case 'multicheckbox':
		case 'multiselect':
			if ($multivalue_separator) {
				$this->setMultivalueSeparator($multivalue_separator);
			}
			break;
		}
	}
	
	/**
	 * Set type of encryption to use for password fields
	 * @param string $type (md5|none)
	 */
	public function setEncryptionType($type) {
		switch ($type) {
			case 'md5':
			case 'none':
				$this->encryption_type = $type;
				break;
			default:
				P4A_Error('unknown encryption type:' . $type);
		}
	}

	/**
	 * If we use fields like combo box we have to set a data source.
	 * By default we'll take the data source primary key as value field
	 * and the first fiels (not pk) as description.
	 * @param P4A_Data_Source $data_source
	 */
	public function setSource(&$data_source)
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
			P4A_Error("ONLY ONE PK IN THIS CASE");
		} elseif (is_null($pk)) {
			P4A_Error("PLEASE DEFINE A PRIMARY KEY");
		}

		$this->setDefaultVisualizationProperties();
	}

	/**
	 * When the field has a source, this is used to know which source's field keeps the value to be used in this field
	 * @param string $name
	 */
	public function setSourceValueField($name)
	{
		$this->data_value_field = $name ;
	}

	/**
	 * When the field has a source, this is used to know which source's field keeps the description to be displayed by this field
	 * @param string $name
	 */
	public function setSourceDescriptionField($name)
	{
		$this->data_description_field = $name ;
		$this->setDefaultVisualizationProperties();
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
	 * @return	string
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
	 * @return boolean
	 */
	public function isFormatted($enable_formatting = null)
	{
		if ($enable_formatting === null) return $this->formatted;
		$this->formatted = $enable_formatting;
		return $enable_formatting;
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

		$type   = $this->type;
		$suffix = '';

		if ($type == 'rich_textarea') {
			if (!$this->isEnabled()) {
				return $this->getAsLabel();
			}

			$type = 'textarea';
			$suffix = $this->getAsRichTextarea();
		}

		$new_method = 'getAs' . $type;
		$string = $this->$new_method();
		$sReturn =  $string . $suffix ;

		if ($this->_error !== null) {
			$container_class = 'class="field_error"';
			$error = "<div class='field_error_msg' >{$this->_error}</div>";
			$this->_error = null;
		} else {
			$container_class = '';
			$error = '';
		}

		return "<div id='{$id}' $container_class>{$sReturn}{$error}</div>";
	}

	/**
	 * Returns the HTML rendered field as '<input type="text"'.
	 * @return string
	 * @access public
	 */
	function getAsText()
	{
		$id = $this->getId();
		$header 		= "<input id='{$id}input' type='text' class='border_color1 font_normal' ";
		$close_header 	= '/>';

		if (!$this->isEnabled()) {
			$header .= 'disabled="disabled" ';
		}

		$sReturn = $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
		if ($this->isEnabled() and is_object($this->data)) {
			$sReturn .= "<script type='text/javascript'>\$(function(){\$('#{$id}input').autocomplete('index.php?_p4a_autocomplete&_object={$id}',{delay:10,minChars:2,matchSubset:1,matchContains:1,cacheLength:10,autoFill:true});});</script>";
		}
		return $sReturn;
	}

	function getAsDate()
	{
		$p4a =& P4A::singleton();
		$id = $this->getId();

		$enabled = $this->isEnabled();
		$disabled = $enabled ? "": " disabled='disabled' ";

		$header = "<input id='{$id}input' type='text' class='p4a_date_calendar border_color1 font_normal' $disabled";
		$close_header = "/>";

		if (!$p4a->isHandheld()) {
			$value = $this->data_field->getNewValue();
			if ($enabled) $close_header .= "<input type='hidden' value='$value' name='p4a_{$id}' id='p4a_{$id}' onchange=\"p4a_calendar_select('p4a_{$id}', '{$id}input')\" />";
			$close_header .= "<input type='button' value='...' id='{$id}button' class='border_box font4 no_print' $disabled onclick=\"return p4a_calendar_open('p4a_{$id}');\" />";
		}

		return $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
	}

	/**
	 * Returns the HTML rendered field as '<input type="password"'.
	 * We use P4A_PASSWORD_OBFUSCATOR for password value so the old password isn't sent over the net.
	 * @return string
	 * @access public
	 */
	function getAsPassword()
	{
		$id = $this->getId();
		$header = "<input id='{$id}input' type='password' class='border_color1 font_normal' ";
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
	 * @access public
	 */
	function getAsTextarea()
	{
		$id = $this->getId();
		$cols = floor($this->getWidth() / 6) - 4;
		$rows = floor($this->getHeight() / 13);
		$header = "<textarea id='{$id}input' class='border_color1 font_normal' cols='$cols' rows='$rows' ";
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
	 * @access public
	 */
	function getAsRichTextarea()
	{
		$this->useTemplate('rich_textarea');
		$this->addTempVar("connector", urlencode(P4A_APPLICATION_PATH . "/index.php?_rte_file_manager=1&_object_id=" . $this->getId()));
		return $this->fetchTemplate();
	}

	/**
	 * Returns the HTML rendered field as '<input type="hidden"'.
	 * @return string
	 * @access public
	 */
	function getAsHidden()
	{
		$id = $this->getId();
		return "<input type='hidden' id='{$id}input' " . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . " />";
	}

	/**
	 * Returns the HTML rendered field as '<div>$value</div>'.
	 * @return string
	 * @access public
	 */
	function getAsLabel()
	{
		$header         = '<div class="field_as_label" ';
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
	 * @access public
	 */
	function getAsSelect()
	{
		$p4a =& P4A::singleton();
		$id = $this->getId();

		$header 			= "<select id='{$id}input' class='border_box font_normal' ";
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

			$sContent  = "<option $selected value='" . htmlspecialchars($current[$value_field]) ."'>";
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

	function getAsMultiselect()
	{
		$p4a =& P4A::singleton();
		$id = $this->getID();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();

		$sReturn  = "<input type='hidden' name='{$id}' id='{$id}input' value='' />";
		$sReturn .= "<select id='{$id}input' class='border_box font_normal' multiple='multiple' " . $this->composeStringStyle() . " ";
		foreach($this->properties as $property_name=>$property_value){
			if ($property_name == "name") {
				$property_value .= '[]';
			}
			$sReturn .= $property_name . '="' . $property_value . '" ' ;
		}
		$sReturn .= "$actions>";

		$external_data		= $this->data->getAll();
		$value_field		= $this->getSourceValueField();
		$description_field	= $this->getSourceDescriptionField();
		$new_value			= $this->getNewValue();

		foreach ($external_data as $key=>$current)
		{
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

	function getAsMulticheckbox()
	{
		$p4a =& P4A::singleton();
		$id = $this->getID();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();

		$sReturn  = "<div class='font_normal' style='float:left;text-align:left;'>";
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
			$sReturn .= "<div><input type='checkbox' class='border_none' id='{$id}_{$i}input' name='{$id}[]' value='{$current[$value_field]}' $checked /><label for='{$id}_{$i}input'>{$current[$description_field]}</label></div>\n";
			$i++;
		}

		$sReturn .= "</div>";
		return $this->composeLabel() . $sReturn;
	}

	function setMultivalueSeparator($string)
	{
		$this->multivalue_separator = $string;
	}

	/**
	 * Used ony for select, sets the select to allow a "none selected" record.
	 * @param string		The message for "none selected"
	 * @access public
	 */
	function allowNull($message = null)
	{
		$this->allow_null = true;
		$this->null_message = $message;
	}

	/**
	 * Used ony for select, sets the select to do not allow a "none selected" record.
	 * @access public
	 */
	function noAllowNull()
	{
		$this->allow_null = false ;
	}

	/**
	 * Used ony for select, returns if the select allows a "none selected" record.
	 * @return boolean
	 * @access public
	 */
	function isNullAllowed()
	{
		return $this->allow_null ;
	}

	/**
	 * Returns the HTML rendered field as radio buttons group.
	 * @return string
	 * @access public
	 */
	function getAsRadio()
	{
		$p4a =& P4A::singleton();
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

			$sContent .= "<div><input $enabled class='radio' name='{$id}' id='{$id}_{$key}input' type='radio' " . $this->composeStringActions() . " $checked value='" . htmlspecialchars($current[$value_field]) ."'/>";
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
		$return = $this->composeLabel() . "<div class='font_normal' style='float:left;text-align:left;'>$sContent</div>";
		$this->label->setProperty('for', "{$id}input");
		return $return;
	}

	/**
	 * Returns the HTML rendered field as checkbox.
	 * @return string
	 * @access public
	 */
	function getAsCheckbox()
	{
		$new_value = $this->getNewValue();

		// PostgreSQL uses "t" and "f" to return boolen values
		// For all the others we assume "1" or "0"
		if ($new_value == 1 or $new_value === 't') {
			$checked = "checked='checked'" ;
		} else {
			$checked = '' ;
		}

		$id = $this->getId();
		$header = "<input type='hidden' name='{$id}' value='0' /><input type='checkbox' id='{$id}input' class='border_none' value='1' $checked ";
		$close_header = "/>";

		if( !$this->isEnabled() ) {
			$header .= 'disabled="disabled" ';
		}

		$header .= $this->composeStringActions() . $this->composeStringProperties() . $close_header;
		return $this->composeLabel() . $header ;
	}

	/**
	 * Returns the HTML rendered field as file upload.
	 * @return string
	 * @access public
	 */
	function getAsFile()
	{
		$p4a =& P4A::singleton();
		$id = $this->getID();

		if ($this->getNewValue() === null) {
			//if ($p4a->isAjaxEnabled()) {
			//	$action = 'executeAjaxEvent';
			//} else {
				$action = 'executeEvent';
			//}
			$sReturn = "<div style='float:left'><input type='file' id='{$id}input' onchange='$action(\"$id\", \"onchange\");' class='border_box font_normal clickable' ";
			$this->intercept($this, 'onchange', 'redesign');
			if (!$this->isEnabled()) {
				$sReturn .= 'disabled="disabled" ';
			}

			$sReturn .= $this->composeStringActions() . $this->composeStringProperties() . ' /></div>';
		} else {
			if (!isset($this->buttons->button_file_delete)) {
				$button_file_delete =& $this->buttons->build("p4a_button", "button_file_delete");
				$button_file_preview =& $this->buttons->build("p4a_button", "button_file_preview");
				$button_file_download =& $this->buttons->build("p4a_button", "button_file_download");

				$button_file_delete->setLabel('Delete');
				$button_file_preview->setLabel('Preview');
				$button_file_download->setLabel('Download');

				$button_file_delete->addAjaxAction("onClick");
				$this->intercept($button_file_delete, 'onclick', 'fileDeleteOnClick');
				$this->intercept($button_file_preview, 'onclick', 'filePreviewOnClick');
				$this->intercept($button_file_download, 'onclick', 'fileDownloadOnClick');
			}

			if ($this->isEnabled()) {
				$this->buttons->button_file_delete->enable();
			} else {
				$this->buttons->button_file_delete->disable();
			}

			$src = P4A_UPLOADS_URL . $this->getNewValue(1);
			$mime_type = $this->getNewValue(3);
			$this->label->unsetProperty('for');

			$sReturn  = '<table class="border_box">';
			$sReturn .= '<tr><td align="left">' . __('Name') . ':&nbsp;&nbsp;</td><td align="left">' . $this->getNewValue(0) . '</td></tr>';
			$sReturn .= '<tr><th align="left">' . __('Size') . ':&nbsp;&nbsp;</th><td align="left">' . $p4a->i18n->format($this->getNewValue(2)/1024, "decimal") . ' KB</td></tr>';
			$sReturn .= '<tr><td align="left">' . __('Type') . ':&nbsp;&nbsp;</td><td align="left">' . $this->getNewValue(3) . '</td></tr>';

			if (P4A_Is_Mime_Type_Embeddable($mime_type)) {
				$sReturn .= '<tr><td colspan="2" align="center">' . $this->buttons->button_file_preview->getAsString() . ' '. $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
			} else {
				$sReturn .= '<tr><td colspan="2" align="center">' . $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
			}

			$sReturn .= '</table>';
		}

		$sReturn = $this->composeLabel() . $sReturn;
		$this->label->setProperty('for', "{$id}input");
		return $sReturn;
	}

	/**
	 * Action handler for file deletetion.
	 * @access public
	 */
	function fileDeleteOnClick()
	{
		$this->redesign();
		$this->setNewValue(null);
	}

	/**
	 * Action handler for file preview (only images).
	 * @access public
	 */
	function filePreviewOnClick()
	{
		$p4a =& p4a::singleton();
		$p4a->openMask("P4A_Mask_Preview");
		$p4a->active_mask->setTitle($this->getNewValue(0));

		if (P4A_Is_Mime_Type_Embeddable($this->getNewValue(3))) {
			$raw_html = P4A_Embedded_Player(P4A_UPLOADS_URL . $this->getNewValue(1), $this->getNewValue(3), $this->getNewValue(4), $this->getNewValue(5));
		} else {
			$raw_html = '<img alt="" src="' . P4A_UPLOADS_URL . $this->getNewValue(1) . '" />';
		}
		$p4a->active_mask->setRawHTML($raw_html);
	}

	/**
	 * Action handler for file download.
	 * @access public
	 */
	function fileDownloadOnClick()
	{
		$name = $this->getNewValue(0);
		$src = P4A_UPLOADS_DIR . $this->getNewValue(1);
		$size = $this->getNewValue(2);
		
		if (file_exists($src)) {
			header("Cache-control: private");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$name\"");
			header("Content-Length: $size");

			echo readfile($src, "r");
			die();
		} else {
			$e = new P4A_Error("File \"$name\" does not exist", $this);
			if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
				die();
			}
		}
	}

	/**
	 * Sets the subpath of P4A_UPLOADS_PATH where the upload will happen.
	 * @access public
	 * @param string	The subdir (can be "test", "test/", "test/test", "test/test/test/")
	 */
	function setUploadSubpath($subpath = null)
	{
		$this->upload_subpath = $subpath;
	}

	/**
	 * Removes the subpath for upload.
	 * @access public
	 */
	function unsetUploadSubpath()
	{
		$this->upload_subpath = null;
	}

	/**
	 * Return the subpath for upload..
	 * @access public
	 */
	function getUploadSubpath()
	{
		return $this->upload_subpath;
	}

	/**
	 * Returns the HTML rendered field as image upload.
	 * @return string
	 * @access public
	 */
	function getAsImage()
	{
		$p4a =& P4A::singleton();
		$id = $this->getID();

		if ($this->getNewValue() === null) {
			//if ($p4a->isAjaxEnabled()) {
			//	$action = 'executeAjaxEvent';
			//} else {
				$action = 'executeEvent';
			//}
			$sReturn = "<div style='float:left'><input id='{$id}input' onchange='{$action}(\"$id\", \"onchange\");' type='file' class='border_box font_normal clickable' ";
			$this->intercept($this,'onchange','redesign');

			if (!$this->isEnabled()) {
				$sReturn .= 'disabled="disabled" ';
			}

			$sReturn .= $this->composeStringActions() . $this->composeStringProperties() . ' /></div>';
		} else {
			$mime_type = explode('/', $this->getNewValue(3));
			$mime_type = $mime_type[0];

			if (!isset($this->buttons->button_file_delete)) {
				$button_file_delete =& $this->buttons->build("p4a_button", "button_file_delete");
				$button_file_preview =& $this->buttons->build("p4a_button", "button_file_preview");
				$button_file_download =& $this->buttons->build("p4a_button", "button_file_download");

				$button_file_delete->setLabel('Delete');
				$button_file_preview->setLabel('Preview');
				$button_file_download->setLabel('Download');

				$button_file_delete->addAjaxAction("onClick");
				$this->intercept($button_file_delete, 'onclick', 'fileDeleteOnClick');
				$this->intercept($button_file_preview, 'onclick', 'filePreviewOnClick');
				$this->intercept($button_file_download, 'onclick', 'fileDownloadOnClick');
			}

			if ($mime_type != 'image') {
				return $this->getAsFile();
			}

			if ($this->isEnabled()) {
				$this->buttons->button_file_delete->enable();
			} else {
				$this->buttons->button_file_delete->disable();
			}

			$src = P4A_UPLOADS_URL . $this->getNewValue(1);

			$width = $this->getNewValue(4);
			$str_width = '';
			$height = $this->getNewValue(5);
			$str_height = '';

			if ($width > $height) {
				if ($this->max_thumbnail_size !== null and $width > $this->max_thumbnail_size) {
					$width = $this->max_thumbnail_size ;
					$str_width = "width=\"$width\"" ;
				}
			} else {
				if ($this->max_thumbnail_size !== null and $height > $this->max_thumbnail_size) {
					$height = $this->max_thumbnail_size ;
					$str_height = "height=\"$height\"" ;
				}
			}

			$sReturn  = '<table class="border_box" id="' . $this->getId() . '">' ;
			if (P4A_GD) {
				$sReturn .= '<tr><td colspan="2" align="center"><img class="image" alt="' . __('Preview') . '" src="' . P4A_ROOT_PATH . '/p4a/libraries/phpthumb/phpThumb.php?src=' . $src . '&amp;w=' . $width . '&amp;h=' . $height . '" ' . $str_width . ' ' . $str_height . ' /></td></tr>';
			} else {
				$sReturn .= '<tr><td colspan="2" align="center"><img class="image" alt="' . __('Preview') . '" src="' . $src . '" ' . $str_width . ' ' . $str_height . ' /></td></tr>';
			}
			$sReturn .= '<tr><th align="left">' . __('Name') . ':&nbsp;&nbsp;</th><td align="left">' . $this->getNewValue(0) . '</td></tr>';
			$sReturn .= '<tr><th align="left">' . __('Size') . ':&nbsp;&nbsp;</th><td align="left">' . $p4a->i18n->format($this->getNewValue(2)/1024, "decimal") . ' KB</td></tr>';
			$sReturn .= '<tr><th align="left">' . __('Type') . ':&nbsp;&nbsp;</th><td align="left">' . $this->getNewValue(3) . '</td></tr>';
			$sReturn .= '<tr><td colspan="2" align="center">' . $this->buttons->button_file_preview->getAsString() . ' '. $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
			$sReturn .= '</table>' ;
		}

		return $this->composeLabel() . $sReturn;
	}

	/**
	 * Sets the maximum size for image thumbnails.
	 * @param integer		Max size (width and height)
	 * @access public
	 */
	function setMaxThumbnailSize($size)
	{
		$this->max_thumbnail_size = $size;
	}

	/**
	 * Removes the maximum size for image thumbnails.
	 * @access public
	 */
	function unsetMaxThumbnailSize()
	{
		$this->max_thumbnail_size = null;
	}

	/**
	 * Returns the maximum size for image thumbnails.
	 * @return integer
	 * @access public
	 */
	function getMaxThumbnailSize()
	{
		return $this->max_thumbnail_size;
	}

	function getAsColor()
	{
		$p4a =& p4a::singleton();
		$id = $this->getId();

		if ($this->isEnabled()) {
			$enabled = "";
		} else {
			$enabled = " disabled='disabled' ";
		}

		$return  = $this->getAsText();
		if (!$p4a->isHandheld()) {
			$return .= "<input type='button' value='...' id='{$id}button' class='border_box font4 no_print' $enabled onclick='toggleColorPicker(\"$id\")' />";
		}

		return $return;
	}

	/**
	 * Sets the label for the field.
	 * In rendering phase it will be added with ':  '.
	 * @param string	The string to set as label.
	 * @access public
	 * @see P4A_Widget::$label
	 */
	function setLabel($value)
	{
		$this->label->setLabel($value);
	}

	/**
	 * Returns the label for the field.
	 * @return string
	 * @access public
	 */
	function getLabel()
	{
		return $this->label->getLabel();
	}

	/**
	 * Gets the HTML rendered field's label.
	 * @return string
	 * @access public
	 */
	function composeLabel()
	{
		return $this->label->getAsString();
	}

	/**
	 * Sets the alignment property for the field.
	 * @access public
	 * @param string		The align property.
	 */
	function setAlign($align)
	{
		$this->align = $align;
	}

	/**
	 * Composes a string containing all the HTML properties of the widget.
	 * Note: it will also contain the name and the value.
	 * @return string
	 * @access public
	 */
	function composeStringProperties()
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
	 * @access public
	 */
	function composeStringValue()
	{
		$value = $this->getNewValue();
		if (is_array($value)) {
			$value = join($value, ",");
		}

		switch ($this->type) {
			case 'color':
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

	function enableUpload($enable = true)
	{
		$this->upload = $enable;
	}

	function isUploadEnabled()
	{
		return $this->upload;
	}

	/**
	 * sets the rich textarea theme (Basic|Default|Full)
	 * @access public
	 */
	function setRichTextareaTheme($theme)
	{
		$this->rich_textarea_theme = $theme;
	}

	/**
	 * DOESN'T WORK WITH FCK
	 * @access public
	 * @return string
	 */
	function getRichTextareaTheme()
	{
		return $this->rich_textarea_theme;
	}

	/**
	 * sets buttons for every richtextarea toolbar
	 * @access public
	 * @param integer the toolbar index (1|2|3)
	 * @param string buttons in a comma separated list
	 */
	function setRichTextareaToolbar($index, $buttons)
	{
		$this->rich_textarea_toolbars[$index] = $buttons;
	}

	/**
	 * @access public
	 * @return string
	 */
	function getRichTextareaToolbar($index)
	{
		return $this->rich_textarea_toolbars[$index];
	}

	/**
	 * returns all toolbars buttons
	 * @access public
	 * @return array
	 */
	function getRichTextareaToolbars()
	{
		return $this->rich_textarea_toolbars;
	}
}