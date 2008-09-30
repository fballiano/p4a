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
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
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
				return function_exists("imagecreatefromgif");
			case 'jpeg':
			case 'pjpeg':
				return function_exists("imagecreatefromjpeg");
			case 'png':
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
			$this->cached_filename = $this->cached_filename_prefix . md5("{$this->filename}|{$size}|{$this->thumbnail_width}|{$this->thumbnail_height}");
			switch ($this->filetype) {
				case IMAGETYPE_GIF:
					$this->cached_filename .= '.gif';
					break;
				case IMAGETYPE_JPEG:
					$this->cached_filename .= '.jpg';
					break;
				case IMAGETYPE_PNG:
					$this->cached_filename .= '.png';
					break;
			}
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
		
		$thumb = imagecreatetruecolor($this->thumbnail_width, $this->thumbnail_height);
		switch ($this->filetype) {
			case IMAGETYPE_GIF:
				$original = imagecreatefromgif($this->filename);
				$transparent = imagecolorsforindex($original, imagecolortransparent($original));
				$transparent_index = imagecolorallocate($thumb, $transparent['red'], $transparent['green'], $transparent['blue']);
				imagefill($thumb, 0, 0, $transparent_index);
				imagecolortransparent($thumb, $transparent_index);
				if (!imagecopyresized($thumb, $original, 0, 0, 0, 0, $this->thumbnail_width, $this->thumbnail_height, $this->original_width, $this->original_height)) {
					throw new P4A_Thumbnail_Generator_Exception("There was an error resizing your image");
				}
				break;
			case IMAGETYPE_JPEG:
				$original = imagecreatefromjpeg($this->filename);
				if (!imagecopyresampled($thumb, $original, 0, 0, 0, 0, $this->thumbnail_width, $this->thumbnail_height, $this->original_width, $this->original_height)) {
					throw new P4A_Thumbnail_Generator_Exception("There was an error resizing your image");
				}
				break;
			case IMAGETYPE_PNG:
				$original = imagecreatefrompng($this->filename);
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
				if (!imagecopyresampled($thumb, $original, 0, 0, 0, 0, $this->thumbnail_width, $this->thumbnail_height, $this->original_width, $this->original_height)) {
					throw new P4A_Thumbnail_Generator_Exception("There was an error resizing your image");
				}
				break;
		}
		
		return $thumb;
	}
	
	public function cacheThumbnail()
	{
		if ($this->isCacheEnabled() and !$this->isCached()) {
			$target_filename = $this->cache_dir . '/' . $this->getCachedFilename();
			switch ($this->filetype) {
				case IMAGETYPE_GIF:
					imagegif($this->generateThumbnail(), $target_filename);
					break;
				case IMAGETYPE_JPEG:
					imagejpeg($this->generateThumbnail(), $target_filename);
					break;
				case IMAGETYPE_PNG:
					imagepng($this->generateThumbnail(), $target_filename);
					break;
			}
		}
	}
	
	public function outputThumbnail()
	{
		if ($this->isCacheEnabled()) {
			if (!$this->isCached()) $this->cacheThumbnail();
			header("Expires: " . gmdate("D, d M Y H:i:s", time()+24*60*60) . " GMT");
			header("Pragma: cache");
			header("Cache-Control: public");
			$this->sendContentTypeHeaders();
			$fp = fopen("{$this->cache_dir}/" . $this->getCachedFilename(), "rb");
			fpassthru($fp);
			fclose($fp);
			return;
		}
		
		switch ($this->filetype) {
			case IMAGETYPE_GIF:
				$this->sendContentTypeHeaders();
				imagegif($this->generateThumbnail());
				die();
			case IMAGETYPE_JPEG:
				$this->sendContentTypeHeaders();
				imagejpeg($this->generateThumbnail());
				die();
			case IMAGETYPE_PNG:
				$this->sendContentTypeHeaders();
				imagepng($this->generateThumbnail());
				die();
		}
	}
	
	protected function sendContentTypeHeaders()
	{
		switch ($this->filetype) {
			case IMAGETYPE_GIF:
				header("Content-type: image/gif");
				break;
			case IMAGETYPE_JPEG:
				header("Content-type: image/jpeg");
				break;
			case IMAGETYPE_PNG:
				header("Content-type: image/png");
				break;
		}
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