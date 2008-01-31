p4a_event_execute_prepare = function (object_name, action_name, param1, param2, param3, param4)
{
	p4a_rte_update_all_instances(document.forms['p4a']);

	if (!param1) param1 = "";
	if (!param2) param2 = "";
	if (!param3) param3 = "";
	if (!param4) param4 = "";

	var f = document.getElementById('p4a');

	f._object.value = object_name;
	f._action.value = action_name;
	f.param1.value = param1;
	f.param2.value = param2;
	f.param3.value = param3;
	f.param4.value = param4;

	if (typeof f.onsubmit == "function") f.onsubmit();
}

p4a_rte_update_all_instances = function (form)
{
	for (i=0; i<form.elements.length; i++) {
		var e = form.elements[i];
		if (e.type == 'textarea') {
			try {
				FCKeditorAPI.GetInstance(e.id).UpdateLinkedField();
			} catch (e) {}
		}
	}
}

p4a_event_execute = function (object_name, action_name, param1, param2, param3, param4)
{
	p4a_event_execute_prepare(object_name, action_name, 0, param1, param2, param3, param4);
	document.getElementById('p4a')._ajax.value = 0;
	document.getElementById('p4a').submit();
}

p4a_keypressed_is_return = function (event)
{
	var characterCode = (window.event) ? event.keyCode : event.which;
	return (characterCode == 13);
}

p4a_keypressed_get = function (event)
{
	return (window.event) ? event.keyCode : event.which;
}

p4a_focus_set = function (id)
{
	try {
		document.forms['p4a'].elements[id].focus();
	} catch (e) {}
}

p4a_event_execute_ajax = function (object_name, action_name, param1, param2, param3, param4)
{
	p4a_event_execute_prepare(object_name, action_name, param1, param2, param3, param4);
	document.getElementById('p4a')._ajax.value = 1;
	$('#colorpicker').hide();

	$('#p4a').ajaxSubmit({
		dataType: 'xml',
		success: p4a_ajax_process_response
	});
}

p4a_ajax_process_response = function (response)
{
	try {
		document.forms['p4a']._action_id.value = response.getElementsByTagName('ajax-response')[0].attributes[0].value;

		var widgets = response.getElementsByTagName('widget');
		for (i=0; i<widgets.length; i++) {
	   		var object_id = widgets[i].attributes[0].value;
	   		var object = $('#'+object_id);
			if (object.size() > 0) {
	   			var display = widgets[i].attributes[1].value;
	   			var html = widgets[i].getElementsByTagName('html').item(0);
	   			if (html) {
	   				object.parent().css('display', 'block').html(html.firstChild.data);
	   			}
	   			var javascript = widgets[i].getElementsByTagName('javascript').item(0);
	   			if (javascript) {
	   				eval(javascript.firstChild.data);
	   			}
	   		}
		}
		
		var messages = response.getElementsByTagName('message');
		if (messages.length > 0) {
			var new_messages_container = $('<div class="p4a_system_messages"></div>').appendTo(document.body);
			for (i=0; i<messages.length; i++) {
				$('<div class="p4a_system_message">'+messages[i].firstChild.data+'</div>').appendTo(new_messages_container);
			}
			p4a_messages_show();
		}
		
		if (typeof p4a_png_fix == 'function') p4a_png_fix();
	} catch (e) {
		p4a_ajax_error();
	}
}

p4a_ajax_error = function ()
{
	p4a_refresh();
}

p4a_refresh = function ()
{
	document.location = 'index.php';
}

p4a_loading_show = function ()
{
	$('#p4a_loading').jqm({modal:true, overlay:0}).show();
}

p4a_loading_hide = function ()
{
	$('#p4a_loading').hide();
}

p4a_tooltip_show = function (handler, text_id)
{
	handler = $(handler);
	var tooltip = $('#p4a_tooltip');
	if (tooltip.length == 0) {
		tooltip = $("<div id='p4a_tooltip'></div>").appendTo("body");
	}
	tooltip.html('<div id="p4a_tooltip_inner_container">' + $('#' + text_id).html() + '</div>');
	tooltip.css('top', handler.offset().top);
	tooltip.css('left', handler.offset().left + handler.width() + 100);
	tooltip.jqm({overlay:0}).jqmShow();
	handler.mouseout(function() {tooltip.jqmHide()});
}

p4a_colorpicker_toggle = function (id)
{
	var left = $('#' + id + 'button').offset().left + $('#' + id + 'button').width() + 10;
	var top = $('#' + id + 'button').offset().top;
	var colorpicker = $('#colorpicker');
	if (colorpicker.length == 0) {
		colorpicker = $('<div id="colorpicker"></div>').appendTo("body");
	}
	colorpicker.css('left', left);
	colorpicker.css('top', top);
	colorpicker.farbtastic('#' + id + 'input');
	colorpicker.toggle();
}

p4a_calendar_open = function (id)
{
	var element = $('#'+id);
	if(!element.hasClass($.datepicker.markerClassName)) {
		element.datepicker();
	}
	$.datepicker.showFor(element);
	return false;
}

p4a_calendar_select = function (value_id, description_id)
{
	$.get(
		$('#p4a').attr('action'),
		{_p4a_date_format: $('#'+value_id).attr('value')},
		function (new_value) {
			$('#'+description_id).attr('value', new_value);
		}
	);
}

p4a_messages_show = function ()
{
	if ($('.p4a_system_messages:visible').size() > 0) return false;
	var p4a_system_messages = $('.p4a_system_messages:hidden:first');
	if (p4a_system_messages.children().size() == 0) {
		p4a_system_messages.remove();
		return false;
	}
	var left = ($(window).width() - p4a_system_messages.outerWidth()) / 2;
	p4a_system_messages
		.css('top', $(window).scrollTop() + 20)
		.css('left', left)
		.fadeIn('normal')
		.animate({opacity: 1.0}, 2000)
		.fadeOut('normal', function() {
			$(this).hide().remove();
			p4a_messages_show();
		});
}

$(document).ajaxStart(p4a_loading_show);
$(document).ajaxStop(p4a_loading_hide);
$(document).ajaxError(p4a_ajax_error);

$(function () {
	p4a_messages_show();
	setTimeout(p4a_loading_hide, 1000);
});