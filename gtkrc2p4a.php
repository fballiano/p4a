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

function modify_color($color, $multiply_factor)
{
	if (substr($color, 0, 1) != '#') {
		die("Color must be provided in the #aaa or #aabbcc form\n");
	}
	
	if (strlen($color) == 4) {
		$r = substr($color, 1, 1);
		$g = substr($color, 2, 1);
		$b = substr($color, 3, 1);
		$color = "#{$r}{$r}{$g}{$g}{$b}{$b}";
	}
	
	if (strlen($color) != 7) {
		die("Color must be provided in the #aaa or #aabbcc form\n");
	}
	
	$r = base_convert(substr($color, 1, 2), 16, 10);
	$g = base_convert(substr($color, 3, 2), 16, 10);
	$b = base_convert(substr($color, 5, 2), 16, 10);
	
	$r = intval($r * $multiply_factor);
	$g = intval($g * $multiply_factor);
	$b = intval($b * $multiply_factor);
	
	return '#' . base_convert($r, 10, 16) . base_convert($g, 10, 16) . base_convert($b, 10, 16);
}

if (empty($argv[1])) {
	die("Missing argument: gtkrc filename\n");
}

if (!file_exists($argv[1])) {
	die("gtkrc file does not exists\n");
}

if (empty($argv[2])) {
	$argv[2] = 0.9;
}

$gtkrc = file_get_contents($argv[1]);
preg_match("/gtk[_-]color[_-]scheme = \"(.*)\"/", $gtkrc, $results);
$results = explode('\n',$results[1]);

foreach ($results as $row_index=>$row_data) {
	list($k, $v) = explode(':', $row_data);
	unset($results[$row_index]);
	$results[trim($k)] = trim($v);
}

if (sizeof($results) < 6) {
	die("gtkrc does not contain a valid color set\n");
}

echo "<?php\n";
if (isset($results['fg_color'])) echo "define('P4A_THEME_FG', '{$results['fg_color']}');\n";
if (isset($results['bg_color'])) echo "define('P4A_THEME_BG', '{$results['bg_color']}');\n";
if (isset($results['bg_color'])) echo "define('P4A_THEME_BORDER', '" . modify_color($results['bg_color'], $argv[2]) . "');\n";
if (isset($results['text_color'])) echo "define('P4A_THEME_INPUT_FG', '{$results['text_color']}');\n";
if (isset($results['base_color'])) echo "define('P4A_THEME_INPUT_BG', '{$results['base_color']}');\n";
if (isset($results['base_color'])) echo "define('P4A_THEME_INPUT_BORDER', '" . modify_color($results['base_color'], $argv[2]) . "');\n";
if (isset($results['selected_fg_color'])) echo "define('P4A_THEME_SELECTED_FG', '{$results['selected_fg_color']}');\n";
if (isset($results['selected_bg_color'])) echo "define('P4A_THEME_SELECTED_BG', '{$results['selected_bg_color']}');\n";
if (isset($results['selected_bg_color'])) echo "define('P4A_THEME_SELECTED_BORDER', '" . modify_color($results['selected_bg_color'], $argv[2]) . "');\n";
if (isset($results['tooltip_fg_color'])) echo "define('P4A_THEME_TOOLTIP_FG', '{$results['tooltip_fg_color']}');\n";
if (isset($results['tooltip_bg_color'])) echo "define('P4A_THEME_TOOLTIP_BG', '{$results['tooltip_bg_color']}');\n";
if (isset($results['tooltip_bg_color'])) echo "define('P4A_THEME_TOOLTIP_BORDER', '" . modify_color($results['tooltip_bg_color'], $argv[2]) . "');\n";
echo "define('P4A_THEME_EVEN_ROW', '#eee');\n";
echo "define('P4A_THEME_ODD_ROW', '#fff');\n";