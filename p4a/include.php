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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	//Configuration container
	require_once(dirname(__FILE__) . '/config.php');

	// Changing inclusion path
	$include_path = explode(_SSS_, ini_get('include_path'));
	$dot_key = array_search('.', $include_path);
	unset($include_path[ $dot_key ]) ;
	$new_include_path = '.' . _SSS_ . P4A_APPLICATION_LIBRARIES_DIR . _SSS_ . P4A_LIBRARIES_DIR . _SSS_ . P4A_ROOT_DIR . '/p4a/libraries/pear' . _SSS_ . join(_SSS_, $include_path);
	ini_set('include_path', $new_include_path);

	//Core
	require_once(dirname(__FILE__) . '/p4a_object.php');

	//Libraries
	require_once(dirname(__FILE__) . '/libraries/standard.php');
	require_once(dirname(__FILE__) . '/libraries/smarty/libs/Smarty.class.php');
	require_once(dirname(__FILE__) . '/libraries/pear/DB.php');
	require_once(dirname(__FILE__) . '/libraries/date.php');
	require_once(dirname(__FILE__) . '/libraries/number.php');
	require_once(dirname(__FILE__) . '/libraries/pdf/class.ezpdf.php');
	require_once(dirname(__FILE__) . '/libraries/smarty/libs/plugins/function.assign.php');

	//Core
	require_once(dirname(__FILE__) . '/p4a_error.php');
	require_once(dirname(__FILE__) . '/i18n.php');

	//I18N
	require_once(dirname(__FILE__) . '/i18n/i18n_currency.php');
	require_once(dirname(__FILE__) . '/i18n/i18n_datetime.php');
	require_once(dirname(__FILE__) . '/i18n/i18n_messages.php');
	require_once(dirname(__FILE__) . '/i18n/i18n_numbers.php');

	//Objects
	require_once(dirname(__FILE__) . '/objects/application.php');
	require_once(dirname(__FILE__) . '/objects/db.php');
	require_once(dirname(__FILE__) . '/objects/mask.php');
	require_once(dirname(__FILE__) . '/objects/listener.php');
	require_once(dirname(__FILE__) . '/objects/report.php');
	require_once(dirname(__FILE__) . '/objects/collection.php');
	require_once(dirname(__FILE__) . '/objects/data_field.php');
	//require_once(dirname(__FILE__) . '/objects/data_source.php');
	require_once(dirname(__FILE__) . '/objects/db_source.php');
	//require_once(dirname(__FILE__) . '/objects/data_sources/db_source.php');
	//require_once(dirname(__FILE__) . '/objects/data_sources/txt_source.php');
	require_once(dirname(__FILE__) . '/objects/widget.php');

	//Listeners
	require_once(dirname(__FILE__) . '/objects/listeners/p4a_error.php');

	//Widget Extensions
	require_once(dirname(__FILE__) . '/objects/widgets/button.php');
	require_once(dirname(__FILE__) . '/objects/widgets/canvas.php');
	require_once(dirname(__FILE__) . '/objects/widgets/field.php');
	require_once(dirname(__FILE__) . '/objects/widgets/href.php');
	require_once(dirname(__FILE__) . '/objects/widgets/image.php');
	require_once(dirname(__FILE__) . '/objects/widgets/label.php');
	require_once(dirname(__FILE__) . '/objects/widgets/line.php');
	require_once(dirname(__FILE__) . '/objects/widgets/link.php');
	require_once(dirname(__FILE__) . '/objects/widgets/menu.php');
	require_once(dirname(__FILE__) . '/objects/widgets/multivalue_field.php');
	require_once(dirname(__FILE__) . '/objects/widgets/sheet.php');
	require_once(dirname(__FILE__) . '/objects/widgets/sheets_group.php');
	require_once(dirname(__FILE__) . '/objects/widgets/toolbar.php');
	require_once(dirname(__FILE__) . '/objects/widgets/table.php');

	//Toolbar Extensions
	require_once(dirname(__FILE__) . '/objects/widgets/toolbars/actions_toolbar.php');
	require_once(dirname(__FILE__) . '/objects/widgets/toolbars/standard_toolbar.php');

	//We can have more applications on same site and same browser instance

	session_name('sn_' . preg_replace('~\W~', '_', P4A_APPLICATION_NAME) . '_includes');

	//Applications Objects Includes
	session_start();
	if (!array_key_exists('P4A_INCLUDES', $_SESSION)) {
		$objects_dir = P4A_APPLICATION_DIR . '/objects';
		$_SESSION['P4A_INCLUDES'] = array();
		include_p4a_objects($objects_dir);
	}

	foreach($_SESSION['P4A_INCLUDES'] as $include_file) {
		require_once($include_file);
	}
	session_write_close();
?>
