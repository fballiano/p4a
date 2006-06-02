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

	query_string = formData2QueryString(document.forms['p4a']);
	xmlhttpPost('index.php',query_string,'processResponse');
}

function xmlhttpPost(strURL, strSubmit, strResultFunc) {

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
        xmlHttpReq.setRequestHeader('Content-Type', 
		     'application/x-www-form-urlencoded');
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
	for (i = 0; i < widgets.length ; i++)
	{
   		object_id = widgets[i].attributes[0].value;
   		string_tag = widgets[i].getElementsByTagName('string').item(0);
   		if (string_tag) {
   			string_data = string_tag.firstChild.data;
   		}
   		redesign(object_id,string_data);
	}	
}        

function redesign(object_id,string_data)
{
	$(object_id).parentNode.innerHTML = string_data;
}

/*
 * Copyright 2005 Matthew Eernisse (mde@fleegix.org)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Original code by Matthew Eernisse (mde@fleegix.org)
 * Additional bugfixes by Mark Pruett (mark.pruett@comcast.net)
 *
*/

// The var docForm should be a reference to a <form>

function formData2QueryString(docForm) {

  var submitContent = '';
  var formElem;
  var lastElemName = '';
  
  for (i = 0; i < docForm.elements.length; i++) {
    
    formElem = docForm.elements[i];
    switch (formElem.type) {
      // Text fields, hidden form elements
      case 'text':
      case 'hidden':
      case 'password':
      case 'textarea':
      case 'select-one':
        submitContent += formElem.name + '=' + escape(formElem.value) + '&'
        break;
        
      // Radio buttons
      case 'radio':
        if (formElem.checked) {
          submitContent += formElem.name + '=' + escape(formElem.value) + '&'
        }
        break;
        
      // Checkboxes
      case 'checkbox':
        if (formElem.checked) {
          // Continuing multiple, same-name checkboxes
          if (formElem.name == lastElemName) {
            // Strip of end ampersand if there is one
            if (submitContent.lastIndexOf('&') == submitContent.length-1) {
              submitContent = submitContent.substr(0, submitContent.length - 1);
            }
            // Append value as comma-delimited string
            submitContent += ',' + escape(formElem.value);
          }
          else {
            submitContent += formElem.name + '=' + escape(formElem.value);
          }
          submitContent += '&';
          lastElemName = formElem.name;
        }
        break;
        
    }
  }
  // Remove trailing separator
  submitContent = submitContent.substr(0, submitContent.length - 1);
  return submitContent;
}