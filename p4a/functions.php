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
 * Converts a "file" value into an array
 * @param string $file
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
 * Converts an array into a "file" value
 * @param array $aFile
 * @return string
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
 * Converts a file path into a "file" format value
 * @param string $filename
 * @param string $uploads_dir
 * @return string
 */
function P4A_Filename2File($filename, $uploads_dir)
{
	if (!is_file($filename)) return false;
	
	$aFile['name'] = basename($filename);
	$aFile['path'] = str_replace($uploads_dir,'',$filename);
	$aFile['size'] = filesize($filename);
	$aFile['type'] = null;
	
	if (function_exists("mime_content_type")) {
		$aFile['type'] = mime_content_type($filename);
	}
	
	if (empty($aFile['type']) and class_exists("finfo")) {
		$finfo = new finfo(FILEINFO_MIME);
		$aFile['type'] = $finfo->file($filename);
		$finfo->close();
	}
	
	list($type,$subtype) = explode('/',$aFile['type']);
	if ($type == 'image') {
		list($aFile['width'],$aFile['height']) = getimagesize($filename);
	} else {
		$aFile['width'] = null;
		$aFile['height'] = null;
	}
	return P4A_Array2File($aFile);
}

/**
 * Takes page number, records number, page limit and returns the necessary  offset for a query
 * @param integer $page_number
 * @param integer $records_number
 * @param integer $page_limit
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
 * Takes records number, page limit and returns the num of pages
 * @param integer $records_number
 * @param integer $page_limit
 * @return integer
 */
function P4A_Get_Num_Pages($records_number, $page_limit)
{
	if ($records_number % $page_limit == 0) {
		return $records_number / $page_limit ;
	} else {
		return intval(($records_number / $page_limit)) + 1;
	}
}

/**
 * Tests if a file with the same name exists and return the correct file name.
 * Appends _1 (_2, _3) at the end fo the file name.
 * @param string $filename The filename without path
 * @param string $directory The directory (absolute)
 * @return string
 */
function P4A_Get_Unique_File_Name($filename, $directory)
{
	$aParts = explode('.', $filename);
	$base = '' ;
	$ext = '' ;

	if (sizeof($aParts) > 1) {
		$ext = '.' . array_pop($aParts);
		$base = join($aParts, '.');
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
 * Strips "strange" chars from filename
 * @param string $filename
 * @return string
 */
function P4A_Get_Valid_File_Name($filename)
{
	$filename = str_replace(" ", "_", $filename);
	$filename = preg_replace("/[^A-Za-z0-9_\-\.]/", "", $filename);
	return $filename;
}

/**
 * @return integer
 */
function P4A_Get_Microtime()
{
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Includes all p4a objects for the application
 * @param string $dir
 */
function P4A_Include_Objects($dir)
{
	if (is_dir($dir)) {
		$files = array();
		$dirs  = array();

		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			if (substr($filename, 0, 1) != '.' and $filename != 'CVS') {
				$files[] = $filename;
			}
		}
		closedir($dh);

		for ($i=0; $i<count($files); $i++) {
			if(is_dir("$dir/{$files[$i]}")) {
				$dirs[]	= "$dir/{$files[$i]}";
			} elseif (is_file("$dir/{$files[$i]}") and (substr($files[$i], -4) == '.php')) {
				require_once "$dir/{$files[$i]}";
			}
		}

		foreach ($dirs as $subdir) {
			P4A_Include_Objects($subdir);
		}
	}
}

/**
 * Returns the extension of the passed file path/url
 * @param string $url
 * @return string
 */
function P4A_Get_File_Extension($url)
{
	return substr(strrchr($url, '.'), 1);
}

/**
 * Check if the extension is allowed to be uploaded
 * @param string $extension
 * @return boolean
 */
function P4A_Is_Extension_Allowed($extension)
{
	$allow = explode('|', P4A_DENIED_EXTENSIONS);
	return !in_array(strtolower($extension), $allow);
}

/**
 * Returns an i18n translated string (like gettext)
 * @param string $string string to be translated
 * @return string
 */
function __($string)
{
	return P4A::singleton()->i18n->translate($string);
}

/**
 * @param string $mime_type
 * @return boolean
 */
function P4A_Is_Mime_Type_Embeddable($mime_type)
{
	list($type, $application) = explode('/', $mime_type);
	if ($type == 'audio' or $type == 'video') return true;

	$embeddables = array();
	$embeddables[] = 'application/x-shockwave-flash';
	$embeddables[] = 'application/vnd.rn-realmedia';
	return in_array($mime_type, $embeddables);
}

/**
 * Return HTML tag containig embedded audio/video player
 * @param string $src File path
 * @param string $mime_type
 * @param string $width
 * @param string $height
 * @return string
 */
function P4A_Embedded_Player($src, $mime_type, $width=300, $height=200)
{
	$src = P4A_Strip_Double_Slashes($src);
	if (!$width) $width=300;
	if (!$height) $height=200;

	switch ($mime_type) {
		case 'audio/vnd.rn-realaudio';
		case 'audio/x-pn-realaudio':
		case 'audio/x-pn-realaudio-plugin':
		case 'video/vnd.rn-realvideo':
		case 'application/vnd.rn-realmedia':
			$player_type = 'real';
			break;
		case 'video/quicktime':
			$player_type = 'quicktime';
			break;
		case 'application/x-shockwave-flash':
			$player_type = 'flash';
			break;
		default:
			$player_type = 'wmedia';
	}

	return "<a id='p4a_media_player' href='$src'>$src</a><script type='text/javascript'>require(['" . P4A_THEME_PATH . "/jquery/jmedia.js'],function(){\$('#p4a_media_player').jmedia({},{type:'$player_type'})})</script>";
}

/**
 * Used for internal debugging (within session browser)
 * @param mixed $v Variable to print
 * @return string
 */
function _P4A_Debug_Print_Variable($v)
{
	if ($v === null) $v = '<span style="color:green">NULL</span>';
	if ($v === false) $v = '<span style="color:green">FALSE</span>';
	if ($v === true) $v = '<span style="color:green">TRUE</span>';
	if ($v === '') $v = '<span style="color:green">Empty String</span>';

	if (is_array($v)) {
		if (empty($v)) {
			$v = '<span style="color:green">Empty Array</span>';
		} else {
			$todebug = $v;
			$v = '<table border="1">';
			$v .= "<tr><th colspan='2'>Array</th></tr>";
			$v .= "<tr><th>key</th><th>value</th></tr>";
			foreach ($todebug as $k2=>$v2) {
				$v2 = _P4A_Debug_Print_Variable($v2);
				$v .= "<tr><td>$k2</td><td>$v2</td></tr>";
			}
			$v .= '</table>';
		}
	}

	if (is_object($v)) {
		if (is_a($v, 'p4a_object')) {
			$v = '<a href=".?_p4a_session_browser=' . $v->getId() . '">' . $v->getName() . ' (' . get_class($v) . ')</a>';
		} else {
			$v = '<pre>' . print_r($v, true) . '</pre>';
		}
	}

	return $v;
}

/*
 * @param string $string
 * @return string
 */
function P4A_Strip_Double_Slashes($string)
{
	$string = str_replace('//', '/', $string);
	if (strpos($string, '//') !== false) {
		$string = P4A_Strip_Double_Slashes($string);
	}
	return $string;
}

/*
 * @param string $string
 * @return string
 */
function P4A_Strip_Double_Backslashes($string)
{
	$string = str_replace('\\\\', '\\', $string);
	if (strpos($string, '\\\\') !== false) {
		$string = P4A_Strip_Double_Backslashes($string);
	}
	return $string;
}
	
/**
 * Can the event be managed by the browser?
 * eg: P4A_Is_Browser_Event('onclick') will return true
 *
 * @param string $event
 * @return boolean
 */
function P4A_Is_Browser_Event($event)
{
	return in_array(strtolower($event), array(
		'onblur', 'onchange', 'onclick', 'ondblclick', 'onfocus', 'onkeydown', 
		'onkeypress', 'onkeyup', 'onmousedown', 'onmousemove', 'onmouseout', 
		'onmouseover', 'onmouseup', 'onscroll', 'onreturnpress'
	));
}

/**
 * @param string $string
 * @return string
 */
function P4A_Generate_Default_Label($string)
{
	return ucfirst(str_replace('_', ' ', strtolower($string)));
}

/**
 * @param string $string
 * @param string $accesskey
 * @return string
 */
function P4A_Highlight_AccessKey($string, $accesskey)
{
	if (strlen($accesskey) == 0) return $string;
	return preg_replace("/($accesskey)/i", "<span class=\"accesskey\">$1</span>", $string);
}

/**
 * @param string $column1
 * @param string $column2
 * @param string $additional_css_classes
 * @param string $html_attributes
 * @return string
 */
function P4A_Generate_Widget_Layout_Table($column1 = null, $column2 = null, $additional_css_classes = null, $html_attributes = null)
{
	if (!strlen($html_attributes) and !strlen($additional_css_classes)) {
		if (!strlen($column1)) return $column2;
		if (!strlen($column2)) return $column1;
	}
	return "<table class='p4a_widget_layout_table $additional_css_classes' $html_attributes><tr><td class='c1'>$column1</td><td class='c2'>$column2</td></tr></table>";
}

/**
 * @param string $string
 * @return string
 */
function P4A_Quote_Javascript_String($string)
{
	return str_replace(array("'", '"', "\n"), array('\\x27', '\\x22', '\n'), $string);
}

/**
 * @param string $dir
 * @return boolean
 */
function P4A_Mkdir_Recursive($dir)
{
	return @mkdir($dir, 0777, true);
}

/**
 * @param string $dir
 * @return boolean
 */
function P4A_Rmdir_Recursive($dir)
{
	if (is_dir($dir)) {
		foreach (scandir($dir) as $entry) {
			if ($entry == '.' or $entry == '..') continue;
			$entry = $dir . DIRECTORY_SEPARATOR . $entry;
			if (is_dir($entry) and !is_link($entry)) {
				if (!P4A_Rmdir_Recursive($entry)) return false;
			} else {
				if (!@unlink($entry)) return false;
			}
		}
		return @rmdir($dir);
	}
	return false;
}

/**
 * @param integer $error_number
 * @param string $error_string
 * @param string $error_file
 * @param integer $error_line
 * @return boolean
 */
function P4A_Error_Handler($error_number, $error_string, $error_file, $error_line)
{
	if (!($error_number & error_reporting())) return true; 
	
	$error_file = basename($error_file);
	$message = "$error_string<br /><em>$error_file line $error_line</em>";
	
	switch ($error_number) {
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
			if (P4A_EXTENDED_ERRORS) {
				$message .= "<br /><br /><strong>BACKTRACE:</strong><br /><ol class='p4a_backtrace'>";
				foreach (debug_backtrace() as $k=>$backtrace) {
					$args = array();
					if (isset($backtrace['args'])) {
						foreach ($backtrace['args'] as $arg) {
							if (is_object($arg)) {
								$args[] = get_class($arg);
								continue;
							}
							if (is_array($arg)) {
								$args[] = 'Array(' . sizeof($arg) . ')';
								continue;
							}
							if (is_resource($arg)) {
								$args[] = 'Resource';
								continue;
							}
							if (is_string($arg)) {
								$args[] = "'$arg'";
								continue;
							}
							$args[] = $arg;
						}
					}
					$args = join(', ', $args);
					if (isset($backtrace['line'])) {
						$backtrace['line'] = " line {$backtrace['line']}";
					} else {
						$backtrace['line'] = '';
					}
					if (isset($backtrace['file'])) {
						$backtrace['file'] = basename($backtrace['file']);
					} else {
						$backtrace['file'] = '';
					}
					
					if ($backtrace['file'] and $backtrace['line']) {
						$backtrace['file'] = "<em>{$backtrace['file']}{$backtrace['line']}:</em> ";
					}
					
					$message .= "<li>{$backtrace['file']}{$backtrace['function']}({$args})</li>\n";
				}
				$message .= "</ol>";
			}
			
			$p4a = P4A::singleton();
			$error_mask = $p4a->openMask("P4A_Error_Mask")->setMessage($message);
			if ($p4a->inAjaxCall()) {
				$p4a->raiseXMLResponse();
			} else {
				$error_mask->main();
			}
			$p4a->close();
			die();
		case E_WARNING:
		case E_USER_WARNING:
			P4A::singleton()
				->messageWarning("<strong>WARNING: </strong>$message");
			return true;
		case E_STRICT:
		case E_NOTICE:
		case E_USER_NOTICE:
			P4A::singleton()
				->messageWarning("<strong>NOTICE: </strong>$message");
			return true;
	}
	return false;
}

/**
 * @param Exception $exception
 */
function P4A_Exception_Handler(Exception $e)
{
	$p4a = P4A::singleton();
	$error_file = basename($e->getFile());
	$message = $e->getMessage() . "<br /><em>File: {$error_file}, Line: {$e->getLine()}</em>";
	
	if (P4A_EXTENDED_ERRORS) {
		$message .= "<br /><br /><strong>BACKTRACE:</strong><br /><ol class='p4a_backtrace'>";
		foreach ($e->getTrace() as $k=>$backtrace) {
			$args = array();
			if (isset($backtrace['args'])) {
				foreach ($backtrace['args'] as $arg) {
					if (is_object($arg)) {
						$args[] = get_class($arg);
						continue;
					}
					if (is_array($arg)) {
						$args[] = 'Array(' . sizeof($arg) . ')';
						continue;
					}
					if (is_resource($arg)) {
						$args[] = 'Resource';
						continue;
					}
					if (is_string($arg)) {
						$args[] = "'$arg'";
						continue;
					}
					$args[] = $arg;
				}
			}
			$args = join(', ', $args);
			if (isset($backtrace['line'])) {
				$backtrace['line'] = " line {$backtrace['line']}";
			} else {
				$backtrace['line'] = '';
			}
			if (isset($backtrace['file'])) {
				$backtrace['file'] = basename($backtrace['file']);
			} else {
				$backtrace['file'] = '';
			}
			
			if ($backtrace['file'] and $backtrace['line']) {
				$backtrace['file'] = "<em>{$backtrace['file']}{$backtrace['line']}:</em> ";
			}
			
			$message .= "<li>{$backtrace['file']}{$backtrace['function']}({$args})</li>\n";
		}
		$message .= "</ol>";
	}
	
	$error_mask = $p4a->openMask("P4A_Error_Mask")->setMessage($message);
	if ($p4a->inAjaxCall()) {
		$p4a->raiseXMLResponse();
	} else {
		$error_mask->main();
	}
	$p4a->close();
}

/**
 * @param string $url
 * @param boolean $new_window Only works within AJAX calls
 */
function P4A_Redirect_To_Url($url, $new_window = false)
{
	$p4a = P4A::singleton();
	if ($p4a->inAjaxCall()) {
		$gmdate = gmdate("D, d M Y H:i:s");
		header('Content-type: text/xml; charset: UTF-8');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		header("Last-Modified: $gmdate GMT");
		
		ob_start();
		echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
		echo "<ajax-response action_id=\"" . $p4a->getActionHistoryId() . "\" focus_id=\"\">\n";
		foreach ($p4a->getRenderedMessages() as $message) {
			echo "\n<message><![CDATA[$message]]></message>";
		}
		echo "<widget id='p4a'>\n";
		
		if ($new_window) {
			echo "<javascript_pre><![CDATA[window.open('$url')]]></javascript_pre>\n";
		} else {
			echo "<javascript_pre><![CDATA[window.location='$url']]></javascript_pre>\n";
		}
		
		echo "</widget>\n";
		echo "</ajax-response>";
		
		if (P4A_AJAX_DEBUG) {
			if (($fp = @fopen(P4A_AJAX_DEBUG, 'w')) !== false) {
				@fwrite($fp, ob_get_contents());
				@fclose($fp);
			}
		}

		ob_end_flush();
		die();
		
		/* JSON OUTPUT DISABLED
		$gmdate = gmdate("D, d M Y H:i:s");
		header('Content-type: text/plain; charset: UTF-8');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		header("Last-Modified: $gmdate GMT");
		
		$tmp = new p4a_ajax_response_widget("p4a", null);
		if ($new_window) {
			$tmp->javascript_pre = "window.open('$url')";
		} else {
			$tmp->javascript_pre = "window.location='$url'";
		}

		$resp = new p4a_ajax_response();
		$resp->action_id = $p4a->getActionHistoryId();
		$resp->messages = $p4a->getRenderedMessages();
		$resp->widgets[] = $tmp;
		
		ob_start();
		require_once "Zend/Json.php";
		echo Zend_Json::encode($resp);
		
		if (P4A_AJAX_DEBUG) {
			if (($fp = @fopen(P4A_AJAX_DEBUG, 'w')) !== false) {
				@fwrite($fp, ob_get_contents());
				@fclose($fp);
			}
		}

		ob_end_flush();
		die();
		*/
	}
	
	header("Location: $url");
	die();
}

/**
 * @param string $file
 * @param boolean $new_window Only works within AJAX calls
 * @throws P4A_Exception if the file does not exists in P4A_UPLOADS_DIR
 */
function P4A_Redirect_To_File($file, $new_window = false)
{
	if (!@file_exists(@realpath(P4A_UPLOADS_DIR . "/$file"))) {
		throw new P4A_Exception("The requested file does not exists within P4A_UPLOADS_DIR", P4A_FILESYSTEM_ERROR);
	}
	
	$p4a = P4A::singleton();
	$file = urlencode($file);
	$file = "index.php?_p4a_download_file=$file";
	
	if ($p4a->inAjaxCall()) {
		$gmdate = gmdate("D, d M Y H:i:s");
		header('Content-type: text/xml; charset: UTF-8');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		header("Last-Modified: $gmdate GMT");
		
		ob_start();
		echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
		echo "<ajax-response action_id=\"" . $p4a->getActionHistoryId() . "\" focus_id=\"\">\n";
		foreach ($p4a->getRenderedMessages() as $message) {
			echo "\n<message><![CDATA[$message]]></message>";
		}
		echo "<widget id='p4a'>\n";
		
		if ($new_window) {
			echo "<javascript_pre><![CDATA[window.open('$file')]]></javascript_pre>\n";
		} else {
			echo "<javascript_pre><![CDATA[window.location='$file']]></javascript_pre>\n";
		}
		
		echo "</widget>\n";
		echo "</ajax-response>";
		
		if (P4A_AJAX_DEBUG) {
			if (($fp = @fopen(P4A_AJAX_DEBUG, 'w')) !== false) {
				@fwrite($fp, ob_get_contents());
				@fclose($fp);
			}
		}

		ob_end_flush();
		die();
		/* JSON OUTPUT DISABLED
		$gmdate = gmdate("D, d M Y H:i:s");
		header('Content-type: text/plain; charset: UTF-8');
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");
		header("Last-Modified: $gmdate GMT");
		
		$tmp = new p4a_ajax_response_widget("p4a", null);
		if ($new_window) {
			$tmp->javascript_pre = "window.open('$file')";
		} else {
			$tmp->javascript_pre = "window.location='$file'";
		}

		$resp = new p4a_ajax_response();
		$resp->action_id = $p4a->getActionHistoryId();
		$resp->messages = $p4a->getRenderedMessages();
		$resp->widgets[] = $tmp;
		
		ob_start();
		require_once "Zend/Json.php";
		echo Zend_Json::encode($resp);
		
		if (P4A_AJAX_DEBUG) {
			if (($fp = @fopen(P4A_AJAX_DEBUG, 'w')) !== false) {
				@fwrite($fp, ob_get_contents());
				@fclose($fp);
			}
		}

		ob_end_flush();
		die();
		*/
	}
	
	header("Location: $file");
	die();
}

/**
 * creates a temp file and outputs it to the browser (script will die).
 * The file will be deleted after being transfered to the client.
 * @param string|array $file_content
 * @param string $file_name file name with extension
 * @param boolean $new_window Only works within AJAX calls
 */
function P4A_Output_File($file_content, $file_name, $new_window = false)
{
	$name = '_p4a_' . uniqid() . '_' . $file_name;
	while (file_exists(P4A_UPLOADS_TMP_DIR . "/$name")) {
		$name = '_p4a_' . uniqid() . '_' . $file_name;
	}
	
	$fp = fopen(P4A_UPLOADS_TMP_DIR . "/$name", 'w');
	if (is_array($file_content)) {
		foreach ($file_content as $line) {
			fwrite($fp, $line);
		}
	} else {
		fwrite($fp, $file_content);
	}
	fclose($fp);
	
	P4A_Redirect_To_File(P4A_UPLOADS_TMP_NAME . "/$name", $new_window);
}