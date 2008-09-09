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
 * To contact the authors write to:                                     <br />
 * CreaLabs SNC                                                         <br />
 * Via Medail, 32                                                       <br />
 * 10144 Torino (Italy)                                                 <br />
 * Website: {@link http://www.crealabs.it}                              <br />
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
?><table id="<?php echo $this->getId() ?>" <?php echo $this->composeStringClass() ?> <?php echo $this->composeStringProperties() ?>>
	<?php if ($this->getLabel()): ?>
	<caption><?php echo __($this->getLabel()) ?></caption>
	<?php endif; ?>

	<?php if ($this->_show_row_indicator): ?>
	<col class="select" />
	<?php endif; ?>
	<?php foreach ($table_cols as $col): ?>
	<col <?php echo $col['properties'] ?> />
	<?php endforeach; ?>

	<?php if (@$headers): ?>
	<thead>
		<tr>
			<?php if ($this->_show_row_indicator): ?>
			<th class="p4a_row_indicator">&nbsp;</th>
			<?php endif; ?>
			<?php foreach ($headers as $header): ?>
			<?php if ($header['action']): ?>
				<th>
					<?php if ($header['order'] == 'asc'): ?>
						<div style="float:right">&#x25BC;</div>
					<?php elseif ($header['order'] == 'desc'): ?>
						<div style="float:right">&#x25B2;</div>
					<?php endif; ?>
					<a href="#" <?php echo $header['action']?>><?php echo $header['value']?></a>
				</th>
			<?php else: ?>
				<th>
					<?php if ($header['order'] == 'asc'): ?>
						<div style="float:right">&#x25BC;</div>
					<?php elseif ($header['order'] == 'desc'): ?>
						<div style="float:right">&#x25B2;</div>
					<?php endif; ?>
					<?php echo $header['value']?>
				</th>
			<?php endif; ?>
			<?php endforeach; ?>
		</tr>
	</thead>
	<?php endif; ?>

	<?php if (!empty($table_rows)): ?>
	<tbody <?php echo $this->rows->composeStringProperties()?> <?php echo $this->rows->composeStringClass()?>>
		<?php $i = 0; ?>
		<?php foreach ($table_rows as $row): ?>
			<?php $i++; ?>
			<tr>
				<?php if ($this->_show_row_indicator): ?>
			    <th class="p4a_row_indicator">
			    	<?php if ($row['row']['active']): ?>
						&#x25BA;
					<?php else: ?>
						&nbsp;
					<?php endif; ?>
			    </th>
			    <?php endif; ?>

				<?php foreach ($row['cells'] as $cell): ?>
					<td class="p4a_table_rows<?php echo ($i%2)+1?> <?php echo $cell['type']?>"><?php if ($cell['clickable']): ?><a href="#" <?php echo $cell['action']?>><?php echo $cell['value']?></a><?php else: ?><?php echo $cell['value']?><?php endif; ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<?php endif; ?>
	<?php if (isset($navigation_bar)): ?>
		<tr><th colspan='99' class="p4a_toolbar"><?php echo $navigation_bar ?></th></tr>
	<?php endif; ?>
</table>