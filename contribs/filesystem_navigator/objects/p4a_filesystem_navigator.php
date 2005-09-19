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
 * To contact the authors write to:								<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)											<br>
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
 * @package P4A_Filesystem_Navigator
 */

/**
 * This widget allows a tree navigation within filesystem.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Filesystem_Navigator
 */
class P4A_Filesystem_Navigator extends P4A_Frame
{
	/**
	 * @var P4A_Filesystem_Navigator_Folders
	 * access public
	 */
	var $folders = null;
	
	/**
	 * @var P4A_Fieldset
	 * access public
	 */
	var $f_folders = null;
	
	/**
	 * @var P4A_Filesystem_Navigator_Files
	 * access public
	 */
	var $files = null;
	
	/**
	 * @var P4A_Fieldset
	 * access public
	 */
	var $f_files = null;
	
	/**
	 * The constructor.
	 * @param string		The name of the widget
	 * @access public
	 */
	function P4A_Filesystem_Navigator($name)
	{
		parent::P4A_Frame($name);
		$this->build("P4A_Filesystem_Navigator_Folders", "folders");
		$this->build("P4A_Filesystem_Navigator_Files", "files");
		
		$this->build("p4a_fieldset", "f_folders");
		$this->f_folders->anchor($this->folders);
		$this->f_folders->anchor($this->folders->message);
		$this->f_folders->anchor($this->folders->create_folder_field);
		$this->f_folders->anchorLeft($this->folders->create_folder_button);
		
		$this->build("p4a_fieldset", "f_files");
		$this->f_files->anchor($this->files);
		$this->f_files->anchor($this->files->message);
		$this->f_files->anchor($this->files->upload_field);
		
		$this->anchor($this->f_folders);
		$this->anchorLeft($this->f_files);
	}

	/**
	 * Renders the widget's HTML and returns it.
	 * @access public
	 * @return string
	 */
	function getAsString()
	{
		$p4a =& p4a::singleton();
		$p4a->active_mask->addTempCSS(P4A_APPLICATION_PATH . "/p4a_filesystem_navigator.css");
		
		return parent::getAsString();
	}
}

/**
 * This widget prints the tree of folders in the base folder.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Filesystem_Navigator
 */
class P4A_Filesystem_Navigator_Folders extends P4A_Widget
{
	/**
	 * The base folder for exploration
	 * @var string
	 * @access private
	 */
	var $_base = P4A_UPLOADS_DIR;
	
	/**
	 * The base path for previewing
	 * @var string
	 * @access private
	 */
	var $_base_path = P4A_UPLOADS_PATH;
	
	/**
	 * The currently selected folder.
	 * @var string
	 * @access private
	 */
	var $_current = "";
	
	/**
	 * Expand whole tree or collapse?
	 * @var boolean
	 * @access private
	 */
	var $expand_all = true;
	
	/**
	 * The P4A_Field used to type in the folder name.
	 * @var P4A_Field
	 * @access public
	 */
	var $create_folder_field = null;
	
	/**
	 * The P4A_Button used to create a folder
	 * @var P4A_Button
	 * @access public
	 */
	var $create_folder_button = null;
	
	/**
	 * The P4A_Button used to delete a folder.
	 * @var P4A_Button
	 * @access public
	 */
	var $delete_folder_button = null;
	
	/*
	 * The P4A_Message used to print warnings.
	 * @var P4A_Message
	 * @access public
	 */
	var $message = null;
	
	/**
	 * The text printed when no folder are present.
	 * @var string
	 * @access public
	 */
	var $no_folders_message = "";

	/**
	 * The constructor.
	 * @param string		The name of the widget
	 * @access public
	 */
	function P4A_Filesystem_Navigator_Folders($name)
	{
		parent::P4A_Widget($name);
		$this->addAction("onClick");
		$this->intercept($this, "onClick", "onClick");
		
		$this->build("P4A_Field", "create_folder_field");
		$this->create_folder_field->label->setVisible(false);
		
		$this->build("P4A_Button", "create_folder_button");
		$this->intercept($this->create_folder_button, "onClick", "createFolder");
		
		$this->build("P4A_Button", "delete_folder_button");
		$this->delete_folder_button->requireConfirmation("onClick");
		$this->intercept($this->delete_folder_button, "onClick", "deleteFolder");
		
		$this->build("P4A_Message", "message");
	}
	
	/**
	 * Sets the base folder for the exploration.
	 * @param string		The folder absolute path.
	 * @access public
	 */
	function setBase($folder)
	{
		$this->_base = $folder;
	}
	
	/**
	 * Returns the widget's base folder.
	 * @return string
	 * @access public
	 */
	function getBase()
	{
		return $this->_base;
	}
	
	/**
	 * Sets the base path for previewing.
	 * @param string		The folder absolute path.
	 * @access public
	 */
	function setBasePath($path)
	{
		$this->_base_path = $path;
	}
	
	/**
	 * Returns the widget's base path.
	 * @return string
	 * @access public
	 */
	function getBasePath()
	{
		return $this->_base_path;
	}
	
	/**
	 * Returns the currently selected folder.
	 * @return string
	 * @access public
	 */
	function getCurrent()
	{
		return $this->_current;
	}
	
	/**
	 * Sets the currently selected folder.
	 * @param string		The folder path
	 * @access public
	 */
	function setCurrent($folder)
	{
		if ((strpos($folder, $this->getBase()) === 0) and is_dir($folder)) {
			$this->_current = $folder;
		}
	}
	
	/**
	 * Resets the currently selected folder pointer.
	 * @access public
	 */
	function resetCurrent()
	{
		$this->_current = "";
	}
	
	/**
	 * Returns true if the currently selected folder has folders inside itself.
	 * @return boolean
	 * @access public
	 */
	function hasFolders()
	{
		$base = $this->getBase();
		$handle = opendir($base);
		while (false !== ($file = readdir($handle))) {
			if (is_dir("$base/$file") and ($file != ".") and ($file != "..") and ($file != "CVS") and ("$base/$file" != P4A_UPLOADS_TMP_DIR)) {
				return true;
			}
		}
		closedir($handle);
		return false;
	}
	
	/**
	 * Sets if the tree is expanded or not.
	 * @param boolean		The value
	 * @access public
	 */
	function expandAll($value = true)
	{
		$this->expand_all = $value;
	}
	
	/**
	 * Sets if the tree is collapsed or not.
	 * @param boolean		The value
	 * @access public
	 */
	function collapse($value = true)
	{
		$this->expand_all = !$value;
	}
	
	/**
	 * Renders the widget's HTML and returns it.
	 * @access public
	 * @return string
	 */
	function getAsString($folder = "")
	{
		if (!$this->isVisible()) {
			return "";
		}
		
		$base = $this->getBase();
		$obj_id = $this->getId();
		$return = "";
		$class = "";
		$dirs = array();

		$handle = opendir("$base/$folder");
		while (false !== ($file = readdir($handle))) {
			$files[] = $file;
		}
		closedir($handle);
		natcasesort($files);
		
		$return .= "<ul class=\"p4a_filesystem_navigator\" style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/folder." . P4A_ICONS_EXTENSION . "')\">";
		foreach ($files as $file) {
			$full_path = str_replace("//", "/", "$base/$folder/$file");
			if (is_dir($full_path) and ($file != ".") and ($file != "..") and ($file != "CVS") and ($full_path != P4A_UPLOADS_TMP_DIR)) {
				if ($this->getCurrent() == $full_path) {
					$return .= "<li class='active_node' style='list-style-image:url(" . P4A_ICONS_PATH . "/16/folder_open." . P4A_ICONS_EXTENSION . ")'>{$file}";
				} else {
					$actions = $this->composeStringActions("$folder/$file");
					$return .= "<li><a href='#' $actions>{$file}</a>";
				}
				
				if ($this->expand_all) {
					$return .= $this->getAsString("$folder/$file");
				} else {
					if (strpos($this->getCurrent(), $full_path) !== false) {
						$return .= $this->getAsString("$folder/$file");
					}
				}
				$return .= "</li>\n";
			}
		}
		$return .= "</ul>";
		return $return;
	}
	
	/**
	 * Returns the list of files within the currently selected folder.
	 * @return array
	 * @access public
	 */
	function getFiles()
	{
		$return = array();
		$folder = $this->getCurrent();
		if (!empty($folder)) {
			$handle = opendir($folder);
			while (false !== ($file = readdir($handle))) {
				if (is_file("$folder/$file")) {
					$return[] = $file;
				}
			}
			closedir($handle);
		}

		natcasesort($return);
		return $return;
	}
	
	/**
	 * The onClick event interceptor.
	 * @param array		All params passed by the HTML form.
	 * @access private
	 */
	function onClick($params)
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$folder = $params[0];
		$this->setCurrent($this->getBase() . $folder);
		$parent->files->setCurrent(null);
	}
	
	/**
	 * Creates a folder.
	 * @access public
	 */
	function createFolder()
	{
		$folder = $this->create_folder_field->getNewValue();
		
		$current = $this->getCurrent();
		if (empty($current)) {
			$current = $this->getDirectory();
		}
		
		mkdir("$current/$folder");
		$this->create_folder_field->setNewValue(null);
	}
	
	/**
	 * Deletes the currently selected folder and all files.
	 * @access public
	 */
	function deleteFolder()
	{
		$folder = $this->getCurrent();
		System::rm(array("-r", $folder));
		$this->resetCurrent();
	}
}

/**
 * This widget prints the list of files in the current directory.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package P4A_Filesystem_Navigator
 */
class P4A_Filesystem_Navigator_Files extends P4A_Widget
{
	/**
	 * The currently selected file name.
	 * @var string
	 * @access public
	 */
	var $_current = "";
	
	/**
	 * The P4A_Message used to print out warnings.
	 * @var P4A_Message
	 * @access public
	 */
	var $message = null;
	
	/**
	 * The P4A_Field that uploads a file
	 * @var P4A_Field
	 * @access public
	 */
	var $upload_field = null;
	
	/**
	 * The P4A_Button that deletes a file.
	 * @var P4A_Button
	 * @access public
	 */
	var $delete_file_button = null;
	
	/**
	 * The text printed when there's no file.
	 * @var string
	 * access public
	 */
	var $no_files_message = "";
	
	/**
	 * Adds a preview button
	 * @var boolean
	 * @access private
	 */
	var $_preview = false;
	
	/**
	 * The constructor.
	 * @param string		The name of the widget
	 * @access public
	 */
	function P4A_Filesystem_Navigator_Files($name)
	{
		parent::P4A_Widget($name);
		$this->addAction("onClick");
		$this->intercept($this, "onClick", "onClick");
		
		$this->build("P4A_Message", "message");
		
		$this->build("P4A_Field", "upload_field");
		$this->upload_field->setType("file");
		$this->intercept($this->upload_field, "afterUpload", "afterUpload");
		
		$this->build("P4A_Button", "delete_file_button");
		$this->delete_file_button->requireConfirmation("onClick");
		$this->intercept($this->delete_file_button, "onClick", "deleteFile");
	}
	
	/**
	 * Adds a preview button
	 * @param boolean		Use the preview or not
	 * @access public
	 */
	function enablePreview($value = true)
	{
		$this->_preview = $value;
	}
	
	/**
	 * Returns the currently selected file name.
	 * @return string
	 * @access public
	 */
	function getCurrent()
	{
		return $this->_current;
	}
	
	/**
	 * Sets the currently selected file.
	 * @param string		The file name
	 * @access public
	 */
	function setCurrent($file)
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$folder = $parent->folders->getCurrent();
		
		if (file_exists("$folder/$file")) {
			$this->_current = $file;
		}
	}
	
	/**
	 * Resets the curretly selected file pointer.
	 * @access public
	 */
	function resetCurrent()
	{
		$this->_current = "";
	}
	
	/**
	 * Renders the widget's HTML and returns it.
	 * @access public
	 * @return string
	 */
	function getAsString()
	{
		if (!$this->isVisible()) {
			return "";
		}
		
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$files = $parent->folders->getFiles();
		$base_path = $parent->folders->getBasePath();
		$base_dir = $parent->folders->getBase();
		$current_dir = $parent->folders->getCurrent();
		$return = "";

		if (empty($files)) {
			$this->message->setValue($this->no_files_message);
		} else {
			$return .= "<ul class=\"p4a_filesystem_navigator\" style=\"list-style-image:url('" . P4A_ICONS_PATH . "/16/generic_file." . P4A_ICONS_EXTENSION . "')\">";
			$current = $this->getCurrent();
			
			foreach ($files as $file) {
				if ($this->_preview) {
					$dir = $base_path . str_replace($base_dir, "", $current_dir);
					$preview = " <a href='$dir/$file' target='_blank'>[preview]</a>";
				} else {
					$preview = "";
				}
				
				if ($file == $current) {
					$return .= "<li class='active_node'>{$file}{$preview}</li>";
				} else {
					$actions = $this->composeStringActions($file);
					$return .= "<li><a href='#' $actions>$file</a>$preview</li>";
				}
			}
			$return .= "</ul>";
		}
		
		return $return;
	}
	
	/**
	 * The onClick event interceptor.
	 * @param array		All params passed by the HTML form.
	 * @access private
	 */
	function onClick($params)
	{
		$file = $params[0];
		$this->setCurrent($file);
		$this->actionHandler("afterClick");
	}
	
	/**
	 * The afterUpload event interceptor.
	 * @param array		All params passed by the HTML form.
	 * @access private
	 */
	function afterUpload()
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$current = $parent->folders->getCurrent();
		$name = $this->upload_field->getNewValue(0);
		rename(P4A_UPLOADS_TMP_DIR . "/$name", "$current/$name");
		$this->upload_field->setNewValue(null);
	}
	
	/**
	 * Deletes the currently selected file.
	 * @access public
	 */
	function deleteFile()
	{
		$p4a =& p4a::singleton();
		$parent =& $p4a->getObject($this->getParentID());
		$file = $parent->folders->getCurrent() . "/" . $this->getCurrent();
		unlink($file);
		$this->resetCurrent();
	}
}

?>