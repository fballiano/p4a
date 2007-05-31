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

function setFocus(id)
{
	try {
		document.forms['p4a'].elements[id].focus();
	} catch (e) {}
}

function executeAjaxEvent(object_name, action_name, param1, param2, param3, param4)
{
	prepareExecuteEvent(object_name, action_name, param1, param2, param3, param4);
	document.getElementById('p4a')._ajax.value = 1;

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

		if (window.fixPng) fixPng();
	} catch (e) {
		ajaxError();
	}
}

function ajaxError()
{
	alert("Communication error");
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
	var tooltip = $('#' + text_id);
	tooltip.css('left', handler.offset().left + handler.width() + 20);
	tooltip.jqm({overlay:0}).jqmShow();
	handler.mouseout(function() {tooltip.jqmHide()});
	handler.click(function() {tooltip.jqmHide()});
}

function toggleColorPicker(id)
{
	var left = $('#' + id + 'button').offset().left + $('#' + id + 'button').width() + 10;
	var top = $('#' + id + 'button').offset().top;
	$('#colorpicker').css('left', left).css('top', top).farbtastic('#' + id + 'input').toggle()
}

$(document).ajaxStart(function(request, settings){showLoading()});
$(document).ajaxStop(function(request, settings){hideLoading()});
$(document).ajaxError(function(request, settings){ajaxError()});