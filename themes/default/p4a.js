var IE7_PNG_SUFFIX = ".png";

function executeEvent(object_name, action_name, param1, param2, param3, param4)
{
	if (!param1) param1 = "";
	if (!param2) param2 = "";
	if (!param3) param3 = "";
	if (!param4) param4 = "";

	document.forms['p4a']._object.value = object_name;
	document.forms['p4a']._action.value = action_name;
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