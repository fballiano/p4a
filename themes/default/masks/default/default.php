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

if (isset($menu) and isset($top)) {
	$_top_margin = 70;
} elseif (isset($menu)) {
	$_top_margin = 25;
} elseif (isset($top)) {
	$_top_margin = 45;
} else {
	$_top_margin = 0;
}
?>

<?php if (isset($sidebar_left)): $_sidebar_left_width='280';?>
<div id="p4a_sidebar_left" style="padding-top:<?php echo $_top_margin+10?>px; width:<?php echo $_sidebar_left_width?>px;">
	<?php echo $sidebar_left?>
</div>
<?php endif; ?>

<?php if (isset($sidebar_right)):  $_sidebar_right_width='280';?>
<div id="sidebar_right" style="padding-top:<?php echo $_top_margin+10?>px; width:<?php echo $_sidebar_right_width?>px;">
	<?php echo $sidebar_right?>
</div>
<?php endif; ?>

<!-- TOP -->
<div id="p4a_top_container">
	<?php if (isset($menu)): ?>
	<div id="p4a_menu">
		<?php echo $menu?>
		<div class="br"></div>
	</div>
	<?php endif; ?>

	<?php if (isset($top)): ?>
	<div id="p4a_top">
		<?php echo $top?>
	</div>
	<?php endif; ?>
</div>

<!-- MAIN  -->
<div id="p4a_main_container" style="margin-top:<?php echo $_top_margin?>px; <?php if (isset($_sidebar_left_width)) echo "margin-left:{$_sidebar_left_width}px;"?> <?php if (isset($_sidebar_right_width)) echo "margin-right:{$_sidebar_right_width}px;"?>">
	<?php if (strlen($_title)): ?>
	<h2><?php echo P4A_Generate_Widget_Layout_Table($_icon, $_title) ?></h2>
	<?php endif; ?>

	<?php if (isset($main)): ?>
	<div id="p4a_main_inner_container">
		<?php echo $main?>
	</div>
	<?php endif; ?>
</div>