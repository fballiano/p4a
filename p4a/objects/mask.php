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
	 * The mask is the basic interface object wich contains all widgets and generically every displayed object.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_MASK extends P4A_OBJECT
	{
		/**
		 * The mask that called the current mask.
		 * @var mask
		 * @access public
		 */
       	var $prev_mask = NULL;

       	/**
		 * The mask's data source.
		 * @var mixed
		 * @access public
		 */
		var $data = NULL;

       	/**
		 * The mask's data browser.
		 * @var DATA_BROWSER
		 * @access public
		 */
		var $data_browser = NULL;

       	/**
		 * The fields collection
		 * @var array
		 * @access public
		 */
		var $fields = null;

		/**
		 * Store the external fields' object_id
		 * @var array
		 * @access private
		 */
		var $external_fields = array();

       	/**
		 * Keeps the association between actions events and actions.
		 * @var array
		 * @access public
		 */
		var $map_actions = array();

       	/**
		 * Mask's title.
		 * @var string
		 * @access private
		 */
		var $title = NULL;

       	/**
		 * Template engine object.
		 * @var object
		 * @access private
		 */
		var $smarty = NULL;

       	/**
		 * Stores opening code for form.
		 * @var string
		 * @access private
		 */
		var $sOpen = NULL;

		/**
		 * The object with active focus
		 * @var object
		 * @access private
		 */
		var $focus_object = NULL;

		/**
		 * Currently used template name.
		 * @var string
		 * @access private
		 */
		var $template_name = NULL;

		/**
		 * Mask constructor.
		 * Generates unique ID for the object, istance a new
		 * {@link SHEET} for widget positioning and store
		 * itself into p4a application.
		 * @param string Object name (identifier).
		 */
		function &p4a_mask($name = null)
		{
			//todo controllare
			if ($name==null){
				$name = get_class($this);
			}
			parent::p4a_object($name, 'ma');

			//todo
			$this->build("p4a_collection", "fields");

            $this->title = ucwords(str_replace('_', ' ', $this->getName())) ;
			$this->useTemplate('default');
		}

		//todo
		function &singleton($name)
		{
			$p4a =& P4A::singleton();

 			if (!isset($p4a->masks->$name)) {
				$p4a->masks->build($name, $name);
			}
			return $p4a->masks->$name;
		}

		/**
		 * Sets the focus on object
		 * @access public
		 * @param object
		 */
		function setFocus(&$object)
		{
			$this->focus_object =& $object;
		}

		/**
		 * Removes focus property
		 * @access public
		 */
		function unsetFocus()
		{
			unset( $this->focus_object );
			$this->focus_object = NULL;
		}

		/**
		 * Inizializes the mask.
		 * It means that the 'init' function of the current mask's listener is called.
		 * @access private
		 */
		function init()
		{
			$this->actionHandler('init');
		}

		/**
		 * Shows the mask.
		 * It means that the 'show' function of the current mask's listener is called.
		 * @access private
		 */
		function show()
		{
			return $this->actionHandler('show');
		}

		/**
		 * Shows the caller mask.
		 * @access public
		 */
		function showPrevMask()
		{
			$p4a =& P4A::singleton();
			$p4a->showPrevMask();
		}

		/**
		 * Tells the mask that we're going to use a template.
		 * @param string	"template name" stands for "template name.tpl" in the "CURRENT THEME\masks\" directory.
		 * @access public
		 */
		function useTemplate($template_name)
		{
			$this->use_template = TRUE;
			$this->template_name = $template_name;

			// If smarty is not yes istanced, than we call it.
			if (! is_object($this->smarty)){
				$this->smarty = new SMARTY();

				$this->smarty->compile_dir = P4A_SMARTY_MASK_COMPILE_DIR;
				$this->smarty->left_delimiter = P4A_SMARTY_LEFT_DELIMITER;
				$this->smarty->right_delimiter = P4A_SMARTY_RIGHT_DELIMITER;
				$this->displayText('theme_path', P4A_THEME_PATH);
				$this->displayText('mask_open', $this->maskOpen());
				$this->displayText('mask_close', $this->maskClose());
			}

			if (file_exists(P4A_SMARTY_MASK_TEMPLATES_DIR . '/' . $this->template_name)) {
				$this->smarty->template_dir = P4A_SMARTY_MASK_TEMPLATES_DIR;
				$this->displayText('tpl_path', P4A_SMARTY_MASK_TEMPLATES_PATH . '/' . $this->template_name);
				$this->displayText('base_url', P4A_SERVER_URL . P4A_SMARTY_MASK_TEMPLATES_PATH . '/' . $this->template_name . '/');
			}else{
				$this->smarty->template_dir = P4A_SMARTY_DEFAULT_MASK_TEMPLATES_DIR;
				$this->displayText('tpl_path', P4A_SMARTY_DEFAULT_MASK_TEMPLATES_PATH . '/' . $this->template_name);
				$this->displayText('base_url', P4A_SERVER_URL . P4A_SMARTY_DEFAULT_MASK_TEMPLATES_PATH . '/' . $this->template_name . '/');
			}
		}

		/**
		 * Returns the currently used template name.
		 * @access public
		 * @return string
		 */
		function getTemplateName()
		{
			return $this->template_name;
		}

		/**
		 * Tells the object that we'll not use a template.
		 * @access public
		 */
		function noUseTemplate()
		{
			$this->use_template = FALSE;
			$this->template_name = NULL;
		}

		/**
		 * Tells the template engine to show an object as a variable.
		 * $object will be shown in the $variable template zone.
		 * @param string	Variable name, stands for a template zone.
		 * @param mixed		Widget or string, the value of the assignment.
		 * @access public
		 */
		function display($variable, &$object)
		{
			unset($this->smarty_var[$variable]);
			$this->smarty_var[$variable] =& $object;
		}

		 /**
		 * Tells the template engine to show a strng as a variable.
		 * @param string	Variable name, stands for a template variable.
		 * @param mixed		String, the value of the assignment.
		 * @access public
		 */
		function displayText($variable, $text)
		{
			$this->smarty_var[$variable] = $text;
		}

		/**
		 * Sets the title for the mask.
		 * @param string	Mask title.
		 * @access public
		 */
		function setTitle( $title )
		{
			$this->title = $title ;
		}

		/**
		 * Returns the title for the mask.
		 * @return string
		 * @access public
		 */
		function getTitle()
		{
			return $this->title ;
		}

		/**
		 * Prints out the mask.
		 * @access public
		 */
		function raise()
		{
			$p4a =& P4A::singleton();
			$charset = $p4a->i18n->getCharset();
			header("Content-Type: text/html; charset={$charset}");

			$this->smarty->assign('charset', $charset);
			$this->smarty->assign('title', $this->title);
			$this->smarty->assign('css', $p4a->css);

			if(isset($this->focus_object) and is_object($this->focus_object)){
 				$this->smarty->assign('focus_id', $this->focus_object->getID());
			}

			$this->smarty->assign('application_title', $p4a->getTitle());

			foreach($this->smarty_var as $key=>$value)
			{
				if (is_object($value)){
					$value = $value->getAsString();
				}

				$this->smarty->assign($key, $value);
			}

			$path_template = $this->template_name . '/' . $this->template_name . '.' . P4A_SMARTY_TEMPLATE_EXSTENSION;
			$this->smarty->display($path_template);
		}

		/**
		 * Removes every template variable assigned.
		 * @access public
		 */
		function clearDisplay()
		{
			$this->smarty_var = array();
			$this->smarty->clear_all_assign();
			unset($this->smarty);
			$this->useTemplate($this->template_name);
		}

        /**
		 * Add a multivalue external field to mask
		 * @access public
		 */
        function addMultivalueField($fieldname)
        {
			$this->fields->build("p4a_multivalue_field", $fieldname);
        	$this->external_fields[] = $this->fields->$fieldname->getID();

        	$pk_value = $this->fields->{$this->data->pk}->getNewValue();
			$this->fields->$fieldname->setPkValue($pk_value);
        }

		/**
		 * Associates a data source with the mask.
		 * Also set the data structure to allow correct widget rendering.
		 * Also moves to the first row of the data source.
		 * @param data_source
		 * @access public
		 */
		function setSource(&$data_source)
		{
			$this->data =& $data_source;

			while($field =& $this->data->fields->nextItem()) {
				$field_name = $field->getName();
				$this->fields->build("P4A_Field",$field_name, false);
				$this->fields->$field_name->setDataField($field);
			}
		}

		/**
		 * Loads the current record data.
		 * @param integer		The wanted row number.
		 * @access public
		 */
		function loadRow($num_row = NULL)
		{
			$p4a =& P4A::singleton();
			if( $this->actionHandler( 'beforeLoadRow' ) == ABORT ) return ABORT;

			if( $this->isActionTriggered( 'onLoadRow' ) )
			{
				if( $this->actionHandler( 'onLoadRow' ) == ABORT ) return ABORT;
			}
			else
			{
				$this->data->loadRow($num_row);

				foreach($this->external_fields as $object_id){
					$pk_value = $this->fields->{$this->data->pk}->getNewValue();
					$p4a->objects[$object_id]->setPkValue($pk_value);
					$p4a->objects[$object_id]->load();
				}

			}

            $this->actionHandler( 'afterLoadRow' ) ;
		}

		/**
		 * Reloads data for the current record.
		 * @access public
		 */
		function reloadRow()
		{
			if ($this->data->isNew()) {
				$this->lastRow();
			} else {
				$this->data->row();
			}
		}

		/**
		 * Overwrites internal data with the data arriving from the submitted mask.
		 * @access public
		 * @throws onFileSystemError
		 */
		function updateRow()
		{
			if ($this->actionHandler('beforeUpdateRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onUpdateRow')) {
				if($this->actionHandler('onUpdateRow') == ABORT) return ABORT;
			} else {
				$p4a =& P4A::singleton();

				// FILE UPLOADS
				while ($field =& $this->fields->nextItem()) {
					$field_type = $field->getType();
					if ($field_type=='file' or $field_type=='image') {
						$new_value  = $field->getNewValue();
						$old_value  = $field->getValue();
						$target_dir = P4A_UPLOADS_DIR . '/' . $field->getUploadSubpath();

						if (!is_dir($target_dir)) {
							if (!System::mkDir("-p $target_dir")) {
								$e = new P4A_ERROR("Cannot create directory \"$target_dir\"", $this, $rs);
    							if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
    								die();
    							}
							}
						}

						$a_new_value = explode(',', substr($new_value, 1, -1 ));
						$a_old_value = explode(',', substr($old_value, 1, -1 ));

						if ($old_value === NULL) {
							if ($new_value !== NULL) {
								$a_new_value[0] = get_unique_file_name( $a_new_value[0], $target_dir );
                    			$new_path = $target_dir . '/' . $a_new_value[0];
								$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
								if (!rename($old_path, $new_path)) {
									$e = new P4A_ERROR("Cannot rename file \"$old_path\" to \"$new_path\"", $this, $rs);
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
								unlink($target_dir . '/' . $a_old_value[1]);
								$field->setNewValue(NULL);
							} elseif ($new_value!=$old_value) {
								$path = $target_dir . '/' . $a_old_value[1];
								if (!unlink($path)) {
									$e = new P4A_ERROR("Cannot delete file \"$path\"", $this, $rs);
									if ($this->errorHandler('onFileSystemError', $e) !== PROCEED) {
										die();
									}
								}
								$a_new_value[0] = get_unique_file_name($a_new_value[0], $target_dir);
								$new_path = $target_dir . '/' . $a_new_value[0];
								$old_path = P4A_UPLOADS_DIR . '/' . $a_new_value[1];
								if (!rename($old_path, $new_path)) {
									$e = new P4A_ERROR("Cannot rename file \"$old_path\" to \"$new_path\"", $this, $rs);
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

				// EXECUTE THE DATA BROWSER COMMIT
				$this->data->saveRow();

				// EXTERNAL FIELDS
				/*
				foreach($this->external_fields as $object_id) {
					$p4a->objects->$object_id->update();
				}
				*/
			}

			$this->actionHandler('afterUpdateRow');
		}

		/**
		 * Goes in "new row" modality.
		 * This means that we prepare p4a for adding a new record
		 * to the data source wich is associated to the mask.
		 * @access public
		 */
		function newRow()
		{
			$p4a =& P4A::singleton();
			if ($this->actionHandler('beforeNewRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onNewRow')) {
				if( $this->actionHandler( 'onNewRow' ) == ABORT ) return ABORT;
			} else {
    			$this->data->newRow();
				/*
				foreach($this->external_fields as $object_id){
					$pk_value = $this->fields[$this->data->pk]->getNewValue();
					$p4a->objects[$object_id]->setPkValue($pk_value);
					$p4a->objects[$object_id]->load();
				}
				*/
			}

			$this->actionHandler('afterNewRow');
		}

		/**
		 * Deletes the currently pointed record.
		 * @access public
		 */
		function deleteRow()
		{
			$p4a =& P4A::singleton();
			if ($this->actionHandler('beforeDeleteRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onDeleteRow')) {
				if ($this->actionHandler('onDeleteRow') == ABORT) return ABORT;
			} else {
				// EXTERNAL FIELDS
				/*
				foreach($this->external_fields as $object_id){
					$p4a->objects[$object_id]->setNewValue();
					$p4a->objects[$object_id]->update();
				}*/

    			$this->data->deleteRow();
			}

			$this->actionHandler('afterDeleteRow') ;
		}

		/**
		 * Moves to the next row.
		 * @access public
		 */
		function nextRow()
		{
			if ($this->actionHandler('beforeMoveRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onMoveRow')) {
				if ($this->actionHandler('onMoveRow') == ABORT) return ABORT;
			} else {
    			$this->data->nextRow();
			}

			$this->actionHandler('afterMoveRow');
		}

		/**
		 * Moves to the previous row.
		 * @access public
		 */
		function prevRow()
		{
			if ($this->actionHandler('beforeMoveRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onMoveRow')) {
				if ($this->actionHandler('onMoveRow') == ABORT) return ABORT;
			} else {
    			$this->data->prevRow();
			}

			$this->actionHandler('afterMoveRow');
		}

		/**
		 * Moves to the last row.
		 * @access public
		 */
		function lastRow()
		{
			if ($this->actionHandler('beforeMoveRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onMoveRow')) {
				if ($this->actionHandler('onMoveRow') == ABORT) return ABORT;
			} else {
    			$this->data->lastRow();
			}

			$this->actionHandler('afterMoveRow');
		}

		/**
		 * Moves to the first row.
		 * @access public
		 */
		function firstRow()
		{
			if ($this->actionHandler('beforeMoveRow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onMoveRow'))
			{
				if ($this->actionHandler('onMoveRow') == ABORT) return ABORT;
			} else {
    			$this->data->firstRow();
			}

			$this->actionHandler('afterMoveRow');
		}


		/**
		 * Returns the opening code for the mask.
		 * @return string
		 * @access public
		 */
		function maskOpen()
		{
			$this->sOpen  = '';
			$this->sOpen .= '<SCRIPT LANGUAGE="JavaScript1.2">'													. "\n";
			$this->sOpen .= 'function executeEvent(object_name, action_name, param1, param2, param3, param4)'	. "\n";
			$this->sOpen .= '{'																					. "\n";
            $this->sOpen .= '	if (!param1) param1 = "" ;'														. "\n";
            $this->sOpen .= '	if (!param2) param2 = "" ;'														. "\n";
            $this->sOpen .= '	if (!param3) param3 = "" ;'														. "\n";
            $this->sOpen .= '	if (!param4) param4 = "" ;'														. "\n";
			$this->sOpen .= ''																					. "\n";
			$this->sOpen .= '	document.forms["'. $this->getName() .'"]._object.value = object_name;'				. "\n";
			$this->sOpen .= '	document.forms["'. $this->getName() .'"]._action.value = action_name;'				. "\n";
            $this->sOpen .= '	document.forms["'. $this->getName() .'"].param1.value = param1;'						. "\n";
            $this->sOpen .= '	document.forms["'. $this->getName() .'"].param2.value = param2;'						. "\n";
            $this->sOpen .= '	document.forms["'. $this->getName() .'"].param3.value = param3;'						. "\n";
            $this->sOpen .= '	document.forms["'. $this->getName() .'"].param4.value = param4;'						. "\n";
			$this->sOpen .= '	if (typeof document.forms["'. $this->getName() .'"].onsubmit == "function") {'		. "\n";
			$this->sOpen .= '		document.forms["'. $this->getName() .'"].onsubmit();'							. "\n";
			$this->sOpen .= '	}'																				. "\n";
			$this->sOpen .= '	document.forms["'. $this->getName() .'"].submit();'									. "\n";
			$this->sOpen .= '}'																					. "\n";
			$this->sOpen .= ''																					. "\n";
			$this->sOpen .= 'function isReturnPressed(e)'														. "\n";
			$this->sOpen .= '{'																					. "\n";
			$this->sOpen .= '	var characterCode;'																. "\n";
            $this->sOpen .= ''																					. "\n";
            $this->sOpen .= '	if(e && e.which) {'																. "\n";
			$this->sOpen .= '		e = e; characterCode = e.which;'											. "\n";
			$this->sOpen .= '	} else {'																		. "\n";
			$this->sOpen .= '		e = event; characterCode = e.keyCode;'										. "\n";
            $this->sOpen .= '	}'																				. "\n";
            $this->sOpen .= ''																					. "\n";
            $this->sOpen .= '	if(characterCode == 13) {'														. "\n";
            $this->sOpen .= '		return true;'																. "\n";
			$this->sOpen .= '	} else {'																		. "\n";
			$this->sOpen .= '		return false;'																. "\n";
			$this->sOpen .= '	}'																				. "\n";
			$this->sOpen .= '}'																					. "\n";
			$this->sOpen .= ''																					. "\n";
			$this->sOpen .= 'function setFocus(id)'															. "\n";
			$this->sOpen .= '{'																					. "\n";
			$this->sOpen .= '	if( (id != null) && (document.forms["'. $this->getName() .'"].elements[id] != null) && (document.forms["'. $this->getName() .'"].elements[id].disabled == false) ) {' . "\n";
			$this->sOpen .= '		document.forms["'. $this->getName() .'"].elements[id].focus();'					. "\n";
			$this->sOpen .= '	}'																				. "\n";
			$this->sOpen .= '}'																					. "\n";
			$this->sOpen .= ''																					. "\n";
			$this->sOpen .= '</SCRIPT>'																			. "\n";
			$this->sOpen .= '<FORM method="post" enctype="multipart/form-data" name="' . $this->getName() . '" action="index.php">';
			$this->sOpen .= "<INPUT TYPE='hidden' name='_object' value='" . $this->getId() . "'>" . "\n";
			$this->sOpen .= "<INPUT TYPE='hidden' name='_action' value='none'>" . "\n";
            $this->sOpen .= "<INPUT TYPE='hidden' name='param1'>" . "\n";
            $this->sOpen .= "<INPUT TYPE='hidden' name='param2'>" . "\n";
            $this->sOpen .= "<INPUT TYPE='hidden' name='param3'>" . "\n";
            $this->sOpen .= "<INPUT TYPE='hidden' name='param4'>" . "\n";

			return $this->sOpen;
		}

		/**
		 * Returns the closing code for the mask.
		 * @return string
		 * @access public
		 */
		function maskClose()
		{
			$this->sClose = "</FORM>";
			return $this->sClose;
		}

		/**
		 * Does nothing.
		 * @access public
		 */
		function none()
		{
		}
	}
?>
