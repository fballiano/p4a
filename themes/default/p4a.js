function executeEvent(object_name, action_name, param1, param2, param3, param4)
{
	if (typeof tinyMCE != "undefined") {
		for (var i in tinyMCE.instances) {
			var instance = tinyMCE.instances[i];
			if (tinyMCE.isInstance(instance) && instance.getDoc() != null) {
				instance.triggerSave();
			}
		}
	}

	if (!param1) param1 = "";
	if (!param2) param2 = "";
	if (!param3) param3 = "";
	if (!param4) param4 = "";

	document.forms['p4a']._object.value = object_name;
	document.forms['p4a']._action.value = action_name;
	document.forms['p4a']._ajax.value = 0;
	document.forms['p4a'].param1.value = param1;
	document.forms['p4a'].param2.value = param2;
	document.forms['p4a'].param3.value = param3;
	document.forms['p4a'].param4.value = param4;

	if (typeof document.forms['p4a'].onsubmit == "function") {
		document.forms['p4a'].onsubmit();
	}

	document.forms['p4a'].submit();
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

	for (var i in tinyMCE.instances) {
		var instance = tinyMCE.instances[i];
		if (tinyMCE.isInstance(instance) && instance.getDoc() != null) {
			instance.triggerSave();
		}
	}

	if (!param1) param1 = "";
	if (!param2) param2 = "";
	if (!param3) param3 = "";
	if (!param4) param4 = "";

	document.forms['p4a']._object.value = object_name;
	document.forms['p4a']._action.value = action_name;
	document.forms['p4a']._ajax.value = 1;
	document.forms['p4a'].param1.value = param1;
	document.forms['p4a'].param2.value = param2;
	document.forms['p4a'].param3.value = param3;
	document.forms['p4a'].param4.value = param4;

	if (typeof document.forms['p4a'].onsubmit == "function") {
		document.forms['p4a'].onsubmit();
	}

	var query_string = form2string(document.forms['p4a']);
	var ajax_params = {
		method: 'post',
		parameters: query_string,
		onComplete: function(response) {processAjaxResponse(response)}
	}
	new Ajax.Request('index.php', ajax_params);
}

function processAjaxResponse(response)
{
	document.forms['p4a']._action_id.value = response.responseXML.getElementsByTagName('ajax-response')[0].attributes[0].value;

	var widgets = response.responseXML.getElementsByTagName('widget');
	for (i=0; i<widgets.length; i++) {

   		var object_id = widgets[i].attributes[0].value;
		if ($(object_id) != undefined) {
   			
   			var display = widgets[i].attributes[1].value;
   			var html = widgets[i].getElementsByTagName('html').item(0);

   			if (html) {
   				$(object_id).parentNode.style.display = 'block';   		
   				$(object_id).parentNode.innerHTML = html.firstChild.data;
   			}
   			var javascript = widgets[i].getElementsByTagName('javascript').item(0);
   			if (javascript) {
   				eval(javascript.firstChild.data);
   			}
   		}
	}
	
	if (window.fixPng) fixPng();
	hideLoading();
}

function form2string(form)
{
	var sReturn = '';
	var e;

	for (i=0; i<form.elements.length; i++) {
		e = form.elements[i];
		switch (e.type) {
			case 'checkbox':
			case 'radio':
				value = new String(e.value);
				if (e.checked && value.length>0) {
					sReturn += e.name + '=' + escape(value) + '&';
				}
				break;
			default:
				value = new String(e.value);
				sReturn += e.name + '=' + escape(value) + '&';
		}
	}

	return sReturn.substr(0, sReturn.length - 1);
}

function showLoading()
{
	Element.show('p4a_loading');
}

function hideLoading()
{
	Element.hide('p4a_loading');
}