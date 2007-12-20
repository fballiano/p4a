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
require_once "$dir/config.php";

//Checking if license has been approved
//Removing this code or hide this message to the user is agains P4A license and
//give P4A authors the right to start a lawsuit against you for license violation
/*
if (!isset($_COOKIE['p4a_license_accepted'])) {
	if (isset($_GET['license']) and $_GET['license'] == 'accepted') {
		setcookie('p4a_license_accepted', 1, time() + 315360000, P4A_APPLICATION_PATH);
	} else {
		echo "<?xml version='1.0' encoding='utf-8'?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'><html xmlns='http://www.w3.org/1999/xhtml'>";
		echo "<head><title>Software license</title>";
		echo "<link href='" . P4A_THEME_PATH . "/screen.css' rel='stylesheet' type='text/css' media='all'></link>";
		echo "<link href='" . P4A_THEME_PATH . "/screen.css' rel='stylesheet' type='text/css' media='print'></link>";
		echo "<link href='" . P4A_THEME_PATH . "/print.css' rel='stylesheet' type='text/css' media='print'></link>";
		echo "<link href='" . P4A_THEME_PATH . "/handheld.css' rel='stylesheet' type='text/css' media='handheld'></link>";
		echo "<script type='text/javascript' src='" . P4A_THEME_PATH . "/iefixes.js'></script>";
		echo "</head><body style='text-align:center'><h1 style='padding:10px;'>This software is based on <a href='http://p4a.sourceforge.net/welcome'>P4A - PHP For Applications</a> and it's released under the <a href='http://www.gnu.org/copyleft/gpl.html'>GNU GPL License</a>.</h1>";
		if (@file_exists(dirname(dirname(__FILE__)) . '/COPYING')) {
			echo "<p style='margin:auto;width:500px;height:400px;overflow:auto;padding:10px;text-align:left' class='border_box'>";
			echo nl2br(htmlspecialchars(file_get_contents(dirname(dirname(__FILE__)) . '/COPYING')));
			echo "</p>";
			echo "<p style='text-align:center'><a href='mailto:info@crealabs.it?subject=P4A license problem' class='link_button' style='width:350px;text-align:left;margin:auto'><img class='img_button' alt='' src='" . P4A_ICONS_PATH . "/32/error." . P4A_ICONS_EXTENSION . "' />I need more info or there are some problems</a>";
			echo "<a href='.?license=accepted' class='link_button' style='width:350px;text-align:left;margin:auto'><img class='img_button' alt='' src='" . P4A_ICONS_PATH . "/32/apply." . P4A_ICONS_EXTENSION . "' />All is OK, let's procede to the application</a></p>";
		} else {
			echo "<p style='text-align:center'><span style='border:2px solid red;padding:5px;'>LICENSE FILE NOT FOUND, PLEASE <a href='mailto:info@crealabs.it?subject=P4A license violation'>WRITE US</a> REPORTING THIS ISSUE.</span></p>";
		}
		echo "</body></html>";
		die();
	}
}
*/

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
require_once "$dir/objects/widgets/icon.php";
require_once "$dir/objects/widgets/label.php";
require_once "$dir/objects/widgets/line.php";
require_once "$dir/objects/widgets/link.php";
require_once "$dir/objects/widgets/menu.php";
require_once "$dir/objects/widgets/message.php";
require_once "$dir/objects/widgets/sheet.php";
require_once "$dir/objects/widgets/toolbar.php";
require_once "$dir/objects/widgets/table.php";
require_once "$dir/objects/widgets/tab_pane.php";
require_once "$dir/objects/widgets/frames/fieldset.php";

//Toolbar Extensions
require_once "$dir/objects/widgets/toolbars/actions.php";
require_once "$dir/objects/widgets/toolbars/navigation.php";
require_once "$dir/objects/widgets/toolbars/simple.php";
require_once "$dir/objects/widgets/toolbars/standard.php";
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