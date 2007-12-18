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

class P4A_Dir_Source extends P4A_Data_Source
{

	var $_dir = NULL;
	var $_cache_enabled = FALSE;
	var $_scan_subdirs = FALSE;
	var $_listing_subdirs = FALSE;
	var $_files = array();
	var $_is_loaded = FALSE;

	public function __construct($name)
	{
		parent::__construct($name);
		$this->build("P4A_Collection", "fields");
		$this->fields->build("p4a_data_field", 'filename');
		$this->setPk('filename');
	}

	function load($dir=NULL)
	{
		if ($dir !== NULL) {
			$this->setDir($dir);
		}

		if ($this->_cache_enabled == TRUE) {
			$files = array();
			$this->_array = $this->_scanDir($this->_dir,$files);
		} else {
			$this->_array = array();
		}

		$this->_is_loaded = TRUE;
	}

	function reload()
	{
		$this->load();
	}

	function setDir($dir)
	{
		$this->_dir = $dir;
	}

	function getDir()
	{
		return $this->_dir;
	}

	function scanSubDirs($scan_subdirs=TRUE)
	{
		$this->_scan_subdirs = $scan_subdirs;
	}

	function listingSubDirs($listing_subdirs=TRUE)
	{
		$this->_listing_subdirs = $listing_subdirs;
	}

	function enableCache($cache_enabled=TRUE)
	{
		$this->_cache_enabled = $cache_enabled;
		if ($this->_is_loaded) {
			$this->reload();
		}
	}

	function getAll()
	{
		if ($this->_cache_enabled) {
			return $this->_array();
		} else {
			$files = array();
			return $this->_scanDir($this->_dir,$files);
		}
	}

	function _scanDir($dir,&$files)
	{
		if ($dir==$this->_dir or $dir == '') {
			$basepath = '';
			$real_dir = $this->_dir;
		} else {
			$basepath = $dir . '/';
			$real_dir = $this->_dir . '/' . $dir;
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
						$this->_scanDir($filename,$files);
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