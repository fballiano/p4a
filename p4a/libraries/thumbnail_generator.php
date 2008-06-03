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
 * @package p4a_thumbnail_generator
 */

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 */
class P4A_Thumbnail_Generator
{
	/**
	 * @var integer
	 */
	protected $thumbnail_width = null;
	
	/**
	 * @var integer
	 */
	protected $thumbnail_height = null;
	
	/**
	 * @var integer
	 */
	protected $thumbnail_max_width = null;
	
	/**
	 * @var integer
	 */
	protected $thumbnail_max_height = null;
	
	/**
	 * @var integer
	 */
	protected $original_width = null;
	
	/**
	 * @var integer
	 */
	protected $original_height = null;
	
	/**
	 * @var string
	 */
	protected $cache_dir = null;
	
	/**
	 * @var string
	 */
	protected $cached_filename_prefix = 'cache_';
	
	/**
	 * @var string
	 */
	protected $filename = null;
	
	/**
	 * @var integer
	 */
	protected $filetype = null;
	
	/**
	 * @var string
	 */
	private $cached_filename = null;
	
	/**
	 * @param string $mime
	 */
	public function isMimeTypeSupported($mime_type)
	{
		if (substr($mime_type, 0, 5) != 'image') return false;
		
		$type = substr($mime_type, 6);
		switch ($type) {
			case 'gif':
			case 'jpeg':
			case 'png':
				return function_exists("imagecreatefrom$type");
			case 'x-png':
				return function_exists("imagecreatefrompng");
			default:
				return false;
		}
	}
	
	/**
	 * @param integer $width
	 * @return P4A_Thumbnail_Generator
	 */
	public function setWidth($width)
	{
		if ($width) $this->thumbnail_width = $width;
		return $this;
	}
	
	/**
	 * @param integer $height
	 * @return P4A_Thumbnail_Generator
	 */
	public function setHeight($height)
	{
		if ($height) $this->thumbnail_height = $height;
		return $this;
	}
	
	/**
	 * @param integer $width
	 * @return P4A_Thumbnail_Generator
	 */
	public function setMaxWidth($width)
	{
		if ($width) $this->thumbnail_max_width = $width;
		return $this;
	}
	
	/**
	 * @param integer $height
	 * @return P4A_Thumbnail_Generator
	 */
	public function setMaxHeight($height)
	{
		if ($height) $this->thumbnail_max_height = $height;
		return $this;
	}
	
	/**
	 * @param string $dir
	 * @return P4A_Thumbnail_Generator
	 */
	public function setCacheDir($dir)
	{
		if ($dir) $this->cache_dir = $dir;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function isCacheEnabled()
	{
		return ($this->cache_dir !== null);
	}
	
	/**
	 * @param string $prefix
	 * @return P4A_Thumbnail_Generator
	 */
	public function setCachedFilenamePrefix($prefix)
	{
		if ($prefix) $this->cached_filename_prefix = $prefix;
		return $this;
	}
	
	/**
	 * @param string $filename
	 * @return P4A_Thumbnail_Generator
	 */
	public function setFilename($filename)
	{
		if ($filename) $this->filename = $filename;
		return $this;
	}
	
	/**
	 * @throws P4A_Thumbnail_Generator_Exception
	 * @return string
	 */
	public function getCachedFilename()
	{
		if (!$this->isCacheEnabled()) {
			throw new P4A_Thumbnail_Generator_Exception("You can't call getCachedFilename() when cache is disabled");
		}
		
		if ($this->cached_filename === null) {
			throw new P4A_Thumbnail_Generator_Exception("You must call processFile() before calling getCachedFilename()");
		}
		
		return $this->cached_filename;
	}
	
	/**
	 * @return boolean
	 */
	public function isCached()
	{
		if (!$this->isCacheEnabled()) return false;
		
		$cached_filename = $this->getCachedFilename();
		return file_exists("{$this->cache_dir}/{$cached_filename}"); 
	}
	
	/**
	 * @throws P4A_Thumbnail_Generator_Exception
	 * @return P4A_Thumbnail_Generator
	 */
	public function processFile()
	{
		if ($this->thumbnail_width === null and $this->thumbnail_height === null and $this->thumbnail_max_width === null and $this->thumbnail_max_height === null) {
			throw new P4A_Thumbnail_Generator_Exception("You must call setWidth() or setHeight() or setMaxWidth() or setMaxHeight() before calling processFile()");
		}
		if (!file_exists($this->filename)) {
			throw new P4A_Thumbnail_Generator_Exception("{$this->filename} does not exists");
		}
		if (!is_readable($this->filename)) {
			throw new P4A_Thumbnail_Generator_Exception("{$this->filename} is not readable");
		}
		
		list($this->original_width, $this->original_height, $this->filetype) = @getimagesize($this->filename);
		if ($this->original_width === null or $this->original_height === null) {
			throw new P4A_Thumbnail_Generator_Exception("{$this->filename} is not an image or could not be analyzed");
		}
		
		switch ($this->filetype) {
			case IMAGETYPE_GIF:
				if (!function_exists("imagecreatefromgif")) {
					throw new P4A_Thumbnail_Generator_Exception("Your server does not support GIF images manipulation");
				}
				break;
			case IMAGETYPE_JPEG:
				if (!function_exists("imagecreatefromjpeg")) {
					throw new P4A_Thumbnail_Generator_Exception("Your server does not support JPEG images manipulation");
				}
				break;
			case IMAGETYPE_PNG:
				if (!function_exists("imagecreatefrompng")) {
					throw new P4A_Thumbnail_Generator_Exception("Your server does not support PNG images manipulation");
				}
				break;
			default:
				throw new P4A_Thumbnail_Generator_Exception("This type of image is not supported");
		}
		
		if ($this->thumbnail_width === null) {
			$this->thumbnail_width = $this->thumbnail_max_width;
			if ($this->thumbnail_height === null) {
				$this->thumbnail_height = round($this->thumbnail_width * $this->original_height / $this->original_width);
			}
		}
		
		if ($this->thumbnail_height === null) {
			$this->thumbnail_height = $this->thumbnail_max_height;
			if ($this->thumbnail_width === null) {
				$this->thumbnail_width = round($this->thumbnail_height * $this->original_width / $this->original_height);
			}
		}
		
		if ($this->thumbnail_width > $this->thumbnail_max_width) {
			$this->thumbnail_width = $this->thumbnail_max_width;
			$this->thumbnail_height = round($this->thumbnail_width * $this->original_height / $this->original_width);
		}
		
		if ($this->thumbnail_height > $this->thumbnail_max_height) {
			$this->thumbnail_height = $this->thumbnail_max_height;
			$this->thumbnail_width = round($this->thumbnail_height * $this->original_width / $this->original_height);
		}

		$size = @filesize($this->filename);
		if ($size <= 0) {
			throw new P4A_Thumbnail_Generator_Exception("{$this->filename} seems to be corrupted (file size is 0kb)");
		}
		
		if ($this->isCacheEnabled()) {
			$this->cached_filename = $this->cached_filename_prefix . md5("{$this->filename}|{$size}|{$this->thumbnail_width}|{$this->thumbnail_height}") . '.jpg';
		}
		
		return $this;
	}
	
	/**
	 * @throws P4A_Thumbnail_Generator_Exception
	 * @return resource
	 */
	protected function generateThumbnail()
	{
		if ($this->filetype === null) {
			throw new P4A_Thumbnail_Generator_Exception("You must call processFile() before calling generateThumbnail()");
		}
		
		switch ($this->filetype)
		{
			case IMAGETYPE_GIF:
				$original = imagecreatefromgif($this->filename);
				break;
			case IMAGETYPE_JPEG:
				$original = imagecreatefromjpeg($this->filename);
				break;
			case IMAGETYPE_PNG:
				$original = imagecreatefrompng($this->filename);
				break;
		}
		
		$thumb = imagecreatetruecolor($this->thumbnail_width, $this->thumbnail_height);
		if (!imagecopyresized($thumb, $original, 0, 0, 0, 0, $this->thumbnail_width, $this->thumbnail_height, $this->original_width, $this->original_height)) {
			throw new P4A_Thumbnail_Generator_Exception("There was an error resizing your image");
		}
		return $thumb;
	}
	
	public function cacheThumbnail()
	{
		if ($this->isCacheEnabled() and !$this->isCached()) {
			imagejpeg($this->generateThumbnail(), $this->cache_dir . '/' . $this->getCachedFilename());
		}
	}
	
	public function outputThumbnail()
	{
		header("Content-type: image/jpeg");
		imagejpeg($this->generateThumbnail());
		die();
	}
}

/**
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 */
class P4A_Thumbnail_Generator_Exception extends Exception
{
}