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
	if( (id != null) && (document.forms['p4a'].elements[id] != null) && (document.forms['p4a'].elements[id].disabled == false) ) {
		document.forms['p4a'].elements[id].focus();
	}
}

function correctPNG()
{
	var agt = navigator.userAgent.toLowerCase();
	var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
	if (!is_ie) return;

	for(var i=0; i<document.images.length; i++) {
		var img = document.images[i];
		var imgName = img.src.toUpperCase();

		if (imgName.substring(imgName.length-3, imgName.length) == "PNG") {
			var imgID = (img.id) ? "id='" + img.id + "' " : "";
			var imgClass = (img.className) ? "class='" + img.className + "' " : "";
			var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' ";
			var imgStyle = "display:inline-block;" + img.style.cssText;
			var imgAttribs = img.attributes;

			for (var j=0; j<imgAttribs.length; j++) {
				var imgAttrib = imgAttribs[j];

				if (imgAttrib.nodeName == "align") {
					if (imgAttrib.nodeValue == "left") imgStyle = "float:left;" + imgStyle;
					if (imgAttrib.nodeValue == "right") imgStyle = "float:right;" + imgStyle;
					break;
				}
			}

			var strNewHTML = "<span " + imgID + imgClass + imgTitle;
			strNewHTML += " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";";
			strNewHTML += "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader";
			strNewHTML += "(src=\'" + img.src + "\', sizingMethod='image');\" ></" + "span>";
			img.outerHTML = strNewHTML;
			i = i-1;
		}
	}
}