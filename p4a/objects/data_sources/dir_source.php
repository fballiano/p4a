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
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Dir_Source extends P4A_Data_Source
{
	protected $_dir = null;
	protected $_cache_enabled = false;
	protected $_scan_subdirs = false;
	protected $_listing_subdirs = false;
	protected $_files = array();
	protected $_is_loaded = false;

	public function __construct($name)
	{
		parent::__construct($name);
		$this->fields->build('P4A_Data_Field', 'filename');
		$this->setPk('filename');
	}

	public function load($dir = null)
	{
		if ($dir !== null) {
			$this->setDir($dir);
		}

		if ($this->_cache_enabled == true) {
			$files = array();
			$this->_array = $this->_scanDir($this->_dir, $files);
		} else {
			$this->_array = array();
		}

		$this->_is_loaded = true;
	}

	public function reload()
	{
		$this->load();
	}

	public function setDir($dir)
	{
		$this->_dir = $dir;
	}

	public function getDir()
	{
		return $this->_dir;
	}

	public function scanSubDirs($scan_subdirs = true)
	{
		$this->_scan_subdirs = $scan_subdirs;
	}

	public function listingSubDirs($listing_subdirs = true)
	{
		$this->_listing_subdirs = $listing_subdirs;
	}

	public function enableCache($cache_enabled = true)
	{
		$this->_cache_enabled = $cache_enabled;
		if ($this->_is_loaded) {
			$this->reload();
		}
	}

	public function getAll()
	{
		if ($this->_cache_enabled) {
			return $this->_array();
		} else {
			$files = array();
			return $this->_scanDir($this->_dir, $files);
		}
	}

	protected function _scanDir($dir, &$files)
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
			if ($filename != '.' and $filename != '..') {
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
					$files[]['filename'] = $filename;
				}
			}
		}
		closedir($dh);
		return $files;
	}
}