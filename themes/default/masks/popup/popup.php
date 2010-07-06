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
<div id="p4a_popup">
	<div id="p4a_main_container">
		<div id="p4a_main_inner_container" style='width:<?php echo $this->_tpl_vars['main']->getWidth() ?>'>
			<div id="p4a_popup_top_container">
				<h2>
					<a style="float:right" href="#" <?php echo $this->close_popup_button->composeStringActions() ?> id="p4a_popup_close_handler"><img src="<?php echo P4A_ICONS_PATH ?>/32/actions/window-close.png" /></a>
					<?php echo P4A_Generate_Widget_Layout_Table($_icon, $_title) ?>
				</h2>
				
				<?php if (isset($top)): ?>
					<?php echo $top?>
				<?php endif; ?>
			</div>
		
			<?php if (isset($main)) echo "<div id='p4a_main'>$main</div>" ?>
			<div class="br"></div>
		</div>
	</div>
	
	<!-- Removing or modifying the following lines is forbidden and it's a
	     violation of the GNU Lesser General Public License. -->
	<div id="p4a_footer">
		Powered by <a href="http://p4a.sourceforge.net">P4A - PHP For Applications</a> <?php echo P4A_VERSION?>
	</div>
</div>