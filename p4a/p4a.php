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
 * @package p4a
 */

$dir = dirname(__FILE__);

//Configuration container
require_once "$dir/constants.php";

// Changing inclusion path
$include_path = explode(_SSS_, ini_get('include_path'));
$dot_key = array_search('.', $include_path);
unset($include_path[$dot_key]);
$new_include_path = '.' . _SSS_ . P4A_APPLICATION_LIBRARIES_DIR . _SSS_ . P4A_LIBRARIES_DIR . _SSS_ . P4A_ROOT_DIR . '/p4a/libraries/pear' . _SSS_ . P4A_ROOT_DIR . '/p4a/libraries' . _SSS_ .  join(_SSS_, $include_path);
ini_set('include_path', $new_include_path);

//Libraries
require_once "$dir/libraries/standard.php";
require_once "$dir/libraries/check_configuration.php";
require_once "$dir/libraries/p4a_db_select.php";
require_once "$dir/libraries/p4a_db_table.php";
require_once "$dir/libraries/validate.php";

//Core
require_once "$dir/p4a_exception.php";
require_once "$dir/p4a_object.php";
require_once "$dir/p4a_i18n.php";
require_once "$dir/p4a_db.php";

//Objects
require_once "$dir/objects/p4a.php";
require_once "$dir/objects/mask.php";
require_once "$dir/objects/collection.php";
require_once "$dir/objects/data_field.php";
require_once "$dir/objects/data_source.php";
require_once "$dir/objects/data_sources/db_source.php";
require_once "$dir/objects/data_sources/array_source.php";
require_once "$dir/objects/data_sources/dir_source.php";
require_once "$dir/objects/widget.php";

//Masks
require_once "$dir/objects/masks/base.php";
require_once "$dir/objects/masks/error.php";
require_once "$dir/objects/masks/login.php";
require_once "$dir/objects/masks/preview.php";

//Widgets
require_once "$dir/objects/widgets/box.php";
require_once "$dir/objects/widgets/button.php";
require_once "$dir/objects/widgets/canvas.php";
require_once "$dir/objects/widgets/db_navigator.php";
require_once "$dir/objects/widgets/field.php";
require_once "$dir/objects/widgets/frame.php";
require_once "$dir/objects/widgets/image.php";
require_once "$dir/objects/widgets/label.php";
require_once "$dir/objects/widgets/line.php";
require_once "$dir/objects/widgets/link.php";
require_once "$dir/objects/widgets/menu.php";
require_once "$dir/objects/widgets/message.php";
require_once "$dir/objects/widgets/sheet.php";
require_once "$dir/objects/widgets/tab_pane.php";
require_once "$dir/objects/widgets/table.php";
require_once "$dir/objects/widgets/toolbar.php";
require_once "$dir/objects/widgets/frames/fieldset.php";

//Toolbars
require_once "$dir/objects/widgets/toolbars/actions.php";
require_once "$dir/objects/widgets/toolbars/navigation.php";
require_once "$dir/objects/widgets/toolbars/simple.php";
require_once "$dir/objects/widgets/toolbars/full.php";
require_once "$dir/objects/widgets/toolbars/quit.php";

//External application inclusion
if (defined("P4A_REQUIRE_APPLICATION")) {
	if (strpos(P4A_REQUIRE_APPLICATION, "/") !== false) {
		$objects_dir = P4A_REQUIRE_APPLICATION . '/objects';
	} else {
		$objects_dir = P4A_ROOT_DIR . '/applications/' . P4A_REQUIRE_APPLICATION . '/objects';
	}
	P4A_Include_Objects($objects_dir);
}

//Application inclusion
if (P4A_ENABLE_AUTO_INCLUSION) {
	P4A_Include_Objects(P4A_APPLICATION_DIR . '/objects');
}

//Setting PHP error reporting
set_exception_handler('P4A_Exception_Handler');
if (P4A_EXTENDED_ERRORS) {
	error_reporting(P4A_EXTENDED_ERROR_REPORTING);
	set_error_handler('P4A_Error_Handler', P4A_EXTENDED_ERROR_REPORTING);
} else {
	error_reporting(P4A_DEFAULT_ERROR_REPORTING);
	set_error_handler('P4A_Error_Handler', P4A_DEFAULT_ERROR_REPORTING);
}