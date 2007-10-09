<div <?php echo $style?>>
<?php echo $toolbar?>

<table class="border_box p4a_calendar p4a_calendar_day">
<tr>
	<th colspan="2" class="p4a_calendar_header"><?php echo $dayname?></th>
</tr>
<?php foreach ($hours as $hour): ?>
<tr>
	<th class="p4a_calendar_week_header2"><?php echo $hour['time']?></th>
	<td>
		<?php foreach ($hour['events'] as $event): ?>
		<span class="p4a_calendar_appointment"><?php echo $event[0]?></span> <?php echo $event[1]?><br />
		<?php endforeach; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
</div>