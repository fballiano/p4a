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
		 * The mask's data source.
		 * @var mixed
		 * @access public
		 */
		var $data = null;

       	/**
		 * The mask's data browser.
		 * @var DATA_BROWSER
		 * @access public
		 */
		var $data_browser = null;

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
		var $title = null;

       	/**
		 * Stores opening code for form.
		 * @var string
		 * @access private
		 */
		var $sOpen = null;

		/**
		 * The object with active focus
		 * @var object
		 * @access private
		 */
		var $focus_object = null;

		/**
		 * Currently used template name.
		 * @var string
		 * @access private
		 */
		var $template_name = null;
		
		/**
		 * CSS container.
		 * @var array
		 * @access private
		 */
		var $_css = array();

		/**
		 * Temporary javascript container.
		 * These javascripts are rendered and removed
		 * @var array
		 * @access private
		 */
		var $_temp_javascript = array();
		
		/**
		 * Temporary CSS container.
		 * These CSS are rendered and removed
		 * @var array
		 * @access private
		 */
		var $_temp_css = array();
		
		/**
		 * Temporary variables container.
		 * These vars are usally in the templates, removed after main
		 * @var array
		 * @access private
		 */
		var $_temp_vars = array();

		/**
		 * javascript container.
		 * @var array
		 * @access private
		 */
		var $_javascript = array();
		
		/**
		 * variables used for templates
		 * @var array
		 * @access private
		 */
		var $_tpl_vars = array();

		/**
		 * Mask constructor.
		 * Generates unique ID for the object, istance a new
		 * {@link SHEET} for widget positioning and store
		 * itself into p4a application.
		 * @param string Object name (identifier).
		 */
		function p4a_mask($name = null)
		{
			if ($name == null) {
				$name = get_class($this);
			}
			
			$name = strtolower($name);
			parent::p4a_object($name, 'ma');

			//todo
			$this->build("p4a_collection", "fields");

			$this->title = ucwords(str_replace('_', ' ', $this->getName())) ;
			$this->useTemplate('default');
		}

		//todo
		function &singleton($name)
		{
			$name = strtolower($name);
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
		 * Shows the caller mask.
		 * @access public
		 */
		function showPrevMask()
		{
			$p4a =& P4A::singleton();
			$p4a->showPrevMask();
		}

		/**
		 * Get the caller mask.
		 * @access public
		 */
		function &getPrevMask()
		{
			$p4a =& P4A::singleton();
			return $p4a->getPrevMask();
		}

		/**
		 * Tells the mask that we're going to use a template.
		 * @param string	"template name" stands for "template name.tpl" in the "CURRENT THEME\masks\" directory.
		 * @access public
		 */
		function useTemplate($template_name)
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
		 * Returns the currently used template name.
		 * @access public
		 * @return string
		 */
		function getTemplateName()
		{
			return $this->template_name;
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
			$this->_tpl_vars[$variable] =& $object;
		}

		 /**
		 * Tells the template engine to show a strng as a variable.
		 * @param string	Variable name, stands for a template variable.
		 * @param mixed		String, the value of the assignment.
		 * @access public
		 */
		function displayText($variable, $text)
		{
			$this->_tpl_vars[$variable] = $text;
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
		function main()
		{
			$p4a =& P4A::singleton();
			$charset = $p4a->i18n->getCharset();
			header("Content-Type: text/html; charset={$charset}");
			
			$tpl_container = (object)'';
			$tpl_container->charset = $charset;
			$tpl_container->title = $this->getTitle();
			$tpl_container->theme_path = P4A_THEME_PATH;
			$tpl_container->application_title = $p4a->getTitle();
			$tpl_container->mask_open = $this->maskOpen();
			$tpl_container->mask_close = $this->maskClose();
			
			if (is_object($this->focus_object)) {
				$tpl_container->focus_id = $this->focus_object->getId();
			}
			
			foreach ($this->_tpl_vars as $k=>$v) {
				if (is_object($v)) {
					$tpl_container->$k = $v->getAsString();
				} else {
					$tpl_container->$k = $v;
				}
			}
			
			foreach ($this->_temp_vars as $k=>$v) {
				$tpl_container->$k = $v;
			}
			
			$tpl_container->javascript = array_merge($p4a->_javascript, $this->_javascript, $this->_temp_javascript);
			$tpl_container->css = array_merge_recursive($p4a->_css, $this->_css, $this->_temp_css);

			$template = $this->getTemplateName();
			print P4A_Template_Engine::getAsString($tpl_container, "masks/{$template}/{$template}.tpl");
			
			$this->clearTempCSS();
			$this->clearTempJavascript();
			$this->clearTempVars();
		}

		/**
		 * Removes every template variable assigned.
		 * @access public
		 */
		function clearTemplateVars()
		{
			$this->_tpl_vars = array();
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
			$this->data->row($num_row);
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
		function saveRow()
		{
				$p4a =& P4A::singleton();

				// FILE UPLOADS
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

				$this->data->saveRow();
		}

		/**
		 * Goes in "new row" modality.
		 * This means that we prepare p4a for adding a new record
		 * to the data source wich is associated to the mask.
		 * @access public
		 */
		function newRow()
		{
    		$this->data->newRow();
		}

		/**
		 * Deletes the currently pointed record.
		 * @access public
		 */
		function deleteRow()
		{
    		$this->data->deleteRow();
		}

		/**
		 * Moves to the next row.
		 * @access public
		 */
		function nextRow()
		{
			$this->data->nextRow();
		}

		/**
		 * Moves to the previous row.
		 * @access public
		 */
		function prevRow()
		{
			$this->data->prevRow();
		}

		/**
		 * Moves to the last row.
		 * @access public
		 */
		function lastRow()
		{
			$this->data->lastRow();
		}

		/**
		 * Moves to the first row.
		 * @access public
		 */
		function firstRow()
		{
			$this->data->firstRow();
		}


		/**
		 * Returns the opening code for the mask.
		 * @return string
		 * @access public
		 */
		function maskOpen()
		{
			$p4a =& p4a::singleton();
			
			$return  = "<form method='post' enctype='multipart/form-data' id='p4a' action='index.php'>\n";
			$return .= "<div>\n";
			$return .= "<div id='p4a_loading'><img src='/p4a/icons/default/loading.gif' alt='' /> Loading... </div>\n";
			$return .= "<input type='hidden' name='_object' value='" . $this->getId() . "' />\n";
			$return .= "<input type='hidden' name='_action' value='none' />\n";
			$return .= "<input type='hidden' name='_ajax' value='0' />\n";
			$return .= "<input type='hidden' name='_action_id' value='" . $p4a->getActionHistoryId() . "' />\n";
			$return .= "<input type='hidden' name='param1' />\n";
			$return .= "<input type='hidden' name='param2' />\n";
			$return .= "<input type='hidden' name='param3' />\n";
			$return .= "<input type='hidden' name='param4' />\n";

			return $return;
		}

		/**
		 * Returns the closing code for the mask.
		 * @return string
		 * @access public
		 */
		function maskClose()
		{
			return "</div>\n</form>";
		}

		/**
		 * Does nothing.
		 * @access public
		 */
		function none()
		{
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
		 * @access public
		 */
		function dropCss($uri)
		{
			if(isset($this->_css[$uri]) and isset($this->_css[$uri][$media])){
				unset($this->_css[$uri][$media]);
				if (empty($this->_css[$uri])) {
					unset($this->_css);
				}
			}
		}
		
		/**
		 * Include CSS
		 * These CSS are removed after rendering
		 * @param string		The URI of CSS.
		 * @param string		The CSS media.
		 * @access public
		 */
		function addTempCss($uri, $media = "screen")
		{
			if (!isset($this->_temp_css[$uri])) {
				$this->_temp_css[$uri] = array();
			}
			$this->_temp_css[$uri][$media] = null;
		}

		/**
		 * Drop inclusion of CSS file
		 * These CSS are removed after rendering
		 * @param string		The URI of CSS.
		 * @access public
		 */
		function dropTempCss($uri)
		{
			if(isset($this->_temp_css[$uri]) and isset($this->_temp_css[$uri][$media])){
				unset($this->_temp_css[$uri][$media]);
				if (empty($this->_temp_css[$uri])) {
					unset($this->_temp_css);
				}
			}
		}
		
		/**
		 * Clear temporary CSS list
		 * @access public
		 */
		function clearTempCss()
		{
			$this->_temp_css = array();
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
		
		/**
		 * Include a javascript file
		 * These javascripts are removed after rendering
		 * @param string		The URI of file.
		 * @access public
		 */
		function addTempJavascript($uri)
		{
			$this->_temp_javascript[$uri] = null;
		}

		/**
		 * Drop inclusion of javascript file
		 * These javascripts are removed after rendering
		 * @param string		The URI of CSS.
		 * @access public
		 */
		function dropTempJavascript($uri)
		{
			if(isset($this->_temp_javascript[$uri])){
				unset($this->_temp_javascript[$uri]);
			}
		}
		
		/**
		 * Clear temporary javascript list
		 * @access public
		 */
		function clearTempJavascript()
		{
			$this->_temp_javascript = array();
		}
		
		/**
		 * Add a temporary variable
		 * @param string		The URI of file.
		 * @access public
		 */
		function addTempVar($name, $value)
		{
			$this->_temp_vars[$name] = $value;
		}

		/**
		 * Drop a temporary variable
		 * @param string		The URI of CSS.
		 * @access public
		 */
		function dropTempVar($name)
		{
			if(isset($this->_temp_vars[$name])){
				unset($this->_temp_vars[$name]);
			}
		}
		
		/**
		 * Clear temporary vars list
		 * @access public
		 */
		function clearTempVars()
		{
			$this->_temp_vars = array();
		}
	}
?>
