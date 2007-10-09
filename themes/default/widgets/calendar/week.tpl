<div <?php echo $style?>>
<?php echo $toolbar?>

<table class="border_box p4a_calendar p4a_calendar_week">
<tr>
	<th>&nbsp;</th>
	<?php foreach ($days as $day): ?>
	<th class="p4a_calendar_week_header" <?php echo $day['day_actions']?>><?php echo $day['day_number']?></th>
	<?php endforeach; ?>
</tr>
<?php foreach ($days[0]['hours'] as $k=>$hour): ?>
<tr>
	<th class="p4a_calendar_week_header2"><?php echo $hour['time']?></th>
	<?php foreach ($days as $day): ?>
	<td>
		<?php foreach ($day['hours'][$k]['events'] as $event) { ?>
			<span class="p4a_calendar_appointment"><?php echo $event[0]?></span><br /><?php echo $event[1]?><br /><br />
		<?php } ?>
	</td>
	<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>
</div>