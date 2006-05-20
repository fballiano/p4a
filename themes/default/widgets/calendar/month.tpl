<div flexy:raw="{style:h}">
{toolbar:h}

<table class="border_box p4a_calendar">
<tr>
	<th>&nbsp;</th>
	<th class="p4a_calendar_header" flexy:foreach="weekdays,dayname">{dayname}</th>
</tr>
<tr flexy:foreach="weeks,week">
	<th class="p4a_calendar_weeknumber" flexy:raw="{week[week_actions]:h}">{week[week_number]}</th>
	{foreach:week[days],day}
	<td class="border_box" flexy:if="day[day_number]">
		<table>
			<tr><th flexy:raw="{day[day_actions]:h}">{day[day_number]}</th></tr>
			<tr>
				<td>
					{foreach:day[events],event}
					<span class="p4a_calendar_appointment">{event[0]}</span> {event[1]}<br />
					{end:}
				</td>
			</tr>
		</table>
	</td>
	<td flexy:if="!day[day_number]">&nbsp;</td>
	{end:}
</tr>
</table>
</div>