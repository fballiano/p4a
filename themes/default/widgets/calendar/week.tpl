<div flexy:raw="{style:h}">
{toolbar:h}

<table class="border_box p4a_calendar p4a_calendar_week">
<tr>
	<th>&nbsp;</th>
	<th class="p4a_calendar_week_header" flexy:foreach="days,day" flexy:raw="{day[day_actions]:h}">{day[day_number]}</th>
</tr>
<tr flexy:foreach="days[0][hours],k,hour">
	<th class="p4a_calendar_week_header2">{hour[time]}</th>
	<td flexy:foreach="days,day">
		<?php foreach ($day['hours'][$k]['events'] as $event) { ?>
			<span class="p4a_calendar_appointment"><?=$event[0]?></span><br /><?=$event[1]?><br /><br />
		<?php } ?>
	</td>
</tr>
</table>
</div>