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
?>

<?php if (isset($menu)): ?>
<div id="p4a_menu" class="row">
    <?php echo $menu?>
</div>
<?php endif; ?>

<?php if (isset($top)): ?>
<div id="p4a_top" class="row">
    <?php echo $top?>
</div>
<?php endif; ?>

<?php if (strlen($_title)): ?>
<div class="page-header row">
    <h1>
        <?php if ($_icon): ?>
            <span class="<?= P4A_ICONSET ?> <?= P4A_ICONSET ?>-<?= $_icon ?>"></span>
        <?php endif ?>
        <?= $_title ?>
    </h1>
</div>
<?php endif; ?>

<?php if (isset($main)): ?>
<div id="p4a_main" class="row"><?php echo $main ?></div>
<?php endif; ?>

<?php if (isset($status_bar)): ?>
<div id="p4a_statusbar" class="row" style="height:<?php $_statusbar_height = $this->getStatusbarHeight(); echo "{$_statusbar_height[0]}{$_statusbar_height[1]}" ?>;">
    <?php echo $status_bar; ?>
</div>
<?php endif; ?>

<!-- Removing or modifying the following lines is forbidden and it's a
     violation of the GNU Lesser General Public License. -->
<div id="p4a_footer"  class="row">
    Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a> <?php echo P4A_VERSION?>
</div>