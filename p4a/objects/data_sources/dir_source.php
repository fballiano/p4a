<?php

class P4A_Dir_Source extends P4A_Data_Source
{

	var $_dir = NULL;
	var $_cache_enabled = FALSE;
	var $_scan_subdirs = FALSE;
	var $_listing_subdirs = FALSE;
	var $_files = array();
	var $_is_loaded = FALSE;

	function P4A_Dir_Source($name)
	{
		parent::P4A_Data_Source($name);
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

?>
