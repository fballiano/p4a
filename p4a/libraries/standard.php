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
	 * Prints out one or many HTML "new lines".
	 * @param integer	The number of new lines to print.
	 * @access public
	 */
	function P4A_New_Line($number=1)
	{
		for($i=0;$i<$number;$i++) {
			print '<br>' . "\n";
		}
	}

	/**
	 * Prints out one or many HTML "spaces".
	 * @param integer	The number of spaces to print.
	 * @access public
	 */
	function P4A_Space($number=1)
	{
		for($i=0;$i<$number;$i++) {
			print "&nbsp;\n";
		}
	}

	/**
	 * Stops program execution with an error.
	 * @param string	Error identifier.
	 * @param string	Error description or other message.
	 * @access public
	 */
	function P4A_Error($error, $message = '')
	{
		if (strlen($message)) {
			$error .= ": $message";
		}
		die($error);
	}

	/**
	 * Checks if an object is an error object.
	 * @param object object		The object.
	 * @access public
	 */
	function P4A_Is_Error($object)
	{
		return PEAR::isError($object);
	}

	/**
	 * Converts a "file" value into an array.
	 * @access public
	 * @param string	The file.
	 * @return array
	 */
	function P4A_File2array($file)
	{
		//name, path, size, type, width, height
		//{image 96.gif,image 96.gif,7170,image/gif,96,70}
		$file = substr($file, 1, -1);
		$aFileTmp = explode(',', $file);
		$aFile['name'] = $aFileTmp[0];
		$aFile['path'] = $aFileTmp[1];
		$aFile['size'] = $aFileTmp[2];
		$aFile['type'] = $aFileTmp[3];
		$aFile['width'] = $aFileTmp[4];
		$aFile['height'] = $aFileTmp[5];
		//$aFile['url'] = rawurlencode($aFileTmp[1]);
		$aFile['url'] = $aFileTmp[1];
		return $aFile;
	}

	/**
	 * Converts an array into a "file" value.
	 * @access public
	 * @param array		The file.
	 * @return array
	 */
	function P4A_Array2file($aFile)
	{
		//name, path, size, type, width, height
		//{image 96.gif,image 96.gif,7170,image/gif,96,70}
		$aFileNew[] = $aFile['name'];
		$aFileNew[] = $aFile['path'];
		$aFileNew[] = $aFile['size'];
		$aFileNew[] = $aFile['type'];
		$aFileNew[] = $aFile['width'];
		$aFileNew[] = $aFile['height'];
		$sFile = '{' . join(',' , $aFileNew ) . '}';
		return $sFile;
	}

	/**
	 * Takes page number, records number, page limit and returns the necessary  offset for a query.
	 * @access public
	 * @param integer	Page limit.
	 * @param integer	Records limit.
	 * @param integer	Page limit.
	 * @return integer
	 */
	function P4A_Get_Offset($page_number, $records_number, $page_limit)
	{
		$offset = $page_limit * ($page_number -1);
		if ($offset > $records_number) {
			$offset = $page_limit * (P4A_Get_Num_Pages($records_number, $page_limit) -1);
		}
		return $offset;
	}
	/**
	 * Takes records number, page limit and returns the num of pages.
	 * @access public
	 * @param integer	Page Number.
	 * @param integer	Records
	 * @return integer
	 */
	function P4A_Get_Num_Pages($records_number, $page_limit){
		if ($records_number % $page_limit == 0) {
			return $records_number / $page_limit ;
		} else {
			return intval(($records_number / $page_limit)) + 1;
		}
	}

	/**
	 * Tests if a file with the same name exists and return the correct file name.
	 * Appends _1 (_2, _3) at the end fo the file name.
	 * @access private
	 * @param string		The filename without path.
	 * @param string		The directory (absolute).
	 * @return string
	 */
	function P4A_Get_Unique_File_Name( $filename, $directory )
	{
		$aParts = explode('.', $filename);
		$base = '' ;
		$ext = '' ;

		if (sizeof($aParts) > 1) {
			$ext = '.' . array_pop( $aParts );
			$base = join( $aParts, '.' );
		} else {
			$base = $filename;
		}

		$i = 1 ;
		while (file_exists("$directory/$filename")) {
			$filename = $base . '_' . $i . $ext;
			$i++;
		}

		return $filename;
	}
	
	/**
	 * Strips "strange" chars from filename.
	 * @access public
	 * @return string
	 * @param string
	 */
	function P4A_Get_Valid_File_Name($filename) 
	{
		$filename = str_replace(" ","_",$filename);
		$filename = preg_replace("/[^A-Za-z0-9_\-\.]/","",$filename);
		return $filename;
	}

	/**
	 * Returns the microtime.
	 * @access public
	 * @return integer
	 */
	function P4A_Get_Microtime()
	{
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * Includes all p4a objects for the application.
	 * @access private
	 * @param string
	 */
	function P4A_Include_Objects($dir)
	{
		if (is_dir($dir)) {
			$files = array();
			$dirs  = array();

			$dh  = opendir($dir);
			while (false !== ($filename = readdir($dh))) {
				if ($filename != '.' and $filename != '..' and $filename != 'CVS'){
					$files[] = $filename;
				}
			}
			closedir($dh);

			for($i=0;$i<count($files);$i++) {
				if(is_dir($dir .'/' . $files[$i])) {
					$dirs[]	= $dir .'/' . $files[$i];
				} elseif (is_file($dir .'/' . $files[$i]) and (substr($files[$i], -4) == '.php')) {
					$_SESSION['P4A_INCLUDES'][] = $dir .'/' .$files[$i];
				}
			}

			foreach($dirs as $subdir){
				P4A_Include_Objects($subdir);
			}
		}
	}
	
	/**
	 * Returns the extension of the passed file path/url.
	 * @access public
	 * @return string
	 * @param string
	 */
	function P4A_Get_File_Extension($url)
	{
		return substr(strrchr($url, '.'), 1);
	}
	
	/**
	 * Check if the extension is allowed to be uploaded
	 * @access public
	 * @return boolean
	 * @param string
	 */
	function P4A_Is_Extension_Allowed($extension)
	{
		$allow = explode('|', P4A_DENIED_EXTENSIONS);
		return !in_array(strtolower($extension), $allow);
	}
?>