var IE7_PNG_SUFFIX = ".png";

function executeEvent(object_name, action_name, param1, param2, param3, param4)
{
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

function isReturnPressed(e)
{
	var characterCode;

	if(e && e.which) {
		e = e;
		characterCode = e.which;
	} else {
		e = event;
		characterCode = e.keyCode;
	}

	if(characterCode == 13) {
		return true;
	} else {
		return false;
	}
}

function setFocus(id)
{
	if ((id != null) && (id != '') && (document.forms['p4a'].elements[id] != null) && (document.forms['p4a'].elements[id].disabled == false)) {
		document.forms['p4a'].elements[id].focus();
	}
}

function executeAjaxEvent(object_name, action_name, param1, param2, param3, param4)
{
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

	query_string = form2string(document.forms['p4a']);
	xmlhttpPost('index.php',query_string,'processResponse');
}

function xmlhttpPost(strURL, strSubmit, strResultFunc)
{
	var xmlHttpReq = false;
	
	// Mozilla/Safari
	if (window.XMLHttpRequest) {
		xmlHttpReq = new XMLHttpRequest();
		xmlHttpReq.overrideMimeType('text/xml');
	}
	// IE
	else if (window.ActiveXObject) {
		xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlHttpReq.open('POST', strURL, true);
	xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlHttpReq.onreadystatechange = function() {
		if (xmlHttpReq.readyState == 4) {
			eval(strResultFunc + '(xmlHttpReq.responseXML);');
		}
	}
	xmlHttpReq.send(strSubmit);
}

function processResponse(response)
{
	widgets = response.getElementsByTagName('widget');
	for (i = 0; i < widgets.length; i++) {
   		object_id = widgets[i].attributes[0].value;
   		string_tag = widgets[i].getElementsByTagName('string').item(0);
   		if (string_tag) {
   			string_data = string_tag.firstChild.data;
   		}
   		redesign(object_id,string_data);
	}	
}        

function redesign(object_id, string_data)
{
	$(object_id).parentNode.innerHTML = string_data;
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
				if (e.checked) {
					sReturn += e.name + '=' + escape(e.value) + '&';
				}
				break;
			default:
				sReturn += e.name + '=' + escape(e.value) + '&';
		}
	}
	
	return sReturn.substr(0, sReturn.length - 1);
}