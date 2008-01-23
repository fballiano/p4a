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
require_once "$dir/libraries/pear/System.php";
require_once "$dir/libraries/validate.php";
require_once "$dir/libraries/p4a_db_select.php";
require_once "$dir/libraries/p4a_db_table.php";

//Core
require_once "$dir/p4a_object.php";
require_once "$dir/p4a_error.php";
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
require_once "$dir/objects/masks/error.php";
require_once "$dir/objects/masks/preview.php";

//Widget Extensions
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
require_once "$dir/objects/widgets/toolbar.php";
require_once "$dir/objects/widgets/table.php";
require_once "$dir/objects/widgets/tab_pane.php";
require_once "$dir/objects/widgets/frames/fieldset.php";

//Toolbar Extensions
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
if (P4A_EXTENDED_ERRORS) {
	error_reporting(E_ALL);
} else {
	error_reporting(E_ALL ^ E_NOTICE);
}