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
	showLoading();
	prepareExecuteEvent(object_name, action_name, param1, param2, param3, param4);
	document.getElementById('p4a')._ajax.value = 1;

	$.ajax({
		type: 'POST',
		url: 'index.php',
		dataType: 'xml',
		data: $('#p4a').formSerialize(),
		error: function (object, error, exception) {alert('Communication error: ' + exception)},
		success: function (response) {processAjaxResponse(response)},
		complete: function () {hideLoading()}
	});
}


function processAjaxResponse(response)
{
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
	$('#p4a_loading').show();
}

function hideLoading()
{
	$('#p4a_loading').hide();
}