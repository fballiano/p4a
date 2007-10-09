<div <?php echo $style?>>
<?php echo $toolbar?>

<table class="border_box p4a_calendar">
<tr>
	<th>&nbsp;</th>
	<?php foreach ($weekdays as $dayname): ?>
	<th class="p4a_calendar_header"><?php echo $dayname ?></th>
	<?php endforeach; ?>
</tr>
<?php foreach ($weeks as $week): ?>
<tr>
	<th class="p4a_calendar_weeknumber" <?php echo $week['week_actions']?>><?php echo $week['week_number']?></th>
	<?php foreach ($week['days'] as $day): ?>
	<?php if ($day['day_number']): ?>
	<td class="border_box">
		<table>
			<tr><th <?php echo $day['day_actions']?>><?php echo $day['day_number']?></th></tr>
			<tr>
				<td>
					<?php foreach ($day['events'] as $event): ?>
					<span class="p4a_calendar_appointment"><?php echo $event[0]?></span> <?php echo $event[1]?><br />
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
	</td>
	<?php else: ?>
	<td>&nbsp;</td>
	<?php endif; ?>
	<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>
</div>