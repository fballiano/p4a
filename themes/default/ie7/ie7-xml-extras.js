/*
	IE7, version 0.8 (alpha) (2005/05/23)
	Copyright: 2004-2005, Dean Edwards (http://dean.edwards.name/)
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/

// modelled after: http://www.mozilla.org/xmlextras/

function XMLHttpRequest() {

	/* this is a simple wrapper for microsoft's activex object */

	// IE6 has a better version
	var $LIB = /MSIE 6/.test(navigator.userAgent) ? "Msxml2" : "Microsoft";

	// because it's a wrapper, we'll make a clear distinction in code
	//  between the wrapper (public) and the object being wrapped (private)
	var _private = new ActiveXObject($LIB + ".XMLHTTP");
	var _public = this;

	// default property values
	var _defaults = {
		readyState: 0,
		responseXML: null,
		responseText: "",
		status: 0,
		statusText: ""
	};

	// update property values
	function _update() {
		for (var i in _defaults) {
			_public[i] = (typeof _private[i] == "unknown") ? _defaults[i] : _private[i];
		}
	};

	// handle a change in state
	function _onreadystatechange() {
		// refresh properties
		_update();
		// call the public event handler (if it's set)
		if (typeof _public.onreadystatechange == "function") {
			_public.onreadystatechange();
		}
	};

	// public interface
	_public.abort = function() {
		_private.abort();
	};
	_public.getAllResponseHeaders = function() {
		return _private.getAllResponseHeaders();
	};
	_public.getResponseHeader = function($header) {
		return _private.getResponseHeader($header);
	};
	_public.openRequest = function($method, $url, $async, $user, $password) {
		_private.open($method, $url, $async, $user, $password);
		// need to update this here for some reason
		_private.onreadystatechange = _onreadystatechange;
	};
	_public.open = _public.openRequest;
	_public.send = function($body) {
		_private.send($body);
	};
	_public.setRequestHeader = function($header, $value) {
		_private.setRequestHeader($header, $value);
	};

	// initialise attributes
	_update();
};
XMLHttpRequest.prototype = {
	toString: function() {return "[object XMLHttpRequest]"},
	// not supported
	overrideMimeType: new Function,
	channel: null
};

function DOMParser() {/* empty constructor */};
DOMParser.prototype = {
	toString: function() {return "[object DOMParser]"},
	parseFromString: function($str, $contentType) {
		var $xmlDocument = new ActiveXObject("Microsoft.XMLDOM");
		$xmlDocument.loadXML($str);
		return $xmlDocument;
	},
	// not supported
	parseFromStream: new Function,
	baseURI: ""
};

function XMLSerializer() {/* empty constructor */};
XMLSerializer.prototype = {
	toString: function() {return "[object XMLSerializer]"},
	serializeToString: function($root) {
		return $root.xml || $root.outerHTML;
	},
	// not supported
	serializeToStream: new Function
};
