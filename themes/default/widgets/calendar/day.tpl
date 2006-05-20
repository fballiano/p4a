<div flexy:raw="{style:h}">
{toolbar:h}

<table class="border_box p4a_calendar p4a_calendar_day">
<tr>
	<th colspan="2" class="p4a_calendar_header">{dayname}</th>
</tr>
<tr flexy:foreach="hours,hour">
	<th class="p4a_calendar_week_header2">{hour[time]}</th>
	<td>
		{foreach:hour[events],event}
		<span class="p4a_calendar_appointment">{event[0]}</span> {event[1]}<br />
		{end:}
	</td>
</tr>
</table>
</div>