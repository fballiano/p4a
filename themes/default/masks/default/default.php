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

<?php if (isset($sidebar_left)): ?>
<div id="p4a_sidebar_left" style="padding-top:<?php echo $_top_margin+10 ?>px; width:<?php $_sidebar_left_width = $this->getLeftSidebarWidth(); echo "{$_sidebar_left_width[0]}{$_sidebar_left_width[1]}" ?>;">
	<?php echo $sidebar_left ?>
</div>
<?php endif; ?>

<?php if (isset($sidebar_right)): ?>
<div id="p4a_sidebar_right" style="padding-top:<?php echo $_top_margin+10 ?>px; width:<?php $_sidebar_right_width = $this->getRightSidebarWidth(); echo "{$_sidebar_right_width[0]}{$_sidebar_right_width[1]}" ?>;">
	<?php echo $sidebar_right ?>
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
<div id="p4a_main_container" style="margin-top:<?php echo $_top_margin?>px;<?php if (isset($_sidebar_left_width)) echo "margin-left:{$_sidebar_left_width[0]}{$_sidebar_left_width[1]};"?> <?php if (isset($_sidebar_right_width)) echo "margin-right:{$_sidebar_right_width[0]}{$_sidebar_right_width[1]};"?>">
	<?php if (strlen($_title)): ?>
	<h2><?php echo P4A_Generate_Widget_Layout_Table($_icon, $_title) ?></h2>
	<?php endif; ?>

	<?php if (isset($main)): ?>
	<div id="p4a_main_inner_container">
		<div id="p4a_main"><?php echo $main?></div>
		<div class="br"></div>
	</div>
	<?php endif; ?>
	
	<?php if (isset($status_bar)): ?>
		<div id="p4a_statusbar" style="height:<?php $_statusbar_height = $this->getStatusbarHeight(); echo "{$_statusbar_height[0]}{$_statusbar_height[1]}" ?>;">
			<?php echo $status_bar; ?>
		</div>
	<?php endif; ?>
	
	<!-- Removing or modifying the following lines is forbidden and it's a
	     violation of the GNU Lesser General Public License. -->
	<div id="p4a_footer">
		Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a> <?php echo P4A_VERSION?>
	</div>
</div>