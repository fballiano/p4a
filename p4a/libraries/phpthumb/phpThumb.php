<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///                                                         //
// See: phpthumb.changelog.txt for recent changes           //
// See: phpthumb.readme.txt for usage instructions          //
//                                                         ///
//////////////////////////////////////////////////////////////

error_reporting(E_ALL);
ini_set('display_errors', '1');
if (!@ini_get('safe_mode')) {
	set_time_limit(60);  // shouldn't take nearly this long in most cases, but with many filter and/or a slow server...
}
$starttime = array_sum(explode(' ', microtime()));

if (!@$_GET['phpThumbDebug'] && !function_exists('ImageJPEG') && !function_exists('ImagePNG') && !function_exists('ImageGIF')) {
	// base64-encoded error image in GIF format
	$ERROR_NOGD = 'R0lGODlhIAAgALMAAAAAABQUFCQkJDY2NkZGRldXV2ZmZnJycoaGhpSUlKWlpbe3t8XFxdXV1eTk5P7+/iwAAAAAIAAgAAAE/vDJSau9WILtTAACUinDNijZtAHfCojS4W5H+qxD8xibIDE9h0OwWaRWDIljJSkUJYsN4bihMB8th3IToAKs1VtYM75cyV8sZ8vygtOE5yMKmGbO4jRdICQCjHdlZzwzNW4qZSQmKDaNjhUMBX4BBAlmMywFSRWEmAI6b5gAlhNxokGhooAIK5o/pi9vEw4Lfj4OLTAUpj6IabMtCwlSFw0DCKBoFqwAB04AjI54PyZ+yY3TD0ss2YcVmN/gvpcu4TOyFivWqYJlbAHPpOntvxNAACcmGHjZzAZqzSzcq5fNjxFmAFw9iFRunD1epU6tsIPmFCAJnWYE0FURk7wJDA0MTKpEzoWAAskiAAA7';
	header('Content-Type: image/gif');
	echo base64_decode($ERROR_NOGD);
	exit;
}

// this script relies on the superglobal arrays, fake it here for old PHP versions
if (phpversion() < '4.1.0') {
	$_SERVER = $HTTP_SERVER_VARS;
	$_GET    = $HTTP_GET_VARS;
}

if (empty($_GET) && !empty($_SERVER['PATH_INFO'])) {
	$_SERVER['PHP_SELF'] = str_replace($_SERVER['PATH_INFO'], '', @$_SERVER['PHP_SELF']);

	$args = explode(';', substr($_SERVER['PATH_INFO'], 1));
	if (!empty($args)) {
		$_GET['src'] = @$args[count($args) - 1];
	}
	if (eregi('^([0-9]*)x?([0-9]*)$', @$args[count($args) - 2], $matches)) {
		$_GET['w'] = $matches[1];
		$_GET['h'] = $matches[2];
	}
	for ($i = 0; $i < count($args) - 2; $i++) {
		@list($key, $value) = explode('=', @$args[$i]);
		if (substr($key, -2) == '[]') {
			$_GET[substr($key, 0, -2)][] = $value;
		} else {
			$_GET[$key] = $value;
		}
	}
}

// instantiate a new phpThumb() object
ob_start();
if (!include_once(dirname(__FILE__).'/phpthumb.class.php')) {
	ob_end_flush();
	die('failed to include_once("'.realpath(dirname(__FILE__).'/phpthumb.class.php').'")');
}
ob_end_clean();
$phpThumb = new phpThumb();
$phpThumb->DebugTimingMessage('phpThumb.php start', __FILE__, __LINE__, $starttime);

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[0]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '0') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

if (file_exists(dirname(__FILE__).'/phpThumb.config.php')) {
	ob_start();
	if (include_once(dirname(__FILE__).'/phpThumb.config.php')) {
		// great
	} else {
		ob_end_flush();
		$phpThumb->ErrorImage('failed to include_once('.dirname(__FILE__).'/phpThumb.config.php) - realpath="'.realpath(dirname(__FILE__).'/phpThumb.config.php').'"');
	}
	ob_end_clean();
} elseif (file_exists(dirname(__FILE__).'/phpThumb.config.php.default')) {
	$phpThumb->ErrorImage('Please rename "phpThumb.config.php.default" to "phpThumb.config.php"');
} else {
	$phpThumb->ErrorImage('failed to include_once('.dirname(__FILE__).'/phpThumb.config.php) - realpath="'.realpath(dirname(__FILE__).'/phpThumb.config.php').'"');
}

if (@$PHPTHUMB_CONFIG['high_security_enabled']) {
	if (!@$_GET['hash']) {
		$phpThumb->ErrorImage('ERROR: missing hash');
	} elseif (strlen($PHPTHUMB_CONFIG['high_security_password']) < 5) {
		$phpThumb->ErrorImage('ERROR: strlen($PHPTHUMB_CONFIG[high_security_password]) < 5');
	} elseif ($_GET['hash'] != md5(str_replace('&hash='.$_GET['hash'], '', $_SERVER['QUERY_STRING']).$PHPTHUMB_CONFIG['high_security_password'])) {
		$phpThumb->ErrorImage('ERROR: invalid hash');
	}
}

// returned the fixed string if the evil "magic_quotes_gpc" setting is on
if (get_magic_quotes_gpc()) {
	$RequestVarsToStripSlashes = array('src', 'wmf', 'file', 'err', 'goto', 'down');
	foreach ($RequestVarsToStripSlashes as $key) {
		if (isset($_GET[$key])) {
			$_GET[$key] = stripslashes($_GET[$key]);
		}
	}
}

if (!@$_SERVER['PATH_INFO'] && !@$_SERVER['QUERY_STRING']) {

	echo 'phpThumb() v'.$phpThumb->phpthumb_version.'<br><a href="http://phpthumb.sourceforge.net">http://phpthumb.sourceforge.net</a><br><br>ERROR: no parameters specified';
	unset($phpThumb);
	exit;

}

if (@$_GET['src'] && isset($_GET['md5s']) && empty($_GET['md5s'])) {
	if (eregi('^(f|ht)tps?://', $_GET['src'])) {
		if ($fp_source = @fopen($_GET['src'], 'rb')) {
			$filedata = '';
			while (true) {
				$buffer = fread($fp_source, 16384);
				if (strlen($buffer) == 0) {
					break;
				}
				$filedata .= $buffer;
			}
			fclose($fp_source);
			$md5s = md5($filedata);
		}
	} else {
		$SourceFilename = $phpThumb->ResolveFilenameToAbsolute($_GET['src']);
		if (is_readable($SourceFilename)) {
			$md5s = phpthumb_functions::md5_file_safe($SourceFilename);
		} else {
			$phpThumb->ErrorImage('ERROR: "'.$SourceFilename.'" cannot be read');
		}
	}
	if (@$_SERVER['HTTP_REFERER']) {
		$phpThumb->ErrorImage('&md5s='.$md5s);
	} else {
		die('&md5s='.$md5s);
	}
}

if (!empty($PHPTHUMB_CONFIG)) {
	foreach ($PHPTHUMB_CONFIG as $key => $value) {
		$keyname = 'config_'.$key;
		$phpThumb->setParameter($keyname, $value);
	}
} else {
	$phpThumb->DebugMessage('$PHPTHUMB_CONFIG is empty', __FILE__, __LINE__);
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[1]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '1') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

$parsed_url_referer = parse_url(@$_SERVER['HTTP_REFERER']);
if ($phpThumb->config_nooffsitelink_require_refer && !in_array(@$parsed_url_referer['host'], $phpThumb->config_nohotlink_valid_domains)) {
	$phpThumb->ErrorImage('config_nooffsitelink_require_refer enabled and '.(@$parsed_url_referer['host'] ? '"'.$parsed_url_referer['host'].'" is not an allowed referer' : 'no HTTP_REFERER exists'));
}
$parsed_url_src = parse_url(@$_GET['src']);
if ($phpThumb->config_nohotlink_enabled && $phpThumb->config_nohotlink_erase_image && eregi('^(f|ht)tps?://', @$_GET['src']) && !in_array(@$parsed_url_src['host'], $phpThumb->config_nohotlink_valid_domains)) {
	$phpThumb->ErrorImage($phpThumb->config_nohotlink_text_message);
}

if ($phpThumb->config_mysql_query) {
	if ($cid = @mysql_connect($phpThumb->config_mysql_hostname, $phpThumb->config_mysql_username, $phpThumb->config_mysql_password)) {
		if (@mysql_select_db($phpThumb->config_mysql_database, $cid)) {
			if ($result = @mysql_query($phpThumb->config_mysql_query, $cid)) {
				if ($row = @mysql_fetch_array($result)) {

					mysql_free_result($result);
					mysql_close($cid);
					$phpThumb->setSourceData($row[0]);
					unset($row);

				} else {
					mysql_free_result($result);
					mysql_close($cid);
					$phpThumb->ErrorImage('no matching data in database.');
				}
			} else {
				mysql_close($cid);
				$phpThumb->ErrorImage('Error in MySQL query: "'.mysql_error($cid).'"');
			}
		} else {
			mysql_close($cid);
			$phpThumb->ErrorImage('cannot select MySQL database: "'.mysql_error($cid).'"');
		}
	} else {
		$phpThumb->ErrorImage('cannot connect to MySQL server');
	}
	unset($_GET['id']);
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[2]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '2') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

if (@$PHPTHUMB_CONFIG['cache_default_only_suffix'] && (strpos($PHPTHUMB_CONFIG['cache_default_only_suffix'], '*') !== false)) {
	$PHPTHUMB_DEFAULTS_DISABLEGETPARAMS = true;
}
$allowedGETparameters = array('src', 'new', 'w', 'h', 'wp', 'hp', 'wl', 'hl', 'ws', 'hs', 'f', 'q', 'sx', 'sy', 'sw', 'sh', 'zc', 'bc', 'bg', 'bgt', 'fltr', 'file', 'goto', 'err', 'xto', 'ra', 'ar', 'aoe', 'far', 'iar', 'maxb', 'down', 'phpThumbDebug', 'hash', 'md5s');
foreach ($_GET as $key => $value) {
	if (@$PHPTHUMB_DEFAULTS_DISABLEGETPARAMS && ($key != 'src')) {
		// disabled, do not set parameter
		$phpThumb->DebugMessage('ignoring $_GET['.$key.'] because of $PHPTHUMB_DEFAULTS_DISABLEGETPARAMS', __FILE__, __LINE__);
	} elseif (in_array($key, $allowedGETparameters)) {
		$phpThumb->setParameter($key, $value);
	} else {
		$phpThumb->ErrorImage('Forbidden parameter: '.$key);
	}
}

if (!empty($PHPTHUMB_DEFAULTS) && is_array($PHPTHUMB_DEFAULTS)) {
	$phpThumb->DebugMessage('setting $PHPTHUMB_DEFAULTS['.implode(';', array_keys($PHPTHUMB_DEFAULTS)).']', __FILE__, __LINE__);
	foreach ($PHPTHUMB_DEFAULTS as $key => $value) {
		if ($PHPTHUMB_DEFAULTS_GETSTRINGOVERRIDE || !isset($_GET[$key])) {
			$phpThumb->setParameter($key, $value);
		}
	}
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[3]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '3') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

// check to see if file can be output from source with no processing or caching
$CanPassThroughDirectly = true;
if ($phpThumb->rawImageData) {
	// data from SQL, should be fine
} elseif (!@is_file(@$_GET['src']) || !@is_readable(@$_GET['src'])) {
	$CanPassThroughDirectly = false;
}
foreach ($_GET as $key => $value) {
	switch ($key) {
		case 'src':
			// allowed
			break;

		default:
			// all other parameters will cause some processing,
			// therefore cannot pass through original image unmodified
			$CanPassThroughDirectly = false;
			$UnAllowedGET[] = $key;
			break;
	}
}
if (!empty($UnAllowedGET)) {
	$phpThumb->DebugMessage('Cannot pass through directly because $_GET['.implode(';', array_unique($UnAllowedGET)).'] are set', __FILE__, __LINE__);
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[4]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '4') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

function SendSaveAsFileHeaderIfNeeded() {
	if (headers_sent()) {
		return false;
	}
	global $phpThumb;
	if (@$_GET['down']) {
		$downloadfilename = ereg_replace('[/\\:\*\?"<>|]', '_', $_GET['down']);
		if (phpthumb_functions::version_compare_replacement(phpversion(), '4.1.0', '>=')) {
			$downloadfilename = trim($downloadfilename, '.');
		}
		if (@$downloadfilename) {
			$phpThumb->DebugMessage('SendSaveAsFileHeaderIfNeeded() sending header: Content-Disposition: attachment; filename="'.$downloadfilename.'"', __FILE__, __LINE__);
			header('Content-Disposition: attachment; filename="'.$downloadfilename.'"');
			return true;
		}
	}
	$phpThumb->DebugMessage('SendSaveAsFileHeaderIfNeeded() sending header: Content-Disposition: inline', __FILE__, __LINE__);
	header('Content-Disposition: inline');
	return true;
}

while ($CanPassThroughDirectly && $phpThumb->src) {
	// no parameters set, passthru
	$SourceFilename = $phpThumb->ResolveFilenameToAbsolute($phpThumb->src);

	if (@$_GET['phpThumbDebug']) {

		$phpThumb->DebugMessage('Would have passed "'.$SourceFilename.'" through directly, but skipping due to phpThumbDebug', __FILE__, __LINE__);

	} else {

		// security checks
		if ($GetImageSize = @GetImageSize($SourceFilename)) {
			$ImageCreateFunctions = array(1=>'ImageCreateFromGIF', 2=>'ImageCreateFromJPEG', 3=>'ImageCreateFromPNG');
			if (@$ImageCreateFunctions[$GetImageSize[2]]) {
				$theFunction = $ImageCreateFunctions[$GetImageSize[2]];
				if (function_exists($theFunction) && ($dummyImage = @$theFunction($SourceFilename))) {
					// great
					unset($dummyImage);
				} else {
					$phpThumb->DebugMessage('Not passing "'.$SourceFilename.'" through directly because '.$theFunction.'() failed', __FILE__, __LINE__);
					break;
				}
			} else {
				$phpThumb->DebugMessage('Not passing "'.$SourceFilename.'" through directly because GetImageSize() returned unhandled image type "'.$GetImageSize[2].'"', __FILE__, __LINE__);
				break;
			}
		} else {
			$phpThumb->DebugMessage('Not passing "'.$SourceFilename.'" through directly because GetImageSize() failed', __FILE__, __LINE__);
			break;
		}
		if (headers_sent()) {
			$phpThumb->ErrorImage('Headers already sent ('.basename(__FILE__).' line '.__LINE__.')');
			exit;
		}
		SendSaveAsFileHeaderIfNeeded();
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', @filemtime($SourceFilename)).' GMT');
		if (@$GetImageSize[2]) {
			header('Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype($GetImageSize[2]));
		}
		@readfile($SourceFilename);
		exit;

	}
	break;
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[5]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '5') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

function RedirectToCachedFile() {
	global $phpThumb, $PHPTHUMB_CONFIG;

	$nice_cachefile = str_replace($phpThumb->osslash, '/', $phpThumb->cache_filename);
	$nice_docroot   = str_replace($phpThumb->osslash, '/', rtrim($PHPTHUMB_CONFIG['document_root'], '/\\'));

	$parsed_url = @parse_url(@$_SERVER['HTTP_REFERER']);

	$nModified  = filemtime($phpThumb->cache_filename);

	if ($phpThumb->config_nooffsitelink_enabled && @$_SERVER['HTTP_REFERER'] && !in_array(@$parsed_url['host'], $phpThumb->config_nooffsitelink_valid_domains)) {

		$phpThumb->DebugMessage('Would have used cached (image/'.$phpThumb->thumbnailFormat.') file "'.$phpThumb->cache_filename.'" (Last-Modified: '.gmdate('D, d M Y H:i:s', $nModified).' GMT), but skipping because $_SERVER[HTTP_REFERER] ('.@$_SERVER['HTTP_REFERER'].') is not in $phpThumb->config_nooffsitelink_valid_domains ('.implode(';', $phpThumb->config_nooffsitelink_valid_domains).')', __FILE__, __LINE__);

	} elseif ($phpThumb->phpThumbDebug) {

		$phpThumb->DebugMessage('Would have used cached file, but skipping due to phpThumbDebug', __FILE__, __LINE__);
		$phpThumb->DebugMessage('* Would have sent headers (1): Last-Modified: '.gmdate('D, d M Y H:i:s', $nModified).' GMT', __FILE__, __LINE__);
		if ($getimagesize = @GetImageSize($phpThumb->cache_filename)) {
			$phpThumb->DebugMessage('* Would have sent headers (2): Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype($getimagesize[2]), __FILE__, __LINE__);
		}
		if (ereg('^'.preg_quote($nice_docroot).'(.*)$', $nice_cachefile, $matches)) {
			$phpThumb->DebugMessage('* Would have sent headers (3): Location: '.dirname($matches[1]).'/'.urlencode(basename($matches[1])), __FILE__, __LINE__);
		} else {
			$phpThumb->DebugMessage('* Would have sent data: readfile('.$phpThumb->cache_filename.')', __FILE__, __LINE__);
		}

	} else {

		if (headers_sent()) {
			$phpThumb->ErrorImage('Headers already sent ('.basename(__FILE__).' line '.__LINE__.')');
			exit;
		}
		SendSaveAsFileHeaderIfNeeded();

		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $nModified).' GMT');
		if (@$_SERVER['HTTP_IF_MODIFIED_SINCE'] && ($nModified == strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])) && @$_SERVER['SERVER_PROTOCOL']) {
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			exit;
		}

		if ($getimagesize = @GetImageSize($phpThumb->cache_filename)) {
			header('Content-Type: '.phpthumb_functions::ImageTypeToMIMEtype($getimagesize[2]));
		}
		if (!@$PHPTHUMB_CONFIG['cache_force_passthru'] && ereg('^'.preg_quote($nice_docroot).'(.*)$', $nice_cachefile, $matches)) {
			header('Location: '.dirname($matches[1]).'/'.urlencode(basename($matches[1])));
		} else {
			@readfile($phpThumb->cache_filename);
		}
		exit;

	}
	return true;
}

// check to see if file already exists in cache, and output it with no processing if it does
$phpThumb->SetCacheFilename();
if (is_file($phpThumb->cache_filename)) {
	RedirectToCachedFile();
} else {
	$phpThumb->DebugMessage('Cached file "'.$phpThumb->cache_filename.'" does not exist, processing as normal', __FILE__, __LINE__);
}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[6]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '6') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

if ($phpThumb->rawImageData) {

	// great

} elseif (@$_GET['new']) {

	// generate a blank image resource of the specified size/background color/opacity
	if (($phpThumb->w <= 0) || ($phpThumb->h <= 0)) {
		$phpThumb->ErrorImage('"w" and "h" parameters required for "new"');
	}
	@list($bghexcolor, $opacity) = explode('|', $_GET['new']);
	if (!phpthumb_functions::IsHexColor($bghexcolor)) {
		$phpThumb->ErrorImage('BGcolor parameter for "new" is not valid');
	}
	$opacity = (strlen($opacity) ? $opacity : 100);
	if ($phpThumb->gdimg_source = phpthumb_functions::ImageCreateFunction($phpThumb->w, $phpThumb->h)) {
		$alpha = (100 - min(100, max(0, $opacity))) * 1.27;
		if ($alpha) {
			$phpThumb->setParameter('is_alpha', true);
			ImageAlphaBlending($phpThumb->gdimg_source, false);
			ImageSaveAlpha($phpThumb->gdimg_source, true);
		}
		$new_background_color = phpthumb_functions::ImageHexColorAllocate($phpThumb->gdimg_source, $bghexcolor, false, $alpha);
		ImageFilledRectangle($phpThumb->gdimg_source, 0, 0, $phpThumb->w, $phpThumb->h, $new_background_color);
	} else {
		$phpThumb->ErrorImage('failed to create "new" image ('.$phpThumb->w.'x'.$phpThumb->h.')');
	}

} elseif (!$phpThumb->src) {

	$phpThumb->ErrorImage('Usage: '.$_SERVER['PHP_SELF'].'?src=/path/and/filename.jpg'."\n".'read Usage comments for details');

} elseif (eregi('^http\://', $phpThumb->src)) {

	ob_start();
	$HTTPurl = strtr($phpThumb->src, array(' '=>'%20'));
	if ($fp = fopen($HTTPurl, 'rb')) {

		$rawImageData = '';
		do {
			$buffer = fread($fp, 8192);
			if (strlen($buffer) == 0) {
				break;
			}
			$rawImageData .= $buffer;
		} while (true);
		fclose($fp);
		$phpThumb->setSourceData($rawImageData, urlencode($phpThumb->src));

	} else {

		$fopen_error = strip_tags(ob_get_contents());
		ob_end_clean();
		if (ini_get('allow_url_fopen')) {
			$phpThumb->ErrorImage('cannot open "'.$HTTPurl.'" - fopen() said: "'.$fopen_error.'"');
		} else {
			$phpThumb->ErrorImage('"allow_url_fopen" disabled');
		}

	}
	ob_end_clean();

}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[7]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '7') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

$phpThumb->GenerateThumbnail();

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[8]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '8') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

if ($phpThumb->config_allow_parameter_file && $phpThumb->file) {

	$phpThumb->RenderToFile($phpThumb->ResolveFilenameToAbsolute($phpThumb->file));
	if ($phpThumb->config_allow_parameter_goto && $phpThumb->goto && eregi('^(f|ht)tps?://', $phpThumb->goto)) {
		// redirect to another URL after image has been rendered to file
		header('Location: '.$phpThumb->goto);
		exit;
	}

} else {

	if ((file_exists($phpThumb->cache_filename) && is_writable($phpThumb->cache_filename)) || is_writable(dirname($phpThumb->cache_filename))) {

		$phpThumb->CleanUpCacheDirectory();
		if ($phpThumb->RenderToFile($phpThumb->cache_filename)) {
			chmod($phpThumb->cache_filename, 0644);
			RedirectToCachedFile();
		} else {
			$phpThumb->DebugMessage('Failed: RenderToFile('.$phpThumb->cache_filename.')', __FILE__, __LINE__);
		}

	} else {

		$phpThumb->DebugMessage('Cannot write to $phpThumb->cache_filename ('.$phpThumb->cache_filename.') because that directory ('.dirname($phpThumb->cache_filename).') is not writable', __FILE__, __LINE__);

	}

}

////////////////////////////////////////////////////////////////
// Debug output, to try and help me diagnose problems
$phpThumb->DebugTimingMessage('phpThumbDebug[9]', __FILE__, __LINE__);
if (@$_GET['phpThumbDebug'] == '9') {
	$phpThumb->phpThumbDebug();
}
////////////////////////////////////////////////////////////////

$phpThumb->OutputThumbnail();

?>