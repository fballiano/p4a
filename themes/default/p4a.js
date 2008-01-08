function prepareExecuteEvent(object_name, action_name, param1, param2, param3, param4)
{
	updateAllRichTextEditors(document.forms['p4a']);

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

function executeEvent(object_name, action_name, param1, param2, param3, param4)
{
	prepareExecuteEvent(object_name, action_name, 0, param1, param2, param3, param4);
	document.getElementById('p4a')._ajax.value = 0;
	document.getElementById('p4a').submit();
}

function isReturnPressed(event)
{
	var characterCode = (window.event) ? event.keyCode : event.which;
	return (characterCode == 13);
}

function getKeyPressed(event)
{
	return (window.event) ? event.keyCode : event.which;
}

p4a_set_focus = function (id)
{
	try {
		document.forms['p4a'].elements[id].focus();
	} catch (e) {}
}

function executeAjaxEvent(object_name, action_name, param1, param2, param3, param4)
{
	prepareExecuteEvent(object_name, action_name, param1, param2, param3, param4);
	document.getElementById('p4a')._ajax.value = 1;
	$('#colorpicker').hide();

	$('#p4a').ajaxSubmit({
		dataType: 'xml',
		success: function (response) {processAjaxResponse(response)}
	});
}

function processAjaxResponse(response)
{
	try {
		document.forms['p4a']._action_id.value = response.getElementsByTagName('ajax-response')[0].attributes[0].value;

		var widgets = response.getElementsByTagName('widget');
		for (i=0; i<widgets.length; i++) {
	   		var object_id = widgets[i].attributes[0].value;
			if ($(object_id) != undefined) {
	   			var display = widgets[i].attributes[1].value;
	   			var html = widgets[i].getElementsByTagName('html').item(0);
	   			if (html) {
	   				var element = document.getElementById(object_id);
	   				element.parentNode.style.display = 'block';
	   				element.parentNode.innerHTML = html.firstChild.data;
	   			}
	   			var javascript = widgets[i].getElementsByTagName('javascript').item(0);
	   			if (javascript) {
	   				eval(javascript.firstChild.data);
	   			}
	   		}
		}
		
		var messages = response.getElementsByTagName('message');
		if (messages.length > 0) {
			for (i=0; i<messages.length; i++) {
				$('<div>'+messages[i].firstChild.data+'</div>').appendTo('#p4a_messages');
			}
			p4a_messages_show();
		}

		if (window.fixPng) fixPng();
	} catch (e) {
		ajaxError();
	}
}

function ajaxError()
{
	document.location = 'index.php';
}

function updateAllRichTextEditors(form)
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

function showLoading()
{
	$('#p4a_loading').jqm({modal:true, overlay:0}).show();
}

function hideLoading()
{
	$('#p4a_loading').hide();
}

function showPopup()
{
	p4a_popup = $('#popup');

	p4a_popup.css('left', 100000).show();
	var width = p4a_popup.width();
	var top = $(window).scrollTop() + (($(window).height() - p4a_popup.height() - 100) / 2) + "px";
	var left = (($(window).width() - p4a_popup.width()) / 2) + "px";
	p4a_popup.hide();

	p4a_popup.css('width', width);
	p4a_popup.css('top', top);
	p4a_popup.css('left', left);
	$('#popupCloseHandler').css('float', 'right');
	p4a_popup.jqm({modal:true}).jqmShow();
}

function hidePopup()
{
	p4a_popup.jqmHide();
}

function showTooltip(handler, text_id)
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

function toggleColorPicker(id)
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
	$('#p4a_messages').slideDown('normal', function() {p4a_messages_timeout = setTimeout(p4a_messages_hide, 2000)});
}

p4a_messages_hide = function ()
{
	$('#p4a_messages').slideUp('normal', function () {$('#p4a_messages').empty()});
}

$(document).ajaxStart(function(request, settings){showLoading()});
$(document).ajaxStop(function(request, settings){hideLoading()});
$(document).ajaxError(function(request, settings){ajaxError()});

$(function () {
	var p4a_messages = $('#p4a_messages');
	p4a_messages.mouseover(function () {
		clearTimeout(p4a_messages_timeout);
	});
	p4a_messages.mouseout(function () {
		p4a_messages_timeout = setTimeout(p4a_messages_hide, 500);
	});
	if (p4a_messages.children().size() > 0) {
		p4a_messages_show();
	}
});