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

// Server operating system
if (!defined('P4A_OS')) {
	if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
		define('P4A_OS', 'windows');
	} else {
		define('P4A_OS', 'linux');
	}
}

// Directory separator
if (!defined('_DS_')) define('_DS_', DIRECTORY_SEPARATOR);

// System string separator
if (!defined('_SSS_')) define('_SSS_', PATH_SEPARATOR);

//Server constants
if (!defined('P4A_SERVER_NAME')) {
	define('P4A_SERVER_NAME', $_SERVER['SERVER_NAME']);
}

if (!defined('P4A_SERVER_URL')) {
	define('P4A_SERVER_URL', 'http://' . $_SERVER['SERVER_NAME']);
}

if (!defined('P4A_SERVER_DIR')) {
	define('P4A_SERVER_DIR', realpath($_SERVER['DOCUMENT_ROOT']));
}

//P4A root constants
if (!defined('P4A_ROOT_DIR')) {
 	 define('P4A_ROOT_DIR', dirname(dirname(realpath(__FILE__))));
}

if (!defined('P4A_ROOT_PATH')) {
	if (strpos(P4A_ROOT_DIR, P4A_SERVER_DIR) === false) {
		define('P4A_ROOT_PATH', '/p4a');
	} else {
		$tmp_path = str_replace(P4A_SERVER_DIR, '', P4A_ROOT_DIR);
		$tmp_path = str_replace('\\', '/', $tmp_path);
		$tmp_path = P4A_Strip_Double_Slashes("/$tmp_path");
		define('P4A_ROOT_PATH', $tmp_path);
	}
}

if (!defined('P4A_ROOT_URL')) {
	define('P4A_ROOT_URL', P4A_SERVER_URL . P4A_ROOT_PATH);
}

//P4A libraries constants
if (!defined('P4A_LIBRARIES_PATH')) {
	define('P4A_LIBRARIES_PATH', P4A_ROOT_PATH . '/libraries');
}

if (!defined('P4A_LIBRARIES_DIR')) {
 	 define('P4A_LIBRARIES_DIR', P4A_ROOT_DIR . '/libraries');
}

if (!defined('P4A_LIBRARIES_URL')) {
	define('P4A_LIBRARIES_URL', P4A_SERVER_URL . P4A_LIBRARIES_PATH);
}

//Applications constants
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

if (!defined('P4A_APPLICATION_NAME')) {
	define('P4A_APPLICATION_NAME', str_replace(_DS_,'_',P4A_APPLICATION_PATH));
}

//Applications libraries constants
if (!defined('P4A_APPLICATION_LIBRARIES_PATH')) {
	define('P4A_APPLICATION_LIBRARIES_PATH', P4A_APPLICATION_PATH . '/libraries/');
}

if (!defined('P4A_APPLICATION_LIBRARIES_DIR')) {
	define('P4A_APPLICATION_LIBRARIES_DIR', P4A_SERVER_DIR . P4A_APPLICATION_LIBRARIES_PATH);
}

if (!defined('P4A_APPLICATION_LIBRARIES_URL')) {
	define('P4A_APPLICATION_LIBRARIES_URL', P4A_SERVER_URL . P4A_APPLICATION_LIBRARIES_PATH);
}

//Uploads constants
if (!defined('P4A_UPLOADS_PATH')) {
	define('P4A_UPLOADS_PATH', P4A_APPLICATION_PATH . '/uploads');
}

if (!defined('P4A_UPLOADS_DIR')) {
	if (P4A_OS == 'windows') {
		define('P4A_UPLOADS_DIR', P4A_Strip_Double_Backslashes(P4A_SERVER_DIR . str_replace('/', '\\', P4A_UPLOADS_PATH)));
	} else {
		define('P4A_UPLOADS_DIR', P4A_Strip_Double_Slashes(P4A_SERVER_DIR . P4A_UPLOADS_PATH));
	}
}

if (!defined('P4A_UPLOADS_URL')) {
	define('P4A_UPLOADS_URL', P4A_UPLOADS_PATH);
}

//Temporary uploads constants
define('P4A_UPLOADS_TMP_NAME', 'tmp');
define('P4A_UPLOADS_TMP_PATH', P4A_UPLOADS_PATH . '/' . P4A_UPLOADS_TMP_NAME);
define('P4A_UPLOADS_TMP_DIR', P4A_SERVER_DIR . P4A_UPLOADS_TMP_PATH);
define('P4A_UPLOADS_TMP_URL', P4A_SERVER_URL . P4A_UPLOADS_TMP_PATH);

//Current theme configuration
if (!defined('P4A_THEME_NAME')) {
	define('P4A_THEME_NAME', 'default');
}

if (!defined('P4A_THEME_PATH')) {
	define('P4A_THEME_PATH', P4A_ROOT_PATH . '/themes/' . P4A_THEME_NAME);
}

if (!defined('P4A_THEME_DIR')) {
	define('P4A_THEME_DIR', P4A_ROOT_DIR . _DS_ . 'themes' . _DS_ . P4A_THEME_NAME);
}

//Current theme colors
if (!defined('P4A_THEME_FG')) define('P4A_THEME_FG', '#4b718a');
if (!defined('P4A_THEME_BG')) define('P4A_THEME_BG', '#fafafa');
if (!defined('P4A_THEME_BORDER')) define('P4A_THEME_BORDER', '#ccc');

if (!defined('P4A_THEME_INPUT_FG')) define('P4A_THEME_INPUT_FG', '#4b718a');
if (!defined('P4A_THEME_INPUT_BG')) define('P4A_THEME_INPUT_BG', '#fff');
if (!defined('P4A_THEME_INPUT_BORDER')) define('P4A_THEME_INPUT_BORDER', '#c6d3de');

if (!defined('P4A_THEME_SELECTED_FG')) define('P4A_THEME_SELECTED_FG', '#000');
if (!defined('P4A_THEME_SELECTED_BG')) define('P4A_THEME_SELECTED_BG', '#e2e7ed');
if (!defined('P4A_THEME_SELECTED_BORDER')) define('P4A_THEME_SELECTED_BORDER', '#c6d3de');

if (!defined('P4A_THEME_TOOLTIP_FG')) define('P4A_THEME_TOOLTIP_FG', '#777');
if (!defined('P4A_THEME_TOOLTIP_BG')) define('P4A_THEME_TOOLTIP_BG', '#fff');
if (!defined('P4A_THEME_TOOLTIP_BORDER')) define('P4A_THEME_TOOLTIP_BORDER', '#c6d3de');

if (!defined('P4A_THEME_EVEN_ROW')) define('P4A_THEME_EVEN_ROW', '#f4f7fa');
if (!defined('P4A_THEME_ODD_ROW')) define('P4A_THEME_ODD_ROW', '#e2e7ed');

//Image configuration
if (!defined('P4A_TABLE_THUMB_HEIGHT')) define('P4A_TABLE_THUMB_HEIGHT', 40);

//Icons configuration
if (!defined('P4A_ICONS_NAME')) {
	define('P4A_ICONS_NAME', 'default');
}

if (!defined('P4A_ICONS_PATH')) {
	define('P4A_ICONS_PATH', P4A_ROOT_PATH . '/icons/' . P4A_ICONS_NAME );
}

if (!defined('P4A_ICONS_DIR')) {
	define('P4A_ICONS_DIR', P4A_ROOT_DIR . _DS_ . 'icons' . _DS_ . P4A_ICONS_NAME);
}

if (!defined('P4A_ICONS_URL')) {
	define('P4A_ICONS_URL', P4A_ROOT_URL . P4A_ICONS_PATH);
}

if (!defined('P4A_ICONS_EXTENSION')) define('P4A_ICONS_EXTENSION', 'png');

//I18N
if (!defined('P4A_LOCALE')) define('P4A_LOCALE', 'en_US');

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
if (!defined('P4A_ENABLE_AUTO_INCLUSION')) define('P4A_ENABLE_AUTO_INCLUSION', true);
if (!defined('P4A_ENABLE_RENDERING')) define('P4A_ENABLE_RENDERING', true);
if (!defined('P4A_FIELD_CLASS')) define('P4A_FIELD_CLASS', 'P4A_Field');
if (!defined('P4A_EXTENDED_ERRORS')) define('P4A_EXTENDED_ERRORS', false);
if (!defined('P4A_AUTO_DB_PRIMARY_KEYS')) define('P4A_AUTO_DB_PRIMARY_KEYS', true);
if (!defined('P4A_AUTO_DB_SEQUENCES')) define('P4A_AUTO_DB_SEQUENCES', true);
if (!defined('P4A_AJAX_ENABLED')) define('P4A_AJAX_ENABLED', true);
if (!defined('P4A_AJAX_DEBUG')) define('P4A_AJAX_DEBUG', false);
if (!defined('P4A_EXCEPTION_HANDLER')) define('P4A_EXCEPTION_HANDLER', 'P4A_Exception_Handler');
if (!defined('P4A_ERROR_HANDLER')) define('P4A_ERROR_HANDLER', 'P4A_Error_Handler');
if (!defined('P4A_PASSWORD_OBFUSCATOR')) define('P4A_PASSWORD_OBFUSCATOR', '**********');
if (!defined('P4A_DATEPICKER_START_YEAR')) define('P4A_DATEPICKER_START_YEAR', date("Y")-10);
if (!defined('P4A_DATEPICKER_END_YEAR')) define('P4A_DATEPICKER_END_YEAR', date("Y")+10);
if (!defined('P4A_DB_PROFILE')) define('P4A_DB_PROFILE', false);
if (!defined('P4A_DENIED_EXTENSIONS')) {
	define('P4A_DENIED_EXTENSIONS', 'php|php3|php5|phtml|asp|aspx|ascx|jsp|cfm|cfc|pl|bat|exe|dll|reg|cgi');
}

define('P4A_VERSION', '3.8.5');
define('P4A_ORDER_ASCENDING', 'ASC');
define('P4A_ORDER_DESCENDING', 'DESC');
define('P4A_NULL', 'P4A_NULL');
define('PROCEED', 'P4A_PROCEED');
define('ABORT', 'P4A_ABORT');
define('P4A_DATE', '%Y-%m-%d');
define('P4A_TIME', '%H:%M:%S');
define('P4A_DATETIME', '%Y-%m-%d %H:%M:%S');
define('P4A_DEFAULT_ERROR_REPORTING', E_ALL ^ E_NOTICE);
define('P4A_EXTENDED_ERROR_REPORTING', E_ALL);
define('P4A_DEFAULT_MINIMAL_REPORTING', P4A_DEFAULT_ERROR_REPORTING ^ E_WARNING);
define('P4A_FILESYSTEM_ERROR', 1);
define('P4A_UPLOAD_PROGRESS', function_exists('uploadprogress_get_info'));

if (!defined('P4A_GD')) {
	if (function_exists('ImageJPEG') and function_exists('ImagePNG') and function_exists('ImageGIF')) {
		define('P4A_GD', true);
	} else {
		define('P4A_GD', false);
	}
}