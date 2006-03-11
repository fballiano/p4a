<?php

/**
 * P4A - PHP For Applications.
 *
 * p4a configuration keeper.
 * It's possible to override any p4a constant.
 * Any P4A constant start with "P4A_" token
 * URI Naming:
 *
 * P4A_*_DIR  = naming for directories URI. E.g.: /home/http/p4a						<br>
 * P4A_*_FILE = naming for files URI. E.g.: /home/http/p4a/index.php					<br>
 * P4A_*_PATH = naming for server document root relatives URI. E.g.: /p4a/index.php	<br>
 * P4A_*_URL  = naming for http URI. E.g.: http://localhost/index.php
 *
 * Note:
 * URL  + PATH = URL	<br>
 * DIR  + PATH = DIR
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
	 *
	 */
	// Server Operating System
	if (!defined('P4A_OS'))
	{
		if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
			define('P4A_OS', 'windows');
		} else {
			define('P4A_OS', 'linux');
		}
	}

	// Directory Separator
	if (!defined('_DS_')) {
		define('_DS_', DIRECTORY_SEPARATOR);
	}

	// System String Separator
	if (!defined('_SSS_')) {
		define('_SSS_', PATH_SEPARATOR) ;
	}

	if (!defined('P4A_PASSWORD_OBFUSCATOR')) {
		define('P4A_PASSWORD_OBFUSCATOR', '**********');
	}


	// Automatic Application Name Detection
	if (!defined('P4A_APPLICATION_NAME')) {
		$aCwd = explode( _DS_, getcwd() ) ;
		define('P4A_APPLICATION_NAME', $aCwd[ ( sizeof( $aCwd ) - 1 ) ]) ;
	}


	//Server Constants
	if (!defined('P4A_SERVER_NAME')) {
		define('P4A_SERVER_NAME', $_SERVER['SERVER_NAME']) ;
	}

	if (!defined('P4A_SERVER_URL')) {
		define('P4A_SERVER_URL', 'http://' . $_SERVER['SERVER_NAME']) ;
	}

	if (!defined('P4A_SERVER_DIR')){
		define('P4A_SERVER_DIR', realpath($_SERVER['DOCUMENT_ROOT'])) ;
	}

	//P4A Root Constants
	if (!defined('P4A_ROOT_DIR')){
	 	 define('P4A_ROOT_DIR', dirname(dirname(realpath(__FILE__)))) ;
	}

	if (!defined('P4A_ROOT_PATH')){
		if (strpos(P4A_ROOT_DIR, P4A_SERVER_DIR) === false) {
			define('P4A_IN_DOCUMENT_ROOT', false) ;
			define('P4A_ROOT_PATH', "/p4a") ;
		} else {
			define('P4A_IN_DOCUMENT_ROOT', true) ;
			define('P4A_ROOT_PATH', str_replace( '\\', '/', str_replace(P4A_SERVER_DIR, '', P4A_ROOT_DIR))) ;
		}
	}

	if (!defined('P4A_ROOT_URL')){
		define('P4A_ROOT_URL', P4A_SERVER_URL . P4A_ROOT_PATH);
	}

	//P4A Plugins Constants
	if (!defined('P4A_LIBRARIES_PATH')){
		define('P4A_LIBRARIES_PATH', P4A_ROOT_PATH . '/libraries') ;
	}

	if (!defined('P4A_LIBRARIES_DIR')){
	 	 define('P4A_LIBRARIES_DIR', P4A_ROOT_DIR . P4A_LIBRARIES_PATH) ;
	}

	if (!defined('P4A_LIBRARIES_URL')){
		define('P4A_LIBRARIES_URL', P4A_SERVER_URL . P4A_LIBRARIES_PATH);
	}

	//Applications Constants
	if (!defined('P4A_APPLICATION_PATH')) {
		$tmp_dir = dirname($_SERVER["SCRIPT_NAME"]);
		if ($tmp_dir == '/') {
			$tmp_dir = '';
		}

		define("P4A_APPLICATION_PATH", $tmp_dir);
	}

	if (!defined('P4A_APPLICATION_DIR')) {
		if (P4A_OS == "windows") {
			define('P4A_APPLICATION_DIR', P4A_SERVER_DIR . str_replace('/', '\\', P4A_APPLICATION_PATH));
		} else {
			define('P4A_APPLICATION_DIR', P4A_SERVER_DIR . P4A_APPLICATION_PATH);
		}
	}

	if (!defined('P4A_APPLICATION_URL')) {
		define('P4A_APPLICATION_URL', P4A_SERVER_URL . P4A_APPLICATION_PATH);
	}

	//Applications Libraries Constants
	if (!defined('P4A_APPLICATION_LIBRARIES_PATH')){
		define('P4A_APPLICATION_LIBRARIES_PATH', P4A_APPLICATION_PATH . '/libraries/' );
	}

	if (!defined('P4A_APPLICATION_LIBRARIES_DIR')){
		define('P4A_APPLICATION_LIBRARIES_DIR', P4A_SERVER_DIR . P4A_APPLICATION_LIBRARIES_PATH);
	}

	if (!defined('P4A_APPLICATION_LIBRARIES_URL')){
		define('P4A_APPLICATION_LIBRARIES_URL', P4A_SERVER_URL . P4A_APPLICATION_LIBRARIES_PATH);
	}

	//Uploads Constants
	if (!defined('P4A_UPLOADS_PATH')){
		define('P4A_UPLOADS_PATH', P4A_APPLICATION_PATH . '/uploads' );
	}

	if (!defined('P4A_UPLOADS_DIR')){
		define('P4A_UPLOADS_DIR', P4A_SERVER_DIR . P4A_UPLOADS_PATH);
	}

	if (!defined('P4A_UPLOADS_URL')){
		define('P4A_UPLOADS_URL', P4A_UPLOADS_PATH);
	}

	//Temporary Uploads Constants
	define('P4A_UPLOADS_TMP_NAME', 'tmp' );
	define('P4A_UPLOADS_TMP_PATH', P4A_UPLOADS_PATH . '/' . P4A_UPLOADS_TMP_NAME );
	define('P4A_UPLOADS_TMP_DIR', P4A_SERVER_DIR . P4A_UPLOADS_TMP_PATH);
	define('P4A_UPLOADS_TMP_URL', P4A_SERVER_URL . P4A_UPLOADS_TMP_PATH);

	//Themes Path
	if (!defined('P4A_THEMES_PATH')){
		define('P4A_THEMES_PATH', P4A_ROOT_PATH . '/themes');
	}

	//Themes Dir
	if (!defined('P4A_THEMES_DIR')){
		define('P4A_THEMES_DIR', P4A_ROOT_DIR . P4A_THEMES_PATH);
	}

	//Current Theme Configuration
	if (!defined('P4A_THEME_NAME')){
		define('P4A_THEME_NAME', 'default');
	}

	if (!defined('P4A_THEME_PATH')){
		define('P4A_THEME_PATH', P4A_THEMES_PATH . '/' . P4A_THEME_NAME);
	}

	if (!defined('P4A_THEME_DIR')) {
		if (P4A_IN_DOCUMENT_ROOT) {
			define('P4A_THEME_DIR', P4A_ROOT_DIR . _DS_ . 'themes' . _DS_ . P4A_THEME_NAME);
		} else {
			define('P4A_THEME_DIR', P4A_THEMES_DIR . P4A_THEME_PATH);
		}
	}

	//Icons configuration
	if (!defined('P4A_ICONS_NAME')){
		define('P4A_ICONS_NAME', 'default' );
	}

	if (!defined('P4A_ICONS_PATH')){
		define('P4A_ICONS_PATH', P4A_ROOT_PATH . '/icons/' . P4A_ICONS_NAME );
	}

	if (!defined('P4A_ICONS_DIR')){
		if (P4A_IN_DOCUMENT_ROOT) {
			define('P4A_ICONS_DIR', P4A_ROOT_DIR . P4A_ICONS_PATH);
		} else {
			define('P4A_ICONS_DIR', dirname(P4A_ROOT_DIR) . P4A_ICONS_PATH);
		}
	}

	if (!defined('P4A_ICONS_URL')){
		define('P4A_ICONS_URL', P4A_ROOT_URL . P4A_ICONS_PATH);
	}

	if (!defined('P4A_ICONS_EXTENSION')) {
		define('P4A_ICONS_EXTENSION', 'png');
	}
	
	//Template Compile Dir
	if (!defined('P4A_COMPILE_DIR')) {
		define("P4A_COMPILE_DIR", ini_get('session.save_path') . _DS_ . 'p4a_' . str_replace(_DS_, "_", str_replace(':', '', P4A_APPLICATION_DIR)));
	}

	if (!(is_dir(P4A_COMPILE_DIR) and is_readable(P4A_COMPILE_DIR) and is_writable(P4A_COMPILE_DIR))) {
		mkdir(P4A_COMPILE_DIR) or die("ERROR: Unable to create directory " . P4A_COMPILE_DIR . " or directory is not readable/writable.");
	}

	//I18N
	if (!defined('P4A_LOCALE')) {
		define('P4A_LOCALE', 'en_US');
	}

	if (!defined('P4A_APPLICATION_LOCALES_PATH')) {
		define('P4A_APPLICATION_LOCALES_PATH', P4A_APPLICATION_PATH . '/i18n');
	}

	if (!defined('P4A_APPLICATION_LOCALES_DIR')) {
		define('P4A_APPLICATION_LOCALES_DIR', P4A_APPLICATION_DIR . '/i18n');
	}

	if (!defined('P4A_APPLICATION_LOCALES_URL')) {
		define('P4A_APPLICATION_LOCALES_URL', P4A_APPLICATION_URL . '/i18n');
	}

	//Force handheld rendering
	if (!defined('P4A_FORCE_HANDHELD_RENDERING')) {
		define('P4A_FORCE_HANDHELD_RENDERING', false);
	}

	//P4A SYSTEM CONSTANTS
	if (!defined('P4A_EXTENDED_ERRORS')){
		define('P4A_EXTENDED_ERRORS', false);
	}
	
	if (!defined('P4A_DENIED_EXTENSIONS')) {
		define('P4A_DENIED_EXTENSIONS', 'php|php3|php5|phtml|asp|aspx|ascx|jsp|cfm|cfc|pl|bat|exe|dll|reg|cgi');
	}

	define('P4A_ORDER_ASCENDING','ASC');
	define('P4A_ORDER_DESCENDING','DESC');
	define('P4A_NULL','P4A_NULL');
	define('PROCEED', 'P4A_PROCEED');
	define('ABORT', 'P4A_ABORT');
	define('P4A_DATE', '%Y-%m-%d');
	define('P4A_TIME', '%H:%M:%S');
	define('P4A_DATETIME', '%Y-%m-%d %H:%M:%S');
	
	if (!defined('P4A_GD') and function_exists('ImageJPEG') and 
		function_exists('ImagePNG') and function_exists('ImageGIF')) {
		define('P4A_GD', true);
	} else {
		define('P4A_GD', false);
	}
	
?>