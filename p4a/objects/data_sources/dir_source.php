<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 * 
 * To contact the authors write to:                                     <br />
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

/**
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class P4A_Dir_Source extends P4A_Data_Source
{
	/**
	 * @var array
	 */
	protected $_array = array();
	
	/**
	 * @var string
	 */
	protected $_dir = null;
	
	/**
	 * @var boolean
	 */
	protected $_scan_subdirs = false;
	
	/**
	 * @var boolean
	 */
	protected $_listing_subdirs = false;
	
	/**
	 * @var boolean
	 */
	protected $_files = array();
	
	/**
	 * @var boolean
	 */
	protected $_is_loaded = false;

	public function __construct($name)
	{
		parent::__construct($name);
		$this->fields->build('P4A_Data_Field', 'filename');
		$this->fields->build('P4A_Data_Field', 'size')
			->setType("filesize");
		$this->fields->build('P4A_Data_Field', 'last_modified')
			->setType("date");
		$this->setPk('filename');
	}

	/**
	 * @param string $dir
	 * @return P4A_Dir_Source
	 */
	public function load($dir = null)
	{
		if ($dir !== null) {
			$this->setDir($dir);
		}
		
		$files = array();
		$this->_array = $this->_scanDir($this->_dir, $files);
		$this->_is_loaded = true;
		
		return $this;
	}

	/**
	 * @return P4A_Dir_Source
	 */
	public function reload()
	{
		$this->load();
		return $this;
	}

	/**
	 * @param string $dir
	 * @return P4A_Dir_Source
	 */
	public function setDir($dir)
	{
		$this->_dir = $dir;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDir()
	{
		return $this->_dir;
	}

	/**
	 * @param boolean $scan_subdirs
	 * @return P4A_Dir_Source
	 */
	public function scanSubDirs($scan_subdirs = true)
	{
		$this->_scan_subdirs = $scan_subdirs;
		return $this;
	}

	/**
	 * @param boolean $listing_subdirs
	 * @return P4A_Dir_Source
	 */
	public function listingSubDirs($listing_subdirs = true)
	{
		$this->_listing_subdirs = $listing_subdirs;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAll()
	{
		if (!$this->_is_loaded) $this->load();
		return $this->_array;
	}

	protected function _scanDir($dir, array &$files)
	{
		if ($dir == $this->_dir or $dir == '') {
			$basepath = '';
			$real_dir = $this->_dir;
		} else {
			$basepath = "$dir/";
			$real_dir = "{$this->_dir}/{$dir}";
		}

		$dh = opendir($real_dir);
		while (false !== ($filename = readdir($dh))) {
			if (substr($filename, 0, 1) != '.' and $filename != 'CVS') {
				$filepath = $real_dir . '/' . $filename;
				$filename = $basepath . $filename;

				if (is_dir($filepath)) {
					if ($this->_listing_subdirs) {
						$files[]['filename'] = $filename;
					}
					if ($this->_scan_subdirs) {
						$this->_scanDir($filename, $files);
					}
				} elseif(is_file($filepath)) {
					$stat = stat($filepath);
					$tmp = array();
					$tmp["filename"] = $filename;
					$tmp["size"] = $stat["size"];
					$tmp["last_modified"] = date("Y-m-d", $stat["mtime"]);
					$files[] = $tmp;
				}
			}
		}
		closedir($dh);
		return $files;
	}
	
	public function getNumRows()
	{
		return sizeof($this->getAll());
	}
	
	public function row($num_row = null, $move_pointer = true)
	{
		if (!$this->_is_loaded) $this->load();
		
		if ($num_row !== null) {
			$row = @$this->_array[$num_row-1];
		} else {
			$num_row = $this->_pointer;
			$row = @$this->_array[$num_row-1];
		}
		
		if ($row === null) $row = array();

		if ($move_pointer) {
			if ($this->actionHandler('beforemoverow') == ABORT) return ABORT;

			if ($this->isActionTriggered('onmoverow')) {
				if ($this->actionHandler('onmoverow') == ABORT) return ABORT;
			} else {
				if (!empty($row)) {
					$this->_pointer = $num_row;

					foreach($row as $field=>$value){
						$this->fields->$field->setValue($value);
					}
				} elseif ($this->getNumRows() == 0) {
					$this->newRow();
				}
			}

			$this->actionHandler('aftermoverow');
		}

		return $row;
	}
	
	public function __sleep()
	{
		$this->_array = array();
		$this->_is_loaded = false;
		return array_keys(get_object_vars($this));
	}
}